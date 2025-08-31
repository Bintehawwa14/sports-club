<?php
session_start();
require '../include/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: ../login.php");
    exit();
}

// Validate event_id and event_name from URL
if (!isset($_GET['event_id']) || !isset($_GET['event_name'])) {
    echo "<script>alert('Invalid Request! Please select an event.'); window.location.href='../user/get_event.php';</script>";
    exit;
}

$event_id = (int)$_GET['event_id'];
$event_name = mysqli_real_escape_string($con, $_GET['event_name']);

// Validate event exists and is active
$eventQuery = "SELECT event_name FROM events WHERE id = ? AND event_name = ? AND status = 'active' AND is_closed = 'no'";
$stmt = $con->prepare($eventQuery);
$stmt->bind_param("is", $event_id, $event_name);
$stmt->execute();
$eventResult = $stmt->get_result();
if (!$eventResult || $eventResult->num_rows == 0) {
    echo "<script>alert('Invalid or inactive event. Please select a valid event.'); window.location.href='../user/get_event.php';</script>";
    exit;
}
$stmt->close();

// Display success message if redirected from successful registration
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Success!',
                text: 'Registration for " . htmlspecialchars($event_name) . " successful!',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        });
    </script>";
}

$userid = $_SESSION['userid'];
$email = $_SESSION['email'];

// Check if user already registered for this event
$check = "SELECT * FROM volleyball_teams WHERE email = ? AND event_name = ?";
$stmt = $con->prepare($check);
$stmt->bind_param("ss", $email, $event_name);
$stmt->execute();
$exist = $stmt->get_result();

if ($exist && $exist->num_rows > 0) {
    $row = $exist->fetch_assoc();
    $approved = $row['is_approved'];
    if ($approved == "approved") {
        header("Location: ../user/dashboard.php");
        exit();
    } else if ($approved == "pending") {
        echo "<script>
            alert('Your request for " . htmlspecialchars($event_name) . " is not approved yet!');
            window.location.href='../user/join.php?event_id=$event_id&event_name=" . urlencode($event_name) . "';
          </script>";
        exit();
    }
}
$stmt->close();

// Fetch user data if logged in
$userFullName = "";
$userEmail = "";
$isLoggedIn = false;

if (isset($_SESSION['userid'])) {
    $userId = $_SESSION['userid'];
    $userQuery = "SELECT fname, lname, email FROM users WHERE id = ?";
    $stmt = $con->prepare($userQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $userResult = $stmt->get_result();

    if ($userResult && $userResult->num_rows > 0) {
        $userData = $userResult->fetch_assoc();
        $userFullName = $userData['fname'] . ' ' . $userData['lname'];
        $userEmail = $userData['email'];
        $isLoggedIn = true;
    }
    $stmt->close();
}

// Redirect if not logged in
if (!$isLoggedIn) {
    header("Location: ../login.php");
    exit();
}

// Handle form submission only when POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if already registered (on form submission)
    $check = "SELECT * FROM volleyball_teams WHERE email = ? AND event_name = ?";
    $stmt = $con->prepare($check);
    $stmt->bind_param("ss", $email, $event_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('⚠️ Already registered for " . htmlspecialchars($event_name) . "!'); window.location.href='../user/join.php?event_id=$event_id&event_name=" . urlencode($event_name) . "';</script>";
        exit();
    }
    $stmt->close();

    // Basic team information
    $fullName = mysqli_real_escape_string($con, $_POST['fullName']);
    $email = mysqli_real_escape_string($con, $_SESSION['email']);
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

    // Prepare the INSERT statement for team
    $stmt = $con->prepare("INSERT INTO volleyball_teams 
        (fullName, email, event_name, team_name, club_team, captain_name, captain_age, captain_height, 
         captain_handed, captain_position, captain_standing_reach, captain_block_jump, captain_approach_jump, 
         captain_chronic_illness, captain_allergies, captain_medications, captain_surgeries, captain_previous_injuries) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param(
        "ssssssisssiiisssss",
        $fullName, $email, $event_name, $team_name, $club_team, $captain_name, $captain_age, $captain_height,
        $captain_handed, $captain_position, $captain_standing_reach, $captain_block_jump, $captain_approach_jump,
        $captain_chronic_illness, $captain_allergies, $captain_medications, $captain_surgeries, $captain_previous_injuries
    );

    if ($stmt->execute()) {
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

    // Prepare the INSERT statement for players
    $player_stmt = $con->prepare("INSERT INTO volleyball_players 
        (team_name, player_name, age, position, height, handedness, weight, 
         standing_reach, block_jump, approach_jump, chronic_illness, 
         allergies, medications, surgeries, previous_injuries, email, event_name) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $player_stmt->bind_param(
        "ssisisiisssssssss",
        $team_name, $player_name, $player_age, $player_position, $player_height, 
        $handedness, $player_weight, $standing_reach, $block_jump, $approach_jump,
        $chronic_illness, $allergy, $medication, $surgery, $previous_injury, $email, $event_name
    );

    if (!$player_stmt->execute()) {
        echo "<script>alert('Error inserting player: " . addslashes($player_stmt->error) . "');</script>";
    }
    $player_stmt->close();
}

        // Redirect to success page
        header("Location: " . $_SERVER['PHP_SELF'] . "?event_id=$event_id&event_name=" . urlencode($event_name) . "&success=1");
        exit();
    } else {
        echo "<script>alert('Error: " . addslashes($stmt->error) . "');</script>";
    }
    $stmt->close();
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
      padding: 15px;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      background: #f5f5f5;
    }

    .captain-group h4, .player-group h4 {
      margin-top: 0;
      margin-bottom: 15px;
      color: #b21f1f;
      font-size: 22px;
      padding-bottom: 12px;
      border-bottom: 2px solid #ddd;
    }

    .player-group h5 {
      margin-top: 0;
      margin-bottom: 18px;
      color: #b21f1f;
      font-size: 20px;
      padding-bottom: 6px;
      border-bottom: 1px solid #ddd;
    }

    label {
      display: block;
      font-weight: bold;
      margin-bottom: 8px;
      color: #333;
      font-size: 16px;
    }
    input, select, textarea {
      width: 100%;
      padding: 14px;
      border: 2px solid #ddd;
      border-radius: 8px;
      background-color: rgba(255, 255, 255, 0.95);
      font-size: 16px;
      height: 46px;
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
      gap: 18px;
      margin-bottom: 15px;
    }

    .captain-health-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 18px;
      margin-top: 18px;
      padding-top: 18px;
      border-top: 1px dashed #ddd;
    }

    .players-container {
      max-height: 600px;
      overflow-y: auto;
      padding: 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
      background: #fff;
      margin-bottom: 15px;
    }
    .player-entry {
      border: 2px solid rgba(255,255,255,0.2);
      padding: 25px;
      border-radius: 10px;
      background: rgba(255,255,255,0.15);
      margin-bottom: 25px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .player-entry h4 {
      color: #228B22;
      margin-top: 0;
      margin-bottom: 20px;
      padding-bottom: 8px;
      border-bottom: 2px solid rgba(255,255,255,0.2);
      font-size: 20px;
    }

    .player-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 18px;
    }

    .jump-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 18px;
      margin-top: 18px;
      padding-top: 18px;
    }

    .health-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 18px;
      margin-top: 18px;
      padding-top: 18px;
      border-top: 1px solid rgba(255,255,255,0.2);
    }

    /* ===== Validation Styling ===== */
    .error-message {
      color: #d93025;
      font-size: 14px;
      margin-top: 5px;
      min-height: 20px;
    }

    .input-error {
      border: 2px solid #d93025 !important;
    }

    .input-success {
      border: 2px solid #30b55c !important;
    }

    .validation-success {
      color: #30b55c;
      font-size: 14px;
      margin-top: 5px;
    }

    /* ===== Buttons ===== */
    .button-container {
      display: flex;
      gap: 18px;
      margin-top: 20px;
    }

    .add-player-btn, .remove-player-btn {
      background-color: #2d88d2;
      color: white;
      border: none;
      border-radius: 8px;
      padding: 14px 22px;
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

    .submit-btn {
      background: linear-gradient(135deg, #28a745, #218838);
      color: white;
      border: none;
      padding: 16px 28px;
      font-size: 18px;
      font-weight: bold;
      cursor: pointer;
      border-radius: 8px;
      margin-top: 25px;
      transition: transform 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      width: 100%;
    }

    .submit-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(0,0,0,0.15);
      background: linear-gradient(135deg, #218838, #1e7e34);
    }

    .submit-btn:disabled {
      background: #cccccc;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    .back-btn {
      display: inline-block;
      padding: 14px 28px;
      margin-top: 25px;
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
      font-size: 13px;
      color: #666;
      margin-top: 6px;
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
    <h1>Volleyball Team Registration for <?php echo htmlspecialchars($event_name); ?></h1>

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
            <input type="text" id="team_name" name="team_name" required 
                   oninput="validateTeamName(this)">
            <div id="team_nameError" class="error-message"></div>
          </div>
          <div class="form-group">
            <label for="club_team">Club Team (if applicable):</label>
            <input type="text" id="club_team" name="club_team" 
                   oninput="validateClubTeam(this)">
            <div id="club_teamError" class="error-message"></div>
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
              <input type="text" id="captain_name" name="captain_name" required 
                     oninput="validateCaptainName(this)">
              <div id="captain_nameError" class="error-message"></div>
            </div>
            <div>
              <label for="captain_age" class="required">Captain Age:</label>
              <input type="number" id="captain_age" name="captain_age" min="16" max="23" required 
                     oninput="validateCaptainAge(this)">
              <div id="captain_ageError" class="error-message"></div>
            </div>
            <div>
              <label for="captain_height" class="required">Captain Height (cm):</label>
              <input type="number" id="captain_height" name="captain_height" min="150" max="220" required 
                     oninput="validateCaptainHeight(this)">
              <div id="captain_heightError" class="error-message"></div>
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
              <div id="captain_positionError" class="error-message"></div>
            </div>
            <div>
              <label for="captain-standing-reach">Standing Reach (cm):</label>
              <input type="number" id="captain-standing-reach" name="captain_standing_reach" min="200" max="280" 
                     oninput="validateCaptainStandingReach(this)">
              <div class="jump-info" id="captain-standing-info">Select position first</div>
              <div id="captain_standing_reachError" class="error-message"></div>
            </div>
            <div>
              <label for="captain-block-jump">Block Jump (cm):</label>
              <input type="number" id="captain-block-jump" name="captain_block_jump" min="250" max="350" 
                     oninput="validateCaptainBlockJump(this)">
              <div class="jump-info" id="captain-block-info">Select position first</div>
              <div id="captain_block_jumpError" class="error-message"></div>
            </div>
            <div>
              <label for="captain-approach-jump">Approach Jump (cm):</label>
              <input type="number" id="captain-approach-jump" name="captain_approach_jump" min="270" max="380" 
                     oninput="validateCaptainApproachJump(this)">
              <div class="jump-info" id="captain-approach-info">Select position first</div>
              <div id="captain_approach_jumpError" class="error-message"></div>
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

      <button type="submit" class="submit-btn" id="submitBtn">Submit Registration</button>
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

      // Update the info text
      document.getElementById(`${playerType}-standing-info`).textContent = 
        `Range: ${ranges.standing.min}-${ranges.standing.max} cm`;
      document.getElementById(`${playerType}-block-info`).textContent = 
        `Range: ${ranges.block.min}-${ranges.block.max} cm`;
      document.getElementById(`${playerType}-approach-info`).textContent = 
        `Range: ${ranges.approach.min}-${ranges.approach.max} cm`;

      // Validate the updated values
      validateCaptainStandingReach(document.getElementById(`${playerType}-standing-reach`));
      validateCaptainBlockJump(document.getElementById(`${playerType}-block-jump`));
      validateCaptainApproachJump(document.getElementById(`${playerType}-approach-jump`));
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

      // Validate the updated values
      validatePlayerStandingReach(playerEntry.querySelector('.standing-reach-input'));
      validatePlayerBlockJump(playerEntry.querySelector('.block-jump-input'));
      validatePlayerApproachJump(playerEntry.querySelector('.approach-jump-input'));
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

    // Function to validate only alphabets and spaces
    function validateAlphabets(input) {
      input.value = input.value.replace(/[^a-zA-Z\s]/g, '');
      return /^[a-zA-Z\s]+$/.test(input.value);
    }

    // Function to validate team name
    function validateTeamName(input) {
      const name = input.value.trim();
      const errorElement = document.getElementById(input.id + 'Error');

      if (name.length === 0) {
        errorElement.textContent = 'Team name is required';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else if (name.length < 3) {
        errorElement.textContent = 'Team name must be at least 3 characters long';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else if (name.length > 30) {
        errorElement.textContent = 'Team name cannot exceed 30 characters';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else {
        errorElement.textContent = '';
        input.classList.remove('input-error');
        input.classList.add('input-success');
        return true;
      }
    }

    // Function to validate club team name
    function validateClubTeam(input) {
      const name = input.value.trim();
      const errorElement = document.getElementById(input.id + 'Error');

      if (name.length > 0 && name.length < 3) {
        errorElement.textContent = 'Club team name must be at least 3 characters long';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else if (name.length > 30) {
        errorElement.textContent = 'Club team name cannot exceed 30 characters';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else {
        errorElement.textContent = '';
        input.classList.remove('input-error');
        input.classList.add('input-success');
        return true;
      }
    }

    // Function to validate captain name
    function validateCaptainName(input) {
      const name = input.value.trim();
      const errorElement = document.getElementById(input.id + 'Error');

      if (name.length === 0) {
        errorElement.textContent = 'Captain name is required';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else if (name.length < 3) {
        errorElement.textContent = 'Captain name must be at least 3 characters long';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else if (name.length > 20) {
        errorElement.textContent = 'Captain name cannot exceed 20 characters';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else if (!/^[a-zA-Z\s]+$/.test(name)) {
        errorElement.textContent = 'Captain name cannot contain numbers or special characters';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else {
        errorElement.textContent = '';
        input.classList.remove('input-error');
        input.classList.add('input-success');
        return true;
      }
    }

    // Function to validate captain age
    function validateCaptainAge(input) {
      const age = parseInt(input.value);
      const errorElement = document.getElementById(input.id + 'Error');

      if (isNaN(age)) {
        errorElement.textContent = 'Age must be a number';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else if (age < 16 || age > 23) {
        errorElement.textContent = 'Age must be between 16 and 23 years';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else {
        errorElement.textContent = '';
        input.classList.remove('input-error');
        input.classList.add('input-success');
        return true;
      }
    }

    // Function to validate captain height
    function validateCaptainHeight(input) {
      const height = parseInt(input.value);
      const errorElement = document.getElementById(input.id + 'Error');

      if (isNaN(height)) {
        errorElement.textContent = 'Height must be a number';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else if (height < 150 || height > 220) {
        errorElement.textContent = 'Height must be between 150 and 220 cm';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else {
        errorElement.textContent = '';
        input.classList.remove('input-error');
        input.classList.add('input-success');
        return true;
      }
    }

    // Function to validate captain standing reach
    function validateCaptainStandingReach(input) {
      const value = parseInt(input.value);
      const errorElement = document.getElementById(input.id + 'Error');

      if (input.value && (isNaN(value) || value < 200 || value > 280)) {
        errorElement.textContent = 'Standing reach must be between 200 and 280 cm';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else {
        errorElement.textContent = '';
        input.classList.remove('input-error');
        input.classList.add('input-success');
        return true;
      }
    }

    // Function to validate captain block jump
    function validateCaptainBlockJump(input) {
      const value = parseInt(input.value);
      const errorElement = document.getElementById(input.id + 'Error');

      if (input.value && (isNaN(value) || value < 250 || value > 350)) {
        errorElement.textContent = 'Block jump must be between 250 and 350 cm';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else {
        errorElement.textContent = '';
        input.classList.remove('input-error');
        input.classList.add('input-success');
        return true;
      }
    }

    // Function to validate captain approach jump
    function validateCaptainApproachJump(input) {
      const value = parseInt(input.value);
      const errorElement = document.getElementById(input.id + 'Error');

      if (input.value && (isNaN(value) || value < 270 || value > 380)) {
        errorElement.textContent = 'Approach jump must be between 270 and 380 cm';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else {
        errorElement.textContent = '';
        input.classList.remove('input-error');
        input.classList.add('input-success');
        return true;
      }
    }

    // Function to validate player name
    function validatePlayerName(input) {
      const name = input.value.trim();
      const errorElement = input.parentElement.querySelector('.error-message');

      if (!errorElement) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        input.parentElement.appendChild(errorDiv);
      }

      if (name.length === 0) {
        errorElement.textContent = 'Player name is required';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else if (name.length < 3) {
        errorElement.textContent = 'Player name must be at least 3 characters long';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else if (name.length > 20) {
        errorElement.textContent = 'Player name cannot exceed 20 characters';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else if (!/^[a-zA-Z\s]+$/.test(name)) {
        errorElement.textContent = 'Player name cannot contain numbers or special characters';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else {
        errorElement.textContent = '';
        input.classList.remove('input-error');
        input.classList.add('input-success');
        return true;
      }
    }

    // Function to validate player age
    function validatePlayerAge(input) {
      const age = parseInt(input.value);
      const errorElement = input.parentElement.querySelector('.error-message');

      if (!errorElement) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        input.parentElement.appendChild(errorDiv);
      }

      if (isNaN(age)) {
        errorElement.textContent = 'Age must be a number';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else if (age < 16 || age > 23) {
        errorElement.textContent = 'Age must be between 16 and 23 years';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else {
        errorElement.textContent = '';
        input.classList.remove('input-error');
        input.classList.add('input-success');
        return true;
      }
    }

    // Function to validate player height
    function validatePlayerHeight(input) {
      const height = parseInt(input.value);
      const errorElement = input.parentElement.querySelector('.error-message');

      if (!errorElement) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        input.parentElement.appendChild(errorDiv);
      }

      if (isNaN(height)) {
        errorElement.textContent = 'Height must be a number';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else if (height < 150 || height > 220) {
        errorElement.textContent = 'Height must be between 150 and 220 cm';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else {
        errorElement.textContent = '';
        input.classList.remove('input-error');
        input.classList.add('input-success');
        return true;
      }
    }

    // Function to validate player standing reach
    function validatePlayerStandingReach(input) {
      const value = parseInt(input.value);
      const errorElement = input.parentElement.querySelector('.error-message');

      if (!errorElement) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        input.parentElement.appendChild(errorDiv);
      }

      if (input.value && (isNaN(value) || value < 200 || value > 280)) {
        errorElement.textContent = 'Standing reach must be between 200 and 280 cm';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else {
        errorElement.textContent = '';
        input.classList.remove('input-error');
        input.classList.add('input-success');
        return true;
      }
    }

    // Function to validate player block jump
    function validatePlayerBlockJump(input) {
      const value = parseInt(input.value);
      const errorElement = input.parentElement.querySelector('.error-message');

      if (!errorElement) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        input.parentElement.appendChild(errorDiv);
      }

      if (input.value && (isNaN(value) || value < 250 || value > 350)) {
        errorElement.textContent = 'Block jump must be between 250 and 350 cm';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else {
        errorElement.textContent = '';
        input.classList.remove('input-error');
        input.classList.add('input-success');
        return true;
      }
    }

    // Function to validate player approach jump
    function validatePlayerApproachJump(input) {
      const value = parseInt(input.value);
      const errorElement = input.parentElement.querySelector('.error-message');

      if (!errorElement) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        input.parentElement.appendChild(errorDiv);
      }

      if (input.value && (isNaN(value) || value < 270 || value > 380)) {
        errorElement.textContent = 'Approach jump must be between 270 and 380 cm';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else {
        errorElement.textContent = '';
        input.classList.remove('input-error');
        input.classList.add('input-success');
        return true;
      }
    }

    // Function to validate player weight
    function validatePlayerWeight(input) {
      const weight = parseInt(input.value);
      const errorElement = input.parentElement.querySelector('.error-message');

      if (!errorElement) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        input.parentElement.appendChild(errorDiv);
      }

      if (isNaN(weight)) {
        errorElement.textContent = 'Weight must be a number';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else if (weight < 40 || weight > 120) {
        errorElement.textContent = 'Weight must be between 40 and 120 kg';
        input.classList.add('input-error');
        input.classList.remove('input-success');
        return false;
      } else {
        errorElement.textContent = '';
        input.classList.remove('input-error');
        input.classList.add('input-success');
        return true;
      }
    }

    // Function to check if all required fields are valid
    function checkFormValidity() {
      // Check team information
      const isTeamNameValid = validateTeamName(document.getElementById('team_name'));
      const isClubTeamValid = validateClubTeam(document.getElementById('club_team'));

      // Check captain information
      const isCaptainNameValid = validateCaptainName(document.getElementById('captain_name'));
      const isCaptainAgeValid = validateCaptainAge(document.getElementById('captain_age'));
      const isCaptainHeightValid = validateCaptainHeight(document.getElementById('captain_height'));
      const isCaptainPositionValid = document.getElementById('captain_position').value !== '';
      const isCaptainStandingReachValid = validateCaptainStandingReach(document.getElementById('captain-standing-reach'));
      const isCaptainBlockJumpValid = validateCaptainBlockJump(document.getElementById('captain-block-jump'));
      const isCaptainApproachJumpValid = validateCaptainApproachJump(document.getElementById('captain-approach-jump'));

      // Check if captain position is selected
      document.getElementById('captain_positionError').textContent = isCaptainPositionValid ? '' : 'Captain position is required';
      document.getElementById('captain_position').classList.toggle('input-error', !isCaptainPositionValid);
      document.getElementById('captain_position').classList.toggle('input-success', isCaptainPositionValid);

      // Check player count
      const playerCount = document.querySelectorAll('.player-entry').length;
      const isPlayerCountValid = playerCount >= 5 && playerCount <= 12;

      // Check all player fields
      let allPlayersValid = true;
      if (playerCount > 0) {
        const playerNames = document.querySelectorAll('input[name="player_name[]"]');
        const playerAges = document.querySelectorAll('input[name="player_age[]"]');
        const playerHeights = document.querySelectorAll('input[name="player_height[]"]');
        const playerWeights = document.querySelectorAll('input[name="player_weight[]"]');
        const playerPositions = document.querySelectorAll('select[name="player_position[]"]');

        for (let i = 0; i < playerCount; i++) {
          if (!validatePlayerName(playerNames[i])) allPlayersValid = false;
          if (!validatePlayerAge(playerAges[i])) allPlayersValid = false;
          if (!validatePlayerHeight(playerHeights[i])) allPlayersValid = false;
          if (!validatePlayerWeight(playerWeights[i])) allPlayersValid = false;
          if (playerPositions[i].value === '') {
            const errorElement = playerPositions[i].parentElement.querySelector('.error-message') || 
                                document.createElement('div');
            errorElement.className = 'error-message';
            errorElement.textContent = 'Player position is required';
            playerPositions[i].parentElement.appendChild(errorElement);
            playerPositions[i].classList.add('input-error');
            playerPositions[i].classList.remove('input-success');
            allPlayersValid = false;
          } else {
            const errorElement = playerPositions[i].parentElement.querySelector('.error-message');
            if (errorElement) errorElement.textContent = '';
            playerPositions[i].classList.remove('input-error');
            playerPositions[i].classList.add('input-success');
          }
        }
      } else {
        allPlayersValid = false;
      }

      // Enable/disable submit button based on validation
      const submitBtn = document.getElementById('submitBtn');
      const isFormValid = isTeamNameValid && isClubTeamValid && 
                         isCaptainNameValid && isCaptainAgeValid && isCaptainHeightValid && 
                         isCaptainPositionValid && isCaptainStandingReachValid && 
                         isCaptainBlockJumpValid && isCaptainApproachJumpValid && 
                         isPlayerCountValid && allPlayersValid;

      submitBtn.disabled = !isFormValid;

      return isFormValid;
    }

    // Set up real-time validation for all fields
    function setupRealTimeValidation() {
      // Team information
      document.getElementById('team_name').addEventListener('input', function() {
        validateTeamName(this);
        checkFormValidity();
      });

      document.getElementById('club_team').addEventListener('input', function() {
        validateClubTeam(this);
        checkFormValidity();
      });

      // Captain information
      document.getElementById('captain_name').addEventListener('input', function() {
        validateCaptainName(this);
        checkFormValidity();
      });

      document.getElementById('captain_age').addEventListener('input', function() {
        validateCaptainAge(this);
        checkFormValidity();
      });

      document.getElementById('captain_height').addEventListener('input', function() {
        validateCaptainHeight(this);
        checkFormValidity();
      });

      document.getElementById('captain_position').addEventListener('change', function() {
        checkFormValidity();
      });

      document.getElementById('captain-standing-reach').addEventListener('input', function() {
        validateCaptainStandingReach(this);
        checkFormValidity();
      });

      document.getElementById('captain-block-jump').addEventListener('input', function() {
        validateCaptainBlockJump(this);
        checkFormValidity();
      });

      document.getElementById('captain-approach-jump').addEventListener('input', function() {
        validateCaptainApproachJump(this);
        checkFormValidity();
      });

      // Add event listeners for dynamically created player fields
      document.addEventListener('input', function(e) {
        if (e.target.name === 'player_name[]') {
          validatePlayerName(e.target);
          checkFormValidity();
        } else if (e.target.name === 'player_age[]') {
          validatePlayerAge(e.target);
          checkFormValidity();
        } else if (e.target.name === 'player_height[]') {
          validatePlayerHeight(e.target);
          checkFormValidity();
        } else if (e.target.name === 'player_weight[]') {
          validatePlayerWeight(e.target);
          checkFormValidity();
        } else if (e.target.name === 'player_standing_reach[]') {
          validatePlayerStandingReach(e.target);
          checkFormValidity();
        } else if (e.target.name === 'player_block_jump[]') {
          validatePlayerBlockJump(e.target);
          checkFormValidity();
        } else if (e.target.name === 'player_approach_jump[]') {
          validatePlayerApproachJump(e.target);
          checkFormValidity();
        } else if (e.target.name === 'player_position[]') {
          checkFormValidity();
        }
      });
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
              <input type="text" name="player_name[]" placeholder="Player Name*" required 
                     oninput="validatePlayerName(this)">
            </div>
            <div>
              <input type="number" name="player_age[]" placeholder="Age*" min="16" max="23" required 
                     oninput="validatePlayerAge(this)">
            </div>
            <div>
              <input type="number" name="player_height[]" placeholder="Height (cm)*" min="150" max="220" required 
                     oninput="validatePlayerHeight(this)">
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
              <input type="number" name="player_weight[]" placeholder="Weight (kg)*" min="40" max="120" required 
                     oninput="validatePlayerWeight(this)">
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
              <input type="number" name="player_standing_reach[]" class="standing-reach-input" min="200" max="280" 
                     oninput="validatePlayerStandingReach(this)">
              <div class="jump-info standing-info">Select position first</div>
            </div>
            <div>
              <label>Block Jump (cm):</label>
              <input type="number" name="player_block_jump[]" class="block-jump-input" min="250" max="350" 
                     oninput="validatePlayerBlockJump(this)">
              <div class="jump-info block-info">Select position first</div>
            </div>
            <div>
              <label>Approach Jump (cm):</label>
              <input type="number" name="player_approach_jump[]" class="approach-jump-input" min="270" max="380" 
                     oninput="validatePlayerApproachJump(this)">
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

      // Check form validity after adding player
      checkFormValidity();
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

        // Check form validity after removing player
        checkFormValidity();
      }
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

      // Add 5 player fields on page load (minimum requirement)
      for (let i = 0; i < 5; i++) {
        addPlayerField();
      }

      // Set up real-time validation
      setupRealTimeValidation();

      // Initial form validation check
      checkFormValidity();
    });

    // Form Validation
    document.getElementById("volleyballForm").addEventListener("submit", function(event){
      // Final validation check before submission
      if (!checkFormValidity()) {
        event.preventDefault();
        alert("Please fix all validation errors before submitting.");
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

      alert("Team Registration Successful!");
    });
  </script>
</body>
</html>