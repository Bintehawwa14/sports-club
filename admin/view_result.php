<?php
include 'db_connection.php';

$tournament_id = $_GET['tournament_id'];

$query = "SELECT m.*, t1.team_name AS team1, t2.team_name AS team2, w.team_name AS winner
          FROM matches m
          JOIN teams t1 ON m.team1_id = t1.id
          JOIN teams t2 ON m.team2_id = t2.id
          LEFT JOIN teams w ON m.winner_id = w.id
          WHERE m.tournament_id = ?
          ORDER BY m.round";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $tournament_id);
$stmt->execute();
$result = $stmt->get_result();

$round = 0;
while ($row = $result->fetch_assoc()) {
    if ($row['round'] != $round) {
        $round = $row['round'];
        echo "<h3>Round $round</h3>";
    }
    echo $row['team1'] . " vs " . $row['team2'];
    if ($row['winner']) {
        echo " → Winner: " . $row['winner'];
    }
    echo "<br>";
}
?>
