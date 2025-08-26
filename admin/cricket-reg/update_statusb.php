<?php
include_once('../../include/db_connect.php');

if (isset($_POST['is_approved']) && isset($_POST['team_name']) && isset($_POST['game'])) {
    $team_name = mysqli_real_escape_string($con, $_POST['teamName']);
    $status = mysqli_real_escape_string($con, $_POST['is_approved']);
    $game = mysqli_real_escape_string($con, $_POST['game']); // volleyball

     $team_table =  "badminton_players";
    $player_table = $game . "_players";

    // Individual player approval
    if (isset($_POST['player_name'])) {
        $player_name = mysqli_real_escape_string($con, $_POST['player_name']);
        mysqli_query($con, "UPDATE $team_table SET is_approved='$status' 
                            WHERE player1='$player_name' AND teamName='$team_name'");
        
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
    // Optional: If you want a full team approval button, use this block
    elseif (isset($_POST['approve_team'])) {
        mysqli_query($con, "UPDATE $player_table SET is_approved='$status' WHERE teamName='$team_name'");
    }

       header("Location: all_teams.php");
    // Redirect back
    if (isset($_POST['redirect_back'])) {
        header("Location: " . $_POST['redirect_back']);
        exit();
    } 
} 
    
?>
