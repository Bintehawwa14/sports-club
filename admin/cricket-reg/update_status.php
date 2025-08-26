<?php
include_once('../../include/db_connect.php');

if (isset($_POST['is_approved']) && isset($_POST['team_name']) && isset($_POST['game'])) {
    $team_name = mysqli_real_escape_string($con, $_POST['teamName']);
    $status = mysqli_real_escape_string($con, $_POST['is_approved']);
    $game = mysqli_real_escape_string($con, $_POST['game']); // cricket, badminton, etc.

    $player_table = $game . "_players";
    $team_table = $game . "_teams";

    // ✅ Case 1: Individual Player Approval
    if (isset($_POST['player1'])) {
        $player_name = mysqli_real_escape_string($con, $_POST['player_name']);
        $updatePlayer = "UPDATE $player_table SET is_approved='$status' 
                         WHERE player_name='$player_name' AND teamName='$team_name'";
        mysqli_query($con, $updatePlayer);

        // Ab team ka status check karo
        $checkPlayers = "SELECT COUNT(*) as pending_count 
                         FROM $player_table 
                         WHERE teamName='$team_name' AND is_approved='pending'";
        $res = mysqli_query($con, $checkPlayers);
        $row = mysqli_fetch_assoc($res);

        if ($row['pending_count'] > 0) {
            mysqli_query($con, "UPDATE $team_table SET is_approved='pending' WHERE teamName='$team_name'");
        } else {
            mysqli_query($con, "UPDATE $team_table SET is_approved='approved' WHERE teamName='$team_name'");
        }
    } 
    // ✅ Case 2: Full Team Approval directly from all_teams.php
    else {
        $updateTeam = "UPDATE $team_table SET is_approved='$status' WHERE teamName='$team_name'";
        mysqli_query($con, $updateTeam);

        // Agar admin ne full team approved ki, to sare players bhi approve ho jayein
        if ($status == 'approved') {
            mysqli_query($con, "UPDATE $player_table SET is_approved='approved' WHERE teamName='$team_name'");
        }
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
