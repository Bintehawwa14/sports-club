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

// Fetch event_name from events table using event_id
$event_id = mysqli_real_escape_string($con, $_GET['event_id'] ?? 0);
$event_name = "";
if ($event_id) {
    $eventQuery = mysqli_query($con, "SELECT event_name FROM events WHERE id='$event_id'");
    if ($eventRow = mysqli_fetch_assoc($eventQuery)) {
        $event_name = $eventRow['event_name'];
    } else {
        echo "<script>alert('Invalid event ID. Please select a valid event.'); window.location.href='../user/get_event.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('No event ID provided. Please select an event.'); window.location.href='../user/get_event.php';</script>";
    exit();
}

$userid = $_SESSION['userid'];
$email = $_SESSION['email'];

// Check if user already registered for cricket
$check = "SELECT * FROM cricket_teams WHERE email = '$email' AND event_name = '$event_name'";
$exist = mysqli_query($con, $check);

if ($exist && mysqli_num_rows($exist) > 0) {
    $row = mysqli_fetch_assoc($exist);
    $approved = $row['is_approved'];
    if ($approved == "approved") {
        header("Location: ../user/dashboard.php");
        exit();
    } else if ($approved == "pending") {
        echo "<script>
            alert('Your request for cricket is not approved yet!');
            window.location.href='../user/join.php?event_id=$event_id&event_name=".urlencode($event_name)."';</script>";
        exit();
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Team Information
    $fullName = mysqli_real_escape_string($con, $_POST['fullName']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $teamName = mysqli_real_escape_string($con, $_POST['team_name']);
    $captainName = mysqli_real_escape_string($con, $_POST['captain_name']);
    $viceCaptainName = mysqli_real_escape_string($con, $_POST['vice_captain_name']);

    // Check if team name already exists for this event
    $teamCheck = mysqli_query($con, "SELECT * FROM cricket_teams WHERE team_name = '$teamName' AND event_name = '$event_name'");
    if (mysqli_num_rows($teamCheck) > 0) {
        echo "<script>alert('⚠ Team name already exists for this event! Please choose a different name.'); window.location.href='" . $_SERVER['PHP_SELF'] . "?event_id=$event_id&event_name=".urlencode($event_name)."';</script>";
        exit();
    }

    // Insert into cricket_teams
    $stmt = $con->prepare("INSERT INTO cricket_teams (event_name, full_name, email, team_name, captain_name, vice_captain_name) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $event_name, $fullName, $email, $teamName, $captainName, $viceCaptainName);
    $stmt->execute();

    // Player Information
    if (isset($_POST['player-name'])) {
        $playerNames = $_POST['player-name'];
        $ages = $_POST['player-age'];
        $roles = $_POST['player-role'];
        $battingStyles = $_POST['player-batting-style'];
        $bowlingStyles = $_POST['player-bowling-style'];
        $heights = $_POST['player-height'];
        $weights = $_POST['player-weight'];
        $disabilities = $_POST['player-disability'];

        for ($i = 0; $i < count($playerNames); $i++) {
            $stmt2 = $con->prepare("INSERT INTO cricket_players (event_name, player_name, age, team_name, role, batting_style, bowling_style, height, weight, disability) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt2->bind_param("sissssiiis", $event_name, $playerNames[$i], $ages[$i], $teamName, $roles[$i], $battingStyles[$i], $bowlingStyles[$i], $heights[$i], $weights[$i], $disabilities[$i]);
            $stmt2->execute();
        }
    }

    // Redirect to success page
    header("Location: " . $_SERVER['PHP_SELF'] . "?success=1&event_id=$event_id&event_name=".urlencode($event_name));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Cricket Team Registration</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-image: url(../images/cricketpage.jpg)  center center / cover no-repeat;;
        margin: 0;
        padding: 20px;
        height: 100vh;
        width:100vw;
        display: flex;
        justify-content: center;
        align-items: center;}

    .container {
        width: 95%;
        max-width: 1000px;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        background: url(../images/cricketform.jpg) center center / cover no-repeat;
        background-attachment: fixed;
        height: 100%;
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
        <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
        <input type="hidden" name="event_name" value="<?php echo htmlspecialchars($event_name); ?>">

        <!-- Team Information -->
        <div class="form-section">
            <h3>Team Information</h3>
            <div class="grid-2">
                <div>
                    <label for="fullName" class="required">Full Name:</label>
                    <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($userFullName); ?>" required readonly>
                </div>
                <div>
                    <label for="email" class="required">Email Address:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userEmail); ?>" required readonly>
                </div>
                <div>
                    <label for="team_name" class="required">Team Name:</label>
                    <input type="text" id="team_name" name="team_name" required oninput="validateTeamName(this)">
                </div>
                <div>
                    <label for="captain_name" class="required">Captain Name:</label>
                    <input type="text" id="captain_name" name="captain_name" required>
                </div>
                <div>
                    <label for="vice_captain_name" class="required">Vice-Captain Name:</label>
                    <input type="text" id="vice_captain_name" name="vice_captain_name" required>
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
    
    <a href="../user/join.php?event_id=<?php echo $event_id; ?>&event_name=<?php echo urlencode($event_name); ?>" class="back-btn">⬅ Back</a>
</div>

<script>
    // Example list of existing team names (replace with actual data from database if needed)
    const existingTeamNames = ["Team A", "Team B", "Team C"]; // This should be dynamically populated from PHP

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
                    <input type="text" name="player-name[]" placeholder="Player Name*" required oninput="validatePlayerName(this)">
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
            alert("At least 11 players are required.");
            return;
        }
        
        const playersContainer = document.getElementById("playersContainer");
        if (playersContainer.lastElementChild) {
            playersContainer.removeChild(playersContainer.lastElementChild);
            playerCount--;
        }
    }

    function validatePlayerName(input) {
        let value = input.value.trim();
        value = value.replace(/[^a-zA-Z\s]/g, ''); // Allow only alphabets and spaces
        if (value.length < 3) {
            input.setCustomValidity("Name must be at least 3 characters long.");
        } else if (value.length > 15) {
            input.setCustomValidity("Name must not exceed 15 characters.");
        } else {
            input.setCustomValidity(""); // Clear validation message if valid
        }
        input.value = value;
    }

    function validateTeamName(input) {
        const teamName = input.value.trim();
        if (teamName && existingTeamNames.includes(teamName)) {
            alert("⚠ Team name '" + teamName + "' already exists! Please choose a different name.");
            input.value = ''; // Clear the input
            input.focus();
        }
    }

    // Form Validation
    document.getElementById("cricketForm").addEventListener("submit", function(event) {
        if (playerCount < 11) {
            alert("Minimum 11 players required! You have only added " + playerCount + " players.");
            event.preventDefault();
            return;
        }

        const nameInputs = document.querySelectorAll('input[name="player-name[]"]');
        for (let nameInput of nameInputs) {
            const nameValue = nameInput.value.trim();
            if (!/^[a-zA-Z\s]{3,15}$/.test(nameValue)) {
                alert("Player name must contain only alphabets and spaces, with 3-15 characters.");
                nameInput.focus();
                event.preventDefault();
                return;
            }
        }

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

        const teamName = document.getElementById("team_name").value.trim();
        if (existingTeamNames.includes(teamName)) {
            alert("⚠ Team name '" + teamName + "' already exists! Please choose a different name.");
            event.preventDefault();
            return;
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