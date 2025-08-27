
<?php
session_start();
require '../include/db_connect.php';

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

$userid = $_SESSION['userid'];
$email = $_SESSION['email'];

// Check if user already registered for volleyball
$check = "SELECT * FROM volleyball_teams WHERE email = '$email'";
$exist = mysqli_query($con, $check);

if ($exist && mysqli_num_rows($exist) > 0) {
    $row = mysqli_fetch_assoc($exist);
    $approved = $row['is_approved'];
    if ($approved == "approved") {
        header("Location: ../user/dashboard.php");
        exit();
    } else if ($approved == "pending") {
       echo "<script>
            alert('Your request for Volleyball is not approved yet!');
            window.location.href='../user/join.php';
          </script>";
        exit();
    }
}

// Fetch user data if logged in
$userFullName = "";
$userEmail = "";
$isLoggedIn = false;

if (isset($_SESSION['userid'])) {
    $userId = $_SESSION['userid'];  // match the session key
    $userQuery = "SELECT fname, lname, email FROM users WHERE id = '$userId'";
    $userResult = mysqli_query($con, $userQuery);

    if ($userResult && mysqli_num_rows($userResult) > 0) {
        $userData = mysqli_fetch_assoc($userResult);
        $userFullName = $userData['fname'] . ' ' . $userData['lname'];  
        $userEmail = $userData['email'];
        $isLoggedIn = true;
    }
}

// Redirect if not logged in
if (!$isLoggedIn) {
    header("Location: ../login.php");
    exit();
}

// Handle form submission only when POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if already registered (on form submission)
    $check = "SELECT * FROM volleyball_teams WHERE email = '$email'";
    $result = mysqli_query($con, $check);

    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('⚠️ Already registered for Volleyball!'); window.location.href='../user/join.php';</script>";
        exit();
    }

    // Basic team information
    $fullName = $_POST['fullName'];
    $email = $_SESSION['email'];
    $team_name = mysqli_real_escape_string($con, $_POST['team_name']);
    $club_team = isset($_POST['club_team']) ? mysqli_real_escape_string($con, $_POST['club_team']) : '';
    
    // Captain information
    $captain_name = mysqli_real_escape_string($con, $_POST['captain_name']);
    $captain_age = (int)$_POST['captain_age'];
    $captain_height = (int)$_POST['captain_height'];
    $captain_handed = mysqli_real_escape_string($con, $_POST['captain_handed']);
    $captain_position = mysqli_real_escape_string($con, $_POST['captain_position']);
    $captain_standing_reach = !empty($_POST['captain_standing_reach']) ? (int)$_POST['captain_standing_reach'] : NULL;
    $captain_block_jump = !empty($_POST['captain_block_jump']) ? (int)$_POST['captain_block_jump'] : NULL;
    $captain_approach_jump = !empty($_POST['captain_approach_jump']) ? (int)$_POST['captain_approach_jump'] : NULL;
    
    // Captain health information
    $captain_chronic_illness = isset($_POST['captain_chronic_illness']) ? mysqli_real_escape_string($con, $_POST['captain_chronic_illness']) : '';
    $captain_allergies = isset($_POST['captain_allergies']) ? mysqli_real_escape_string($con, $_POST['captain_allergies']) : '';
    $captain_medications = isset($_POST['captain_medications']) ? mysqli_real_escape_string($con, $_POST['captain_medications']) : '';
    $captain_surgeries = isset($_POST['captain_surgeries']) ? mysqli_real_escape_string($con, $_POST['captain_surgeries']) : '';
    $captain_previous_injuries = isset($_POST['captain_previous_injuries']) ? mysqli_real_escape_string($con, $_POST['captain_previous_injuries']) : '';
    
    // Insert team data into database
    $team_sql = "INSERT INTO volleyball_teams 
                 (fullName, email, team_name, club_team, captain_name, captain_age, captain_height, 
                  captain_handed, captain_position, captain_standing_reach, 
                  captain_block_jump, captain_approach_jump, captain_chronic_illness, 
                  captain_allergies, captain_medications, captain_surgeries, captain_previous_injuries) 
                 VALUES 
                 ('$fullName', '$email', '$team_name', '$club_team', '$captain_name', '$captain_age', '$captain_height', 
                  '$captain_handed', '$captain_position', '$captain_standing_reach', 
                  '$captain_block_jump', '$captain_approach_jump', '$captain_chronic_illness', 
                  '$captain_allergies', '$captain_medications', '$captain_surgeries', '$captain_previous_injuries')";
    
    if ($con->query($team_sql)) {
        // Handle players' information
        $player_names = $_POST['player_name'];
        $player_ages = $_POST['player_age'];
        $player_positions = $_POST['player_position'];
        $player_heights = $_POST['player_height'];
        $player_handedness = $_POST['player_handedness'];
        $player_weights = $_POST['player_weight'];
        $player_standing_reaches = $_POST['player_standing_reach'];
        $player_block_jumps = $_POST['player_block_jump'];
        $player_approach_jumps = $_POST['player_approach_jump'];
        
        $chronic_illnesses = $_POST['player_chronic_illness'] ?? array_fill(0, count($player_names), '');
        $allergies = $_POST['player_allergies'] ?? array_fill(0, count($player_names), '');
        $medications = $_POST['player_medications'] ?? array_fill(0, count($player_names), '');
        $surgeries = $_POST['player_surgeries'] ?? array_fill(0, count($player_names), '');
        $previous_injuries = $_POST['player_previous_injuries'] ?? array_fill(0, count($player_names), '');

        for ($i = 0; $i < count($player_names); $i++) {
            $player_name = mysqli_real_escape_string($con, $player_names[$i]);
            $player_age = (int)$player_ages[$i];
            $player_position = mysqli_real_escape_string($con, $player_positions[$i]);
            $player_height = (int)$player_heights[$i];
            $handedness = mysqli_real_escape_string($con, $player_handedness[$i]);
            $player_weight = !empty($player_weights[$i]) ? (int)$player_weights[$i] : NULL;
            $standing_reach = !empty($player_standing_reaches[$i]) ? (int)$player_standing_reaches[$i] : NULL;
            $block_jump = !empty($player_block_jumps[$i]) ? (int)$player_block_jumps[$i] : NULL;
            $approach_jump = !empty($player_approach_jumps[$i]) ? (int)$player_approach_jumps[$i] : NULL;
            
            $chronic_illness = mysqli_real_escape_string($con, $chronic_illnesses[$i]);
            $allergy = mysqli_real_escape_string($con, $allergies[$i]);
            $medication = mysqli_real_escape_string($con, $medications[$i]);
            $surgery = mysqli_real_escape_string($con, $surgeries[$i]);
            $previous_injury = mysqli_real_escape_string($con, $previous_injuries[$i]);

            // Insert player with health information
            $player_sql = "INSERT INTO volleyball_players 
                           (team_name, player_name, age, position, height, handedness, weight, 
                            standing_reach, block_jump, approach_jump, chronic_illness, 
                            allergies, medications, surgeries, previous_injuries, email) 
                           VALUES 
                           ('$team_name', '$player_name', '$player_age', '$player_position', '$player_height', 
                            '$handedness', " . ($player_weight !== NULL ? "'$player_weight'" : "NULL") . ", 
                            " . ($standing_reach !== NULL ? "'$standing_reach'" : "NULL") . ", 
                            " . ($block_jump !== NULL ? "'$block_jump'" : "NULL") . ", 
                            " . ($approach_jump !== NULL ? "'$approach_jump'" : "NULL") . ", 
                            '$chronic_illness', '$allergy', '$medication', '$surgery', '$previous_injury', '$email')";

            if (!$con->query($player_sql)) {
                echo "❌ Error inserting player: " . $con->error . "<br>";
            }
        }
        
        // Redirect to success page
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
        exit();
    } else {
        echo "❌ Error: " . $con->error;
    }
}
?>
   

<!DOCTYPE html>
<html lang="en">
<head>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Volleyball Team Registration</title>
  <style>
    /* ===== Global Styling ===== */
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background-color: #f0f8ff;
      background-image: url(../images/Volleyballpage.jpg);
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
    }

    /* ===== Form Styling ===== */
    .form-container {
      width: 95%;
      max-width: 1000px;
      background-image: url(../images/Volleyballform.jpg);
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    .form-section h3 {
      margin-top: 0;
      margin-bottom: 15px;
      color: #b21f1f;
      font-size: 20px;
      padding-bottom: 8px;
      border-bottom: 1px solid #eee;
    }

    h1 {
      text-align: center;
      color: #fff;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
      margin-bottom: 25px;
      font-size: 32px;
    }
        form {
        display: flex;
        flex-direction: column;
        gap: 25px;
    }
    .form-section {
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 8px;
        background: #f9f9f9;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }
    .form-section h3 {
        margin-top: 0;
        margin-bottom: 15px;
        color: #b21f1f;
        font-size: 20px;
        padding-bottom: 8px;
        border-bottom: 1px solid #eee;
    }
    .form-group {
      margin-bottom: 20px;
    }

    .captain-group, .player-group {
      margin-bottom: 20px;
      padding: 15px; /* Increased padding */
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      background: #f5f5f5;
    }

    .captain-group h4, .player-group h4 {
      margin-top: 0;
      margin-bottom: 15px; /* Increased margin */
      color: #b21f1f;
      font-size: 22px; /* Increased font size */
      padding-bottom: 12px;
      border-bottom: 2px solid #ddd;
    }

    .player-group h5 {
      margin-top: 0;
      margin-bottom: 18px; /* Increased margin */
      color: #b21f1f;
      font-size: 20px; /* Increased font size */
      padding-bottom: 6px;
      border-bottom: 1px solid #ddd;
    }

        label {
        display: block;
        font-weight: bold;
        margin-bottom: 8px; /* Increased margin */
        color: #333;
        font-size: 16px; /* Slightly larger font */
    }
    input, select, textarea {
      width: 100%;
      padding: 14px; /* Increased padding */
      border: 2px solid #ddd;
      border-radius: 8px;
      background-color: rgba(255, 255, 255, 0.95);
      font-size: 16px;
      height: 46px; /* Slightly taller */
      box-sizing: border-box;
      transition: border-color 0.3s;
    }

    input:focus, select:focus, textarea:focus {
      border-color: #2d88d2;
      outline: none;
      box-shadow: 0 0 5px rgba(45, 136, 210, 0.3);
    }

    textarea {
      height: 80px;
      resize: vertical;
    }

    /* ===== Grid Layouts ===== */
    .grid-2 {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
    }

    .grid-3 {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
    }

    .grid-4 {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 18px; /* Slightly larger gap */
      margin-bottom: 15px;
    }

    .captain-health-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 18px; /* Slightly larger gap */
      margin-top: 18px; /* Increased margin */
      padding-top: 18px; /* Increased padding */
      border-top: 1px dashed #ddd;
    }

    .players-container {
      max-height: 600px;
      overflow-y: auto;
      padding: 15px; /* Increased padding */
      border: 1px solid #ddd;
      border-radius: 8px;
      background: #fff;
      margin-bottom: 15px;
    }
    .player-entry {
      border: 2px solid rgba(255,255,255,0.2);
      padding: 25px; /* Increased padding */
      border-radius: 10px;
      background: rgba(255,255,255,0.15);
      margin-bottom: 25px; /* Increased margin */
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .player-entry h4 {
      color: #228B22; /* Changed to green color */
      margin-top: 0;
      margin-bottom: 20px; /* Increased margin */
      padding-bottom: 8px;
      border-bottom: 2px solid rgba(255,255,255,0.2);
      font-size: 20px; /* Increased font size */
    }

    .player-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 18px; /* Slightly larger gap */
    }

    .jump-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 18px; /* Slightly larger gap */
      margin-top: 18px; /* Increased margin */
      padding-top: 18px; /* Increased padding */
    }

    .health-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 18px; /* Slightly larger gap */
      margin-top: 18px; /* Increased margin */
      padding-top: 18px; /* Increased padding */
      border-top: 1px solid rgba(255,255,255,0.2);
    }

    /* ===== Buttons ===== */
    .button-container {
      display: flex;
      gap: 18px; /* Slightly larger gap */
      margin-top: 20px;
    }

    .add-player-btn, .remove-player-btn {
      background-color: #2d88d2;
      color: white;
      border: none;
      border-radius: 8px;
      padding: 14px 22px; /* Increased padding */
      cursor: pointer;
      font-size: 16px;
      font-weight: bold;
      transition: background-color 0.3s;
      flex: 1;
    }

    .add-player-btn:hover {
      background-color: #1a6cb2;
    }

    .remove-player-btn {
      background-color: #d23d2d;
    }

    .remove-player-btn:hover {
      background-color: #b22a1a;
    }
    .health-grid div,
    .captain-health-grid div {
      margin-bottom: 18px; /* Increased margin */
    }

    .health-grid label,
    .captain-health-grid label {
      display: block;
      margin-bottom: 8px; /* Increased margin */
      font-weight: bold;
      color: #0e0d0dff;
      font-size: 16px; /* Slightly larger font */
    }

    .health-grid select,
    .health-grid input,
    .captain-health-grid select,
    .captain-health-grid input {
      width: 100%;
      padding: 10px; /* Increased padding */
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 15px; /* Slightly larger font */
    }
    .submit-btn {
      background: linear-gradient(135deg, #28a745, #218838);
      color: white;
      border: none;
      padding: 16px 28px; /* Increased padding */
      font-size: 18px;
      font-weight: bold;
      cursor: pointer;
      border-radius: 8px;
      margin-top: 25px; /* Increased margin */
      transition: transform 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      width: 100%;
    }

    .submit-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(0,0,0,0.15);
      background: linear-gradient(135deg, #218838, #1e7e34);
    }

    .back-btn {
      display: inline-block;
      padding: 14px 28px; /* Increased padding */
      margin-top: 25px; /* Increased margin */
      background: linear-gradient(135deg, #ff416c, #ff4b2b);
      color: white;
      font-size: 16px;
      font-weight: bold;
      border-radius: 8px;
      text-decoration: none;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      transition: 0.3s ease-in-out;
      text-align: center;
    }

    .back-btn:hover {
      background: linear-gradient(135deg, #ff4b2b, #ff416c);
      transform: translateY(-3px);
      box-shadow: 0 6px 12px rgba(0,0,0,0.3);
    }

    .required::after {
      content: " *";
      color: #ff4b2b;
    }

    .jump-info {
      font-size: 13px; /* Slightly larger font */
      color: #666;
      margin-top: 6px; /* Increased margin */
    }
    .health-cert-notice {
      background-color: #fff3cd;
      border: 1px solid #ffeaa7;
      border-radius: 5px;
      padding: 15px;
      margin: 20px 0;
      color: #856404;
      text-align: center;
      font-weight: bold;
    }
    .eligibility-link {
      text-align: center;
      margin-bottom: 15px;
      color:white;
    }
    
    .eligibility-link a {
      color: #ff6b6b;
      font-weight: bold;
      text-decoration: none;
    }
    
    .eligibility-link a:hover {
      text-decoration: underline;
    }

    /* ===== Responsive Design ===== */
    @media (max-width: 768px) {
      .grid-2, .grid-3, .grid-4, .player-grid, .jump-grid, .health-grid, .captain-health-grid {
        grid-template-columns: 1fr;
      }
      
      .button-container {
        flex-direction: column;
      }
      
      .form-container {
        padding: 20px;
        margin: 10px;
      }
      
      .captain-group, .player-group {
        padding: 15px;
      }
      
      .captain-group h4, .player-group h4 {
        font-size: 20px;
      }
      
      .player-group h5 {
        font-size: 18px;
      }
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h1>Volleyball Team Registration Form</h1>

    <div class="eligibility-link">
        <p>To view the detailed eligibility criteria <a href="volleyball.html" style="color: #ff6b6b; font-weight: bold;">click here</a></p>
    </div>
      
    <div class="health-cert-notice">
        Players must bring their health certificate to the venue, otherwise they will be rejected.
    </div>
    
    <form method="POST" id="volleyballForm">
      <!-- Team Information -->
      <div class="form-section">
        <h3 class="section-title">Team Information</h3>
        <div class="grid-2">
         <div>
    <label for="fullName" class="required">Full Name:</label>
    <input type="text" id="fullName" name="fullName"  
           value="<?php echo htmlspecialchars($userFullName); ?>" 
           required readonly>
</div>
<div>
    <label for="email" class="required">Email Address:</label>
    <input type="email" id="email" name="email"  
           value="<?php echo htmlspecialchars($userEmail); ?>" 
           required readonly>
</div>
          <div class="form-group">
            <label for="team_name" class="required">Team Name:</label>
            <input type="text" id="team_name" name="team_name" required>
          </div>
          <div class="form-group">
            <label for="club_team">Club Team (if applicable):</label>
            <input type="text" id="club_team" name="club_team">
          </div>
        </div>
      </div>
      
      <!-- Captain Information -->
      <div class="form-section">
        <h3 class="section-title">Captain Information</h3>
        
        <!-- Basic Info Group -->
        <div class="captain-group">
          <h4>Basic Information</h4>
          <div class="grid-4">
            <div>
              <label for="captain_name" class="required">Captain Name:</label>
              <input type="text" id="captain_name" name="captain_name" required>
            </div>
            <div>
              <label for="captain_age" class="required">Captain Age:</label>
              <input type="number" id="captain_age" name="captain_age" min="16" max="23" required>
            </div>
            <div>
              <label for="captain_height" class="required">Captain Height (cm):</label>
              <input type="number" id="captain_height" name="captain_height" min="150" max="220" required>
            </div>
            <div>
              <label for="captain_handed">Captain Handedness:</label>
              <select id="captain_handed" name="captain_handed">
                <option value="right">Right Handed</option>
                <option value="left">Left Handed</option>
              </select>
            </div>
          </div>
        </div>
        
        <!-- Position and Jump Info Group -->
        <div class="captain-group">
          <h4>Position & Jump Information</h4>
          <div class="grid-4">
            <div>
              <label for="captain_position" class="required">Captain Primary Position:</label>
              <select id="captain_position" name="captain_position" required onchange="updateJumpRanges('captain', this.value)">
                <option value="">Select Position</option>
                <option value="outside">Outside Hitter (OH)</option>
                <option value="opposite">Opposite Hitter (OP)</option>
                <option value="middle">Middle Blocker (MB)</option>
                <option value="setter">Setter (S)</option>
                <option value="libero">Libero (L)</option>
              </select>
            </div>
            <div>
              <label for="captain-standing-reach">Standing Reach (cm):</label>
              <input type="number" id="captain-standing-reach" name="captain_standing_reach" min="200" max="280">
              <div class="jump-info" id="captain-standing-info">Select position first</div>
            </div>
            <div>
              <label for="captain-block-jump">Block Jump (cm):</label>
              <input type="number" id="captain-block-jump" name="captain_block_jump" min="250" max="350">
              <div class="jump-info" id="captain-block-info">Select position first</div>
            </div>
            <div>
              <label for="captain-approach-jump">Approach Jump (cm):</label>
              <input type="number" id="captain-approach-jump" name="captain_approach_jump" min="270" max="380">
              <div class="jump-info" id="captain-approach-info">Select position first</div>
            </div>
          </div>
        </div>
        
        <!-- Health Information Group -->
        <div class="captain-group">
          <h4>Health Information</h4>
          <div class="grid-4">
            <!-- Chronic Illness -->
            <div>
              <label>Chronic Illness:</label>
              <select name="captain_chronic_illness_option" class="illness-option" onchange="toggleCaptainIllnessField(this)">
                <option value="select">select</option>
                <option value="yes">Yes</option>
                <option value="no">No</option>
              </select>
              <div class="illness-details" style="display: none; margin-top: 8px;">
                <input type="text" name="captain_chronic_illness" placeholder="Type of chronic illness" 
                  oninput="validateAlphabets(this)" class="illness-input">
              </div>
            </div>

            <!-- Allergies -->
            <div>
              <label>Allergies:</label>
              <input type="text" name="captain_allergies" placeholder="Allergies (if any)" 
                oninput="validateAlphabets(this)">
            </div>

            <!-- Current Medications -->
            <div>
              <label>Current Medications:</label>
              <select name="captain_medications_option" class="medication-option" onchange="toggleCaptainMedicationField(this)">
                <option value="select">select</option>
                <option value="yes">Yes</option>
                <option value="no">No</option>
              </select>
              <div class="medication-details" style="display: none; margin-top: 8px;">
                <input type="text" name="captain_medications" placeholder="Type of medication" 
                  oninput="validateAlphabets(this)" class="medication-input">
              </div>
            </div>

            <!-- Recent Surgeries -->
            <div>
              <label>Recent Surgeries:</label>
              <input type="text" name="captain_surgeries" placeholder="Type of surgery" 
                oninput="validateAlphabets(this)">
            </div>
          </div>
          
          <!-- Previous Injuries (full width) -->
          <div style="grid-column: 1 / -1; margin-top: 15px;">
            <label>Previous Injuries:</label>
            <input type="text" name="captain_previous_injuries" placeholder="Type of injury" 
              oninput="validateAlphabets(this)">
          </div>
        </div>
      </div>
      
      <!-- Players Information -->
      <div class="form-section">
        <h3 class="section-title">Players Information</h3>
        <p style="color: Black; text-align: center; margin-bottom: 15px;"><strong>Add Team Players (Minimum 5, Maximum 12)</strong></p>
        
        <div class="players-container" id="playersContainer">
          <!-- Player entries will be added here dynamically -->
        </div>

        <div class="button-container">
          <button type="button" class="add-player-btn" onclick="addPlayerField()">+ Add Player</button>
          <button type="button" class="remove-player-btn" onclick="removePlayerField()">- Remove Player</button>
        </div>
      </div>
      
      <button type="submit" class="submit-btn">Submit Registration</button>
    </form>
    
    <a href="../user/get_event.php" class="back-btn">⬅ Back to Dashboard</a>
  </div>

 <script>
    // Jump range data based on position
    const positionJumpRanges = {
      'outside': {
        standing: { min: 220, max: 240 },
        block: { min: 300, max: 330 },
        approach: { min: 320, max: 350 }
      },
      'opposite': {
        standing: { min: 225, max: 245 },
        block: { min: 310, max: 335 },
        approach: { min: 325, max: 355 }
      },
      'middle': {
        standing: { min: 230, max: 250 },
        block: { min: 320, max: 345 },
        approach: { min: 335, max: 360 }
      },
      'setter': {
        standing: { min: 215, max: 235 },
        block: { min: 290, max: 315 },
        approach: { min: 305, max: 330 }
      },
      'libero': {
        standing: { min: 210, max: 230 },
        block: { min: 280, max: 300 },
        approach: { min: 295, max: 315 }
      }
    };

    // Function to update jump ranges based on position
    function updateJumpRanges(playerType, position) {
      if (!position) return;
      
      const ranges = positionJumpRanges[position];
      if (!ranges) return;
      
      // Update the input fields with the average values
      document.getElementById(`${playerType}-standing-reach`).value = 
        Math.round((ranges.standing.min + ranges.standing.max) / 2);
      document.getElementById(`${playerType}-block-jump`).value = 
        Math.round((ranges.block.min + ranges.block.max) / 2);
      document.getElementById(`${playerType}-approach-jump`).value = 
        Math.round((ranges.approach.min + ranges.approach.max) / 2);
      
      // Update the info text
      document.getElementById(`${playerType}-standing-info`).textContent = 
        `Range: ${ranges.standing.min}-${ranges.standing.max} cm`;
      document.getElementById(`${playerType}-block-info`).textContent = 
        `Range: ${ranges.block.min}-${ranges.block.max} cm`;
      document.getElementById(`${playerType}-approach-info`).textContent = 
        `Range: ${ranges.approach.min}-${ranges.approach.max} cm`;
    }

    // Function to update player jump ranges
    function updatePlayerJumpRanges(selectElement) {
      const position = selectElement.value;
      const playerEntry = selectElement.closest('.player-entry');
      
      if (!position) return;
      
      const ranges = positionJumpRanges[position];
      if (!ranges) return;
      
      // Update the input fields with the average values
      playerEntry.querySelector('.standing-reach-input').value = 
        Math.round((ranges.standing.min + ranges.standing.max) / 2);
      playerEntry.querySelector('.block-jump-input').value = 
        Math.round((ranges.block.min + ranges.block.max) / 2);
      playerEntry.querySelector('.approach-jump-input').value = 
        Math.round((ranges.approach.min + ranges.approach.max) / 2);
      
      // Update the info text
      playerEntry.querySelector('.standing-info').textContent = 
        `Range: ${ranges.standing.min}-${ranges.standing.max} cm`;
      playerEntry.querySelector('.block-info').textContent = 
        `Range: ${ranges.block.min}-${ranges.block.max} cm`;
      playerEntry.querySelector('.approach-info').textContent = 
        `Range: ${ranges.approach.min}-${ranges.approach.max} cm`;
    }

    // Function to toggle captain chronic illness field
    function toggleCaptainIllnessField(select) {
      const detailsDiv = select.parentElement.querySelector('.illness-details');
      const inputField = select.parentElement.querySelector('.illness-input');
      
      if (select.value === 'yes') {
        detailsDiv.style.display = 'block';
        inputField.setAttribute('required', 'required');
      } else {
        detailsDiv.style.display = 'none';
        inputField.removeAttribute('required');
        inputField.value = '';
      }
    }

    // Function to toggle captain medication field
    function toggleCaptainMedicationField(select) {
      const detailsDiv = select.parentElement.querySelector('.medication-details');
      const inputField = select.parentElement.querySelector('.medication-input');
      
      if (select.value === 'yes') {
        detailsDiv.style.display = 'block';
        inputField.setAttribute('required', 'required');
      } else {
        detailsDiv.style.display = 'none';
        inputField.removeAttribute('required');
        inputField.value = '';
      }
    }

    let playerCount = 0;

    function addPlayerField() {
      if (playerCount >= 12) {
        alert("Maximum 12 players allowed!");
        return;
      }

      const playersContainer = document.getElementById("playersContainer");
      const playerDiv = document.createElement("div");
      playerDiv.className = "player-entry";
      playerDiv.innerHTML = `
        <h4>Player ${playerCount + 1}</h4>
        
        <!-- Basic Information Group -->
        <div class="player-group">
          <h5>Basic Information</h5>
          <div class="grid-4">
            <div>
              <input type="text" name="player_name[]" placeholder="Player Name*" required>
            </div>
            <div>
              <input type="number" name="player_age[]" placeholder="Age*" min="16" max="23" required>
            </div>
            <div>
              <input type="number" name="player_height[]" placeholder="Height (cm)*" min="150" max="220" required>
            </div>
            <div>
              <select name="player_handedness[]" required>
                <option value="">Handedness*</option>
                <option value="right">Right</option>
                <option value="left">Left</option>
              </select>
            </div>
          </div>
          <div class="grid-2">
            <div>
              <input type="number" name="player_weight[]" placeholder="Weight (kg)*" min="40" max="120" required>
            </div>
            <div>
              <select name="player_position[]" required onchange="updatePlayerJumpRanges(this)">
                <option value="">Position*</option>
                <option value="outside">Outside Hitter</option>
                <option value="opposite">Opposite</option>
                <option value="middle">Middle Blocker</option>
                <option value="setter">Setter</option>
                <option value="libero">Libero</option>
              </select>
            </div>
          </div>
        </div>
        
        <!-- Position and Jump Information Group -->
        <div class="player-group">
          <h5>Jump Information</h5>
          <div class="jump-grid">
            <div>
              <label>Standing Reach (cm):</label>
              <input type="number" name="player_standing_reach[]" class="standing-reach-input" min="200" max="280">
              <div class="jump-info standing-info">Select position first</div>
            </div>
            <div>
              <label>Block Jump (cm):</label>
              <input type="number" name="player_block_jump[]" class="block-jump-input" min="250" max="350">
              <div class="jump-info block-info">Select position first</div>
            </div>
            <div>
              <label>Approach Jump (cm):</label>
              <input type="number" name="player_approach_jump[]" class="approach-jump-input" min="270" max="380">
              <div class="jump-info approach-info">Select position first</div>
            </div>
          </div>
        </div>
        
        <!-- Health Information Group -->
        <div class="player-group">
          <h5>Health Information</h5>
          <div class="grid-4">
            <!-- Chronic Illness -->
            <div>
              <label>Chronic Illness:</label>
              <select name="player_chronic_illness_option[]" class="illness-option" onchange="toggleIllnessField(this)">
                <option value="select">select</option>
                <option value="yes">Yes</option>
                <option value="no">No</option>
              </select>
              <div class="illness-details" style="display: none; margin-top: 8px;">
                <input type="text" name="player_chronic_illness[]" placeholder="Type of chronic illness" 
                  oninput="validateAlphabets(this)" class="illness-input">
              </div>
            </div>

            <!-- Allergies -->
            <div>
              <label>Allergies:</label>
                <input type="text" name="player_allergies[]" placeholder="Allergies (if any)" 
                  oninput="validateAlphabets(this)">
            </div>

            <!-- Current Medications -->
            <div>
              <label>Current Medications:</label>
              <select name="player_medications_option[]" class="medication-option" onchange="toggleMedicationField(this)">
                <option value="select">select</option>
                <option value="yes">Yes</option>
                <option value="no">No</option>
              </select>
              <div class="medication-details" style="display: none; margin-top: 8px;">
                <input type="text" name="player_medications[]" placeholder="Type of medication" 
                  oninput="validateAlphabets(this)" class="medication-input">
              </div>
            </div>

            <!-- Recent Surgeries -->
            <div>
              <label>Recent Surgeries:</label>
              <input type="text" name="player_surgeries[]" placeholder="Type of surgery" 
                oninput="validateAlphabets(this)">
            </div>
          </div>
          
          <!-- Previous Injuries (full width) -->
          <div style="grid-column: 1 / -1; margin-top: 15px;">
            <label>Previous Injuries:</label>
            <input type="text" name="player_previous_injuries[]" placeholder="Type of injury" 
              oninput="validateAlphabets(this)">
          </div>
        </div>
      `;
      playersContainer.appendChild(playerDiv);
      playerCount++;
      
      // Initialize the new player's health dropdowns
      const newIllnessOption = playerDiv.querySelector('.illness-option');
      const newMedicationOption = playerDiv.querySelector('.medication-option');
      toggleIllnessField(newIllnessOption);
      toggleMedicationField(newMedicationOption);
    }

    function removePlayerField() {
      if (playerCount <= 5) {
        alert("At least 5 players are required.");
        return;
      }
      
      const playersContainer = document.getElementById("playersContainer");
      if (playersContainer.lastElementChild) {
        playersContainer.removeChild(playersContainer.lastElementChild);
        playerCount--;
      }
    }
    
    // Function to validate only alphabets and spaces
    function validateAlphabets(input) {
        input.value = input.value.replace(/[^a-zA-Z\s]/g, '');
    }

    // Function to validate numeric inputs with range
    function validateNumber(input, min, max) {
        const value = parseInt(input.value);
        if (isNaN(value) || value < min || value > max) {
            alert(`Please enter a value between ${min} and ${max}`);
            input.value = '';
            input.focus();
            return false;
        }
        return true;
    }

    // Function to toggle chronic illness field
    function toggleIllnessField(select) {
        const detailsDiv = select.parentElement.querySelector('.illness-details');
        const inputField = select.parentElement.querySelector('.illness-input');
        
        if (select.value === 'yes') {
            detailsDiv.style.display = 'block';
            inputField.setAttribute('required', 'required');
        } else {
            detailsDiv.style.display = 'none';
            inputField.removeAttribute('required');
            inputField.value = '';
        }
    }

    // Function to toggle medication field
    function toggleMedicationField(select) {
        const detailsDiv = select.parentElement.querySelector('.medication-details');
        const inputField = select.parentElement.querySelector('.medication-input');
        
        if (select.value === 'yes') {
            detailsDiv.style.display = 'block';
            inputField.setAttribute('required', 'required');
        } else {
            detailsDiv.style.display = 'none';
            inputField.removeAttribute('required');
            inputField.value = '';
        }
    }

    // Initialize all dropdowns on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize captain health dropdowns
        const captainIllnessOption = document.querySelector('select[name="captain_chronic_illness_option"]');
        const captainMedicationOption = document.querySelector('select[name="captain_medications_option"]');
        
        if (captainIllnessOption) toggleCaptainIllnessField(captainIllnessOption);
        if (captainMedicationOption) toggleCaptainMedicationField(captainMedicationOption);
        
        // Initialize player health dropdowns
        document.querySelectorAll('.illness-option').forEach(select => {
            toggleIllnessField(select);
        });
        
        document.querySelectorAll('.medication-option').forEach(select => {
            toggleMedicationField(select);
        });
        
        // Add input validation for numeric fields
        document.getElementById('captain_height').addEventListener('blur', function() {
            validateNumber(this, 150, 220);
        });
        
        document.getElementById('captain-standing-reach').addEventListener('blur', function() {
            validateNumber(this, 200, 280);
        });
        
        document.getElementById('captain-block-jump').addEventListener('blur', function() {
            validateNumber(this, 250, 350);
        });
        
        document.getElementById('captain-approach-jump').addEventListener('blur', function() {
            validateNumber(this, 270, 380);
        });
    });
    
    // Form Validation
    document.getElementById("volleyballForm").addEventListener("submit", function(event){
      // Check at least 5 players (changed from 6 to 5)
      if (playerCount < 5) {
        alert("Minimum 5 players required! You have only added " + playerCount + " players.");
        event.preventDefault();
        return;
      }

      // Validate player names (no numbers)
      const nameInputs = document.querySelectorAll('input[name="player_name[]"]');
      for (let nameInput of nameInputs) {
        const nameValue = nameInput.value.trim();
        if (/\d/.test(nameValue)) {
          alert("Player names cannot contain numbers. Please check: " + nameValue);
          nameInput.focus();
          event.preventDefault();
          return;
        }
      }

      // Validate ages
      const ageInputs = document.querySelectorAll('input[name="player_age[]"]');
      for (let ageInput of ageInputs) {
        const age = parseInt(ageInput.value);
        if (isNaN(age) || age < 16 || age > 23) {
          alert("Each player's age must be between 16 and 23 years.");
          ageInput.focus();
          event.preventDefault();
          return;
        }
      }
      
      // Validate heights
      const heightInputs = document.querySelectorAll('input[name="player_height[]"]');
      for (let heightInput of heightInputs) {
        const height = parseInt(heightInput.value);
        if (isNaN(height) || height < 150 || height > 220) {
          alert("Each player's height must be between 150 and 220 cm.");
          heightInput.focus();
          event.preventDefault();
          return;
        }
      }
    // Enhanced validation functions
    function validateJumpRange(input, min, max, fieldName) {
        const value = parseInt(input.value);
        if (isNaN(value) || value < min || value > max) {
            alert(`${fieldName} must be between ${min} and ${max} cm`);
            input.value = '';
            input.focus();
            return false;
        }
        return true;
    }

    // Real-time validation for jump inputs
    function setupJumpValidation() {
        // Captain validations
        const captainStandingReach = document.getElementById('captain-standing-reach');
        const captainBlockJump = document.getElementById('captain-block-jump');
        const captainApproachJump = document.getElementById('captain-approach-jump');
        
        if (captainStandingReach) {
            captainStandingReach.addEventListener('blur', function() {
                validateJumpRange(this, 200, 280, 'Standing Reach');
            });
        }
        
        if (captainBlockJump) {
            captainBlockJump.addEventListener('blur', function() {
                validateJumpRange(this, 250, 350, 'Block Jump');
            });
        }
        
        if (captainApproachJump) {
            captainApproachJump.addEventListener('blur', function() {
                validateJumpRange(this, 270, 380, 'Approach Jump');
            });
        }
        
        // Setup validation for player jump inputs (will be added for each player)
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('standing-reach-input')) {
                validateJumpRange(e.target, 200, 280, 'Standing Reach');
            } else if (e.target.classList.contains('block-jump-input')) {
                validateJumpRange(e.target, 250, 350, 'Block Jump');
            } else if (e.target.classList.contains('approach-jump-input')) {
                validateJumpRange(e.target, 270, 380, 'Approach Jump');
            }
        });
    }

    // Enhanced form validation
    document.getElementById("volleyballForm").addEventListener("submit", function(event){
        // Check at least 5 players
        if (playerCount < 5) {
            alert("Minimum 5 players required! You have only added " + playerCount + " players.");
            event.preventDefault();
            return;
        }

        // Validate captain jump ranges
        const captainStandingReach = document.getElementById('captain-standing-reach');
        const captainBlockJump = document.getElementById('captain-block-jump');
        const captainApproachJump = document.getElementById('captain-approach-jump');
        
        if (captainStandingReach && !validateJumpRange(captainStandingReach, 200, 280, 'Captain Standing Reach')) {
            event.preventDefault();
            return;
        }
        
        if (captainBlockJump && !validateJumpRange(captainBlockJump, 250, 350, 'Captain Block Jump')) {
            event.preventDefault();
            return;
        }
        
        if (captainApproachJump && !validateJumpRange(captainApproachJump, 270, 380, 'Captain Approach Jump')) {
            event.preventDefault();
            return;
        }

        // Validate player jump ranges
        const standingReachInputs = document.querySelectorAll('.standing-reach-input');
        const blockJumpInputs = document.querySelectorAll('.block-jump-input');
        const approachJumpInputs = document.querySelectorAll('.approach-jump-input');
        
        for (let input of standingReachInputs) {
            if (input.value && !validateJumpRange(input, 200, 280, 'Standing Reach')) {
                event.preventDefault();
                return;
            }
        }
        
        for (let input of blockJumpInputs) {
            if (input.value && !validateJumpRange(input, 250, 350, 'Block Jump')) {
                event.preventDefault();
                return;
            }
        }
        
        for (let input of approachJumpInputs) {
            if (input.value && !validateJumpRange(input, 270, 380, 'Approach Jump')) {
                event.preventDefault();
                return;
            }
        }

        // Validate player names (no numbers)
        const nameInputs = document.querySelectorAll('input[name="player_name[]"]');
        for (let nameInput of nameInputs) {
            const nameValue = nameInput.value.trim();
            if (/\d/.test(nameValue)) {
                alert("Player names cannot contain numbers. Please check: " + nameValue);
                nameInput.focus();
                event.preventDefault();
                return;
            }
        }

        // Validate ages
        const ageInputs = document.querySelectorAll('input[name="player_age[]"]');
        for (let ageInput of ageInputs) {
            const age = parseInt(ageInput.value);
            if (isNaN(age) || age < 16 || age > 23) {
                alert("Each player's age must be between 16 and 23 years.");
                ageInput.focus();
                event.preventDefault();
                return;
            }
        }
        
        // Validate heights
        const heightInputs = document.querySelectorAll('input[name="player_height[]"]');
        for (let heightInput of heightInputs) {
            const height = parseInt(heightInput.value);
            if (isNaN(height) || height < 150 || height > 220) {
                alert("Each player's height must be between 150 and 220 cm.");
                heightInput.focus();
                event.preventDefault();
                return;
            }
        }
        
        alert("Team Registration Successful!");
    });

    // Initialize validation on page load
    window.onload = function() {
        for (let i = 0; i < 1; i++) {
            addPlayerField();
        }
        setupJumpValidation();
    };
      
      alert("Team Registration Successful!");
    });

    // Add 1 player fields on page load
    window.onload = function() {
      for (let i = 0; i < 1; i++) {
        addPlayerField();
      }
    };
  </script>
</body>
</html>