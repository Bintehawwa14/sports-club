<?php
include_once('../../include/db_connect.php');

header('Content-Type: application/json'); // Set JSON header

if (isset($_POST['is_approved'], $_POST['team_name'])) {
    $team_name = mysqli_real_escape_string($con, $_POST['team_name']);
    $status = mysqli_real_escape_string($con, $_POST['is_approved']);
    $response = ['success' => false, 'error' => '', 'team_status' => ''];

    $team_table = "cricket_teams";
    $player_table = "cricket_players";

    // Approve individual player
    if (isset($_POST['player_name'])) {
        $player_name = mysqli_real_escape_string($con, $_POST['player_name']);

        $update = mysqli_query($con, "UPDATE $player_table 
                                     SET is_approved='$status' 
                                     WHERE player_name='$player_name' 
                                       AND team_name='$team_name'");

        if ($update) {
            // Check if any players are still pending
            $check = mysqli_query($con, "SELECT COUNT(*) AS pending_count 
                                         FROM $player_table 
                                         WHERE team_name='$team_name' 
                                           AND is_approved='pending'");
            $row = mysqli_fetch_assoc($check);

            // Update team status
            $team_status = ($row['pending_count'] > 0) ? 'pending' : 'approved';
            $team_update = mysqli_query($con, "UPDATE $team_table 
                                              SET is_approved='$team_status' 
                                              WHERE team_name='$team_name'");

            if ($team_update) {
                $response['success'] = true;
                $response['team_status'] = $team_status;
            } else {
                $response['error'] = 'Failed to update team status';
            }
        } else {
            $response['error'] = 'Failed to update player status';
        }
    } else {
        $response['error'] = 'Player name not provided';
    }

    echo json_encode($response);
    exit();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit();
}
?>