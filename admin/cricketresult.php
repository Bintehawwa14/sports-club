<?php
session_start();
include_once('../include/db_connect.php');
// Display success message if redirected from successful registration
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Success!',
                text: 'Registration successful!',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        });
    </script>";
}



// Initialize variables
$event_name = "";
$matches = [];

// Fetch cricket matches
$sql = "SELECT m.id, m.event_id, m.team1_name, m.team2_name, m.round, 
               m.match_date, m.match_status, m.team1_score, m.team2_score,
               e.event_name 
        FROM matches m
        JOIN events e ON m.event_id = e.id
        WHERE m.game='cricket' AND m.match_status = 'scheduled'
        ORDER BY m.match_date ASC, m.round ASC";
$result = mysqli_query($con, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $matches = mysqli_fetch_all($result, MYSQLI_ASSOC);
    if (!empty($matches)) {
        $event_name = $matches[0]['event_name'];
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['match_id'])) {
    foreach ($_POST['match_id'] as $index => $match_id) {
        // Get values from form
        $score_a = (int)$_POST['score_a'][$index];
        $score_b = (int)$_POST['score_b'][$index];
        $team_a = $_POST['team_a'][$index];
        $team_b = $_POST['team_b'][$index];
        
        // Determine winner
        if ($score_a > $score_b) {
            $winner_name = $team_a;
            $loser_name = $team_b;
        } elseif ($score_b > $score_a) {
            $winner_name = $team_b;
            $loser_name = $team_a;
        } else {
            $winner_name = "Draw";
            $loser_name = "";
        }
        

                // Matches table update
        $stmt2 = $con->prepare("UPDATE matches 
            SET team1_score = ?, team2_score = ?, total_over = ?, toss_winner = ?, result_winner = ?, match_status='Completed' WHERE match_id = ?");
        $stmt2->bind_param("iisssi", $team1_score, $team2_score, $total_over, $toss_winner, $result_winner, $match_id);
        $stmt2->execute();

        
        // Insert into cricketresult table
        $event_name   = $_POST['event_name'][$index];
        $match_date   = $_POST['match_date'][$index];
        $round_name   = $_POST['round_name'][$index];
        $total_overs  = (int)$_POST['total_overs'][$index];
        $tos_winner   = $_POST['toss_winner'][$index];
        $decision_after_toss = $_POST['Decision_after_toss'][$index];
        
        $stmt = $con->prepare("INSERT INTO cricketresult 
            (event_name, match_date, round_name, team_a, team_b, total_overs, toss_winner, decision_after_toss, score_a, score_b, winner) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssissiis", $event_name, $match_date, $round_name, $team_a, $team_b, $total_overs, $tos_winner, $decision_after_toss, $score_a, $score_b, $winner_name);
        $stmt->execute();
        $stmt->close();
        
        // Get the inserted ID for detailed results
        $result_id = $con->insert_id;
        
        // Detailed results
        $runs_a     = $_POST['runs_a'][$index];
        $runs_b     = $_POST['runs_b'][$index];
        $wickets_a  = $_POST['wickets_a'][$index];
        $wickets_b  = $_POST['wickets_b'][$index];
        $catches_a  = $_POST['catches_a'][$index];
        $catches_b  = $_POST['catches_b'][$index];
        $overs_a    = $_POST['overs_a'][$index];
        $overs_b    = $_POST['overs_b'][$index];
        $fours_a    = $_POST['fours_a'][$index];
        $fours_b    = $_POST['fours_b'][$index];
        $extras_a   = $_POST['extras_a'][$index];
        $extras_b   = $_POST['extras_b'][$index];
        $sixes_a    = $_POST['sixes_a'][$index];
        $sixes_b    = $_POST['sixes_b'][$index];
        $outs_a     = $_POST['outs_a'][$index];
        $outs_b     = $_POST['outs_b'][$index];
        
        // Insert into cricketresultdetails
        $stmt2 = $con->prepare("INSERT INTO cricketresultdetails 
            (result_id, runs_a, runs_b, wickets_a, wickets_b, catches_a, catches_b, overs_a, overs_b, fours_a, fours_b, extras_a, extras_b, sixes_a, sixes_b, outs_a, outs_b) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt2->bind_param(
            "iiiiiiiiiiiiiiiii", 
            $result_id, $runs_a, $runs_b, $wickets_a, $wickets_b, 
            $catches_a, $catches_b, $overs_a, $overs_b, 
            $fours_a, $fours_b, $extras_a, $extras_b, 
            $sixes_a, $sixes_b, $outs_a, $outs_b
        );

        $stmt2->execute();
        $stmt2->close();
    }
    
    echo "<script>alert('Cricket match results saved successfully.');</script>";
    echo "<script>window.location.href = 'cricket-result.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Cricket Match Results</title>
<style>
    body {
      font-family: Arial, sans-serif;
      background-image: url(../images/cricketpage.jpg);
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .container {
      background-color: rgba(255, 255, 255, 0.95);
      background-image: url(../images/cricketform.jpg);
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
      width: 70%;
      max-width: 1200px;
    }

    .header {
      text-align: center;
      margin-bottom: 30px;
      padding-bottom: 20px;
    }

    h2 {
      color: #3478bdff;
      margin: 0;
      font-size: 32px;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.1);
    }
    .alert-warning {
        background-color: #fff3cd; /* Light yellow background */
        border: 2px solid #ffcc00; /* Darker yellow border */
        color: #856404; /* Text color */
        font-weight: bold;
        padding: 15px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
    .subtitle {
      color: #e41c1cff;
      font-size: 18px;
      margin-top: 5px;
    }

    .form-row {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: space-between;
      margin-bottom: 20px;
    }

    .field {
      flex: 1 1 220px;
      display: flex;
      flex-direction: column;
    }

    label {
      font-weight: bold;
      margin-bottom: 8px;
      color: #2c3e50;
      font-size: 14px;
    }

    input[type="text"],
    input[type="number"],
    input[type="date"],
    select {
      padding: 12px 15px;
      border: 2px solid #ddd;
      border-radius: 8px;
      background-color: #f9f9f9;
      font-size: 15px;
      transition: border-color 0.3s;
    }
    
    input:focus, select:focus {
      border-color: #3498db;
      outline: none;
      box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
    }
    
    input[readonly] {
      background-color: #eef7ff;
      border-color: #bdc3c7;
    }

    .detailedScore {
        margin-top: 15px;
        padding: 20px;
        background: #e8f4fc;
        border-radius: 10px;
        display: none;
        border: 2px solid #3498db;
    }

    .toggle-btn {
        display: block;
        margin: 15px 0;
        padding: 12px 20px;
        font-size: 16px;
        background-color: #3498db;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s;
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
    }

    .toggle-btn:hover {
        background-color: #2980b9;
        transform: translateY(-2px);
        box-shadow: 0 5px 10px rgba(0,0,0,0.15);
    }

    .match-block {
        border: 2px solid #e0e0e0;
        padding: 25px;
        margin-bottom: 15px;
        background: #fff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s;
    }
    
    .match-block:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }

    .submit-btn {
        display: block;
        margin: 30px auto;
        padding: 15px 40px;
        font-size: 18px;
        font-weight: bold;
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
    }
    
    .submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(231, 76, 60, 0.5);
    }

    .match-info {
        background: linear-gradient(135deg, #2c3e50, #4a6580);
        color: white;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .team-box {
        flex: 1;
        padding: 20px;
        background: #f9f9f9;
        border: 2px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }
    
    .team-title {
        text-align: center;
        color: #2c3e50;
        padding-bottom: 12px;
        margin-bottom: 20px;
        border-bottom: 2px solid #3498db;
        font-size: 20px;
    }
    .back-btn {
      display: inline-block;
      padding: 10px 20px;
      margin-top: 15px;
      background: linear-gradient(135deg, #ff416c, #ff4b2b);
      color: white;
      font-size: 16px;
      font-weight: bold;
      align-items: center;
      border-radius: 8px;
      text-decoration: none;
      box-shadow: 0px 4px 8px rgba(0,0,0,0.2);
      transition: 0.3s ease-in-out;
    }

    .back-btn:hover {
      background: linear-gradient(135deg, #ff4b2b, #ff416c);
      transform: translateY(-3px);
      box-shadow: 0px 6px 12px rgba(0,0,0,0.3);
    }
    .alert {
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        text-align: center;
        font-size: 18px;
    }
    
    .alert-warning {
        background-color: #fff3cd;
        border: 2px solid #ffeaa7;
        color: #856404;
    }
    
    .score-input {
        font-weight: bold;
        font-size: 18px;
        text-align: center;
    }
    
    .cricket-icon {
        display: inline-block;
        margin-right: 10px;
        font-size: 24px;
    }
    
    @media (max-width: 768px) {
        .form-row {
            flex-direction: column;
            gap: 15px;
        }
        
        .team-box {
            margin-bottom: 20px;
        }
    }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2><span class="cricket-icon"></span>Cricket Match Results</h2>
        <div class="subtitle"><?php echo htmlspecialchars($event_name); ?></div>
    </div>
    
    <?php if (empty($matches)): ?>
        <div class="alert alert-warning">No cricket matches found in database!</div>
    <?php else: ?>
    <form id="cricketForm" method="POST" action="">
        <div id="matches-container">
            <?php foreach ($matches as $index => $match): ?>
            <div class="match-block">
                <div class="match-info">
                    <strong>Match Date:</strong> <?php echo $match['match_date']; ?> | 
                    <strong>Round:</strong> <?php echo $match['round']; ?>
                </div>
                
                <input type="hidden" name="match_id[]" value="<?php echo $match['id']; ?>">
                <input type="hidden" name="event_name[]" value="<?php echo $match['event_name']; ?>">
                <input type="hidden" name="match_date[]" value="<?php echo $match['match_date']; ?>">
                <input type="hidden" name="round_name[]" value="<?php echo $match['round']; ?>">
                <input type="hidden" name="team_a[]" value="<?php echo $match['team1_name']; ?>">
                <input type="hidden" name="team_b[]" value="<?php echo $match['team2_name']; ?>">
                
                <div class="form-row">
                  <div class="field">
                      <label>Event Name</label>
                      <input type="text" value="<?php echo $match['event_name']; ?>" readonly>
                  </div>
                  <div class="field">
                      <label>Match Date</label>
                      <input type="date" value="<?php echo $match['match_date']; ?>" readonly>
                  </div>
                  <div class="field">
                      <label>Round</label>
                      <input type="text" value="<?php echo $match['round']; ?>" readonly>
                  </div>
                </div>
                
                <div class="form-row">
                  <div class="field">
                      <label>Team A Name</label>
                      <input type="text" value="<?php echo $match['team1_name']; ?>" readonly>
                  </div>
                  <div class="field">
                      <label>Team B Name</label>
                      <input type="text" value="<?php echo $match['team2_name']; ?>" readonly>
                  </div>
                  <div class="field">
                      <label>Total Overs</label>
                      <input type="number" name="total_overs[]" required min="1" max="50">
                  </div>
                </div>
                
                <div class="form-row">
                  <div class="field">
                      <label>Toss Winner</label>
                      <select name="toss_winner[]" required>
                            <option value="">Select</option>
                            <option value="Team A">Team A</option>
                            <option value="Team B">Team B</option>
                        </select>  
                    </div>
                  <div class="field">
                      <label>Decision After Toss</label>
                      <select name="Decision_after_toss[]" required>
                            <option value="">Select</option>
                            <option value="Bat">Bat</option>
                            <option value="Bowl">Bowl</option>
                        </select>
                  </div>
                </div>
                
                <div class="form-row">
                  <div class="field">
                      <label>Team A Score</label>
                      <input type="number" class="score-input" name="score_a[]" required min="0">
                  </div>
                  <div class="field">
                      <label>Team B Score</label>
                      <input type="number" class="score-input" name="score_b[]" required min="0">
                  </div>
                </div>

                <button type="button" class="toggle-btn" onclick="toggleDetailedScore(this)">Show Detailed Result</button>
                <div class="detailedScore">
                    <h3 style="color: #2c3e50; text-align: center;">Detailed Score</h3>
                    <div class="form-row">
                        <div class="team-box">
                            <div class="team-title"><?php echo $match['team1_name']; ?></div>
                            <div class="form-row">
                                <div class="field"><label>Runs</label><input type="number" name="runs_a[]" required min="0"></div>
                                <div class="field"><label>Overs</label><input type="number" name="overs_a[]" required min="0" step="0.1"></div>
                            </div>
                            <div class="form-row">
                                <div class="field"><label>Wickets</label><input type="number" name="wickets_a[]" required min="0"></div>
                                <div class="field"><label>Extras</label><input type="number" name="extras_a[]" min="0"></div>
                            </div>
                            <div class="form-row">
                                <div class="field"><label>Fours</label><input type="number" name="fours_a[]" min="0"></div>
                                <div class="field"><label>Sixes</label><input type="number" name="sixes_a[]" min="0"></div>
                            </div>
                            <div class="form-row">
                                <div class="field"><label>Catches</label><input type="number" name="catches_a[]" min="0"></div>
                                <div class="field"><label>Outs</label><input type="number" name="outs_a[]" required min="0"></div>
                            </div>
                        </div>

                        <div class="team-box">
                            <div class="team-title"><?php echo $match['team2_name']; ?></div>
                            <div class="form-row">
                                <div class="field"><label>Runs</label><input type="number" name="runs_b[]" required min="0"></div>
                                <div class="field"><label>Overs</label><input type="number" name="overs_b[]" required min="0" step="0.1"></div>
                            </div>
                            <div class="form-row">
                                <div class="field"><label>Wickets</label><input type="number" name="wickets_b[]" required min="0"></div>
                                <div class="field"><label>Extras</label><input type="number" name="extras_b[]" min="0"></div>
                            </div>
                            <div class="form-row">
                                <div class="field"><label>Fours</label><input type="number" name="fours_b[]" min="0"></div>
                                <div class="field"><label>Sixes</label><input type="number" name="sixes_b[]" min="0"></div>
                            </div>
                            <div class="form-row">
                                <div class="field"><label>Catches</label><input type="number" name="catches_b[]" min="0"></div>
                                <div class="field"><label>Outs</label><input type="number" name="outs_b[]" required min="0"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="submit-btn">Submit All Results</button>
        <a href="../user/get_event.php" class="back-btn">⬅ Back</a>
    </form>
    <?php endif; ?>
</div>

<script>
    document.getElementById('cricketForm').addEventListener('submit', function(e) {
        const matchBlocks = document.querySelectorAll('.match-block');
        let valid = true;

        matchBlocks.forEach(block => {
            const numberFields = block.querySelectorAll('input[type="number"]');
            numberFields.forEach(input => {
                if (input.value === "") {
                    alert(`${input.previousElementSibling.innerText} cannot be empty`);
                    valid = false;
                    input.focus();
                    return;
                }
                const val = Number(input.value);
                if (val < 0) {
                    alert(`${input.previousElementSibling.innerText} cannot be negative`);
                    valid = false;
                    input.focus();
                    return;
                }
            });

            const selectFields = block.querySelectorAll('select');
            selectFields.forEach(select => {
                if (select.value === "") {
                    alert(`${select.previousElementSibling.innerText} must be selected`);
                    valid = false;
                    select.focus();
                    return;
                }
            });
        });

        if (!valid) {
            e.preventDefault();
        }
    });
    
    function toggleDetailedScore(btn) {
        var details = btn.nextElementSibling;
        if (details.style.display === "none" || details.style.display === "") {
            details.style.display = "block";
            btn.textContent = "Hide Detailed Result";
        } else {
            details.style.display = "none";
            btn.textContent = "Show Detailed Result";
        }
    }
    
    // Auto-calculate winner when scores change
    document.querySelectorAll('input[name="score_a[]"], input[name="score_b[]"]').forEach(input => {
        input.addEventListener('change', function() {
            const block = this.closest('.match-block');
            const scoreA = parseInt(block.querySelector('input[name="score_a[]"]').value) || 0;
            const scoreB = parseInt(block.querySelector('input[name="score_b[]"]').value) || 0;
            
            if (scoreA > scoreB) {
                // Team A wins - you could add visual indication here
            } else if (scoreB > scoreA) {
                // Team B wins
            } else {
                // Draw
            }
        });
    });
</script>
</body>
</html>