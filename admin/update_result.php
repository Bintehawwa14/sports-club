<?php
include '../include/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $match_id = $_POST['match_id'];
    $winner_id = $_POST['winner_id'];
    $loser_id = $_POST['loser_id'];

    $stmt = $conn->prepare("UPDATE matches SET winner_id = ?, loser_id = ? WHERE id = ?");
    $stmt->bind_param("iii", $winner_id, $loser_id, $match_id);
    $stmt->execute();
    $stmt->close();

    echo "Match result updated successfully.<br>";
    echo "<a href='view_result.php?tournament_id=" . $_POST['tournament_id'] . "'>Back to Results</a>";
}
?>
