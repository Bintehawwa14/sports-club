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

// Fetch volleyball matches
$sql = "SELECT m.id, m.event_id, m.team1_name, m.team2_name, m.round, 
               m.match_date, m.match_status, m.team1_score, m.team2_score,
               e.event_name 
        FROM matches m
        JOIN events e ON m.event_id = e.id
        WHERE m.game='volleyball' AND m.match_status = 'scheduled'
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
        
        // Update matches table
        $stmt2 = $con->prepare("UPDATE matches 
            SET team1_score = ?, team2_score = ?, result_winner = ?, winner_name = ?, loser_name = ?, match_status='Completed' WHERE id = ?");
        $stmt2->bind_param("iisssi", $score_a, $score_b, $winner_name, $winner_name, $loser_name, $match_id);
        $stmt2->execute();
        
        // Insert into volleyballresult table
        $event_name   = $_POST['event_name'][$index];
        $match_date   = $_POST['match_date'][$index];
        $round_name   = $_POST['round_name'][$index];
        
        $stmt = $con->prepare("INSERT INTO volleyballresult 
            (event_name, match_date, round_name, team_a, team_b, score_a, score_b, winner) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssiis", $event_name, $match_date, $round_name, $team_a, $team_b, $score_a, $score_b, $winner_name);
        $stmt->execute();
        $stmt->close();
        
        // Get the inserted ID for detailed results
        $result_id = $con->insert_id;
        
        // Detailed results for sets
        for ($set = 1; $set <= 5; $set++) {
            $team_a_points = $_POST["set{$set}_team_a"][$index] ?? 0;
            $team_b_points = $_POST["set{$set}_team_b"][$index] ?? 0;
            $team_a_aces   = $_POST["set{$set}_aces_a"][$index] ?? 0;
            $team_b_aces   = $_POST["set{$set}_aces_b"][$index] ?? 0;
            $team_a_spikes = $_POST["set{$set}_spikes_a"][$index] ?? 0;
            $team_b_spikes = $_POST["set{$set}_spikes_b"][$index] ?? 0;
            $team_a_errors = $_POST["set{$set}_errors_a"][$index] ?? 0;
            $team_b_errors = $_POST["set{$set}_errors_b"][$index] ?? 0;
            $team_a_blocks = $_POST["set{$set}_blocks_a"][$index] ?? 0;
            $team_b_blocks = $_POST["set{$set}_blocks_b"][$index] ?? 0;
            
            // Insert into volleyball_set_details
            $stmt2 = $con->prepare("INSERT INTO volleyball_set_details 
                (result_id, set_number, team_a_points, team_b_points, team_a_aces, team_b_aces, 
                 team_a_spikes, team_b_spikes, team_a_errors, team_b_errors, team_a_blocks, team_b_blocks) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt2->bind_param(
                "iiiiiiiiiiii", 
                $result_id, $set, $team_a_points, $team_b_points, 
                $team_a_aces, $team_b_aces, $team_a_spikes, $team_b_spikes,
                $team_a_errors, $team_b_errors, $team_a_blocks, $team_b_blocks
            );

            $stmt2->execute();
            $stmt2->close();
        }
    }
    
    echo "<script>alert('Volleyball match results saved successfully.');</script>";
    echo "<script>window.location.href = 'volleyball-result.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Volleyball Result Form</title>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body {
      font-family: Arial, sans-serif;
      background-image: url(../images/volleyballpage.jpg);
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
      background-image: url(../images/volleyballform.jpg);
      background-size: cover;
      background-position: center;
    }

    h2 {
      text-align: center;
      color: white;
      margin-bottom: 20px;
    }
    h3 {
      text-align: center;
      color: #006400;
      margin-bottom: 20px;
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

    .add-match-btn {
        display: block;
        margin: 0 auto 20px auto;
        padding: 10px 20px;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    .add-match-btn:hover {
        background-color: #218838;
    }
    .submit-btn {
        display: block;
        margin: 20px auto;
        padding: 12px 25px;
        font-size: 16px;
        font-weight: bold;
        background-color: #e40e18ff;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }
    .back-btn {
      display: inline-block;
      padding: 10px 20px;
      margin-top: 15px;
      background: linear-gradient(135deg, #ff416c, #ff4b2b);
      color: white;
      font-size: 16px;
      font-weight: bold;
      border-radius: 8px;
      align-items: center;
      text-decoration: none;
      box-shadow: 0px 4px 8px rgba(0,0,0,0.2);
      transition: 0.3s ease-in-out;
    }

    .back-btn:hover {
      background: linear-gradient(135deg, #ff4b2b, #ff416c);
      transform: translateY(-3px);
      box-shadow: 0px 6px 12px rgba(0,0,0,0.3);
    }
        .alert-warning {
        background-color: #fff3cd; /* Light yellow background */
        border: 2px solid #ffcc00; /* Darker yellow border */
        color: #856404; /* Text color */
        font-weight: bold;
        padding: 15px;
        border-radius: 10px;
        text-align: center;
        
    }

    .submit-btn:hover {
        background-color: #830606ff;
        transform: scale(1.05);
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
    <h2>Volleyball Match Result</h2>
    <form id="resultForm" method="POST" action="" enctype="multipart/form-data">

        <div id="matches-container">
            <?php if (!empty($matches)): ?>
                <?php foreach ($matches as $index => $row): ?>
                <div class="match-block">
                    <input type="hidden" name="match_id[]" value="<?php echo $row['id']; ?>">
                    <div class="form-row">
                      <div class="field"><label>Event Name</label><input type="text" name="event_name[]" readonly value="<?php echo htmlspecialchars($row['event_name']); ?>" required></div>
                      <div class="field"><label>Match Date</label><input type="text" name="match_date[]" readonly value="<?php echo $row['match_date']; ?>" required></div>
                      <div class="field"><label>Round</label><input type="text" name="round_name[]" readonly value="<?php echo htmlspecialchars($row['round']); ?>" required></div>
                      <div class="field"><label>Team A Name</label><input type="text" name="team_a[]" readonly value="<?php echo htmlspecialchars($row['team1_name']); ?>" required></div>
                      <div class="field"><label>Team B Name</label><input type="text" name="team_b[]" readonly value="<?php echo htmlspecialchars($row['team2_name']); ?>" required></div>
                      <div class="field"><label>Team A Score</label><input type="number" name="score_a[]" required min="0"></div>
                      <div class="field"><label>Team B Score</label><input type="number" name="score_b[]" required min="0"></div>
                    </div>

                    <button type="button" class="btn-sm-custom" onclick="toggleDetailedScore(this)">Show Detailed Result</button>

                    <div class="detailedScore">
                        <h3>Detailed Score</h3>
                        <div class="two-column-row">
                            <div class="set-section">
                                <div class="form-section-title"><h3>Set 1</h3></div>
                                <div class="form-row">
                                    <div class="field"><label>Team A Points</label><input type="number" name="set1_team_a[]" required></div>
                                    <div class="field"><label>Team B Points</label><input type="number" name="set1_team_b[]" required></div>
                                    <div class="field"><label>Team A Aces</label><input type="number" name="set1_aces_a[]"></div>
                                    <div class="field"><label>Team B Aces</label><input type="number" name="set1_aces_b[]"></div>
                                    <div class="field"><label>Team A Spikes</label><input type="number" name="set1_spikes_a[]" required></div>
                                    <div class="field"><label>Team B Spikes</label><input type="number" name="set1_spikes_b[]" required></div>
                                    <div class="field"><label>Team A Errors</label><input type="number" name="set1_errors_a[]"></div>
                                    <div class="field"><label>Team B Errors</label><input type="number" name="set1_errors_b[]"></div>
                                    <div class="field"><label>Team A Blocks</label><input type="number" name="set1_blocks_a[]" required></div>
                                    <div class="field"><label>Team B Blocks</label><input type="number" name="set1_blocks_b[]" required></div>
                                </div>
                            </div>
                            <div class="set-section">
                                <div class="form-section-title"><h3>Set 2</h3></div>
                                <div class="form-row">
                                    <div class="field"><label>Team A Points</label><input type="number" name="set2_team_a[]" required></div>
                                    <div class="field"><label>Team B Points</label><input type="number" name="set2_team_b[]" required></div>
                                    <div class="field"><label>Team A Aces</label><input type="number" name="set2_aces_a[]"></div>
                                    <div class="field"><label>Team B Aces</label><input type="number" name="set2_aces_b[]"></div>
                                    <div class="field"><label>Team A Spikes</label><input type="number" name="set2_spikes_a[]" required></div>
                                    <div class="field"><label>Team B Spikes</label><input type="number" name="set2_spikes_b[]" required></div>
                                    <div class="field"><label>Team A Errors</label><input type="number" name="set2_errors_a[]"></div>
                                    <div class="field"><label>Team B Errors</label><input type="number" name="set2_errors_b[]"></div>
                                    <div class="field"><label>Team A Blocks</label><input type="number" name="set2_blocks_a[]" required></div>
                                    <div class="field"><label>Team B Blocks</label><input type="number" name="set2_blocks_b[]" required></div>
                                </div>
                            </div>
                        </div>
                        <div class="two-column-row">
                            <div class="set-section">
                                <div class="form-section-title"><h3>Set 3</h3></div>
                                <div class="form-row">
                                    <div class="field"><label>Team A Points</label><input type="number" name="set3_team_a[]" required></div>
                                    <div class="field"><label>Team B Points</label><input type="number" name="set3_team_b[]" required></div>
                                    <div class="field"><label>Team A Aces</label><input type="number" name="set3_aces_a[]"></div>
                                    <div class="field"><label>Team B Aces</label><input type="number" name="set3_aces_b[]"></div>
                                    <div class="field"><label>Team A Spikes</label><input type="number" name="set3_spikes_a[]" required></div>
                                    <div class="field"><label>Team B Spikes</label><input type="number" name="set3_spikes_b[]" required></div>
                                    <div class="field"><label>Team A Errors</label><input type="number" name="set3_errors_a[]"></div>
                                    <div class="field"><label>Team B Errors</label><input type="number" name="set3_errors_b[]"></div>
                                    <div class="field"><label>Team A Blocks</label><input type="number" name="set3_blocks_a[]" required></div>
                                    <div class="field"><label>Team B Blocks</label><input type="number" name="set3_blocks_b[]" required></div>
                                </div>
                            </div>
                            <div class="set-section">
                                <div class="form-section-title"><h3>Set 4</h3></div>
                                <div class="form-row">
                                    <div class="field"><label>Team A Points</label><input type="number" name="set4_team_a[]" required></div>
                                    <div class="field"><label>Team B Points</label><input type="number" name="set4_team_b[]" required></div>
                                    <div class="field"><label>Team A Aces</label><input type="number" name="set4_aces_a[]"></div>
                                    <div class="field"><label>Team B Aces</label><input type="number" name="set4_aces_b[]"></div>
                                    <div class="field"><label>Team A Spikes</label><input type="number" name="set4_spikes_a[]" required></div>
                                    <div class="field"><label>Team B Spikes</label><input type="number" name="set4_spikes_b[]" required></div>
                                    <div class="field"><label>Team A Errors</label><input type="number" name="set4_errors_a[]"></div>
                                    <div class="field"><label>Team B Errors</label><input type="number" name="set4_errors_b[]"></div>
                                    <div class="field"><label>Team A Blocks</label><input type="number" name="set4_blocks_a[]" required></div>
                                    <div class="field"><label>Team B Blocks</label><input type="number" name="set4_blocks_b[]" required></div>
                                </div>
                            </div>
                        </div>
                        <div class="set-section">
                            <div class="form-section-title"><h3>Set 5</h3></div>
                            <div class="form-row">
                                <div class="field"><label>Team A Points</label><input type="number" name="set5_team_a[]" required></div>
                                <div class="field"><label>Team B Points</label><input type="number" name="set5_team_b[]" required></div>
                                <div class="field"><label>Team A Aces</label><input type="number" name="set5_aces_a[]"></div>
                                <div class="field"><label>Team B Aces</label><input type="number" name="set5_aces_b[]"></div>
                                <div class="field"><label>Team A Spikes</label><input type="number" name="set5_spikes_a[]" required></div>
                                <div class="field"><label>Team B Spikes</label><input type="number" name="set5_spikes_b[]" required></div>
                                <div class="field"><label>Team A Errors</label><input type="number" name="set5_errors_a[]"></div>
                                <div class="field"><label>Team B Errors</label><input type="number" name="set5_errors_b[]"></div>
                                <div class="field"><label>Team A Blocks</label><input type="number" name="set5_blocks_a[]" required></div>
                                <div class="field"><label>Team B Blocks</label><input type="number" name="set5_blocks_b[]" required></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-warning text-center">No volleyball matches found in database!</div>
            <?php endif; ?>
        </div>
        <button type="submit" class="submit-btn">Submit</button>
        <a href="../user/get_event.php" class="back-btn">⬅ Back</a>
    </form>
</div>

<script>
    es_b[]" value="5" min="0">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <div class="stat-label">Spikes</div>
                                                        <input type="number" class="form-control" name="set3_spikes_b[]" value="12" min="0" required>
                                                        <div class="invalid-feedback">Please enter spikes count</div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <div class="stat-label">Blocks</div>
                                                        <input type="number" class="form-control" name="set3_blocks_b[]" value="3" min="0" required>
                                                        <div class="invalid-feedback">Please enter blocks count</div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <div class="stat-label">Errors</div>
                                                        <input type="number" class="form-control" name="set3_errors_b[]" value="4" min="0" max="10">
                                                        <div class="invalid-feedback">Errors cannot exceed 10</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-success">Submit Results</button>
                    <a href="../user/get_event.php" class="back-btn">⬅ Back</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
        
        // Client-side validations
        document.getElementById("volleyballForm").addEventListener("submit", function(e) {
            e.preventDefault();
            clearValidationErrors();
            
            let isValid = true;
            let errorMessages = [];
            

            
            // Validate team names
            const teamAInputs = document.querySelectorAll('input[name="team_a[]"]');
            const teamBInputs = document.querySelectorAll('input[name="team_b[]"]');
            
            teamAInputs.forEach((input, index) => {
                const teamA = input.value.trim();
                const teamB = teamBInputs[index].value.trim();
                
                if (teamA.toLowerCase() === teamB.toLowerCase()) {
                    markAsInvalid(input, `Match ${index+1}: Team A and Team B must be different`);
                    markAsInvalid(teamBInputs[index], `Match ${index+1}: Team A and Team B must be different`);
                    isValid = false;
                    errorMessages.push(`Match ${index+1}: Team A and Team B must be different`);
                }
            });
            
            // Validate scores
            const scoreAInputs = document.querySelectorAll('input[name="score_a[]"]');
            const scoreBInputs = document.querySelectorAll('input[name="score_b[]"]');
            
            scoreAInputs.forEach((input, index) => {
                const scoreA = parseInt(input.value);
                const scoreB = parseInt(scoreBInputs[index].value);
                
                if (scoreA < 0 || scoreB < 0) {
                    if (scoreA < 0) {
                        markAsInvalid(input, `Match ${index+1}: Scores cannot be negative`);
                        errorMessages.push(`Match ${index+1}: Team A score cannot be negative`);
                    }
                    if (scoreB < 0) {
                        markAsInvalid(scoreBInputs[index], `Match ${index+1}: Scores cannot be negative`);
                        errorMessages.push(`Match ${index+1}: Team B score cannot be negative`);
                    }
                    isValid = false;
                }
                
                if (scoreA > 5 || scoreB > 5) {
                    if (scoreA > 5) {
                        markAsInvalid(input, `Match ${index+1}: Score cannot exceed 5 sets`);
                        errorMessages.push(`Match ${index+1}: Team A score cannot exceed 5 sets`);
                    }
                    if (scoreB > 5) {
                        markAsInvalid(scoreBInputs[index], `Match ${index+1}: Score cannot exceed 5 sets`);
                        errorMessages.push(`Match ${index+1}: Team B score cannot exceed 5 sets`);
                    }
                    isValid = false;
                }
            });
            
            // Validate set points
            const setPointInputs = document.querySelectorAll('.set-points');
            setPointInputs.forEach(input => {
                const points = parseInt(input.value);
                if (points > 30) {
                    markAsInvalid(input, 'Points cannot exceed 30');
                    isValid = false;
                    errorMessages.push('Set points cannot exceed 30');
                }
            });
            
            // Validate errors
            const errorInputs = document.querySelectorAll('input[name$="_errors_a[]"], input[name$="_errors_b[]"]');
            errorInputs.forEach(input => {
                const errors = parseInt(input.value);
                if (errors > 10) {
                    markAsInvalid(input, 'Errors cannot exceed 10');
                    isValid = false;
                    errorMessages.push('Errors cannot exceed 10');
                }
            });
            
            // Validate required fields
            const requiredInputs = document.querySelectorAll('input[required]');
            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    markAsInvalid(input, 'This field is required');
                    isValid = false;
                    errorMessages.push('Please fill all required fields');
                }
            });
            
            if (!isValid) {
                showValidationErrors(errorMessages);
                return;
            }
            
            // If all validations pass
            Swal.fire({
                title: 'Success!',
                text: 'Volleyball match results have been validated and are ready to be saved!',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        });
        
        function clearValidationErrors() {
            // Remove invalid styling
            const invalidInputs = document.querySelectorAll('.is-invalid');
            invalidInputs.forEach(input => {
                input.classList.remove('is-invalid');
            });
            
            // Hide validation summary
            document.getElementById('validationSummary').style.display = 'none';
        }
        
        function showValidationErrors(messages) {
            const errorList = document.getElementById('validationErrors');
            errorList.innerHTML = '';
            
            // Remove duplicates
            const uniqueMessages = [...new Set(messages)];
            
            uniqueMessages.forEach(message => {
                const li = document.createElement('li');
                li.textContent = message;
                errorList.appendChild(li);
            });
            
            document.getElementById('validationSummary').style.display = 'block';
            
            // Scroll to validation summary
            document.getElementById('validationSummary').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
        
        // Real-time validation for set points
        document.querySelectorAll('.set-points').forEach(input => {
            input.addEventListener('blur', function() {
                const points = parseInt(this.value);
                if (points > 30) {
                    markAsInvalid(this, 'Points cannot exceed 30');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        });
        
        // Real-time validation for errors
        document.querySelectorAll('input[name$="_errors_a[]"], input[name$="_errors_b[]"]').forEach(input => {
            input.addEventListener('blur', function() {
                const errors = parseInt(this.value);
                if (errors > 10) {
                    markAsInvalid(this, 'Errors cannot exceed 10');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
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

    document.getElementById("resultForm").addEventListener("submit", function(e) {
        let valid = true;
        let messages = [];
        let blocks = document.querySelectorAll(".match-block");

        blocks.forEach((block, index) => {
            let date = block.querySelector("input[name='match_date[]']").value;
            let teamA = block.querySelector("input[name='team_a[]']").value.trim();
            let teamB = block.querySelector("input[name='team_b[]']").value.trim();
            let scoreA = parseInt(block.querySelector("input[name='score_a[]']").value);
            let scoreB = parseInt(block.querySelector("input[name='score_b[]']").value);

            if (new Date(date) > new Date()) {
                messages.push(`Match ${index+1}: Date cannot be in the future.`);
                valid = false;
            }
            if (teamA.toLowerCase() === teamB.toLowerCase()) {
                messages.push(`Match ${index+1}: Team A and Team B must be different.`);
                valid = false;
            }
            if (scoreA < 0 || scoreB < 0) {
                messages.push(`Match ${index+1}: Scores cannot be negative.`);
                valid = false;
            }

            // Detailed Sets Validation
            for (let set = 1; set <= 5; set++) {
                let setA = parseInt(block.querySelector(`input[name='set${set}_team_a[]']`).value || 0);
                let setB = parseInt(block.querySelector(`input[name='set${set}_team_b[]']`).value || 0);

                if (setA > 30 || setB > 30) {
                    messages.push(`Match ${index+1}, Set ${set}: Points cannot exceed 30.`);
                    valid = false;
                }

                // errors/faults validation
                let errorsA = parseInt(block.querySelector(`input[name='set${set}_errors_a[]']`)?.value || 0);
                let errorsB = parseInt(block.querySelector(`input[name='set${set}_errors_b[]']`)?.value || 0);
                if (errorsA > 10 || errorsB > 10) {
                    messages.push(`Match ${index+1}, Set ${set}: Errors cannot exceed 10.`);
                    valid = false;
                }
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