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

// if (mysqli_num_rows($exist) > 0) {
//     echo "<script>
//             alert('Already registered for Cricket');
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
$stmt = $con->prepare("INSERT INTO cricket_teams (full_name, email, team_name,
                                                 captain_name, vice_captain_name) 
                       VALUES (?, ?, ?, ?, ?)");
                       
$stmt->bind_param("sssss", $fullName, $email, $teamName, $captainName, $viceCaptainName);
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
        (player_name, age, team_name, role, batting_style, bowling_style, height, weight, disability) 
        VALUES (?,?,?,?,?,?,?,?,?)");

    $stmt2->bind_param(
        "sisssiiis",   // 9 placeholders → string,int,string,string,string,int,int,int,string
        $playerNames[$i], 
        $ages[$i],  
        $teamName,      // team name is SAME for all players
        $roles[$i], 
        $battingStyles[$i], 
        $bowlingStyles[$i], 
        $heights[$i], 
        $weights[$i], 
        $disabilities[$i]
    );

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
        color: #1a2a6c;
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
                    <input type="text" id="team_name" name="team_name" required>
                  

                </div>
                <div>
                    <label for="captain_name" class="required">Captain Name:</label>
                    <input type="text" id="captain_name" name="captain_name" required>
                </div>
                <div>
                   <label for="viceCaptain" class="required">Vice-Captain Name:</label>
                   <input type="text" id="viceCaptain" name="vice_captain_name" required>
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
                    <input type="text" name="player-name[]" placeholder="Player Name*" required>
                </div>
                <div>
                    <input type="number" name="player-age[]" placeholder="Age*" min="16" max="22" required>
                </div>
                <div>
                    <select name="player-role[]" required>
                        <option value="">Select Role*</option>
                        <option value="Batsman">Batsman</option>
                        <option value="Bowler">Bowler</option>
                        <option value="All-rounder">All-rounder</option>
                        <option value="Wicketkeeper">Wicketkeeper</option>
                    </select>
                </div>
                <div>
                    <select name="player-batting-style[]" required>
                        <option value="">Batting Style*</option>
                        <option value="Right-handed">Right-handed</option>
                        <option value="Left-handed">Left-handed</option>
                    </select>
                </div>
                <div>
                    <select name="player-bowling-style[]" required>
                        <option value="">Bowling Style*</option>
                        <option value="Fast">Fast</option>
                        <option value="Medium">Medium</option>
                        <option value="Spin">Spin</option>
                        <option value="None">Does not bowl</option>
                    </select>
                </div>
                <div>
                    <input type="number" name="player-height[]" placeholder="Height (cm)*" min="150" max="220" required>
                </div>
                <div>
                    <input type="number" name="player-weight[]" placeholder="Weight (kg)*" min="40" max="120" required>
                </div>
                <div>
                    <input type="text" name="player-disability[]" placeholder="Disability(if any,specify)*" required>
                </div>
            </div>
        `;
        playersContainer.appendChild(playerDiv);
        playerCount++;
    }

    function removePlayerField() {
        if (playerCount <= 11) {
            alert("At least 11 player is required.");
            return;
        }
        
        const playersContainer = document.getElementById("playersContainer");
        if (playersContainer.lastElementChild) {
            playersContainer.removeChild(playersContainer.lastElementChild);
            playerCount--;
        }
    }

    // Form Validation
    document.getElementById("cricketForm").addEventListener("submit", function(event){
        // Check at least 6 players
        if (playerCount < 11) {
            alert("Minimum 11 players required! You have only added " + playerCount + " players.");
            event.preventDefault();
            return;
        }

        // Validate player names (no numbers)
        const nameInputs = document.querySelectorAll('input[name="player-name[]"]');
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
        const ageInputs = document.querySelectorAll('input[name="player-age[]"]');
        for (let ageInput of ageInputs) {
            const age = parseInt(ageInput.value);
            if (isNaN(age) || age < 16 || age > 22) {
                alert("Each player's age must be between 16 and 22 years.");
                ageInput.focus();
                event.preventDefault();
                return;
            }
        }
        
        alert("Team Registration Successful! 🎉");
    });

    // Add one player field on page load
    window.onload = function() {
        addPlayerField();
    };
</script>
</body>
</html>