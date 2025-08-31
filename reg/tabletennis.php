```php
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

// Fetch event_name from events table using event_id or event_name
$event_id = mysqli_real_escape_string($con, $_GET['event_id'] ?? 0);
$event_name = mysqli_real_escape_string($con, $_GET['event_name'] ?? '');

if ($event_id) {
    $eventQuery = mysqli_query($con, "SELECT event_name FROM events WHERE id='$event_id'");
    if ($eventQuery && mysqli_num_rows($eventQuery) > 0) {
        $eventRow = mysqli_fetch_assoc($eventQuery);
        $event_name = $eventRow['event_name'];
    } else {
        echo "<script>alert('Invalid event ID. Please select a valid event.'); window.location.href='../user/get_event.php';</script>";
        exit();
    }
} elseif ($event_name) {
    // Verify event_name exists in the database
    $eventQuery = mysqli_query($con, "SELECT event_name FROM events WHERE event_name='$event_name' AND status='active'");
    if ($eventQuery && mysqli_num_rows($eventQuery) == 0) {
        echo "<script>alert('Invalid event name. Please select a valid event.'); window.location.href='../user/get_event.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('No event ID or name provided. Please select an event.'); window.location.href='../user/get_event.php';</script>";
    exit();
}

// Check if email already exists in tabletennis_players
$email = isset($_SESSION['email']) ? mysqli_real_escape_string($con, $_SESSION['email']) : null;
if ($email) {
    $check = "SELECT * FROM tabletennis_players WHERE email = '$email'";
    $exist = mysqli_query($con, $check);

    if ($exist && mysqli_num_rows($exist) > 0) {
        $row = mysqli_fetch_assoc($exist);
        $approved = $row['is_approved'];
        if ($approved == "approved") {
            header("Location: ../user/dashboard.php");
            exit();
        } else {
            echo "<script>
                alert('Your request for Table tennis is not approved yet!');
                window.location.href='../user/join.php?event_id=$event_id&event_name=".urlencode($event_name)."';
            </script>";
            exit();
        }
    }
}

// Fetch user data if logged in
$userFullName = "";
$userEmail = "";
$isLoggedIn = false;

if (isset($_SESSION['userid'])) {
    $userId = $_SESSION['userid'];
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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullName = mysqli_real_escape_string($con, $_POST['fullName']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $role = mysqli_real_escape_string($con, $_POST['role']);
    $teamName = isset($_POST['teamName']) ? mysqli_real_escape_string($con, $_POST['teamName']) : null;
    $hand1 = mysqli_real_escape_string($con, $_POST['hand1']);
    $play_style1 = mysqli_real_escape_string($con, $_POST['play_style1']);
    $player1 = mysqli_real_escape_string($con, $_POST['player1']);
    $dob1 = mysqli_real_escape_string($con, $_POST['dob1']);
    $height1 = mysqli_real_escape_string($con, $_POST['height1']);
    $weight1 = mysqli_real_escape_string($con, $_POST['weight1']);
    $chronic_illness1 = mysqli_real_escape_string($con, $_POST['chronic_illness1']);
    $allergies1 = mysqli_real_escape_string($con, $_POST['allergies1']);
    $medications1 = mysqli_real_escape_string($con, $_POST['medications1']);
    $surgeries1 = mysqli_real_escape_string($con, $_POST['surgeries1']);
    $previous_injuries1 = mysqli_real_escape_string($con, $_POST['previous_injuries1']);

    // Player 2 information (if team)
    if ($role === "team") {
        $player2 = !empty($_POST['player2']) ? mysqli_real_escape_string($con, $_POST['player2']) : null;
        $dob2 = !empty($_POST['dob2']) ? mysqli_real_escape_string($con, $_POST['dob2']) : null;
        $height2 = !empty($_POST['height2']) ? mysqli_real_escape_string($con, $_POST['height2']) : null;
        $weight2 = !empty($_POST['weight2']) ? mysqli_real_escape_string($con, $_POST['weight2']) : null;
        $chronic_illness2 = !empty($_POST['chronic_illness2']) ? mysqli_real_escape_string($con, $_POST['chronic_illness2']) : null;
        $allergies2 = !empty($_POST['allergies2']) ? mysqli_real_escape_string($con, $_POST['allergies2']) : null;
        $medications2 = !empty($_POST['medications2']) ? mysqli_real_escape_string($con, $_POST['medications2']) : null;
        $surgeries2 = !empty($_POST['surgeries2']) ? mysqli_real_escape_string($con, $_POST['surgeries2']) : null;
        $previous_injuries2 = !empty($_POST['previous_injuries2']) ? mysqli_real_escape_string($con, $_POST['previous_injuries2']) : null;
        $hand2 = !empty($_POST['hand2']) ? mysqli_real_escape_string($con, $_POST['hand2']) : null;
        $play_style2 = !empty($_POST['play_style2']) ? mysqli_real_escape_string($con, $_POST['play_style2']) : null;
    } else {
        $player2 = $dob2 = $height2 = $weight2 = $chronic_illness2 = 
        $allergies2 = $medications2 = $surgeries2 = $previous_injuries2 = $hand2 = $play_style2 = null;
    }

    $category = ($role === "team") ? "double" : "single";

    // Use prepared statement for insertion
    $stmt = $con->prepare("INSERT INTO tabletennis_players (
        event_name, fullName, email, role, teamName, hand1, play_style1,
        player1, dob1, height1, weight1, chronic_illness1, allergies1, medications1, surgeries1, previous_injuries1,
        player2, dob2, height2, weight2, chronic_illness2, allergies2, medications2, surgeries2, previous_injuries2, hand2, play_style2
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "sssssssssssssssssssssssssss",
        $event_name, $fullName, $email, $category, $teamName, $hand1, $play_style1,
        $player1, $dob1, $height1, $weight1, $chronic_illness1, $allergies1, $medications1, $surgeries1, $previous_injuries1,
        $player2, $dob2, $height2, $weight2, $chronic_illness2, $allergies2, $medications2, $surgeries2, $previous_injuries2, $hand2, $play_style2
    );
    $execute = $stmt->execute();
    if ($execute) {
        header("Location: ".$_SERVER['PHP_SELF']."?success=1&event_id=$event_id&event_name=".urlencode($event_name));
        exit();
    } else {
        echo "❌ Error: " . $stmt->error;
    }
    $stmt->close();
    mysqli_close($con);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Table Tennis Registration Form</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-image: url(../images/tt.jpg);
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
      padding: 20px;
    }

    .form-container {
      background-color: #d2fcffff;
      background-image: url(../images/tablet.jpg);
      background-size: cover;
      background-position: center;
      padding: 30px;
      border-radius: 10px;
      width: 800px;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
    }

    h2 {
      text-align: center;
      color: #856404;
      margin-bottom: 20px;
    }

    h3, h4 {
      color: #b21f1f;
      margin: 15px 0 10px;
    }

    .form-row {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-bottom: 15px;
    }

    .form-group {
      flex: 1 0 calc(50% - 20px);
      min-width: 300px;
    }

    label {
      display: block;
      margin-bottom: 8px;
      color: black;
      font-weight: bold;
    }

    input, select, textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 16px;
      box-sizing: border-box;
    }

    input:focus, select:focus {
      border-color: #007bff;
      outline: none;
      box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    .hidden {
      display: none;
    }

    .error-message {
      color: red;
      font-size: 12px;
      margin-top: 4px;
      display: none;
    }

    .input-error {
      border: 1px solid red;
    }

    .input-success {
      border: 1px solid green;
    }

    button {
      width: 100%;
      padding: 12px;
      background-color: #007bff;
      color: #fff;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
      margin-top: 15px;
    }

    button:hover {
      background-color: #0056b3;
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
      text-decoration: none;
      box-shadow: 0px 4px 8px rgba(0,0,0,0.2);
      transition: 0.3s ease-in-out;
      text-align: center;
    }

    .back-btn:hover {
      background: linear-gradient(135deg, #ff4b2b, #ff416c);
      transform: translateY(-3px);
      box-shadow: 0px 6px 12px rgba(0,0,0,0.3);
    }

    .full-width {
      flex: 1 0 100%;
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

    .section-box {
      background-color: white;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .section-title {
      background-color: #007bff;
      color: white;
      padding: 10px 15px;
      border-radius: 5px;
      margin: -20px -20px 20px -20px;
    }

    .health-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 15px;
      margin-top: 15px;
    }

    .health-grid div {
      margin-bottom: 15px;
    }

    .health-grid label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
      color: #333;
    }

    .health-grid select,
    .health-grid input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    .eligibility-link {
      text-align: center;
      margin-bottom: 15px;
    }

    .eligibility-link a {
      color: #ff6b6b;
      font-weight: bold;
      text-decoration: none;
    }

    .eligibility-link a:hover {
      text-decoration: underline;
    }

    .illness-details, .medication-details {
      margin-top: 8px;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <form method="POST" action="" enctype="multipart/form-data" id="registrationForm">
      <h2>Table Tennis Registration Form</h2>

      <div class="eligibility-link">
        <p>To view the detailed eligibility criteria <a href="tabletennis.html" style="color: #ff6b6b; font-weight: bold;">click here</a></p>
      </div>

      <div class="health-cert-notice">
        Players must bring their health certificate to the venue, otherwise they will be rejected.
      </div>

      <!-- Personal Information Section -->
      <div class="section-box">
        <h3>Personal Information</h3>
        <div class="form-row">
          <div class="form-group">
            <label for="fullName" class="required">Full Name:</label>
            <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($userFullName); ?>" required readonly>
          </div>
          <div class="form-group">
            <label for="email" class="required">Email Address:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userEmail); ?>" required readonly>
          </div>
        </div>

        <input type="hidden" name="event_name" id="event_name" value="<?php echo htmlspecialchars($event_name); ?>">

        <div class="form-row">
          <div class="form-group">
            <label for="role">Role:</label>
            <select id="role" name="role" required onchange="togglePlayerFields()">
              <option value="">Select Role</option>
              <option value="player">Player</option>
              <option value="team">Team</option>
            </select>
            <div id="role-error" class="error-message"></div>
          </div>

          <div class="form-group hidden" id="teamFields">
            <label for="teamName">Team Name:</label>
            <input type="text" id="teamName" name="teamName" onkeypress="allowOnlyAlphabets(event)">
            <div id="teamName-error" class="error-message"></div>
          </div>
        </div>
      </div>

      <!-- Player 1 Information Section -->
      <div class="section-box">
        <h3>Player 1 Information</h3>
        <div class="form-row">
          <div class="form-group">
            <label for="player1">Player 1 Name:</label>
            <input type="text" id="player1" name="player1" required onkeypress="allowOnlyAlphabets(event)">
            <div id="player1-error" class="error-message"></div>
          </div>

          <div class="form-group">
            <label for="dob1">Player 1 DoB:</label>
            <input type="date" id="dob1" name="dob1" required min="2002-01-01" max="2008-12-31">
            <div id="dob1-error" class="error-message"></div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="hand1">Playing Hand:</label>
            <select id="hand1" name="hand1" required>
              <option value="">Select Hand</option>
              <option value="right">Right</option>
              <option value="left">Left</option>
            </select>
            <div id="hand1-error" class="error-message"></div>
          </div>

          <div class="form-group">
            <label for="play_style1">Play Style:</label>
            <select id="play_style1" name="play_style1" required>
              <option value="">Select Style</option>
              <option value="offensive">Offensive</option>
              <option value="defensive">Defensive</option>
              <option value="all-round">All-Round</option>
            </select>
            <div id="play_style1-error" class="error-message"></div>
          </div>
        </div>

        <h4>Basic Health Information - Player 1</h4>
        <div class="form-row">
          <div class="form-group">
            <label for="height1">Height (cm):</label>
            <input type="text" id="height1" name="height1" required onkeypress="allowOnlyNumbers(event)">
            <div id="height1-error" class="error-message"></div>
          </div>

          <div class="form-group">
            <label for="weight1">Weight (kg):</label>
            <input type="text" id="weight1" name="weight1" required onkeypress="allowOnlyNumbers(event)">
            <div id="weight1-error" class="error-message"></div>
          </div>
        </div>

        <h4>Medical History & Health Status - Player 1</h4>
        <div class="health-grid">
          <div>
            <label>Chronic Illness:</label>
            <select name="chronic_illness_option1" class="illness-option" onchange="toggleIllnessField(this, '1')">
              <option value="select">select</option>
              <option value="yes">Yes</option>
              <option value="no">No</option>
            </select>
            <div class="illness-details" id="illness-details-1" style="display: none;">
              <input type="text" name="chronic_illness1" placeholder="Type of chronic illness" onkeypress="allowOnlyAlphabets(event)">
              <div id="chronic_illness1-error" class="error-message"></div>
            </div>
          </div>

          <div>
            <label>Allergies:</label>
            <input type="text" name="allergies1" placeholder="Allergies (if any)" onkeypress="allowOnlyAlphabets(event)">
            <div id="allergies1-error" class="error-message"></div>
          </div>

          <div>
            <label>Current Medications:</label>
            <select name="medications_option1" class="medication-option" onchange="toggleMedicationField(this, '1')">
              <option value="select">select</option>
              <option value="yes">Yes</option>
              <option value="no">No</option>
            </select>
            <div class="medication-details" id="medication-details-1" style="display: none;">
              <input type="text" name="medications1" placeholder="Type of medication" onkeypress="allowOnlyAlphabets(event)">
              <div id="medications1-error" class="error-message"></div>
            </div>
          </div>

          <div>
            <label>Recent Surgeries:</label>
            <input type="text" name="surgeries1" placeholder="Type of surgery" onkeypress="allowOnlyAlphabets(event)">
            <div id="surgeries1-error" class="error-message"></div>
          </div>

          <div>
            <label>Previous Injuries:</label>
            <input type="text" name="previous_injuries1" placeholder="Type of injury" onkeypress="allowOnlyAlphabets(event)">
            <div id="previous_injuries1-error" class="error-message"></div>
          </div>
        </div>
      </div>

      <!-- Player 2 Fields (Initially Hidden) -->
      <div id="player2Fields" class="hidden section-box">
        <h3>Player 2 Information</h3>
        <div class="form-row">
          <div class="form-group">
            <label for="player2">Player 2 Name:</label>
            <input type="text" id="player2" name="player2" onkeypress="allowOnlyAlphabets(event)">
            <div id="player2-error" class="error-message"></div>
          </div>

          <div class="form-group">
            <label for="dob2">Player 2 DoB:</label>
            <input type="date" id="dob2" name="dob2" min="2002-01-01" max="2008-12-31">
            <div id="dob2-error" class="error-message"></div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="hand2">Playing Hand:</label>
            <select id="hand2" name="hand2">
              <option value="">Select Hand</option>
              <option value="right">Right</option>
              <option value="left">Left</option>
            </select>
            <div id="hand2-error" class="error-message"></div>
          </div>

          <div class="form-group">
            <label for="play_style2">Play Style:</label>
            <select id="play_style2" name="play_style2">
              <option value="">Select Style</option>
              <option value="offensive">Offensive</option>
              <option value="defensive">Defensive</option>
              <option value="all-round">All-Round</option>
            </select>
            <div id="play_style2-error" class="error-message"></div>
          </div>
        </div>

        <h4>Basic Health Information - Player 2</h4>
        <div class="form-row">
          <div class="form-group">
            <label for="height2">Height (cm):</label>
            <input type="text" id="height2" name="height2" onkeypress="allowOnlyNumbers(event)">
            <div id="height2-error" class="error-message"></div>
          </div>

          <div class="form-group">
            <label for="weight2">Weight (kg):</label>
            <input type="text" id="weight2" name="weight2" onkeypress="allowOnlyNumbers(event)">
            <div id="weight2-error" class="error-message"></div>
          </div>
        </div>

        <h4>Medical History & Health Status - Player 2</h4>
        <div class="health-grid">
          <div>
            <label>Chronic Illness:</label>
            <select name="chronic_illness_option2" class="illness-option" onchange="toggleIllnessField(this, '2')">
              <option value="select">select</option>
              <option value="yes">Yes</option>
              <option value="no">No</option>
            </select>
            <div class="illness-details" id="illness-details-2" style="display: none;">
              <input type="text" name="chronic_illness2" placeholder="Type of chronic illness" onkeypress="allowOnlyAlphabets(event)">
              <div id="chronic_illness2-error" class="error-message"></div>
            </div>
          </div>

          <div>
            <label>Allergies:</label>
            <input type="text" name="allergies2" placeholder="Allergies (if any)" onkeypress="allowOnlyAlphabets(event)">
            <div id="allergies2-error" class="error-message"></div>
          </div>

          <div>
            <label>Current Medications:</label>
            <select name="medications_option2" class="medication-option" onchange="toggleMedicationField(this, '2')">
              <option value="select">select</option>
              <option value="yes">Yes</option>
              <option value="no">No</option>
            </select>
            <div class="medication-details" id="medication-details-2" style="display: none;">
              <input type="text" name="medications2" placeholder="Type of medication" onkeypress="allowOnlyAlphabets(event)">
              <div id="medications2-error" class="error-message"></div>
            </div>
          </div>

          <div>
            <label>Recent Surgeries:</label>
            <input type="text" name="surgeries2" placeholder="Type of surgery" onkeypress="allowOnlyAlphabets(event)">
            <div id="surgeries2-error" class="error-message"></div>
          </div>

          <div>
            <label>Previous Injuries:</label>
            <input type="text" name="previous_injuries2" placeholder="Type of injury" onkeypress="allowOnlyAlphabets(event)">
            <div id="previous_injuries2-error" class="error-message"></div>
          </div>
        </div>
      </div>

      <button type="submit" name="submit">Register</button>
      <div style="text-align: center; margin-top: 15px;">
        <a href="../user/join.php?event_id=<?php echo $event_id; ?>&event_name=<?php echo urlencode($event_name); ?>" class="back-btn">⬅ Back</a>
      </div>
    </form>
  </div>

<script>
  // Store existing team names for validation
  const existingTeamNames = ["Eagles", "Falcons", "Hawks", "Titans"];

  function togglePlayerFields() {
    const role = document.getElementById('role');
    const player2Fields = document.getElementById('player2Fields');
    const teamFields = document.getElementById('teamFields');

    if (role.value === 'team') {
      player2Fields.classList.remove('hidden');
      teamFields.classList.remove('hidden');

      document.getElementById('player2').setAttribute('required', 'true');
      document.getElementById('dob2').setAttribute('required', 'true');
      document.getElementById('height2').setAttribute('required', 'true');
      document.getElementById('weight2').setAttribute('required', 'true');
      document.getElementById('hand2').setAttribute('required', 'true');
      document.getElementById('play_style2').setAttribute('required', 'true');
      document.getElementById('teamName').setAttribute('required', 'true');
    } else {
      player2Fields.classList.add('hidden');
      teamFields.classList.add('hidden');

      document.getElementById('player2').removeAttribute('required');
      document.getElementById('dob2').removeAttribute('required');
      document.getElementById('height2').removeAttribute('required');
      document.getElementById('weight2').removeAttribute('required');
      document.getElementById('hand2').removeAttribute('required');
      document.getElementById('play_style2').removeAttribute('required');
      document.getElementById('teamName').removeAttribute('required');
    }
  }

  function toggleIllnessField(selectElement, playerNum) {
    const illnessDetails = document.getElementById(`illness-details-${playerNum}`);
    if (selectElement.value === 'yes') {
      illnessDetails.style.display = 'block';
      document.querySelector(`[name="chronic_illness${playerNum}"]`).setAttribute('required', 'true');
    } else {
      illnessDetails.style.display = 'none';
      document.querySelector(`[name="chronic_illness${playerNum}"]`).removeAttribute('required');
    }
  }

  function toggleMedicationField(selectElement, playerNum) {
    const medicationDetails = document.getElementById(`medication-details-${playerNum}`);
    if (selectElement.value === 'yes') {
      medicationDetails.style.display = 'block';
      document.querySelector(`[name="medications${playerNum}"]`).setAttribute('required', 'true');
    } else {
      medicationDetails.style.display = 'none';
      document.querySelector(`[name="medications${playerNum}"]`).removeAttribute('required');
    }
  }

  function allowOnlyNumbers(event) {
    const char = String.fromCharCode(event.which);
    if (!/[0-9]/.test(char)) {
      event.preventDefault();
    }
  }

  function allowOnlyAlphabets(event) {
    const char = String.fromCharCode(event.which);
    if (!/[a-zA-Z\s]/.test(char)) {
      event.preventDefault();
    }
  }

  function showError(inputId, message) {
    const input = document.getElementById(inputId);
    let errorElement = document.getElementById(inputId + '-error');

    if (!errorElement) {
      errorElement = document.createElement('span');
      errorElement.id = inputId + '-error';
      errorElement.className = 'error-message';
      errorElement.style.color = 'red';
      errorElement.style.fontSize = '12px';
      input.parentNode.appendChild(errorElement);
    }

    errorElement.textContent = message;
    errorElement.style.display = 'block';
    input.classList.add('input-error');
    input.classList.remove('input-success');
  }

  function clearError(inputId) {
    const errorElement = document.getElementById(inputId + '-error');
    if (errorElement) {
      errorElement.textContent = '';
      errorElement.style.display = 'none';
      document.getElementById(inputId).classList.remove('input-error');
      document.getElementById(inputId).classList.add('input-success');
    }
  }

  function validateField(inputId, type) {
    const input = document.getElementById(inputId);
    const value = input.value.trim();

    console.log(`Validating ${inputId} with value: "${value}" and type: ${type}`); // Debugging log

    if (value === '') {
      if (type === 'select') {
        let errorMsg = '';
        switch (inputId) {
          case 'role': errorMsg = 'Please select a valid role'; break;
          case 'hand1': case 'hand2': errorMsg = 'Please select a hand'; break;
          case 'play_style1': case 'play_style2': errorMsg = 'Please select a play style'; break;
          default: errorMsg = 'This field is required';
        }
        showError(inputId, errorMsg);
      } else {
        showError(inputId, 'This field is required');
      }
      return false;
    }

    if (type === 'name') {
     
      const alphabetCount = value.replace(/\s/g, '').length;
      if (alphabetCount < 3 || alphabetCount > 20) {
        showError(inputId, 'Name must be 3-20 alphabets');
        return false;
      }
    } else if (type === 'number') {
      if (!/^\d+$/.test(value)) {
        showError(inputId, 'Only numbers are allowed');
        return false;
      }
      const numValue = parseInt(value);
      if (inputId === 'height1' || inputId === 'height2') {
        if (numValue < 100 || numValue > 250) {
          showError(inputId, 'Height must be between 100cm and 250cm');
          return false;
        }
      }
      if (inputId === 'weight1' || inputId === 'weight2') {
        if (numValue < 30 || numValue > 150) {
          showError(inputId, 'Weight must be between 30kg and 150kg');
          return false;
        }
      }
    } else if (type === 'team') {
      if (value.length < 2) {
        showError(inputId, 'Team name must be at least 2 characters long');
        return false;
      }
      if (existingTeamNames.includes(value)) {
        showError(inputId, 'Team name already exists. Please choose a different name.');
        return false;
      }
    } else if (type === 'select') {
      const validOptions = {
        'role': ['player', 'team'],
        'hand1': ['right', 'left'],
        'hand2': ['right', 'left'],
        'play_style1': ['offensive', 'defensive', 'all-round'],
        'play_style2': ['offensive', 'defensive', 'all-round']
      };
      if (!validOptions[inputId] || !validOptions[inputId].includes(value)) {
        let errorMsg = '';
        switch (inputId) {
          case 'role': errorMsg = 'Please select a valid role'; break;
          case 'hand1': case 'hand2': errorMsg = 'Please select a valid hand'; break;
          case 'play_style1': case 'play_style2': errorMsg = 'Please select a valid play style'; break;
        }
        showError(inputId, errorMsg);
        return false;
      }
    }

    clearError(inputId);
    return true;
  }

  function validateDob(inputId) {
    const input = document.getElementById(inputId);
    const value = input.value;

    if (value === '') {
      showError(inputId, 'Date of birth is required');
      return false;
    }

    const dob = new Date(value);
    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    const monthDiff = today.getMonth() - dob.getMonth();

    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
      age--;
    }

    if (age < 16 || age > 22) {
      showError(inputId, 'Age must be between 16 and 22 years');
      return false;
    }

    clearError(inputId);
    return true;
  }

  function validateForm() {
    let isValid = true;

    console.log('Starting form validation'); // Debugging log

    // Explicitly set correct types for validation
    if (!validateField('event_name', 'name')) isValid = false;
    if (!validateField('role', 'select')) isValid = false; // Ensure 'select' type
    if (!validateField('player1', 'name')) isValid = false;
    if (!validateDob('dob1')) isValid = false;
    if (!validateField('height1', 'number')) isValid = false;
    if (!validateField('weight1', 'number')) isValid = false;
    if (!validateField('hand1', 'select')) isValid = false; // Ensure 'select' type
    if (!validateField('play_style1', 'select')) isValid = false; // Ensure 'select' type

    if (document.getElementById('role').value === 'team') {
      if (!validateField('teamName', 'team')) isValid = false;
      if (!validateField('player2', 'name')) isValid = false;
      if (!validateDob('dob2')) isValid = false;
      if (!validateField('height2', 'number')) isValid = false;
      if (!validateField('weight2', 'number')) isValid = false;
      if (!validateField('hand2', 'select')) isValid = false; // Ensure 'select' type
      if (!validateField('play_style2', 'select')) isValid = false; // Ensure 'select' type
    }

    const medicalFields = [
      'chronic_illness1', 'allergies1', 'medications1', 'surgeries1', 'previous_injuries1',
      'chronic_illness2', 'allergies2', 'medications2', 'surgeries2', 'previous_injuries2'
    ];

    medicalFields.forEach(field => {
      const input = document.querySelector(`[name="${field}"]`);
      if (input && input.offsetParent !== null && input.hasAttribute('required')) {
        if (!validateField(field, 'name')) isValid = false;
      }
    });

    console.log('Form validation result: ', isValid); // Debugging log
    return isValid;
  }

  document.addEventListener('DOMContentLoaded', function() {
    togglePlayerFields();

    document.getElementById('role').addEventListener('change', function() {
      validateField('role', 'select'); // Explicitly set 'select' type
      togglePlayerFields();
    });

    document.getElementById('player1').addEventListener('input', function() {
      validateField('player1', 'name');
    });

    document.getElementById('height1').addEventListener('input', function() {
      validateField('height1', 'number');
    });

    document.getElementById('weight1').addEventListener('input', function() {
      validateField('weight1', 'number');
    });

    document.getElementById('dob1').addEventListener('change', function() {
      validateDob('dob1');
    });

    document.getElementById('hand1').addEventListener('change', function() {
      validateField('hand1', 'select'); // Explicitly set 'select' type
    });

    document.getElementById('play_style1').addEventListener('change', function() {
      validateField('play_style1', 'select'); // Explicitly set 'select' type
    });

    document.getElementById('player2').addEventListener('input', function() {
      validateField('player2', 'name');
    });

    document.getElementById('height2').addEventListener('input', function() {
      validateField('height2', 'number');
    });

    document.getElementById('weight2').addEventListener('input', function() {
      validateField('weight2', 'number');
    });

    document.getElementById('dob2').addEventListener('change', function() {
      validateDob('dob2');
    });

    document.getElementById('hand2').addEventListener('change', function() {
      validateField('hand2', 'select'); // Explicitly set 'select' type
    });

    document.getElementById('play_style2').addEventListener('change', function() {
      validateField('play_style2', 'select'); // Explicitly set 'select' type
    });

    document.getElementById('teamName').addEventListener('input', function() {
      validateField('teamName', 'team');
    });

    const medicalInputs = document.querySelectorAll('input[name$="1"], input[name$="2"]');
    medicalInputs.forEach(input => {
      input.addEventListener('input', function() {
        validateField(this.name, 'name');
      });
    });

    document.getElementById('registrationForm').addEventListener('submit', function(e) {
      if (!validateForm()) {
        e.preventDefault();
        const firstError = document.querySelector('.input-error');
        if (firstError) {
          firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
    });
  });
</script>
</body>
</html>
```