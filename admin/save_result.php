<?php
include 'db_connection.php';

$match_id = $_POST['match_id'];
$winner_id = $_POST['winner_id'];

$get = "SELECT team1_id, team2_id FROM matches WHERE id = ?";
$stmt = $conn->prepare($get);
$stmt->bind_param("i", $match_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

$loser_id = ($row['team1_id'] == $winner_id) ? $row['team2_id'] : $row['team1_id'];

$update = "UPDATE matches SET winner_id = ?, loser_id = ? WHERE id = ?";
$stmt = $conn->prepare($update);
$stmt->bind_param("iii", $winner_id, $loser_id, $match_id);
$stmt->execute();

echo "Result saved.";
?>

