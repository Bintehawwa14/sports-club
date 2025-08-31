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
$event_id = (int)$_GET['event_id'];
$event_name = mysqli_real_escape_string($con, $_GET['event_name']);

$userid = $_SESSION['userid'];
$email = $_SESSION['email'];
$check = "SELECT * FROM cricket_teams WHERE email = '$email'";
$exist = mysqli_query($con, $check);

if ($exist && mysqli_num_rows($exist) > 0) {
    $row = mysqli_fetch_assoc($exist);
    $approved = $row['is_approved'];
    if ($approved == "approved") {
        header("Location: ../user/dashboard.php");
        exit();
    }else if ($approved=="pending"){
       echo "<script>
            alert('Your request for cricket is not approved yet!');
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
    $userFullName = $userData['fname'] . ' ' . $userData['lname'];  // combine first and last name
    $userEmail = $userData['email'];
    $isLoggedIn = true;
}
}

// Redirect if not logged in
if (!$isLoggedIn) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Team Information - Updated to match HTML form fields
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $teamName = $_POST['team_name'];
 $captainName = $_POST['captain_name'];
$viceCaptainName = $_POST['vice_captain_name'];

// Insert into cricket_teams
$stmt = $con->prepare("INSERT INTO cricket_teams 
    (full_name, email, team_name, captain_name, vice_captain_name, event_name) 
    VALUES (?, ?, ?, ?, ?, ?)");
                    
$stmt->bind_param("ssssss", $fullName, $email, $teamName, $captainName, $viceCaptainName, $event_name);
$stmt->execute();
    // Player Information - Updated to match HTML form fields
    if (isset($_POST['player-name'])) {
        $playerNames = $_POST['player-name'];
        $ages = $_POST['player-age'];
        $roles = $_POST['player-role'];
        $battingStyles = $_POST['player-batting-style'];
         $teamName = $_POST['team_name'];
        $bowlingStyles = $_POST['player-bowling-style'];
        $heights = $_POST['player-height'];
        $weights = $_POST['player-weight'];
        $disabilities = $_POST['player-disability'];

        for ($i = 0; $i < count($playerNames); $i++) {
        
    $stmt2 = $con->prepare("INSERT INTO cricket_players 
        (player_name, age, team_name, role, batting_style, bowling_style, height, weight, disability, event_name) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt2->bind_param(
    "sissssiiis",   // 10 fields
    $playerNames[$i],   // s
    $ages[$i],          // i
    $teamName,          // s
    $roles[$i],         // i
    $battingStyles[$i], // s
    $bowlingStyles[$i], // s
    $heights[$i],       // i
    $weights[$i],       // i
    $disabilities,      // s
    $event_name         // s
);
$stmt2->execute();

            $stmt2->execute();
        }
    }

    // Redirect to success page
    header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Cricket Registration Form</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-image: url(../images/cricketpage.jpg);
        margin: 0;
        padding: 20px;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .container {
        width: 95%;
        max-width: 1000px;
        background-image: url(../images/cricketform.jpg);
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #10a075ff;
        font-size: 28px;
        padding-bottom: 10px;
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
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }
    label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
        color: #333;
    }
    input, select, textarea {
        width: 100%;
        padding: 10px;
        font-size: 15px;
        border: 1px solid #ccc;
        border-radius: 6px;
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
    .players-container {
        max-height: 400px;
        overflow-y: auto;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #fff;
        margin-bottom: 15px;
    }
    .player-entry {
        border: 1px solid #eee;
        padding: 15px;
        border-radius: 8px;
        background: #f7f7f7;
        margin-bottom: 12px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .player-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    .player-entry input,
    .player-entry select {
        width: 100%;
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ddd;
        border-radius: 6px;
        height: 42px;
        background: #fff;
    }
    .player-entry input:focus,
    .player-entry select:focus {
        border-color: #2d88d2;
        outline: none;
        box-shadow: 0 0 5px rgba(45, 136, 210, 0.3);
    }
    .button-container {
        display: flex;
        gap: 15px;
        margin-top: 10px;
    }
    .add-player-btn, .remove-player-btn {
        background-color: #2d88d2;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 12px 20px;
        cursor: pointer;
        font-size: 15px;
        font-weight: bold;
        transition: background-color 0.3s;
        flex: 1;
    }
    .add-player-btn:hover, .remove-player-btn:hover {
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
        padding: 15px 25px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        border-radius: 8px;
        margin-top: 20px;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        background: linear-gradient(135deg, #218838, #1e7e34);
    }
    .back-btn {
        display: inline-block;
        padding: 12px 25px;
        margin-top: 20px;
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
        color: #d23d2d;
    }
    .error {
        color: #d23d2d;
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }
    .valid {
        border-color: #28a745 !important;
    }
    .invalid {
        border-color: #d23d2d !important;
    }
    @media (max-width: 768px) {
        .grid-2, .player-grid {
            grid-template-columns: 1fr;
        }
        .button-container {
            flex-direction: column;
        }
    }
</style>
</head>
<body>
<div class="container">
    <h2>Cricket Team Registration</h2>
    <form method="POST" id="cricketForm">
        
        <!-- Team Information -->
        <div class="form-section">
            <h3>Team Information</h3>
            <div class="grid-2">
                <div>
                    <label for="fullName" class="required">Full Name:</label>
                    <input type="text" id="fullName" name="fullName"  value="<?php echo htmlspecialchars($userFullName); ?>" required readonly>
                </div>
                <div>
                    <label for="email" class="required">Email Address:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userEmail); ?>" required readonly>
                </div>
                <div>
                    <label for="team_name" class="required">Team Name:</label>
                    <input type="text" id="team_name" name="team_name" maxlength="20" required>
                    <span class="error" id="team_name_error"></span>
                </div>
                <div>
                    <label for="captain_name" class="required">Captain Name:</label>
                    <input type="text" id="captain_name" name="captain_name" maxlength="20" required>
                    <span class="error" id="captain_name_error"></span>
                </div>
                <div>
                   <label for="viceCaptain" class="required">Vice-Captain Name:</label>
                   <input type="text" id="viceCaptain" name="vice_captain_name" maxlength="20" required>
                   <span class="error" id="vice_captain_name_error"></span>
                </div>
            </div>
        </div>
        
        <!-- Players Information -->
        <div class="form-section">
            <h3>Players Information</h3>
            <p>Add Team Players (Minimum 11, Maximum 15)</p>
            <div class="players-container" id="playersContainer">
                <!-- Player entries will be added here dynamically -->
            </div>

            <div class="button-container">
                <button type="button" class="add-player-btn" onclick="addPlayerField()">
                    + Add Player
                </button>
                <button type="button" class="remove-player-btn" onclick="removePlayerField()">
                    - Remove Player
                </button>
            </div>
        </div>
        
        <button type="submit" class="submit-btn">Submit Registration</button>
    </form>
    
    <a href="../user/join.php" class="back-btn">⬅ Back to Dashboard</a>
</div>

<script>
    let playerCount = 0;
    let playerFields = [];

    // Validation functions
    function validateName(input, errorElementId) {
        const value = input.value.trim();
        const errorElement = document.getElementById(errorElementId);
        
        if (value === '') {
            errorElement.textContent = 'This field is required';
            input.classList.add('invalid');
            input.classList.remove('valid');
            return false;
        }
        
        if (/\d/.test(value)) {
            errorElement.textContent = 'Name cannot contain numbers';
            input.classList.add('invalid');
            input.classList.remove('valid');
            return false;
        }
        
        if (value.length > 15) {
            errorElement.textContent = 'Name cannot exceed 15 characters';
            input.classList.add('invalid');
            input.classList.remove('valid');
            return false;
        }
        
        errorElement.textContent = '';
        input.classList.remove('invalid');
        input.classList.add('valid');
        return true;
    }
    
    function validatePlayerName(input, index) {
        const value = input.value.trim();
        const errorElement = document.getElementById(`player_name_error_${index}`);
        
        if (value === '') {
            errorElement.textContent = 'Player name is required';
            input.classList.add('invalid');
            input.classList.remove('valid');
            return false;
        }
        
        if (/\d/.test(value)) {
            errorElement.textContent = 'Player name cannot contain numbers';
            input.classList.add('invalid');
            input.classList.remove('valid');
            return false;
        }
        
        if (value.length > 20) {
            errorElement.textContent = 'Player name cannot exceed 20 characters';
            input.classList.add('invalid');
            input.classList.remove('valid');
            return false;
        }
        
        errorElement.textContent = '';
        input.classList.remove('invalid');
        input.classList.add('valid');
        return true;
    }
    
    function validateAge(input, index) {
        const value = parseInt(input.value);
        const errorElement = document.getElementById(`player_age_error_${index}`);
        
        if (isNaN(value)) {
            errorElement.textContent = 'Age is required';
            input.classList.add('invalid');
            input.classList.remove('valid');
            return false;
        }
        
        if (value < 16 || value > 22) {
            errorElement.textContent = 'Age must be between 16 and 22';
            input.classList.add('invalid');
            input.classList.remove('valid');
            return false;
        }
        
        errorElement.textContent = '';
        input.classList.remove('invalid');
        input.classList.add('valid');
        return true;
    }
    
    function validateSelect(input, index, fieldName) {
        const value = input.value;
        const errorElement = document.getElementById(`player_${fieldName}_error_${index}`);
        
        if (value === '') {
            errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)} is required`;
            input.classList.add('invalid');
            input.classList.remove('valid');
            return false;
        }
        
        errorElement.textContent = '';
        input.classList.remove('invalid');
        input.classList.add('valid');
        return true;
    }
    
    function validateNumber(input, index, fieldName, min, max) {
        const value = parseInt(input.value);
        const errorElement = document.getElementById(`player_${fieldName}_error_${index}`);
        
        if (isNaN(value)) {
            errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)} is required`;
            input.classList.add('invalid');
            input.classList.remove('valid');
            return false;
        }
        
        if (value < min || value > max) {
            errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)} must be between ${min} and ${max}`;
            input.classList.add('invalid');
            input.classList.remove('valid');
            return false;
        }
        
        errorElement.textContent = '';
        input.classList.remove('invalid');
        input.classList.add('valid');
        return true;
    }
    
    function validateDisability(input, index) {
        const value = input.value.trim();
        const errorElement = document.getElementById(`player_disability_error_${index}`);
        
        if (value === '') {
            errorElement.textContent = 'This field is required';
            input.classList.add('invalid');
            input.classList.remove('valid');
            return false;
        }
        
        errorElement.textContent = '';
        input.classList.remove('invalid');
        input.classList.add('valid');
        return true;
    }

    function addPlayerField() {
        if (playerCount >= 15) {
            alert("Maximum 15 players allowed!");
            return;
        }

        const playersContainer = document.getElementById("playersContainer");
        const playerDiv = document.createElement("div");
        playerDiv.className = "player-entry";
        playerDiv.innerHTML = `
            <h4>Player ${playerCount + 1}</h4>
            <div class="player-grid">
                <div>
                    <input type="text" name="player-name[]" placeholder="Player Name*" maxlength="20" required
                           oninput="validatePlayerName(this, ${playerCount})">
                    <span class="error" id="player_name_error_${playerCount}"></span>
                </div>
                <div>
                    <input type="number" name="player-age[]" placeholder="Age*" min="16" max="22" required
                           oninput="validateAge(this, ${playerCount})">
                    <span class="error" id="player_age_error_${playerCount}"></span>
                </div>
                <div>
                    <select name="player-role[]" required onchange="validateSelect(this, ${playerCount}, 'role')">
                        <option value="">Select Role*</option>
                        <option value="Batsman">Batsman</option>
                        <option value="Bowler">Bowler</option>
                        <option value="All-rounder">All-rounder</option>
                        <option value="Wicketkeeper">Wicketkeeper</option>
                    </select>
                    <span class="error" id="player_role_error_${playerCount}"></span>
                </div>
                <div>
                    <select name="player-batting-style[]" required onchange="validateSelect(this, ${playerCount}, 'batting_style')">
                        <option value="">Batting Style*</option>
                        <option value="Right-handed">Right-handed</option>
                        <option value="Left-handed">Left-handed</option>
                    </select>
                    <span class="error" id="player_batting_style_error_${playerCount}"></span>
                </div>
                <div>
                    <select name="player-bowling-style[]" required onchange="validateSelect(this, ${playerCount}, 'bowling_style')">
                        <option value="">Bowling Style*</option>
                        <option value="Fast">Fast</option>
                        <option value="Medium">Medium</option>
                        <option value="Spin">Spin</option>
                        <option value="None">Does not bowl</option>
                    </select>
                    <span class="error" id="player_bowling_style_error_${playerCount}"></span>
                </div>
                <div>
                    <input type="number" name="player-height[]" placeholder="Height (cm)*" min="150" max="220" required
                           oninput="validateNumber(this, ${playerCount}, 'height', 150, 220)">
                    <span class="error" id="player_height_error_${playerCount}"></span>
                </div>
                <div>
                    <input type="number" name="player-weight[]" placeholder="Weight (kg)*" min="40" max="120" required
                           oninput="validateNumber(this, ${playerCount}, 'weight', 40, 120)">
                    <span class="error" id="player_weight_error_${playerCount}"></span>
                </div>
                <div>
                    <input type="text" name="player-disability[]" placeholder="Disability(if any,specify)*" required
                           oninput="validateDisability(this, ${playerCount})">
                    <span class="error" id="player_disability_error_${playerCount}"></span>
                </div>
            </div>
        `;
        playersContainer.appendChild(playerDiv);
        playerFields.push(playerDiv);
        playerCount++;
    }

    function removePlayerField() {
        if (playerCount <= 11) {
            alert("At least 11 players are required.");
            return;
        }
        
        const playersContainer = document.getElementById("playersContainer");
        if (playersContainer.lastElementChild) {
            playersContainer.removeChild(playersContainer.lastElementChild);
            playerFields.pop();
            playerCount--;
        }
    }

    // Form Validation
    document.getElementById("cricketForm").addEventListener("submit", function(event){
        // Validate team information
        const isTeamNameValid = validateName(document.getElementById('team_name'), 'team_name_error');
        const isCaptainNameValid = validateName(document.getElementById('captain_name'), 'captain_name_error');
        const isViceCaptainNameValid = validateName(document.getElementById('viceCaptain'), 'vice_captain_name_error');
        
        // Validate all player fields
        let allPlayersValid = true;
        
        for (let i = 0; i < playerCount; i++) {
            const nameInput = document.querySelector(`input[name="player-name[]"]:nth-child(${i+1})`);
            const ageInput = document.querySelector(`input[name="player-age[]"]:nth-child(${i+1})`);
            const roleSelect = document.querySelector(`select[name="player-role[]"]:nth-child(${i+1})`);
            const battingSelect = document.querySelector(`select[name="player-batting-style[]"]:nth-child(${i+1})`);
            const bowlingSelect = document.querySelector(`select[name="player-bowling-style[]"]:nth-child(${i+1})`);
            const heightInput = document.querySelector(`input[name="player-height[]"]:nth-child(${i+1})`);
            const weightInput = document.querySelector(`input[name="player-weight[]"]:nth-child(${i+1})`);
            const disabilityInput = document.querySelector(`input[name="player-disability[]"]:nth-child(${i+1})`);
            
            const isNameValid = validatePlayerName(nameInput, i);
            const isAgeValid = validateAge(ageInput, i);
            const isRoleValid = validateSelect(roleSelect, i, 'role');
            const isBattingValid = validateSelect(battingSelect, i, 'batting_style');
            const isBowlingValid = validateSelect(bowlingSelect, i, 'bowling_style');
            const isHeightValid = validateNumber(heightInput, i, 'height', 150, 220);
            const isWeightValid = validateNumber(weightInput, i, 'weight', 40, 120);
            const isDisabilityValid = validateDisability(disabilityInput, i);
            
            if (!(isNameValid && isAgeValid && isRoleValid && isBattingValid && 
                  isBowlingValid && isHeightValid && isWeightValid && isDisabilityValid)) {
                allPlayersValid = false;
            }
        }
        
        // Check at least 11 players
        if (playerCount < 11) {
            alert("Minimum 11 players required!");
            event.preventDefault();
            return;
        }
        
        // Prevent form submission if any validation fails
        if (!(isTeamNameValid && isCaptainNameValid && isViceCaptainNameValid && allPlayersValid)) {
            event.preventDefault();
            alert("Please fix all validation errors before submitting.");
            return;
        }
        
        alert("Team Registration Successful! 🎉");
    });

    // Add event listeners for team name validation
    document.getElementById('team_name').addEventListener('input', function() {
        validateName(this, 'team_name_error');
    });
    
    document.getElementById('captain_name').addEventListener('input', function() {
        validateName(this, 'captain_name_error');
    });
    
    document.getElementById('viceCaptain').addEventListener('input', function() {
        validateName(this, 'vice_captain_name_error');
    });

    // Add initial player fields on page load
    window.onload = function() {
        // Add 11 player fields by default
        for (let i = 0; i < 1; i++) {
            addPlayerField();
        }
    };
</script>
</body>
</html>