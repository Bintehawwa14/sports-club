<?php
// Clear output buffer to prevent accidental output
ob_clean();

include_once('../../include/db_connect.php');

// Set JSON header
header('Content-Type: application/json');

$response = ['success' => false, 'error' => '', 'team_status' => ''];

try {
    // Check required POST parameters
    if (!isset($_POST['is_approved'], $_POST['team_name'], $_POST['game'])) {
        throw new Exception('Invalid request: Missing required parameters');
    }

    $team_name = $_POST['team_name'];
    $status = $_POST['is_approved'];
    $game = $_POST['game'];

    if ($game !== 'tabletennis') {
        throw new Exception('Invalid game type');
    }

    $player_table = "tabletennis_players";

    // Update team approval status in tabletennis_players
    $stmt = $con->prepare("UPDATE $player_table SET is_approved = ? WHERE teamName = ?");
    if (!$stmt) {
        throw new Exception('Failed to prepare update statement: ' . $con->error);
    }
    $stmt->bind_param("ss", $status, $team_name);
    $success = $stmt->execute();
    if (!$success) {
        throw new Exception('Failed to update team approval status: ' . $stmt->error);
    }

    // Check team status
    $stmt_check = $con->prepare("SELECT is_approved FROM $player_table WHERE teamName = ?");
    if (!$stmt_check) {
        throw new Exception('Failed to prepare check statement: ' . $con->error);
    }
    $stmt_check->bind_param("s", $team_name);
    $stmt_check->execute();
    $check_result = $stmt_check->get_result();
    $row = $check_result->fetch_assoc();

    $team_status = $row['is_approved'] ?? 'pending';

    $response['success'] = true;
    $response['team_status'] = $team_status;

} catch (Exception $e) {
    // Log error for debugging (adjust path to a valid writable path)
    error_log(date('Y-m-d H:i:s') . ' - ' . $e->getMessage() . PHP_EOL, 3, './error.log');
    $response['error'] = 'Server error occurred'; // Avoid exposing detailed errors in production
}

echo json_encode($response);
exit();
?>