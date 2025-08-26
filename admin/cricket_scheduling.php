<?php
// Full merged scheduling page with event-date validation, match_date, PDF export, winner-locking
session_start();
include_once('../include/db_connect.php'); // must define $con = mysqli_connect(...)

// Admin-only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// --- Config ---
$gameName = 'cricket';

// event_id: prefer GET ?event_id=... else choose first active event with sport containing 'tabletennis' or id=1 fallback
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
if ($event_id <= 0) {
    // try to pick an event which includes 'cricket' in sport or pick first event
    $res = $con->query("SELECT id FROM events WHERE sport LIKE '%cricket%' LIMIT 1");
    if ($res && $res->num_rows) {
        $row = $res->fetch_assoc();
        $event_id = intval($row['id']);
    } else {
        $res2 = $con->query("SELECT id FROM events LIMIT 1");
        if ($res2 && $res2->num_rows) {
            $row2 = $res2->fetch_assoc();
            $event_id = intval($row2['id']);
        } else {
            $event_id = 0;
        }
    }
}

// --- Ensure matches has needed columns: match_date, winner_name, loser_name, match_status, team1_name/team2_name (we expect these) ---
$needed = [
    'match_date'   => "ALTER TABLE matches ADD COLUMN match_date DATE DEFAULT NULL",
    'winner_name'  => "ALTER TABLE matches ADD COLUMN winner_name VARCHAR(255) DEFAULT NULL",
    'loser_name'   => "ALTER TABLE matches ADD COLUMN loser_name VARCHAR(255) DEFAULT NULL",
    'match_status' => "ALTER TABLE matches ADD COLUMN match_status VARCHAR(50) DEFAULT 'Scheduled'"
];
foreach ($needed as $col => $alterSql) {
    $check = $con->query("SHOW COLUMNS FROM matches LIKE '{$col}'");
    if (!$check || $check->num_rows === 0) {
        // attempt to add
        @$con->query($alterSql);
    }
}

// --- Fetch event start/end dates ---
$event_start = null;
$event_end   = null;
$event_row   = null;

if ($event_id > 0) {
    $stmt = $con->prepare("SELECT id, event_name, start_date, end_date FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $er = $stmt->get_result();
    if ($er && $er->num_rows) {
        $event_row = $er->fetch_assoc();
        // Assign start and end dates directly
        $event_start = !empty($event_row['start_date']) ? $event_row['start_date'] : null;
        $event_end   = !empty($event_row['end_date']) ? $event_row['end_date'] : null;
    }
    $stmt->close();
}


// --- Fetch registered cricket teams (only players role) ---
$registered_players = [];
$registered_rows = [];
$pstmt = $con->prepare("SELECT team_name, captain_name
    FROM cricket_teams
    ORDER BY team_name ASC");
$pstmt->execute();
$pres = $pstmt->get_result();
while ($r = $pres->fetch_assoc()) {
    $registered_rows[] = $r;
    $registered_players[] = $r['team_name'];
}
$pstmt->close();

// Helper: sanitize
function h($s) { return htmlspecialchars($s, ENT_QUOTES); }

// --------- Handle POST actions: delete_match, delete_round, schedule_matches, set_result, export_pdf ----------
$errors = [];
$messages = [];

// Delete single match
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_match') {
    $mid = intval($_POST['match_id'] ?? 0);
    if ($mid > 0) {
        $d = $con->prepare("DELETE FROM matches WHERE id = ?");
        $d->bind_param("i", $mid);
        $d->execute();
        $d->close();
        $messages[] = "Match deleted";
        // stay on same page
        header("Location: cricket_scheduling.php?event_id={$event_id}");
        exit();
    }
}

// Delete all matches in a round
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_round') {
    $round_no = intval($_POST['round_no'] ?? 0);
    if ($round_no > 0) {
        $d = $con->prepare("DELETE FROM matches WHERE game = ? AND round = ?");
        $d->bind_param("si", $gameName, $round_no);
        $d->execute();
        $d->close();
        $messages[] = "All matches for round deleted";
        header("Location:  cricket_scheduling.php?event_id={$event_id}");
        exit();
    }
}

// Set result (winner) — only allowed if today's date >= match_date
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'set_result') {
    $match_id = intval($_POST['match_id'] ?? 0);
    $winner = trim($_POST['winner'] ?? '');
    if ($match_id <= 0 || $winner === '') {
        $errors[] = "Invalid input for result.";
    } else {
        $mstmt = $con->prepare("SELECT team1_name, team2_name, match_date FROM matches WHERE id = ?");
        $mstmt->bind_param("i", $match_id);
        $mstmt->execute();
        $mr = $mstmt->get_result();
        if (!$mr || $mr->num_rows === 0) {
            $errors[] = "Match not found.";
        } else {
            $mrow = $mr->fetch_assoc();
            $mstmt->close();
            $t1 = $mrow['team1_name'];
            $t2 = $mrow['team2_name'];
            $mdate = $mrow['match_date'];

            // server-side date check: allow only on/after match_date
            $today = date('Y-m-d');
            if (empty($mdate)) {
                $errors[] = "Match date not set; cannot record result.";
            } elseif (strtotime($today) < strtotime($mdate)) {
                $errors[] = "You cannot set result before the scheduled match date ({$mdate}).";
            } elseif (!in_array($winner, [$t1, $t2])) {
                $errors[] = "Invalid winner selected for that match.";
            } else {
                $loser = ($winner === $t1) ? $t2 : $t1;
                $ust = $con->prepare("UPDATE matches SET winner_name = ?, loser_name = ?, match_status = 'Completed' WHERE id = ?");
                $ust->bind_param("ssi", $winner, $loser, $match_id);
                $ust->execute();
                $ust->close();
                $messages[] = "Result saved.";
                header("Location:  cricket_scheduling.php?event_id={$event_id}");
                exit();
            }
        }
    }
    if (!empty($errors)) {
        // show errors later in UI
    }
}

// Schedule matches
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'schedule_matches') {
    $round = intval($_POST['round'] ?? 0);
    $team_names = $_POST['team_names'] ?? [];
    $match_date = $_POST['match_date'] ?? null;

    // Validate round
    if ($round < 1 || $round > 4) {
        $errors[] = "Invalid round selected.";
    }

    // Validate event date range exists
    if ($event_start && $event_end && !empty($match_date)) {
        if (strtotime($match_date) < strtotime($event_start) || strtotime($match_date) > strtotime($event_end)) {
            $errors[] = "Match date must be within event dates: {$event_start} — {$event_end}.";
        }
    }

    // Clean team names: trim & remove empty
    $team_names = array_map('trim', $team_names);
    $team_names = array_values(array_filter($team_names, function($x){ return $x !== ''; }));

    // Round 1 constraints: min 2, max 8 (power of two not strictly required, BYE used)
    if ($round === 1) {
        $min = 2; $max = 8;
        $count = count($team_names);
        if ($count < $min || $count > $max) {
            $errors[] = "First Round requires between {$min} and {$max} teams.";
        }
        // Validate all names exist in registered_players
        foreach ($team_names as $nm) {
            if (!in_array($nm, $registered_players)) {
                $errors[] = "Team '{$nm}' is not registered for cricket.";
            }
        }
    } else {
        // For later rounds, compute eligible winners from prev round
        $prevRound = $round - 1;
        $wstmt = $con->prepare("SELECT DISTINCT winner_name FROM matches WHERE game = ? AND round = ? AND winner_name IS NOT NULL");
        $wstmt->bind_param("si", $gameName, $prevRound);
        $wstmt->execute();
        $wres = $wstmt->get_result();
        $eligible = [];
        while ($wr = $wres->fetch_assoc()) {
            if (!empty($wr['winner_name'])) $eligible[] = $wr['winner_name'];
        }
        $wstmt->close();
        if (count($eligible) === 0) {
            $errors[] = "Cannot schedule round {$round} because previous round has no recorded winners yet.";
        } else {
            if (count($team_names) !== count($eligible)) {
                $errors[] = "You must submit exactly " . count($eligible) . " names for this round (winners of previous round).";
            } else {
                // ensure each submitted name is in eligible list
                foreach ($team_names as $nm) {
                    if (!in_array($nm, $eligible)) {
                        $errors[] = "Player '{$nm}' is not a winner from previous round; cannot schedule.";
                    }
                }
            }
        }
    }

    // Duplicate check
    if (count($team_names) !== count(array_unique($team_names))) {
        $errors[] = "Duplicate player names are not allowed.";
    }

    // Only proceed if no errors
    if (empty($errors)) {
        // shuffle for random matchups (only for fairness)
        shuffle($team_names);
        $pairs = [];
        for ($i = 0; $i < count($team_names); $i += 2) {
            $t1 = $team_names[$i];
            $t2 = $team_names[$i+1] ?? 'BYE';
            $pairs[] = [$t1, $t2];
        }
        // Pehle event_name fetch karo
$ename = $con->prepare("SELECT event_name FROM events WHERE id = ?");
$ename->bind_param("i", $event_id);
$ename->execute();
$ename->bind_result($event_name);
$ename->fetch();
$ename->close();

// Abhi insert query
$ins = $con->prepare("INSERT INTO matches (event_id, event_name, game, round, team1_name, team2_name, match_status, match_date) 
VALUES (?, ?, ?, ?, ?, ?, 'Scheduled', ?)");

foreach ($pairs as $p) {
    $ins->bind_param("ississs", $event_id, $event_name, $gameName, $round, $p[0], $p[1], $match_date);
    $ins->execute();
}
$ins->close();

        $messages[] = "Matches scheduled successfully for round {$round}.";
        header("Location: cricket_scheduling.php?event_id={$event_id}");
        exit();
    }
}

// --- Fetch scheduled matches grouped by round (for display) ---
$rounds = [1 => 'First Round', 2 => 'Quarter Final', 3 => 'Semi Final', 4 => 'Final'];
$scheduled_matches = [];
foreach ($rounds as $r_no => $r_label) {
    $stmt = $con->prepare("SELECT * FROM matches WHERE game = ? AND round = ? ORDER BY match_date ASC, id ASC");
    $stmt->bind_param("si", $gameName, $r_no);
    $stmt->execute();
    $scheduled_matches[$r_no] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// get winners_by_round for JS
$winners_by_round = [];
foreach ($rounds as $r_no => $r_label) {
    $winners_by_round[$r_no] = [];
    $wq = $con->prepare("SELECT DISTINCT winner_name FROM matches WHERE game = ? AND round = ? AND winner_name IS NOT NULL");
    $wq->bind_param("si", $gameName, $r_no);
    $wq->execute();
    $wr = $wq->get_result();
    while ($rw = $wr->fetch_assoc()) {
        if (!empty($rw['winner_name'])) $winners_by_round[$r_no][] = $rw['winner_name'];
    }
    $wq->close();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Cricket Scheduling</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root{--accent:#0b6efd;}
    body{background:linear-gradient(180deg,#eaf4ff 0,#f8fbff 100%);font-family:Arial,Helvetica,sans-serif}
    .container{max-width:1150px;margin-top:20px;margin-bottom:40px}
    .card{border-radius:12px;box-shadow:0 10px 30px rgba(11,79,158,0.06);padding:16px}
    .logo{width:56px;height:56px;background:var(--accent);color:white;display:flex;align-items:center;justify-content:center;border-radius:10px;font-weight:700}
    .section-title{color:var(--accent);font-weight:700}
    table thead th{background:linear-gradient(90deg,var(--accent),#1456c8);color:#fff;border:0}
    .small-muted{color:#6b7280;font-size:13px}
    .btn-pdf{background:#22c55e;color:#fff;border:0}
    .form-note{font-size:13px;color:#555}
    .completed{color:green;font-weight:700}
  </style>
</head>
<body>
<div class="container">
  <div class="d-flex align-items-center mb-3">
    <div class="logo me-3">CS</div>
    <div>
      <h3 style="margin:0">Cricket — Match Scheduling</h3>
      <div class="small-muted">Single-elimination — Round-wise scheduling</div>
    </div>
  </div>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $e) echo "<div>".h($e)."</div>"; ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($messages)): ?>
    <div class="alert alert-success">
      <?php foreach ($messages as $m) echo "<div>".h($m)."</div>"; ?>
    </div>
  <?php endif; ?>

  <div class="card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div>
        <h5 class="section-title">Registered Players</h5>
        <div class="form-note">Showing players registered for Cricket. Use these exact names when scheduling.</div>
      </div>
      <div>
        <a href="cricket_teams.php" class="btn btn-outline-secondary">Back to Registrations</a>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-striped table-bordered mb-0">
        <thead>
          <tr><th style="width:70px">Sno.</th><th>Team Name</th><th>Captain Name</th></tr>
        </thead>
        <tbody>
          <?php if (empty($registered_rows)): ?>
            <tr><td colspan="4" class="text-center small-muted">No registered players found.</td></tr>
          <?php else: $c=1; foreach ($registered_rows as $pr): ?>
            <tr>
              <td><?= $c++ ?></td>
              <td><?= h($pr['team_name']) ?></td>
              <td><?= h($pr['captain_name']) ?></td>

            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Scheduling form -->
  <div class="card mb-4">
    <h5 class="section-title">Generate Matches (Single Elimination)</h5>
    <div class="small-muted mb-2">
      Event: <?= h($event_row['event_name'] ?? 'N/A') ?> |
      Dates: <?= $event_start && $event_end ? h($event_start)." to ".h($event_end) : 'Event dates not set' ?>
    </div>

    <form method="post" id="scheduleForm">
      <input type="hidden" name="action" value="schedule_matches">
      <div class="row g-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label">Round</label>
          <select name="round" id="round" class="form-select" required>
            <option value="">Select round</option>
            <?php foreach ($rounds as $num => $label): ?>
              <option value="<?= $num ?>"><?= h($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Match Date</label>
          <input type="date" id="match_date" name="match_date" class="form-control" min="<?= h($event_start) ?>" max="<?= h($event_end) ?>" required>
          <div class="form-note">Date must be within event dates.</div>
        </div>

        <div class="col-md-4">
          <label class="form-label">Number of Players</label>
          <input type="number" id="num_players" class="form-control" min="2" value="2" required>
          <div class="form-note">Minimum 2 entries required.</div>
        </div>

        <div class="col-md-2 d-grid">
          <button type="submit" class="btn btn-primary">Generate & Schedule</button>
        </div>

        <div class="col-12" id="names_section"></div>
      </div>
    </form>

  </div>


  <!-- Round-wise scheduled matches -->
  <?php foreach ($rounds as $r_no => $r_label): ?>
    <div class="card mb-3">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
          <h6 style="margin:0"><?= h($r_label) ?></h6>
          <div class="small-muted">Matches for <?= h($r_label) ?></div>
        </div>
        <div>
          <?php if (!empty($scheduled_matches[$r_no])): ?>
            <form method="post" style="display:inline">
              <input type="hidden" name="action" value="delete_round">
              <input type="hidden" name="round_no" value="<?= $r_no ?>">
              <button class="btn btn-danger btn-sm" onclick="return confirm('Delete ALL matches for <?= addslashes($r_label) ?>?')">Delete All</button>
            </form>
          <?php endif; ?>
        </div>
      </div>

      <?php if (empty($scheduled_matches[$r_no])): ?>
        <div class="small-muted">No matches scheduled.</div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Player 1</th>
                <th>Player 2</th>
                <th>Date</th>
                <th>Status</th>
                <th>Result (admin)</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($scheduled_matches[$r_no] as $m): 
                $can_set_result = !empty($m['match_date']) && (strtotime(date('Y-m-d')) >= strtotime($m['match_date']));
              ?>
                <tr>
                  <td><?= h($m['team1_name']) ?></td>
                  <td><?= h($m['team2_name']) ?></td>
                  <td><?= h($m['match_date']) ?></td>
                  <td><?= !empty($m['winner_name']) ? '<span class="completed">Completed</span>' : '<span class="small-muted">Scheduled</span>' ?></td>
                  <td>
                    <?php if (empty($m['winner_name'])): ?>
                      <?php if ($can_set_result): ?>
                        <form method="post" style="display:flex;gap:8px;align-items:center;">
                          <input type="hidden" name="action" value="set_result">
                          <input type="hidden" name="match_id" value="<?= intval($m['id']) ?>">
                          
                          
                        </form>
                      <?php else: ?>
                        <div class="small-muted">Result can be set on/after <?= h($m['match_date']) ?></div>
                      <?php endif; ?>
                    <?php else: ?>
                      <div><strong>Winner:</strong> <?= h($m['winner_name']) ?></div>
                      <div><strong>Loser:</strong> <?= h($m['loser_name'] ?? '-') ?></div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <form method="post" style="display:inline">
                      <input type="hidden" name="action" value="delete_match">
                      <input type="hidden" name="match_id" value="<?= intval($m['id']) ?>">
                      <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this match?')">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>

</div>

<script>
  // client-side data
  const registeredPlayers = <?= json_encode($registered_players, JSON_HEX_TAG) ?>;
  const winnersByRound = <?= json_encode($winners_by_round, JSON_HEX_TAG) ?>;
  const firstRoundMin = 2, firstRoundMax = 8;

  const roundSelect = document.getElementById('round');
  const numInput = document.getElementById('num_players');
  const namesSection = document.getElementById('names_section');
  const matchDateInput = document.getElementById('match_date');

  // adjust num input & fields on round change
  roundSelect.addEventListener('change', function(){
    const r = parseInt(this.value || 0);
    if (r === 1) {
      numInput.min = firstRoundMin;
      numInput.max = firstRoundMax;
      numInput.value = Math.max(firstRoundMin, Math.min(firstRoundMax, parseInt(numInput.value || firstRoundMin)));
      numInput.disabled = false;
      renderNameInputs(numInput.value, 1);
    } else if (r > 1) {
      // set number to winners of previous round
      const prev = r - 1;
      const allowed = winnersByRound[prev] ? winnersByRound[prev].length : 0;
      if (allowed === 0) {
        alert('No winners recorded from previous round — cannot schedule this round yet.');
        numInput.value = 0;
        namesSection.innerHTML = '<div class="small-muted">No eligible players available until previous round results are set.</div>';
        numInput.disabled = true;
      } else {
        numInput.value = allowed;
        numInput.min = allowed;
        numInput.max = allowed;
        numInput.disabled = true;
        renderNameInputs(allowed, r);
      }
    } else {
      namesSection.innerHTML = '';
    }
  });

  // when num changes
  numInput.addEventListener('input', function(){
    const r = parseInt(roundSelect.value || 0);
    let n = parseInt(this.value || 0);
    if (r === 1) {
      if (n < firstRoundMin) n = firstRoundMin;
      if (n > firstRoundMax) n = firstRoundMax;
      this.value = n;
      renderNameInputs(n, r);
    }
  });

  function renderNameInputs(n, round) {
    namesSection.innerHTML = '';
    let allowed = registeredPlayers;
    if (round > 1) {
      allowed = winnersByRound[round - 1] || [];
    }
    for (let i=1;i<=n;i++){
      const div = document.createElement('div');
      div.className = 'mb-2';
      const label = document.createElement('label');
      label.className = 'form-label';
      label.textContent = 'Player ' + i;
      const input = document.createElement('input');
      input.type = 'text';
      input.name = 'team_names[]';
      input.className = 'form-control';
      input.placeholder = allowed.length ? 'Select from suggestions' : 'No eligible players';
      input.setAttribute('list', 'list'+i);
      input.required = true;
      // datalist
      const dlist = document.createElement('datalist');
      dlist.id = 'list'+i;
      allowed.forEach(v=>{
        const opt = document.createElement('option'); opt.value = v; dlist.appendChild(opt);
      });
      // blur validation
      input.addEventListener('blur', function(e){
        const val = e.target.value.trim();
        if (!val) return;
        // correct allowed check
        if (round > 1) {
          const prev = round - 1;
          const allow = winnersByRound[prev] || [];
          if (!allow.includes(val)) {
            alert('"' + val + '" is not eligible (not a winner from previous round). Please choose from allowed list.');
            e.target.value = '';
            e.target.focus();
            return;
          }
        } else {
          if (!registeredPlayers.includes(val)) {
            alert('"' + val + '" is not a registered player. Please choose a registered name.');
            e.target.value = '';
            e.target.focus();
            return;
          }
        }
        // duplicate prevention
        const all = Array.from(document.querySelectorAll('input[name="team_names[]"]')).map(x=>x.value.trim()).filter(Boolean);
        const dup = all.filter((v,i,a)=>a.indexOf(v)!==i);
        if (dup.length > 0) {
          alert('Duplicate player: ' + dup[0] + '. Please ensure unique names.');
          e.target.value = '';
          e.target.focus();
        }
      });
      div.appendChild(label);
      div.appendChild(input);
      div.appendChild(dlist);
      namesSection.appendChild(div);
    }
  }

  // final submit validation
  document.getElementById('scheduleForm').addEventListener('submit', function(e){
    const r = parseInt(roundSelect.value || 0);
    if (!r) { alert('Select a round'); e.preventDefault(); return; }
    const md = matchDateInput.value;
    if (!md) { alert('Select match date'); e.preventDefault(); return; }
    // event range check (browser will enforce via input min/max, but double-check)
    const minD = matchDateInput.min;
    const maxD = matchDateInput.max;
    if (minD && md < minD) { alert('Match date cannot be before event start date'); e.preventDefault(); return; }
    if (maxD && md > maxD) { alert('Match date cannot be after event end date'); e.preventDefault(); return; }
    const inputs = Array.from(document.querySelectorAll('input[name="team_names[]"]')).map(i=>i.value.trim()).filter(Boolean);
    if (inputs.length === 0) { alert('Add player names'); e.preventDefault(); return; }
    // round-based validation
    if (r === 1) {
      if (inputs.length < firstRoundMin || inputs.length > firstRoundMax) { alert('First Round requires between '+firstRoundMin+' and '+firstRoundMax+' players'); e.preventDefault(); return; }
      // registration validation
      for (const nm of inputs) if (!registeredPlayers.includes(nm)) { alert('"'+nm+'" is not registered'); e.preventDefault(); return; }
    } else {
      const prev = r-1;
      const allowed = winnersByRound[prev] || [];
      if (allowed.length === 0) { alert('No winners from previous round yet'); e.preventDefault(); return; }
      if (inputs.length !== allowed.length) { alert('You must enter exactly ' + allowed.length + ' players (winners of previous round)'); e.preventDefault(); return; }
      for (const nm of inputs) if (!allowed.includes(nm)) { alert('"'+nm+'" is not a winner from previous round'); e.preventDefault(); return; }
    }
    // duplicates
    if (new Set(inputs).size !== inputs.length) { alert('Duplicate names not allowed'); e.preventDefault(); return; }
    // OK
  });

  // Optional: default to First Round
  // roundSelect.value = '1';
  // roundSelect.dispatchEvent(new Event('change'));
</script>
</body>
</html>

