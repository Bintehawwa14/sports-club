<?php
// admin/badminton_double.php
// Full Double-Elimination scheduling with role validation

session_start();
include_once(__DIR__ . '/../include/db_connect.php');

// Admin-only access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// ---------- CONFIG ----------
$gameName = 'badminton';

// Bracket types
const BRACKET_WB = 'WB';
const BRACKET_LB = 'LB';
const BRACKET_GF = 'GF';
const BRACKET_GFR = 'GF-Reset';

// Display rounds
$WB_LABELS = [1 => 'Winners Round 1', 2 => 'Winners Round 2', 3 => 'Winners Round 3', 4 => 'Winners Round 4'];
$LB_LABELS = [1 => 'Losers Round 1', 2 => 'Losers Round 2', 3 => 'Losers Round 3', 4 => 'Losers Round 4'];

// ---------- HELPERS ----------
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Get player role
function getPlayerRole(mysqli $con, string $game, string $playerName) : ?string {
    $stmt = $con->prepare("SELECT role FROM badminton_players WHERE game = ? AND fullName = ?");
    $stmt->bind_param("ss", $game, $playerName);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['role'];
    }
    return null;
}

// Ensure required columns exist
$ensureCols = [
    'match_date'   => "ALTER TABLE matches ADD COLUMN match_date DATE DEFAULT NULL",
    'winner_name'  => "ALTER TABLE matches ADD COLUMN winner_name VARCHAR(255) DEFAULT NULL",
    'loser_name'   => "ALTER TABLE matches ADD COLUMN loser_name VARCHAR(255) DEFAULT NULL",
    'match_status' => "ALTER TABLE matches ADD COLUMN match_status VARCHAR(50) DEFAULT 'Scheduled'",
    'bracket_type' => "ALTER TABLE matches ADD COLUMN bracket_type VARCHAR(50) DEFAULT NULL",
];
foreach ($ensureCols as $col => $sql) {
    $ck = $con->query("SHOW COLUMNS FROM matches LIKE '{$col}'");
    if (!$ck || $ck->num_rows === 0) { @$con->query($sql); }
}

// Get losses map
function getLossesMap(mysqli $con, string $game) : array {
    $map = [];
    $q = $con->prepare("SELECT loser_name FROM matches WHERE game=? AND loser_name IS NOT NULL AND match_status='Completed'");
    $q->bind_param("s", $game);
    $q->execute(); $res = $q->get_result();
    while ($r = $res->fetch_assoc()) {
        $n = trim($r['loser_name']);
        if ($n === '' || strtoupper($n) === 'BYE') continue;
        $map[$n] = ($map[$n] ?? 0) + 1;
    }
    $q->close();
    return $map;
}

// Check if player already scheduled in round
function alreadyScheduledInRound(mysqli $con, string $game, string $bracket, int $round, string $name) : bool {
    $q = $con->prepare("SELECT id FROM matches WHERE game=? AND bracket_type=? AND round=? AND (team1_name=? OR team2_name=?) LIMIT 1");
    $q->bind_param("ssiss", $game, $bracket, $round, $name, $name);
    $q->execute(); $res = $q->get_result();
    $exists = ($res && $res->num_rows > 0);
    $q->close();
    return $exists;
}

// Get winners for a bracket and round
function getWinners(mysqli $con, string $game, string $bracket, int $round) : array {
    $out = [];
    $q = $con->prepare("SELECT DISTINCT winner_name FROM matches WHERE game=? AND bracket_type=? AND round=? AND winner_name IS NOT NULL AND match_status='Completed'");
    $q->bind_param("ssi", $game, $bracket, $round);
    $q->execute(); $res = $q->get_result();
    while ($r = $res->fetch_assoc()) {
        $nm = trim($r['winner_name']);
        if ($nm !== '' && strtoupper($nm) !== 'BYE') $out[] = $nm;
    }
    $q->close();
    return $out;
}

// Create match helper
// Add this function to your helpers section
function getMatchResultFromExternal($match_id) {
    // URL to your badmintonresult.php API endpoint
    $url = "http://yourdomain.com/badmintonresult.php?match_id=" . $match_id;
    
    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    // Execute and decode response
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Check if request was successful
    if ($httpCode === 200 && !empty($response)) {
        $result = json_decode($response, true);
        if (is_array($result) && isset($result['winner']) && isset($result['loser'])) {
            return $result;
        }
    }
    
    return null;
}
function createMatch(mysqli $con, int $event_id, string $game, int $round, ?string $team1, ?string $team2, ?string $match_date, string $bracket, string $status = 'Scheduled') : int {
    if ($event_id > 0) {
        $stmt = $con->prepare("INSERT INTO matches (event_id, game, round, team1_name, team2_name, match_date, bracket_type, match_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isisssss", $event_id, $game, $round, $team1, $team2, $match_date, $bracket, $status);
    } else {
        $stmt = $con->prepare("INSERT INTO matches (game, round, team1_name, team2_name, match_date, bracket_type, match_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisssss", $game, $round, $team1, $team2, $match_date, $bracket, $status);
    }
    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();
    return intval($id);
}

// Promote after result
function promoteAfterResult(mysqli $con, int $event_id, string $game, array $matchRow) {
    $round = intval($matchRow['round']);
    $bracket = $matchRow['bracket_type'];
    $winner = $matchRow['winner_name'] ?? '';
    $loser  = $matchRow['loser_name'] ?? '';
    $mdate  = $matchRow['match_date'] ?? date('Y-m-d');

    if (!$winner) return;

    if ($bracket === BRACKET_WB) {
        // winner -> next WB round
        $nextWB = $round + 1;
        createMatch($con, $event_id, $game, $nextWB, $winner, null, $mdate, BRACKET_WB, 'Scheduled');

        // loser -> losers bracket
        if (!empty($loser) && strtoupper($loser) !== 'BYE') {
            createMatch($con, $event_id, $game, $round, $loser, null, $mdate, BRACKET_LB, 'Scheduled');
        }
    } elseif ($bracket === BRACKET_LB) {
        // loser bracket winner -> next LB round
        $nextLB = $round + 1;
        createMatch($con, $event_id, $game, $nextLB, $winner, null, $mdate, BRACKET_LB, 'Scheduled');
    }
}

// Resolve BYE matches
function resolveByeIfAny(mysqli $con, int $match_id, string $gameName, int $event_id = 0) {
    $q = $con->prepare("SELECT id, team1_name, team2_name, bracket_type, round, match_date FROM matches WHERE id=?");
    $q->bind_param("i", $match_id);
    $q->execute(); $res = $q->get_result();
    if (!$res || $res->num_rows === 0) { $q->close(); return; }
    $row = $res->fetch_assoc(); $q->close();

    $t1 = trim((string)$row['team1_name']); $t2 = trim((string)$row['team2_name']);
    if ($t1 !== '' && ($t2 === '' || strtoupper($t2) === 'BYE')) {
        $u = $con->prepare("UPDATE matches SET winner_name=?, loser_name=?, match_status='Completed' WHERE id=?");
        $loserName = ($t2 === '' ? 'BYE' : $t2);
        $u->bind_param("ssi", $t1, $loserName, $match_id);
        $u->execute(); $u->close();

        $matchRow = [
            'id' => $match_id,
            'round' => $row['round'],
            'bracket_type' => $row['bracket_type'],
            'match_date' => $row['match_date'],
            'winner_name' => $t1,
            'loser_name' => $loserName,
        ];
        promoteAfterResult($con, $event_id, $gameName, $matchRow);
    }
}

// ---------- EVENT RESOLUTION ----------
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
$event_row = null; $event_start = null; $event_end = null;

if ($event_id <= 0) {
    $r = $con->query("SELECT id, event_name, start_date, end_date FROM events ORDER BY id DESC LIMIT 1");
    if ($r && $r->num_rows) {
        $event_row = $r->fetch_assoc();
        $event_id = intval($event_row['id']);
    }
}

if ($event_id > 0 && !$event_row) {
    $st = $con->prepare("SELECT id, event_name, start_date, end_date FROM events WHERE id=?");
    $st->bind_param("i", $event_id);
    $st->execute(); $res = $st->get_result();
    if ($res && $res->num_rows) $event_row = $res->fetch_assoc();
    $st->close();
}

if ($event_row) {
    $event_start = $event_row['start_date'] ?? null;
    $event_end   = $event_row['end_date'] ?? null;
}

// ---------- FETCH REGISTERED PLAYERS ----------
$registered_rows = [];
$registered_players = [];
$playerRoleMap = []; // Map player names to their roles

$pr = $con->prepare("SELECT id, fullName, email, role FROM badminton_players WHERE game=? ORDER BY fullName ASC");
$pr->bind_param("s", $gameName);
$pr->execute(); $rres = $pr->get_result();
while ($rr = $rres->fetch_assoc()) {
    $registered_rows[] = $rr;
    if (!empty($rr['fullName'])) {
        $registered_players[] = $rr['fullName'];
        $playerRoleMap[$rr['fullName']] = $rr['role'];
    }
}
$pr->close();

// ---------- HANDLE POST ACTIONS ----------
$errors = []; 
$messages = [];

// Delete single match
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_match') {
    $mid = intval($_POST['match_id'] ?? 0);
    if ($mid > 0) {
        $d = $con->prepare("DELETE FROM matches WHERE id=?");
        if ($d) {
            $d->bind_param("i", $mid);
            $d->execute();
            $d->close();
            $messages[] = "Match deleted.";
        } else {
            $errors[] = "Query prepare failed: " . $con->error;
        }
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        $errors[] = "Invalid match id.";
    }
}

// Delete all matches for bracket + round
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_round') {
    $br = $_POST['bracket'] ?? BRACKET_WB;
    $rd = intval($_POST['round_no'] ?? 0);

    $d = $con->prepare("DELETE FROM matches WHERE game=? AND bracket_type=? AND round=?");
    if ($d) {
        $d->bind_param("ssi", $gameName, $br, $rd);
        $d->execute();
        $d->close();
        $messages[] = "Deleted all matches for {$br} round {$rd}.";
    } else {
        $errors[] = "Query prepare failed: " . $con->error;
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// Update result (single match)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'set_result') {
    $mid = intval($_POST['match_id'] ?? 0);
    $winner = trim($_POST['winner'] ?? '');
    if ($mid <= 0 || $winner === '') {
        $errors[] = "Invalid match or winner.";
    } else {
        $q = $con->prepare("SELECT id, game, round, team1_name, team2_name, bracket_type, match_date FROM matches WHERE id=? LIMIT 1");
        $q->bind_param("i", $mid);
        $q->execute(); 
        $res = $q->get_result();
        if (!$res || $res->num_rows === 0) { 
            $errors[] = "Match not found."; 
            $q->close(); 
        } else {
            $mr = $res->fetch_assoc(); 
            $q->close();
            $t1 = $mr['team1_name']; 
            $t2 = $mr['team2_name'];
            if (!in_array($winner, [$t1, $t2])) $errors[] = "Winner must be one of the match players.";
            $loser = ($winner === $t1) ? $t2 : $t1;

            $today = date('Y-m-d');
            if (!empty($mr['match_date']) && strtotime($today) < strtotime($mr['match_date'])) {
                $errors[] = "Cannot set result before scheduled match date ({$mr['match_date']}).";
            }

            if (empty($errors)) {
                $up = $con->prepare("UPDATE matches SET winner_name=?, loser_name=?, match_status='Completed' WHERE id=?");
                $up->bind_param("ssi", $winner, $loser, $mid);
                $up->execute(); 
                $up->close();

                $matchRow = [
                    'id' => $mid,
                    'round' => intval($mr['round']),
                    'bracket_type' => $mr['bracket_type'],
                    'match_date' => $mr['match_date'],
                    'winner_name' => $winner,
                    'loser_name' => $loser
                ];
                promoteAfterResult($con, $event_id ?? 0, $gameName, $matchRow);

                if ($mr['bracket_type'] === BRACKET_GF) {
                    if ($winner === $mr['team2_name']) {
                        createMatch($con, $event_id ?? 0, $gameName, 1, 
                                    $mr['team1_name'], $mr['team2_name'], date('Y-m-d'), 
                                    BRACKET_GFR, 'Scheduled');
                        $messages[] = "LB champion beat WB champion — Reset Final scheduled.";
                    }
                }

                $messages[] = "Result saved and bracket progressed.";
                header("Location: " . $_SERVER['REQUEST_URI']); 
                exit();
            }
        }
    }
}

// Schedule matches
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'schedule_matches') {
    $bracket = $_POST['bracket'] ?? BRACKET_WB;
    $round = intval($_POST['round'] ?? 0);
    $team_names = $_POST['team_names'] ?? [];
    $match_date = $_POST['match_date'] ?? null;

    // Validate bracket
    if (!in_array($bracket, [BRACKET_WB, BRACKET_LB, BRACKET_GF, BRACKET_GFR])) {
        $errors[] = "Invalid bracket selected.";
    }

    // Validate round
    if ($round < 1) {
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

    // Validate roles - all players must have the same role
    $roles = [];
    foreach ($team_names as $playerName) {
        $role = getPlayerRole($con, $gameName, $playerName);
        if ($role) {
            $roles[$playerName] = $role;
        } else {
            $errors[] = "Player '{$playerName}' not found or role not set.";
        }
    }

    // Check if all players have the same role
    if (count(array_unique($roles)) > 1) {
        $errors[] = "All players must be of the same role (either all single or all double).";
    }

    // Validate names based on bracket and round
    if ($bracket === BRACKET_WB && $round === 1) {
        // First round of winners bracket
        $min = 2;
        $count = count($team_names);
        if ($count < $min) {
            $errors[] = "First Round requires {$min} players.";
        }

        // Validate all names exist in registered_players
        foreach ($team_names as $nm) {
            if (!in_array($nm, $registered_players)) {
                $errors[] = "Player '{$nm}' is not registered for badminton.";
            }
        }
    } else {
        // For later rounds, validate based on previous bracket results
        if ($bracket === BRACKET_WB) {
            // Winners bracket - must be winners from previous WB round
            $prevRound = $round - 1;
            $winners = getWinners($con, $gameName, BRACKET_WB, $prevRound);
            
            if (count($winners) === 0) {
                $errors[] = "Cannot schedule round {$round} because previous round has no recorded winners yet.";
            } else {
                if (count($team_names) !== count($winners)) {
                    $errors[] = "You must submit exactly " . count($winners) . " names for this round (winners of previous round).";
                } else {
                    // ensure each submitted name is in winners list
                    foreach ($team_names as $nm) {
                        if (!in_array($nm, $winners)) {
                            $errors[] = "Player '{$nm}' is not a winner from previous round; cannot schedule.";
                        }
                    }
                }
            }
        } elseif ($bracket === BRACKET_LB) {
            // Losers bracket - must be losers from WB or winners from previous LB round
            $lossesMap = getLossesMap($con, $gameName);
            $lbWinners = getWinners($con, $gameName, BRACKET_LB, $round - 1);
            $eligible = array_merge(array_keys(array_filter($lossesMap, function($v) { return $v === 1; })), $lbWinners);
            
            if (count($eligible) === 0) {
                $errors[] = "No eligible players for losers bracket round {$round}.";
            } else {
                if (count($team_names) !== count($eligible)) {
                    $errors[] = "You must submit exactly " . count($eligible) . " names for this round.";
                } else {
                    foreach ($team_names as $nm) {
                        if (!in_array($nm, $eligible)) {
                            $errors[] = "Player '{$nm}' is not eligible for this losers bracket round.";
                        }
                    }
                }
            }
        } elseif ($bracket === BRACKET_GF) {
            // Grand Final - must be WB champion and LB champion
            $wbChamp = getWinners($con, $gameName, BRACKET_WB, max(array_keys($WB_LABELS)));
            $lbChamp = getWinners($con, $gameName, BRACKET_LB, max(array_keys($LB_LABELS)));
            
            if (count($wbChamp) !== 1 || count($lbChamp) !== 1) {
                $errors[] = "Both winners bracket and losers bracket must have champions to schedule grand final.";
            } else {
                if (count($team_names) !== 2) {
                    $errors[] = "Grand Final requires exactly 2 players.";
                } else {
                    $expected = array_merge($wbChamp, $lbChamp);
                    foreach ($team_names as $nm) {
                        if (!in_array($nm, $expected)) {
                            $errors[] = "Player '{$nm}' is not a bracket champion.";
                        }
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
        // shuffle for random matchups
        shuffle($team_names);
        $pairs = [];
        for ($i = 0; $i < count($team_names); $i += 2) {
            $t1 = $team_names[$i];
            $t2 = $team_names[$i+1] ?? 'BYE';
            $pairs[] = [$t1, $t2];
        }
        
        $ins = $con->prepare("INSERT INTO matches (event_id, game, round, team1_name, team2_name, match_date, bracket_type, match_status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Scheduled')");
        
        foreach ($pairs as $p) {
            $ins->bind_param("isissss", $event_id, $gameName, $round, $p[0], $p[1], $match_date, $bracket);
            $ins->execute();
        }
        $ins->close();
        
        $messages[] = "Matches scheduled successfully for {$bracket} round {$round}.";
        header("Location: badminton_double.php?event_id={$event_id}");
        exit();
    }
}

// ---------- FETCH SCHEDULED MATCHES ----------
$scheduled_matches = [];
$brackets = [BRACKET_WB, BRACKET_LB, BRACKET_GF, BRACKET_GFR];

foreach ($brackets as $bracket) {
    $stmt = $con->prepare("SELECT * FROM matches WHERE game = ? AND bracket_type = ? ORDER BY round ASC, match_date ASC, id ASC");
    $stmt->bind_param("ss", $gameName, $bracket);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $round = intval($row['round']);
        $scheduled_matches[$bracket][$round][] = $row;
    }
    $stmt->close();
}

// Get winners by round for JS
$winners_by_round = ['WB' => [], 'LB' => []];
foreach ($WB_LABELS as $r_no => $label) {
    $winners_by_round['WB'][$r_no] = getWinners($con, $gameName, BRACKET_WB, $r_no);
}
foreach ($LB_LABELS as $r_no => $label) {
    $winners_by_round['LB'][$r_no] = getWinners($con, $gameName, BRACKET_LB, $r_no);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Badminton — Double Elimination Scheduling</title>
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
    .bracket-header {background-color: #e9ecef; padding: 10px; border-radius: 5px; margin-bottom: 15px;}
    .badge {font-size: 0.7em; margin-left: 5px;}
  </style>
</head>
<body>
<div class="container">
  <div class="d-flex align-items-center mb-3">
    <div class="logo me-3">BD</div>
    <div>
      <h3 style="margin:0">Badminton — Double Elimination Scheduling</h3>
      <div class="small-muted">Winners & Losers brackets — schedule & results</div>
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
        <div class="form-note">Showing players registered for Badminton. Use these exact names when scheduling.</div>
      </div>
      <div>
        <a href="badminton_players.php" class="btn btn-outline-secondary">Back to Registrations</a>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-striped table-bordered mb-0">
        <thead>
          <tr><th style="width:70px">Sno.</th><th>Name</th><th>Email</th><th style="width:120px">Role</th></tr>
        </thead>
        <tbody>
          <?php if (empty($registered_rows)): ?>
            <tr><td colspan="4" class="text-center small-muted">No registered players found.</td></tr>
          <?php else: $c=1; foreach ($registered_rows as $pr): ?>
            <tr>
              <td><?= $c++ ?></td>
              <td><?= h($pr['fullName']) ?></td>
              <td><?= h($pr['email']) ?></td>
              <td><span class="badge bg-secondary"><?= h($pr['role']) ?></span></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Scheduling form -->
  <div class="card mb-4">
    <h5 class="section-title">Generate Matches (Double Elimination)</h5>
    <div class="small-muted mb-2">
      Event: <?= h($event_row['event_name'] ?? 'N/A') ?> |
      Dates: <?= $event_start && $event_end ? h($event_start)." to ".h($event_end) : 'Event dates not set' ?>
    </div>

    <form method="post" id="scheduleForm">
      <input type="hidden" name="action" value="schedule_matches">
      <div class="row g-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label">Bracket</label>
          <select name="bracket" id="bracket" class="form-select" required>
            <option value="">Select bracket</option>
            <option value="<?= BRACKET_WB ?>">Winners Bracket</option>
            <option value="<?= BRACKET_LB ?>">Losers Bracket</option>
            <option value="<?= BRACKET_GF ?>">Grand Final</option>
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label">Round</label>
          <input type="number" name="round" id="round" class="form-control" value="1" min="1" required>
        </div>

        <div class="col-md-3">
          <label class="form-label">Match Date</label>
          <input type="date" id="match_date" name="match_date" class="form-control" min="<?= h($event_start) ?>" max="<?= h($event_end) ?>">
          <div class="form-note">Date must be within event dates.</div>
        </div>

        <div class="col-md-2">
          <label class="form-label">Number of Players</label>
          <input type="number" id="num_players" class="form-control" min="2" value="2" required>
        </div>

        <div class="col-md-2 d-grid">
          <button type="submit" class="btn btn-primary">Generate & Schedule</button>
        </div>

        <div class="col-12" id="names_section"></div>
      </div>
    </form>
  </div>

 

  <!-- Bracket-wise scheduled matches -->
  <?php foreach ($scheduled_matches as $bracket => $rounds): ?>
    <div class="card mb-4">
      <div class="bracket-header">
        <h5 class="section-title mb-0">
          <?php 
          switch($bracket) {
            case BRACKET_WB: echo "Winners Bracket"; break;
            case BRACKET_LB: echo "Losers Bracket"; break;
            case BRACKET_GF: echo "Grand Final"; break;
            case BRACKET_GFR: echo "Grand Final Reset"; break;
            default: echo $bracket;
          }
          ?>
        </h5>
      </div>
      
      <?php if (empty($rounds)): ?>
        <div class="small-muted p-3">No matches scheduled.</div>
      <?php else: ?>
        <?php foreach ($rounds as $round_num => $matches): ?>
          <div class="mb-4">
            <h6 class="mb-3">
              <?php 
              if ($bracket === BRACKET_WB && isset($WB_LABELS[$round_num])) {
                echo $WB_LABELS[$round_num];
              } elseif ($bracket === BRACKET_LB && isset($LB_LABELS[$round_num])) {
                echo $LB_LABELS[$round_num];
              } else {
                echo "Round " . $round_num;
              }
              ?>
            </h6>
            
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
                  <?php foreach ($matches as $m): 
                    $can_set_result = !empty($m['match_date']) && (strtotime(date('Y-m-d')) >= strtotime($m['match_date']));
                  ?>
                    <tr>
                      <td>
                        <?= h($m['team1_name']) ?>
                        <?php if (!empty($m['team1_name']) && isset($playerRoleMap[$m['team1_name']])): ?>
                          <span class="badge bg-secondary"><?= h($playerRoleMap[$m['team1_name']]) ?></span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?= h($m['team2_name']) ?>
                        <?php if (!empty($m['team2_name']) && isset($playerRoleMap[$m['team2_name']])): ?>
                          <span class="badge bg-secondary"><?= h($playerRoleMap[$m['team2_name']]) ?></span>
                        <?php endif; ?>
                      </td>
                      <td><?= h($m['match_date']) ?></td>
                      <td><?= !empty($m['winner_name']) ? '<span class="completed">Completed</span>' : '<span class="small-muted">Scheduled</span>' ?></td>
                      <td>
                        <?php if (empty($m['winner_name'])): ?>
                          <?php if ($can_set_result): ?>
                            <form method="post" style="display:flex;gap:8px;align-items:center;">
                              <input type="hidden" name="action" value="set_result">
                              <input type="hidden" name="match_id" value="<?= intval($m['id']) ?>">
                              <select name="winner" class="form-select form-select-sm" required>
                                <option value="">Select winner</option>
                                <option value="<?= h($m['team1_name']) ?>"><?= h($m['team1_name']) ?></option>
                                <option value="<?= h($m['team2_name']) ?>"><?= h($m['team2_name']) ?></option>
                              </select>
                              <button class="btn btn-sm btn-success">Save</button>
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
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>

<script>
  // Client-side data
  const registeredPlayers = <?= json_encode($registered_players, JSON_HEX_TAG) ?>;
  const winnersByRound = <?= json_encode($winners_by_round, JSON_HEX_TAG) ?>;
  const playerRoleMap = <?= json_encode($playerRoleMap, JSON_HEX_TAG) ?>;
  const firstRoundMin = 2;

  const bracketSelect = document.getElementById('bracket');
  const roundInput = document.getElementById('round');
  const numInput = document.getElementById('num_players');
  const namesSection = document.getElementById('names_section');
  const matchDateInput = document.getElementById('match_date');

  // Show name fields on page load if bracket is selected
  if (bracketSelect.value) {
    renderNameInputs(numInput.value, bracketSelect.value, parseInt(roundInput.value));
  }

  // Adjust inputs on bracket/round change
  bracketSelect.addEventListener('change', function() {
    updateNameInputs();
  });

  roundInput.addEventListener('input', function() {
    updateNameInputs();
  });

  numInput.addEventListener('input', function() {
    updateNameInputs();
  });

  function updateNameInputs() {
    const bracket = bracketSelect.value;
    const round = parseInt(roundInput.value || 1);
    
    if (bracket && round > 0) {
      if (bracket === '<?= BRACKET_WB ?>' && round === 1) {
        numInput.min = firstRoundMin;
        numInput.disabled = false;
        renderNameInputs(numInput.value, bracket, round);
      } else {
        // For other brackets/rounds, determine eligible players count
        let eligibleCount = 0;
        
        if (bracket === '<?= BRACKET_WB ?>' && round > 1) {
          // WB rounds after 1: winners from previous WB round
          const prevWinners = winnersByRound.WB[round - 1] || [];
          eligibleCount = prevWinners.length;
        } else if (bracket === '<?= BRACKET_LB ?>') {
          // LB rounds: complex eligibility (simplified for UI)
          eligibleCount = 2; // Default to 2, will be validated server-side
        } else if (bracket === '<?= BRACKET_GF ?>') {
          // Grand Final: always 2 players
          eligibleCount = 2;
        }
        
        if (eligibleCount > 0) {
          numInput.value = eligibleCount;
          numInput.min = eligibleCount;
          numInput.max = eligibleCount;
          numInput.disabled = true;
          renderNameInputs(eligibleCount, bracket, round);
        } else {
          namesSection.innerHTML = '<div class="small-muted">No eligible players available for this bracket/round combination.</div>';
        }
      }
    } else {
      namesSection.innerHTML = '';
    }
  }

  function renderNameInputs(n, bracket, round) {
    namesSection.innerHTML = '';
    let allowed = registeredPlayers;
    
    if (bracket === '<?= BRACKET_WB ?>' && round > 1) {
      allowed = winnersByRound.WB[round - 1] || [];
    } else if (bracket === '<?= BRACKET_LB ?>') {
      // For LB, we need to combine various sources - simplified for UI
      allowed = [...(winnersByRound.LB[round - 1] || [])];
    } else if (bracket === '<?= BRACKET_GF ?>') {
      // Grand Final should be WB champion vs LB champion
      const wbChamp = winnersByRound.WB[Object.keys(winnersByRound.WB).pop()] || [];
      const lbChamp = winnersByRound.LB[Object.keys(winnersByRound.LB).pop()] || [];
      allowed = [...wbChamp, ...lbChamp];
    }
    
    for (let i = 1; i <= n; i++) {
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
      input.setAttribute('list', 'list' + i);
      input.required = true;
      
      // Datalist for suggestions
      const dlist = document.createElement('datalist');
      dlist.id = 'list' + i;
      
      allowed.forEach(v => {
        const opt = document.createElement('option');
        opt.value = v;
        opt.textContent = v + ' (' + (playerRoleMap[v] || 'Unknown') + ')';
        dlist.appendChild(opt);
      });
      
      // Validation on blur
      input.addEventListener('blur', function(e) {
        const val = e.target.value.trim();
        if (!val) return;
        
        // Check if player is registered
        if (!registeredPlayers.includes(val)) {
          alert('"' + val + '" is not a registered player. Please choose a registered name.');
          e.target.value = '';
          e.target.focus();
          return;
        }
        
        // Check role consistency
        const currentRole = playerRoleMap[val];
        const allInputs = Array.from(document.querySelectorAll('input[name="team_names[]"]'));
        const filledInputs = allInputs.filter(input => input.value.trim() !== '');
        
        if (filledInputs.length > 0) {
          // Check against the first filled input's role
          const firstVal = filledInputs[0].value.trim();
          const firstRole = playerRoleMap[firstVal];
          
          if (firstRole !== currentRole) {
            alert('"' + val + '" is a ' + currentRole + ' player. All players must be ' + firstRole + ' in this match.');
            e.target.value = '';
            e.target.focus();
            return;
          }
        }
        
        // Duplicate prevention
        const all = Array.from(document.querySelectorAll('input[name="team_names[]"]')).map(x => x.value.trim()).filter(Boolean);
        const dup = all.filter((v, i, a) => a.indexOf(v) !== i);
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

  // Final submit validation
  document.getElementById('scheduleForm').addEventListener('submit', function(e) {
    const bracket = bracketSelect.value;
    const round = parseInt(roundInput.value || 0);
    
    if (!bracket) {
      alert('Select a bracket');
      e.preventDefault();
      return;
    }
    
    if (round < 1) {
      alert('Round must be at least 1');
      e.preventDefault();
      return;
    }
    
    const md = matchDateInput.value;
    if (md) {
      const minD = matchDateInput.min;
      const maxD = matchDateInput.max;
      
      if (minD && md < minD) {
        alert('Match date cannot be before event start date');
        e.preventDefault();
        return;
      }
      
      if (maxD && md > maxD) {
        alert('Match date cannot be after event end date');
        e.preventDefault();
        return;
      }
    }
    
    const inputs = Array.from(document.querySelectorAll('input[name="team_names[]"]')).map(i => i.value.trim()).filter(Boolean);
    
    if (inputs.length === 0) {
      alert('Add player names');
      e.preventDefault();
      return;
    }
    
    // Bracket-specific validation
    if (bracket === '<?= BRACKET_WB ?>' && round === 1) {
      if (inputs.length < firstRoundMin) {
        alert('First Round requires at least ' + firstRoundMin + ' players');
        e.preventDefault();
        return;
      }
      
      for (const nm of inputs) {
        if (!registeredPlayers.includes(nm)) {
          alert('"' + nm + '" is not registered');
          e.preventDefault();
          return;
        }
      }
    } else if (bracket === '<?= BRACKET_GF ?>') {
      if (inputs.length !== 2) {
        alert('Grand Final requires exactly 2 players');
        e.preventDefault();
        return;
      }
    }
    
    // Role validation
    const roles = inputs.map(nm => playerRoleMap[nm]);
    if (new Set(roles).size > 1) {
      alert('All players must be of the same role (either all single or all double)');
      e.preventDefault();
      return;
    }
    
    // Duplicates
    if (new Set(inputs).size !== inputs.length) {
      alert('Duplicate names not allowed');
      e.preventDefault();
      return;
    }
  });
</script>
</body>
</html>