<?php
session_start();
require '../include/db_connect.php';

// DB connection
$userid = $_SESSION['userid'];
$email = $_SESSION['email'];
$check = "SELECT * FROM badminton_players WHERE email = '$email'";
$exist = mysqli_query($con, $check);

if ($exist && mysqli_num_rows($exist) > 0) {
    $row = mysqli_fetch_assoc($exist);
    $approved = $row['is_approved'];
    if ($approved == "approved") {
        header("Location: ../user/dashboard.php");
        exit();
    }
}
// /if (mysqli_num_rows($exist) > 0) {
//     echo "<script>
//             alert('Already registered for badminton');
//             window.location.href='../user/join.php';
//           </script>";
//     exit();
// }

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
    $userFullName = $userData['fname'] . ' ' . $userData['lname'];  // combine first and last name
    $userEmail = $userData['email'];
    $isLoggedIn = true;
}
}

// Handle form submission only when POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Basic information
    $fullName = mysqli_real_escape_string($con, $_POST['fullName']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $role = mysqli_real_escape_string($con, $_POST['role']);
    $teamName = isset($_POST['teamName']) ? mysqli_real_escape_string($con, $_POST['teamName']) : null;
    
    // Player 1 information
    $player1 = mysqli_real_escape_string($con, $_POST['player1']);
    $dob1 = mysqli_real_escape_string($con, $_POST['dob1']);
    $height1 = mysqli_real_escape_string($con, $_POST['height1']);
    $weight1 = mysqli_real_escape_string($con, $_POST['weight1']);
    $chronic_illness1 = mysqli_real_escape_string($con, $_POST['chronic_illness1']);
    $allergies1 = mysqli_real_escape_string($con, $_POST['allergies1']);
    $medications1 = mysqli_real_escape_string($con, $_POST['medications1']);
    $surgeries1 = mysqli_real_escape_string($con, $_POST['surgeries1']);
    $previous_injuries1 = mysqli_real_escape_string($con, $_POST['previous_injuries1']);
    if ($role === "team") {
    // agar team hai toh values le lo
    $player2 = !empty($_POST['player2']) ? mysqli_real_escape_string($con, $_POST['player2']) : null;
    $dob2 = !empty($_POST['dob2']) ? mysqli_real_escape_string($con, $_POST['dob2']) : null;
    $height2 = !empty($_POST['height2']) ? mysqli_real_escape_string($con, $_POST['height2']) : null;
    $weight2 = !empty($_POST['weight2']) ? mysqli_real_escape_string($con, $_POST['weight2']) : null;
    $chronic_illness2 = !empty($_POST['chronic_illness2']) ? mysqli_real_escape_string($con, $_POST['chronic_illness2']) : null;
    $allergies2 = !empty($_POST['allergies2']) ? mysqli_real_escape_string($con, $_POST['allergies2']) : null;
    $medications2 = !empty($_POST['medications2']) ? mysqli_real_escape_string($con, $_POST['medications2']) : null;
    $surgeries2 = !empty($_POST['surgeries2']) ? mysqli_real_escape_string($con, $_POST['surgeries2']) : null;
    $previous_injuries2 = !empty($_POST['previous_injuries2']) ? mysqli_real_escape_string($con, $_POST['previous_injuries2']) : null;
} else {
    // agar sirf player hai toh NULL force kar do
    $player2 = $dob2 = $height2 = $weight2 = $chronic_illness2 = 
    $allergies2 = $medications2 = $surgeries2 = $previous_injuries2 = null;
}

    // Determine category based on role
    $category = ($role === "team") ? "double" : "single";

    // Insert query (updated to remove endurance and agility fields)
    $query = "INSERT INTO badminton_players (
    fullName, email, role, teamName, category,
    player1, dob1, height1, weight1, chronic_illness1,
    allergies1, medications1, surgeries1, previous_injuries1,
    player2, dob2, height2, weight2, chronic_illness2,
    allergies2, medications2, surgeries2, previous_injuries2
) VALUES (
    '$fullName', '$email', '$role', " . ($teamName ? "'$teamName'" : "NULL") . ", '$category',
    '$player1', '$dob1', '$height1', '$weight1', '$chronic_illness1',
    '$allergies1', '$medications1', '$surgeries1', '$previous_injuries1',
    " . ($player2 ? "'$player2'" : "NULL") . ",
    " . ($dob2 ? "'$dob2'" : "NULL") . ",
    " . ($height2 ? "'$height2'" : "NULL") . ",
    " . ($weight2 ? "'$weight2'" : "NULL") . ",
    " . ($chronic_illness2 ? "'$chronic_illness2'" : "NULL") . ",
    " . ($allergies2 ? "'$allergies2'" : "NULL") . ",
    " . ($medications2 ? "'$medications2'" : "NULL") . ",
    " . ($surgeries2 ? "'$surgeries2'" : "NULL") . ",
    " . ($previous_injuries2 ? "'$previous_injuries2'" : "NULL") . "
)";

    $execute = mysqli_query($con, $query);

    if ($execute) {
        // Redirect to avoid form resubmission
        header("Location: ".$_SERVER['PHP_SELF']."?success=1");
        exit();
    } else {
        echo "❌ Error: " . mysqli_error($con);
    }

    mysqli_close($con);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Badminton Registration Form</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-image: url(../images/badmintonpage.jpg);
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
      background-image: url(../images/badminton.jpeg);
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
      padding: 12px;
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
      font-size: 14px;
      margin-top: -5px;
      margin-bottom: 10px;
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
        <h2>Badminton Registration Form</h2>
        
        <div class="eligibility-link">
          <p>To view the detailed eligibility criteria <a href="batminton.html" style="color: #ff6b6b; font-weight: bold;">click here</a></p>
        </div>
        
        <div class="health-cert-notice">
            Players must bring their health certificate to the venue, otherwise they will be rejected.
        </div>
        
        <!-- Personal Information Section -->
        <div class="section-box">
            <h3>Personal Information</h3>
                <div class="grid-2">
                <div>
                    <label for="fullName" class="required">Full Name:</label>
                    <input type="text" id="fullName" name="fullName"  value="<?php echo htmlspecialchars($userFullName); ?>" required readonly>
                </div>
                <div>
                    <label for="email" class="required">Email Address:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userEmail); ?>" required readonly>
                </div>
            
            <div class="form-row">
              <div class="form-group">
                <label for="role">Role:</label>
                <select id="role" name="role" required onchange="togglePlayerFields()">
                  <option value="player">Player</option>
                  <option value="team">Team</option>
                </select>
              </div>
              
              <div class="form-group hidden" id="teamFields">
                <label for="teamName">Team Name:</label>
                <input type="text" id="teamName" name="teamName">
              </div>
            </div>
        </div>
        
        <!-- Player 1 Information Section -->
        <div class="section-box">
            <h3>Player 1 Information</h3>
            <div class="form-row">
              <div class="form-group">
                <label for="player1">Player 1 Name:</label>
                <input type="text" id="player1" name="player1" required>
              </div>
              
              <div class="form-group">
                <label for="dob1">Player 1 DoB:</label>
                <input type="date" id="dob1" name="dob1" required min="2002-01-01" max="2008-12-31">
                <div id="dob1Error" class="error-message"></div>
              </div>
            </div>
            
            <h4>Basic Health Information - Player 1</h4>
            <div class="form-row">
              <div class="form-group">
                <label for="height1">Height (cm):</label>
                <input type="number" id="height1" name="height1" required min="100" max="250">
              </div>
              
              <div class="form-group">
                <label for="weight1">Weight (kg):</label>
                <input type="number" id="weight1" name="weight1" required min="30" max="150">
              </div>
            </div>
            
            <h4>Medical History & Health Status - Player 1</h4>
            <div class="health-grid">
              <!-- Chronic Illness -->
              <div>
                <label>Chronic Illness:</label>
                <select name="chronic_illness_option1" class="illness-option" onchange="toggleIllnessField(this, '1')">
                  <option value="select">select</option>
                  <option value="yes">Yes</option>
                  <option value="no">No</option>
                </select>
                <div class="illness-details" id="illness-details-1" style="display: none;">
                  <input type="text" name="chronic_illness1" placeholder="Type of chronic illness" oninput="validateAlphabets(this)">
                </div>
              </div>

              <!-- Allergies -->
              <div>
                <label>Allergies:</label>
                <input type="text" name="allergies1" placeholder="Allergies (if any)" oninput="validateAlphabets(this)">
              </div>

              <!-- Current Medications -->
              <div>
                <label>Current Medications:</label>
                <select name="medications_option1" class="medication-option" onchange="toggleMedicationField(this, '1')">
                  <option value="select">select</option>
                  <option value="yes">Yes</option>
                  <option value="no">No</option>
                </select>
                <div class="medication-details" id="medication-details-1" style="display: none;">
                  <input type="text" name="medications1" placeholder="Type of medication" oninput="validateAlphabets(this)">
                </div>
              </div>

              <!-- Recent Surgeries -->
              <div>
                <label>Recent Surgeries:</label>
                <input type="text" name="surgeries1" placeholder="Type of surgery" oninput="validateAlphabets(this)">
              </div>

              <!-- Previous Injuries -->
              <div>
                <label>Previous Injuries:</label>
                <input type="text" name="previous_injuries1" placeholder="Type of injury" oninput="validateAlphabets(this)">
              </div>
            </div>
        </div>
        
        <!-- Player 2 Fields (Initially Hidden) -->
        <div id="player2Fields" class="hidden section-box">
            <h3>Player 2 Information</h3>
            <div class="form-row">
              <div class="form-group">
                <label for="player2">Player 2 Name:</label>
                <input type="text" id="player2" name="player2">
              </div>
              
              <div class="form-group">
                <label for="dob2">Player 2 DoB:</label>
                <input type="date" id="dob2" name="dob2" min="2002-01-01" max="2008-12-31">
                <div id="dob2Error" class="error-message"></div>
              </div>
            </div>
            
            <h4>Basic Health Information - Player 2</h4>
            <div class="form-row">
              <div class="form-group">
                <label for="height2">Height (cm):</label>
                <input type="number" id="height2" name="height2" min="100" max="250">
              </div>
              
              <div class="form-group">
                <label for="weight2">Weight (kg):</label>
                <input type="number" id="weight2" name="weight2" min="30" max="150">
              </div>
            </div>
            
            <h4>Medical History & Health Status - Player 2</h4>
            <div class="health-grid">
              <!-- Chronic Illness -->
              <div>
                <label>Chronic Illness:</label>
                <select name="chronic_illness_option2" class="illness-option" onchange="toggleIllnessField(this, '2')">
                  <option value="select">select</option>
                  <option value="yes">Yes</option>
                  <option value="no">No</option>
                </select>
                <div class="illness-details" id="illness-details-2" style="display: none;">
                  <input type="text" name="chronic_illness2" placeholder="Type of chronic illness" oninput="validateAlphabets(this)">
                </div>
              </div>

              <!-- Allergies -->
              <div>
                <label>Allergies:</label>
                <input type="text" name="allergies2" placeholder="Allergies (if any)" oninput="validateAlphabets(this)">
              </div>

              <!-- Current Medications -->
              <div>
                <label>Current Medications:</label>
                <select name="medications_option2" class="medication-option" onchange="toggleMedicationField(this, '2')">
                  <option value="select">select</option>
                  <option value="yes">Yes</option>
                  <option value="no">No</option>
                </select>
                <div class="medication-details" id="medication-details-2" style="display: none;">
                  <input type="text" name="medications2" placeholder="Type of medication" oninput="validateAlphabets(this)">
                </div>
              </div>

              <!-- Recent Surgeries -->
              <div>
                <label>Recent Surgeries:</label>
                <input type="text" name="surgeries2" placeholder="Type of surgery" oninput="validateAlphabets(this)">
              </div>

              <!-- Previous Injuries -->
              <div>
                <label>Previous Injuries:</label>
                <input type="text" name="previous_injuries2" placeholder="Type of injury" oninput="validateAlphabets(this)">
              </div>
            </div>
        </div>

        <button type="submit" name="submit">Register</button>
        <div style="text-align: center; margin-top: 15px;">
          <a href="../user/get_event.php" class="back-btn">⬅ Back</a>
        </div>
    </form>
  </div>  

<script>
    function togglePlayerFields() {
      const role = document.getElementById("role").value;
      const player2Fields = document.getElementById("player2Fields");
      const teamFields = document.getElementById("teamFields");
      
      if (role === "team") {
        player2Fields.classList.remove("hidden");
        teamFields.classList.remove("hidden");
        
        // Make Player 2 fields required
        document.getElementById("player2").required = true;
        document.getElementById("dob2").required = true;
        document.getElementById("height2").required = true;
        document.getElementById("weight2").required = true;
        document.getElementById("teamName").required = true;
      } else {
        player2Fields.classList.add("hidden");
        teamFields.classList.add("hidden");
        
        // Make Player 2 fields not required
        document.getElementById("player2").required = false;
        document.getElementById("dob2").required = false;
        document.getElementById("height2").required = false;
        document.getElementById("weight2").required = false;
        document.getElementById("teamName").required = false;
      }
    }

    // Function to validate only alphabets and spaces
    function validateAlphabets(input) {
        input.value = input.value.replace(/[^a-zA-Z\s]/g, '');
    }

    // Function to toggle chronic illness field
    function toggleIllnessField(select, playerNum) {
        const detailsDiv = document.getElementById(`illness-details-${playerNum}`);
        const inputField = detailsDiv.querySelector('input');
        
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
    function toggleMedicationField(select, playerNum) {
        const detailsDiv = document.getElementById(`medication-details-${playerNum}`);
        const inputField = detailsDiv.querySelector('input');
        
        if (select.value === 'yes') {
            detailsDiv.style.display = 'block';
            inputField.setAttribute('required', 'required');
        } else {
            detailsDiv.style.display = 'none';
            inputField.removeAttribute('required');
            inputField.value = '';
        }
    }

    function calculateAge(dob) {
      const birthDate = new Date(dob);
      const today = new Date();
      let age = today.getFullYear() - birthDate.getFullYear();
      const m = today.getMonth() - birthDate.getMonth();
      if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
      }
      return age;
    }

    // Validation functions
    function validateName(name) {
      return name.length >= 2 && /^[a-zA-Z\s]+$/.test(name);
    }

    function validateEmail(email) {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return re.test(email);
    }

    function validateForm(event) {
      let isValid = true;
      
      // Basic information validation
      const fullName = document.getElementById("fullName").value;
      if (!validateName(fullName)) {
        alert("Full name must be at least 2 characters and contain only letters");
        isValid = false;
      }
      
      const email = document.getElementById("email").value;
      if (!validateEmail(email)) {
        alert("Please enter a valid email address");
        isValid = false;
      }
      
      const role = document.getElementById("role").value;
      if (!role) {
        alert("Please select a role");
        isValid = false;
      }
      
      if (role === "team") {
        const teamName = document.getElementById("teamName").value;
        if (!validateName(teamName)) {
          alert("Team name must be at least 2 characters and contain only letters");
          isValid = false;
        }
      }
      
      // Player 1 validation
      const player1 = document.getElementById("player1").value;
      if (!validateName(player1)) {
        alert("Player 1 name must be at least 2 characters and contain only letters");
        isValid = false;
      }
      
      const dob1 = document.getElementById("dob1").value;
      const age1 = calculateAge(dob1);
      if (age1 < 16 || age1 > 22) {
        alert("Player 1 age must be between 16 and 22 years");
        isValid = false;
      }
      
      // Player 1 health validation
      const height1 = document.getElementById("height1").value;
      if (height1 < 100 || height1 > 250) {
        alert("Height must be between 100cm and 250cm");
        isValid = false;
      }
      
      const weight1 = document.getElementById("weight1").value;
      if (weight1 < 30 || weight1 > 150) {
        alert("Weight must be between 30kg and 150kg");
        isValid = false;
      }
      
      // Player 2 validation (if team role)
      if (role === "team") {
        const player2 = document.getElementById("player2").value;
        if (!validateName(player2)) {
          alert("Player 2 name must be at least 2 characters and contain only letters");
          isValid = false;
        }
        
        const dob2 = document.getElementById("dob2").value;
        const age2 = calculateAge(dob2);
        if (age2 < 16 || age2 > 22) {
          alert("Player 2 age must be between 16 and 22 years");
          isValid = false;
        }
        
        // Player 2 health validation
        const height2 = document.getElementById("height2").value;
        if (height2 < 100 || height2 > 250) {
          alert("Height must be between 100cm and 250cm");
          isValid = false;
        }
        
        const weight2 = document.getElementById("weight2").value;
        if (weight2 < 30 || weight2 > 150) {
          alert("Weight must be between 30kg and 150kg");
          isValid = false;
        }
      }

      if (!isValid) {
        event.preventDefault();
      }
    }

    // Initialize the form on page load
    document.addEventListener("DOMContentLoaded", function() {
      togglePlayerFields();
      
      document.getElementById("role").addEventListener("change", function() {
        togglePlayerFields();
      });
      
      document.getElementById("registrationForm").addEventListener("submit", validateForm);
    });
</script>
</body>
</html>