<?php
include_once('../../include/db_connect.php');

header('Content-Type: application/json');

if (isset($_POST['is_approved'], $_POST['team_name'], $_POST['game'])) {
    $team_name = $_POST['team_name'];
    $status = $_POST['is_approved'];
    $game = $_POST['game'];
    $response = ['success' => false, 'error' => '', 'team_status' => ''];

    if ($game !== 'volleyball') {
        $response['error'] = 'Invalid game type';
        echo json_encode($response);
        exit();
    }

    $team_table = "volleyball_teams";
    $player_table = "volleyball_players";

    // Individual player approval
    if (isset($_POST['player_name'])) {
        $player_name = $_POST['player_name'];

        $stmt = $con->prepare("UPDATE $player_table SET is_approved = ? WHERE player_name = ? AND team_name = ?");
        $stmt->bind_param("sss", $status, $player_name, $team_name);
        $success = $stmt->execute();

        if ($success) {
            // Check if any players are still pending
            $stmt_check = $con->prepare("SELECT COUNT(*) AS pending_count FROM $player_table WHERE team_name = ? AND is_approved = 'pending'");
            $stmt_check->bind_param("s", $team_name);
            $stmt_check->execute();
            $check_result = $stmt_check->get_result();
            $row = $check_result->fetch_assoc();

            // Update team status
            $team_status = ($row['pending_count'] > 0) ? 'pending' : 'approved';
            $stmt_team = $con->prepare("UPDATE $team_table SET is_approved = ? WHERE team_name = ?");
            $stmt_team->bind_param("ss", $team_status, $team_name);
            $team_success = $stmt_team->execute();

            if ($team_success) {
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