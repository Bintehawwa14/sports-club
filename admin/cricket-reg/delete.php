<?php
include_once('../../include/db_connect.php');

if (!isset($_GET['game'])) {
    echo "Invalid request: game missing";
    exit;
}

$game = mysqli_real_escape_string($con, $_GET['game']);

// ✅ Table mapping
if ($game == 'cricket') {
    $teamTable   = "cricket_teams";
    $playerTable = "cricket_players";
} elseif ($game == 'badminton') {
    $teamTable   = null;
    $playerTable = "badminton_players";
} elseif ($game == 'tabletennis') {
    $teamTable   = null;
    $playerTable = "tabletennis_players";
} else {
    echo "Invalid game selected.";
    exit;
}

// ✅ Delete cricket team + its players
if ($game == 'cricket' && isset($_GET['teamName']) && !isset($_GET['playerName'])) {
    $teamName = mysqli_real_escape_string($con, $_GET['teamName']);

    // delete from teams
    $sqlTeam = "DELETE FROM cricket_teams WHERE team_name='$teamName'";
    mysqli_query($con, $sqlTeam);

    // delete all players of that team
    $sqlPlayers = "DELETE FROM cricket_players WHERE team_name='$teamName'";
    mysqli_query($con, $sqlPlayers);

    $type = 'team';
    $sql = null; // already executed separately
}

// ✅ Delete badminton & tabletennis player
// ✅ Delete badminton players (category required)
elseif ($game == 'badminton') {
    if (!isset($_GET['email'], $_GET['category'])) {
        echo "Missing parameters for badminton player.";
        exit;
    }
    $email    = mysqli_real_escape_string($con, $_GET['email']);
    $category = mysqli_real_escape_string($con, $_GET['category']);

    $sql = "DELETE FROM badminton_players 
            WHERE email='$email' AND category='$category'";
    $type = 'player';
}

// ✅ Delete table tennis players (role required)
elseif ($game == 'tabletennis') {
    if (!isset($_GET['email'], $_GET['role'])) {
        echo "Missing parameters for table tennis player.";
        exit;
    }
    $email = mysqli_real_escape_string($con, $_GET['email']);
    $role  = mysqli_real_escape_string($con, $_GET['role']);

    $sql = "DELETE FROM tabletennis_players 
            WHERE email='$email' AND role='$role'";
    $type = 'player';
}
// ✅ Delete cricket player individually
elseif ($game == 'cricket' && isset($_GET['playerName'], $_GET['teamName'])) {
    $playerName = mysqli_real_escape_string($con, $_GET['playerName']);
    $teamName   = mysqli_real_escape_string($con, $_GET['teamName']);

    $sql = "DELETE FROM cricket_players WHERE player_name='$playerName' AND team_name='$teamName'";
    $type = 'player';
}

else {
    echo "Invalid request: missing or wrong parameters.";
    exit;
}

// ✅ Execute query (if $sql exists)
if (!empty($sql)) {
    if (mysqli_query($con, $sql)) {
        header("Location: c_teams.php?game=$game");
        exit;
    } else {
        echo "Error deleting $type: " . mysqli_error($con);
    }
} else {
    header("Location: c_teams.php?game=$game");
    exit;
}
?>
