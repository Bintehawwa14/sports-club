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
// Fetch all badminton matches with event names
$sql = "SELECT m.*, e.event_name 
        FROM matches m
        JOIN events e ON m.event_id = e.id
        WHERE m.game='badminton' AND match_status = 'completed' AND result_winner IS NULL
        ORDER BY m.match_date ASC, m.round ASC";
$result = mysqli_query($con, $sql);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'save_results') {
        foreach ($_POST['match_id'] as $index => $match_id) {
            // Get values from form
            $event_name = $_POST['event_name'][$index];
            $match_date = $_POST['match_date'][$index];
            $round_name = $_POST['round_name'][$index];
            $team_a = $_POST['team_a'][$index];
            $team_b = $_POST['team_b'][$index];
            $score_a = (int)$_POST['score_a'][$index];
            $score_b = (int)$_POST['score_b'][$index];

            // Winner calculation
            if ($score_a > $score_b) {
                $winner = $team_a;
            } elseif ($score_b > $score_a) {
                $winner = $team_b;
            } else {
                $winner = "Draw";
            }
            
            // Basic Validations
            if (empty($match_date) || empty($team_a) || empty($team_b)) {
                die("Required fields missing.");
            }
            if (!is_numeric($score_a) || !is_numeric($score_b)) {
                die("Scores must be numeric.");
            }

            // Insert into badmintonresult table
            $stmt = $con->prepare("INSERT INTO badmintonresult (event_name, match_date, round_name, team_a, team_b, score_a, score_b, winner) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssiis", $event_name, $match_date, $round_name, $team_a, $team_b, $score_a, $score_b, $winner);
            $stmt->execute();
            $stmt->close();

            // Update matches table
            $stmt2 = $con->prepare("UPDATE matches SET team1_score = ?, team2_score = ?, result_winner = ?, match_status='Completed' WHERE id = ?");
            $stmt2->bind_param("iisi", $score_a, $score_b, $winner, $match_id);
            $stmt2->execute();
            $stmt2->close();

            // Detailed Results Data
            $set1_team_a = $_POST['set1_team_a'][$index];
            $set1_team_b = $_POST['set1_team_b'][$index];
            $set1_faults_a = $_POST['set1_faults_a'][$index];
            $set1_faults_b = $_POST['set1_faults_b'][$index];

            $set2_team_a = $_POST['set2_team_a'][$index];
            $set2_team_b = $_POST['set2_team_b'][$index];
            $set2_faults_a = $_POST['set2_faults_a'][$index];
            $set2_faults_b = $_POST['set2_faults_b'][$index];

            $set3_team_a = $_POST['set3_team_a'][$index];
            $set3_team_b = $_POST['set3_team_b'][$index];
            $set3_faults_a = $_POST['set3_faults_a'][$index];
            $set3_faults_b = $_POST['set3_faults_b'][$index];

            $net_errors_a = $_POST['net_errors_a'][$index];
            $net_errors_b = $_POST['net_errors_b'][$index];
            $smashes_a = $_POST['smashes_a'][$index];
            $smashes_b = $_POST['smashes_b'][$index];
            $let_serves_a = $_POST['let_serves_a'][$index];
            $let_serves_b = $_POST['let_serves_b'][$index];

            // Insert into badmintonresultdetails table
            $stmt2 = $con->prepare("INSERT INTO badmintonresultdetails 
            (set1_team_a, set1_team_b, set1_faults_a, set1_faults_b, 
            set2_team_a, set2_team_b, set2_faults_a, set2_faults_b, 
            set3_team_a, set3_team_b, set3_faults_a, set3_faults_b, 
            net_errors_a, net_errors_b, smashes_a, smashes_b, 
            let_serves_a, let_serves_b) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt2->bind_param(
                "iiiiiiiiiiiiiiiiii", 
                $set1_team_a, $set1_team_b, $set1_faults_a, $set1_faults_b, 
                $set2_team_a, $set2_team_b, $set2_faults_a, $set2_faults_b, 
                $set3_team_a, $set3_team_b, $set3_faults_a, $set3_faults_b, 
                $net_errors_a, $net_errors_b, $smashes_a, $smashes_b, 
                $let_serves_a, $let_serves_b
            );

            $stmt2->execute();
            $stmt2->close();
        }

        echo "<script>
                Swal.fire({
                    title: 'Success!',
                    text: 'Badminton match results saved successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'badminton-result.php';
                });
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Badminton Result Form</title>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    body {
      font-family: Arial, sans-serif;
      background-image: url(../images/badmintonpage.jpg);
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
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
      width: 90%;
      max-width: 1200px;
      background-image: url(../images/badmintonform.jpg);
      background-size: cover;
      background-position: center;
    }

    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 20px;
    }
        h3 {
      text-align: center;
      color: #cf0dafff;
      margin-bottom: 20px;
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
    .form-row {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: space-between;
    }

    .field {
      flex: 1 1 200px;
      display: flex;
      flex-direction: column;
    }

    label {
      font-weight: bold;
      margin-bottom: 5px;
    }

    
    input[type="text"],
    input[type="number"],
    input[type="date"] {
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .detailedScore {
        margin-top: 10px;
        padding: 10px;
        background: #eef;
        border-radius: 5px;
        display: none;
    }

    .btn-sm-custom {
        display: block;
        margin: 10px 0;
        padding: 8px 16px;
        font-size: 15px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-sm-custom:hover {
        background-color: #0056b3;
    }

    .match-block {
        border: 1px solid #ccc;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        background: #f9f9f9;
    }

    .submit-btn {
        display: block;
        margin: 20px auto; /* Center horizontally with space */
        padding: 12px 25px;
        font-size: 16px;
        font-weight: bold;
        background-color: #e40e18ff; /* Green */
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .submit-btn:hover {
        background-color: #830606ff; /* Darker green on hover */
        transform: scale(1.05); /* Slight zoom effect */
    }

    .submit-btn:focus {
        outline: none;
        box-shadow: 0 0 5px #218838;
    }


    .two-column-row {
      display: flex;
      gap: 20px;
      margin-bottom: 20px;
    }

    .two-column-row .set-section {
      flex: 1;
      border: 1px solid #ccc;
      padding: 10px;
      border-radius: 8px;
      background-color: #fff;
    }

    @media (max-width: 768px) {
      .two-column-row, .form-row {
        flex-direction: column;
      }
    }
</style>
</head>
<body>
<div class="container">
    <h2>Badminton Match Result</h2>
    <form id="resultForm" method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="action" value="save_results">

        <div id="matches-container">
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
            ?>
            <div class="match-block">
                <input type="hidden" name="match_id[]" value="<?php echo $row['id']; ?>">
                <div class="form-row">
                  <div class="field">
                    <label>Event Name</label>
                    <input type="text" name="event_name[]" value="<?php echo htmlspecialchars($row['event_name']); ?>" readonly>
                  </div>
                  <div class="field">
                    <label>Match Date</label>
                    <input type="text" name="match_date[]" value="<?php echo $row['match_date']; ?>" readonly>
                  </div>
                  <div class="field">
                    <label>Match Status</label>
                    <input type="text" name="match_status[]" value="<?php echo $row['match_status']; ?>" readonly>
                  </div>
                  <div class="field">
                    <label>Round</label>
                    <input type="text" name="round_name[]" value="<?php echo htmlspecialchars($row['round']); ?>" readonly>
                  </div>
                  <div class="field">
                    <label>Team A Name</label>
                    <input type="text" name="team_a[]" value="<?php echo htmlspecialchars($row['team1_name']); ?>" readonly>
                  </div>
                  <div class="field">
                    <label>Team B Name</label>
                    <input type="text" name="team_b[]" value="<?php echo htmlspecialchars($row['team2_name']); ?>" readonly>
                  </div>
                  <div class="field">
                    <label>Team A Score</label>
                    <input type="number" name="score_a[]" value="<?php echo $row['team1_score']; ?>" required min="0" max="3">
                  </div>
                  <div class="field">
                    <label>Team B Score</label>
                    <input type="number" name="score_b[]" value="<?php echo $row['team2_score']; ?>" required min="0" max="3">
                  </div>
                </div>

                <button type="button" class="btn-sm-custom" onclick="toggleDetailedScore(this)">Show Detailed Result</button>

                <div class="detailedScore">
                    <h3>Detailed Score</h3>
                    <div class="two-column-row">
                        <div class="set-section">
                            <div class="form-section-title"><h3>Set 1</h3></div>
                            <div class="form-row">
                                <div class="field"><label>Team A Points</label><input type="number" name="set1_team_a[]" required min="0" max="30"></div>
                                <div class="field"><label>Team B Points</label><input type="number" name="set1_team_b[]" required min="0" max="30"></div>
                                <div class="field"><label>Team A Faults</label><input type="number" name="set1_faults_a[]" min="0" max="10"></div>
                                <div class="field"><label>Team B Faults</label><input type="number" name="set1_faults_b[]" min="0" max="10"></div>
                            </div>
                        </div>
                        <div class="set-section">
                            <div class="form-section-title"><h3>Set 2</h3></div>
                            <div class="form-row">
                                <div class="field"><label>Team A Points</label><input type="number" name="set2_team_a[]" required min="0" max="30"></div>
                                <div class="field"><label>Team B Points</label><input type="number" name="set2_team_b[]" required min="0" max="30"></div>
                                <div class="field"><label>Team A Faults</label><input type="number" name="set2_faults_a[]" min="0" max="10"></div>
                                <div class="field"><label>Team B Faults</label><input type="number" name="set2_faults_b[]" min="0" max="10"></div>
                            </div>
                        </div>
                    </div>
                    <div class="two-column-row">
                        <div class="set-section">
                            <div class="form-section-title"><h3>Set 3</h3></div>
                            <div class="form-row">
                                <div class="field"><label>Team A Points</label><input type="number" name="set3_team_a[]" required min="0" max="30"></div>
                                <div class="field"><label>Team B Points</label><input type="number" name="set3_team_b[]" required min="0" max="30"></div>
                                <div class="field"><label>Team A Faults</label><input type="number" name="set3_faults_a[]" min="0" max="10"></div>
                                <div class="field"><label>Team B Faults</label><input type="number" name="set3_faults_b[]" min="0" max="10"></div>
                            </div>
                        </div>
                        <div class="set-section">
                            <div class="form-section-title"><h3>Other Match Stats</h3></div>
                            <div class="form-row">
                                <div class="field"><label>Team A Net Errors</label><input type="number" name="net_errors_a[]" min="0" max="10"></div>
                                <div class="field"><label>Team B Net Errors</label><input type="number" name="net_errors_b[]" min="0" max="10"></div>
                                <div class="field"><label>Team A Smashes</label><input type="number" name="smashes_a[]" min="0" max="50"></div>
                                <div class="field"><label>Team B Smashes</label><input type="number" name="smashes_b[]" min="0" max="50"></div>
                                <div class="field"><label>Team A Let Serves</label><input type="number" name="let_serves_a[]" min="0" max="5"></div>
                                <div class="field"><label>Team B Let Serves</label><input type="number" name="let_serves_b[]" min="0" max="5"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
                }
            } else {
                echo '<div class="alert alert-warning text-center">No badminton matches found in database!</div>';
            }
            ?>
        </div>
        <button type="submit" class="submit-btn">Submit</button>
    </form>
</div>

<script>
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

    document.getElementById("resultForm").addEventListener("submit", function(e) {
        let valid = true;
        let messages = [];
        let blocks = document.querySelectorAll(".match-block");

        blocks.forEach((block, index) => {
            let teamA = block.querySelector("input[name='team_a[]']").value.trim();
            let teamB = block.querySelector("input[name='team_b[]']").value.trim();
            let scoreA = parseInt(block.querySelector("input[name='score_a[]']").value);
            let scoreB = parseInt(block.querySelector("input[name='score_b[]']").value);

            if (teamA.toLowerCase() === teamB.toLowerCase()) {
                messages.push(`Match ${index+1}: Team A and Team B must be different.`);
                valid = false;
            }
            if (scoreA < 0 || scoreB < 0) {
                messages.push(`Match ${index+1}: Scores cannot be negative.`);
                valid = false;
            }
            if (scoreA > 3 || scoreB > 3) {
                messages.push(`Match ${index+1}: Scores cannot exceed 3 sets.`);
                valid = false;
            }

            // Detailed Sets Validation
            for (let set = 1; set <= 3; set++) {
                let setA = parseInt(block.querySelector(`input[name='set${set}_team_a[]']`).value || 0);
                let setB = parseInt(block.querySelector(`input[name='set${set}_team_b[]']`).value || 0);

                if (setA > 30 || setB > 30) {
                    messages.push(`Match ${index+1}, Set ${set}: Points cannot exceed 30.`);
                    valid = false;
                }

                let faultsA = parseInt(block.querySelector(`input[name='set${set}_faults_a[]']`).value || 0);
                let faultsB = parseInt(block.querySelector(`input[name='set${set}_faults_b[]']`).value || 0);
                if (faultsA > 10 || faultsB > 10) {
                    messages.push(`Match ${index+1}, Set ${set}: Faults cannot exceed 10.`);
                    valid = false;
                }
            }

            let netA = parseInt(block.querySelector("input[name='net_errors_a[]']").value || 0);
            let netB = parseInt(block.querySelector("input[name='net_errors_b[]']").value || 0);
            if (netA > 10 || netB > 10) {
                messages.push(`Match ${index+1}: Net errors cannot exceed 10.`);
                valid = false;
            }

            let smA = parseInt(block.querySelector("input[name='smashes_a[]']").value || 0);
            let smB = parseInt(block.querySelector("input[name='smashes_b[]']").value || 0);
            if (smA > 50 || smB > 50) {
                messages.push(`Match ${index+1}: Smashes cannot exceed 50.`);
                valid = false;
            }

            let letA = parseInt(block.querySelector("input[name='let_serves_a[]']").value || 0);
            let letB = parseInt(block.querySelector("input[name='let_serves_b[]']").value || 0);
            if (letA > 5 || letB > 5) {
                messages.push(`Match ${index+1}: Let serves cannot exceed 5.`);
                valid = false;
            }
        });

        if (!valid) {
            e.preventDefault();
            alert("Validation Errors:\n\n" + messages.join("\n"));
        }
    });
</script>
</body>
</html>