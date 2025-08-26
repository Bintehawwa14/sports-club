<?php
include_once('../../include/db_connect.php');

if (isset($_POST['is_approved']) && isset($_POST['team_name']) && isset($_POST['game'])) {
    $team_name = mysqli_real_escape_string($con, $_POST['team_name']);
    $status = mysqli_real_escape_string($con, $_POST['is_approved']);
    $game = mysqli_real_escape_string($con, $_POST['game']); // volleyball

     $team_table = $game . "_teams";
    $player_table = $game . "_players";

    // Individual player approval
    if (isset($_POST['player_name'])) {
        $player_name = mysqli_real_escape_string($con, $_POST['player_name']);
        mysqli_query($con, "UPDATE $player_table SET is_approved='$status' 
                            WHERE player_name='$player_name' AND team_name='$team_name'");
        
         $checkPlayers = "SELECT COUNT(*) as pending_count 
                         FROM $player_table 
                         WHERE team_name='$team_name' AND is_approved='pending'";
        $res = mysqli_query($con, $checkPlayers);
        $row = mysqli_fetch_assoc($res);

     if ($row['pending_count'] > 0) {
            mysqli_query($con, "UPDATE $team_table SET is_approved='pending' WHERE team_name='$team_name'");
        } else {
            mysqli_query($con, "UPDATE $team_table SET is_approved='approved' WHERE team_name='$team_name'");
        }
    }
    // Optional: If you want a full team approval button, use this block
    elseif (isset($_POST['approve_team'])) {
        mysqli_query($con, "UPDATE $player_table SET is_approved='$status' WHERE team_name='$team_name'");
    }

       header("Location: all_teams.php");
    // Redirect back
    if (isset($_POST['redirect_back'])) {
        header("Location: " . $_POST['redirect_back']);
        exit();
    } 
} 
    
?>
