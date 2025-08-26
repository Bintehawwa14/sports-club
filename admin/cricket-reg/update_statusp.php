<?php
include_once('../../include/db_connect.php');

if (isset($_POST['is_approved']) && isset($_POST['team_name']) && isset($_POST['game'])) {

    $team_name = mysqli_real_escape_string($con, $_POST['team_name']); // fix here
    $status = mysqli_real_escape_string($con, $_POST['is_approved']);
    $game = mysqli_real_escape_string($con, $_POST['game']); // badminton, cricket, etc.

    $player_table = $game . "_players";

    // Case 1: Individual Player Approval
    if (isset($_POST['player_name'])) {
        $player_name = mysqli_real_escape_string($con, $_POST['player_name']);

        $updatePlayer = "UPDATE $player_table SET is_approved='$status' 
                         WHERE player_name='$player_name' AND teamName='$team_name'";
        mysqli_query($con, $updatePlayer);
    }
    // Case 2: Full Team Approval
    else {
        // Update all players of the team
        mysqli_query($con, "UPDATE $player_table SET is_approved='$status' WHERE teamName='$team_name'");
    }

    // Redirect back
    if (isset($_POST['redirect_back'])) {
        header("Location: " . $_POST['redirect_back']);
    } else {
        header("Location: all_teams.php");
    }
    exit();
}
?>
