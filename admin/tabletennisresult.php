<?php
session_start();
include_once('../include/db_connect.php');

// Initialize variables to avoid undefined warnings
$result = null;

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

            // Update matches table
            $stmt = $con->prepare("UPDATE matches SET team1_score = ?, team2_score = ?, result_winner = ?, match_status='Completed' WHERE id = ?");
            $stmt->bind_param("iisi", $score_a, $score_b, $winner, $match_id);
            $stmt->execute();
            $stmt->close();

            // Insert into tabletennisresult table
            $stmt = $con->prepare("INSERT INTO tabletennisresult (event_name, match_date, round_name, team_a, team_b, score_a, score_b, winner) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssiis", $event_name, $match_date, $round_name, $team_a, $team_b, $score_a, $score_b, $winner);
            $stmt->execute();
            $stmt->close();

            // Get the inserted ID for detailed results
            $result_id = $con->insert_id;
            
            // Detailed results for sets (1-7)
            for ($set = 1; $set <= 7; $set++) {
                $team_a_points = $_POST["set{$set}_team_a"][$index] ?? 0;
                $team_b_points = $_POST["set{$set}_team_b"][$index] ?? 0;
                
                if ($team_a_points > 0 || $team_b_points > 0) {
                    // Insert into tabletennis_set_details
                    $stmt2 = $con->prepare("INSERT INTO tabletennis_set_details 
                        (result_id, set_number, team_a_points, team_b_points) 
                        VALUES (?, ?, ?, ?)");
                    $stmt2->bind_param("iiii", $result_id, $set, $team_a_points, $team_b_points);
                    $stmt2->execute();
                    $stmt2->close();
                }
            }
        }

        // Show success message
        echo "<script>
            Swal.fire({
                title: 'Success!',
                text: 'Table tennis match results saved successfully!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'tabletennis-result.php';
            });
        </script>";
    }
}

// Fetch all table tennis matches with event names - MOVED BEFORE HTML OUTPUT
$sql = "SELECT m.*, e.event_name 
        FROM matches m
        JOIN events e ON m.event_id = e.id
        WHERE m.game='tabletennis' AND match_status = 'scheduled'
        ORDER BY m.match_date ASC, m.round ASC";
$result = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Table Tennis Result Form</title>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    body {
      font-family: Arial, sans-serif;
      background-image: url(../images/tt.jpg);
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
      background-image: url(../images/tablet.jpg);
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
    .alert-warning {
        background-color: #fff3cd; /* Light yellow background */
        border: 2px solid #ffcc00; /* Darker yellow border */
        color: #856404; /* Text color */
        font-weight: bold;
        padding: 15px;
        border-radius: 10px;
        text-align: center;
        
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
    
    .is-invalid {
        border-color: #e74c3c;
        background-color: #ffeaea;
    }
    
    .invalid-feedback {
        color: #e74c3c;
        font-size: 1rem;
        margin-top: 5px;
    }
    
    .validation-summary {
        background-color: #ffeaea;
        border-left: 4px solid #e74c3c;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
        display: none;
    }
</style>
</head>
<body>
<div class="container">
    <h2>Table Tennis Match Result</h2>
    
    <div class="validation-summary" id="validationSummary">
        <h5>Please fix the following errors:</h5>
        <ul id="validationErrors"></ul>
    </div>
    
    <form id="resultForm" method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="action" value="save_results">

        <div id="matches-container">
            <?php
            // Check if $result is valid and has rows
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
                    <input type="number" name="score_a[]" value="<?php echo $row['team1_score']; ?>" required min="0" max="4">
                    <div class="invalid-feedback">Score must be between 0 and 4</div>
                  </div>
                  <div class="field">
                    <label>Team B Score</label>
                    <input type="number" name="score_b[]" value="<?php echo $row['team2_score']; ?>" required min="0" max="4">
                    <div class="invalid-feedback">Score must be between 0 and 4</div>
                  </div>
                </div>

                <button type="button" class="btn-sm-custom" onclick="toggleDetailedScore(this)">Show Detailed Result</button>

                <div class="detailedScore">
                    <h3>Detailed Score</h3>
                    <div class="two-column-row">
                        <div class="set-section">
                            <div class="form-section-title"><h3>Set 1</h3></div>
                            <div class="form-row">
                                <div class="field">
                                    <label>Team A Points</label>
                                    <input type="number" name="set1_team_a[]" min="0" max="11">
                                    <div class="invalid-feedback">Points cannot exceed 11</div>
                                </div>
                                <div class="field">
                                    <label>Team B Points</label>
                                    <input type="number" name="set1_team_b[]" min="0" max="11">
                                    <div class="invalid-feedback">Points cannot exceed 11</div>
                                </div>
                            </div>
                        </div>
                        <div class="set-section">
                            <div class="form-section-title"><h3>Set 2</h3></div>
                            <div class="form-row">
                                <div class="field">
                                    <label>Team A Points</label>
                                    <input type="number" name="set2_team_a[]" min="0" max="11">
                                    <div class="invalid-feedback">Points cannot exceed 11</div>
                                </div>
                                <div class="field">
                                    <label>Team B Points</label>
                                    <input type="number" name="set2_team_b[]" min="0" max="11">
                                    <div class="invalid-feedback">Points cannot exceed 11</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="two-column-row">
                        <div class="set-section">
                            <div class="form-section-title"><h3>Set 3</h3></div>
                            <div class="form-row">
                                <div class="field">
                                    <label>Team A Points</label>
                                    <input type="number" name="set3_team_a[]" min="0" max="11">
                                    <div class="invalid-feedback">Points cannot exceed 11</div>
                                </div>
                                <div class="field">
                                    <label>Team B Points</label>
                                    <input type="number" name="set3_team_b[]" min="0" max="11">
                                    <div class="invalid-feedback">Points cannot exceed 11</div>
                                </div>
                            </div>
                        </div>
                        <div class="set-section">
                            <div class="form-section-title"><h3>Set 4</h3></div>
                            <div class="form-row">
                                <div class="field">
                                    <label>Team A Points</label>
                                    <input type="number" name="set4_team_a[]" min="0" max="11">
                                    <div class="invalid-feedback">Points cannot exceed 11</div>
                                </div>
                                <div class="field">
                                    <label>Team B Points</label>
                                    <input type="number" name="set4_team_b[]" min="0" max="11">
                                    <div class="invalid-feedback">Points cannot exceed 11</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="two-column-row">
                        <div class="set-section">
                            <div class="form-section-title"><h3>Set 5</h3></div>
                            <div class="form-row">
                                <div class="field">
                                    <label>Team A Points</label>
                                    <input type="number" name="set5_team_a[]" min="0" max="11">
                                    <div class="invalid-feedback">Points cannot exceed 11</div>
                                </div>
                                <div class="field">
                                    <label>Team B Points</label>
                                    <input type="number" name="set5_team_b[]" min="0" max="11">
                                    <div class="invalid-feedback">Points cannot exceed 11</div>
                                </div>
                            </div>
                        </div>
                        <div class="set-section">
                            <div class="form-section-title"><h3>Set 6</h3></div>
                            <div class="form-row">
                                <div class="field">
                                    <label>Team A Points</label>
                                    <input type="number" name="set6_team_a[]" min="0" max="11">
                                    <div class="invalid-feedback">Points cannot exceed 11</div>
                                </div>
                                <div class="field">
                                    <label>Team B Points</label>
                                    <input type="number" name="set6_team_b[]" min="0" max="11">
                                    <div class="invalid-feedback">Points cannot exceed 11</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="set-section">
                        <div class="form-section-title"><h3>Set 7</h3></div>
                        <div class="form-row">
                            <div class="field">
                                <label>Team A Points</label>
                                <input type="number" name="set7_team_a[]" min="0" max="11">
                                <div class="invalid-feedback">Points cannot exceed 11</div>
                            </div>
                            <div class="field">
                                <label>Team B Points</label>
                                <input type="number" name="set7_team_b[]" min="0" max="11">
                                <div class="invalid-feedback">Points cannot exceed 11</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
                }
            } else {
                echo '<div class="alert alert-warning text-center">No table tennis matches found in database!</div>';
            }
            ?>
        </div>
        <button type="submit" class="submit-btn">Submit Results</button>
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
    
    // Client-side validations
    document.getElementById("resultForm").addEventListener("submit", function(e) {
        e.preventDefault();
        clearValidationErrors();
        
        let isValid = true;
        let errorMessages = [];
        
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
            
            if (scoreA > 4 || scoreB > 4) {
                if (scoreA > 4) {
                    markAsInvalid(input, `Match ${index+1}: Score cannot exceed 4 sets`);
                    errorMessages.push(`Match ${index+1}: Team A score cannot exceed 4 sets`);
                }
                if (scoreB > 4) {
                    markAsInvalid(scoreBInputs[index], `Match ${index+1}: Score cannot exceed 4 sets`);
                    errorMessages.push(`Match ${index+1}: Team B score cannot exceed 4 sets`);
                }
                isValid = false;
            }
            
            // Check if at least one team has won 3 sets (minimum for table tennis)
            if ((scoreA < 3 && scoreB < 3) || (scoreA === scoreB)) {
                markAsInvalid(input, `Match ${index+1}: One team must win at least 3 sets`);
                markAsInvalid(scoreBInputs[index], `Match ${index+1}: One team must win at least 3 sets`);
                errorMessages.push(`Match ${index+1}: One team must win at least 3 sets`);
                isValid = false;
            }
        });
        
        // Validate set points
        for (let set = 1; set <= 7; set++) {
            const setAInputs = document.querySelectorAll(`input[name="set${set}_team_a[]"]`);
            const setBInputs = document.querySelectorAll(`input[name="set${set}_team_b[]"]`);
            
            setAInputs.forEach((input, index) => {
                const pointsA = parseInt(input.value) || 0;
                const pointsB = parseInt(setBInputs[index].value) || 0;
                
                if (pointsA > 11 || pointsB > 11) {
                    if (pointsA > 11) {
                        markAsInvalid(input, `Set ${set}: Points cannot exceed 11`);
                        errorMessages.push(`Match ${index+1}, Set ${set}: Points cannot exceed 11`);
                    }
                    if (pointsB > 11) {
                        markAsInvalid(setBInputs[index], `Set ${set}: Points cannot exceed 11`);
                        errorMessages.push(`Match ${index+1}, Set ${set}: Points cannot exceed 11`);
                    }
                    isValid = false;
                }
                
                // Check if set has a valid winner (one team must have at least 11 points and lead by 2)
                if (pointsA > 0 || pointsB > 0) {
                    if (pointsA < 11 && pointsB < 11) {
                        markAsInvalid(input, `Set ${set}: One team must reach 11 points`);
                        markAsInvalid(setBInputs[index], `Set ${set}: One team must reach 11 points`);
                        errorMessages.push(`Match ${index+1}, Set ${set}: One team must reach 11 points`);
                        isValid = false;
                    } else if (Math.abs(pointsA - pointsB) < 2 && (pointsA > 10 || pointsB > 10)) {
                        markAsInvalid(input, `Set ${set}: Must win by at least 2 points after 10-10`);
                        markAsInvalid(setBInputs[index], `Set ${set}: Must win by at least 2 points after 10-10`);
                        errorMessages.push(`Match ${index+1}, Set ${set}: Must win by at least 2 points after 10-10`);
                        isValid = false;
                    }
                }
            });
        }
        
        if (!isValid) {
            showValidationErrors(errorMessages);
            return;
        }
        
        // If all validations pass, submit the form
        this.submit();
    });
    
    function markAsInvalid(input, message) {
        input.classList.add('is-invalid');
        
        // Create or update feedback element
        let feedback = input.nextElementSibling;
        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            input.parentNode.appendChild(feedback);
        }
        
        feedback.textContent = message;
    }
    
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
</script>
</body>
</html>