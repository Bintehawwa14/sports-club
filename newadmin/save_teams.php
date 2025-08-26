<?php
include '../include/db_connect.php';

$tournament_id = $_GET['tournament_id'] ?? null;

if (!$tournament_id) {
    echo "Tournament ID is missing.";
    exit;
}

// Fetch all rounds
$rounds_query = "SELECT DISTINCT round FROM matches WHERE tournament_id = ? ORDER BY id";
$stmt = $con->prepare($rounds_query);
if (!$stmt) {
    die("Prepare failed: " . $con->error);
}
$stmt->bind_param("i", $tournament_id);
$stmt->execute();
$rounds_result = $stmt->get_result();

while ($round_row = $rounds_result->fetch_assoc()) {
    $round = htmlspecialchars($round_row['round']);
    echo "<h3>$round</h3>";

    $matches_query = "
        SELECT m.id, t1.team_name AS team1, t2.team_name AS team2,
               w.team_name AS winner, l.team_name AS loser
        FROM matches m
        LEFT JOIN teams t1 ON m.team1_id = t1.id
        LEFT JOIN teams t2 ON m.team2_id = t2.id
        LEFT JOIN teams w ON m.winner_id = w.id
        LEFT JOIN teams l ON m.loser_id = l.id
        WHERE m.tournament_id = ? AND m.round = ?
    ";
    $stmt = $con->prepare($matches_query);
    if (!$stmt) {
        die("Prepare failed: " . $con->error);
    }
    $stmt->bind_param("is", $tournament_id, $round);
    $stmt->execute();
    $matches = $stmt->get_result();

    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Team 1</th><th>VS</th><th>Team 2</th><th>Winner</th><th>Loser</th></tr>";
    while ($match = $matches->fetch_assoc()) {
        echo "<tr>
            <td>" . htmlspecialchars($match['team1']) . "</td>
            <td>vs</td>
            <td>" . htmlspecialchars($match['team2']) . "</td>
            <td>" . htmlspecialchars($match['winner'] ?? '-') . "</td>
            <td>" . htmlspecialchars($match['loser'] ?? '-') . "</td>
        </tr>";
    }
    echo "</table><br>";
}
$stmt->close();
?>