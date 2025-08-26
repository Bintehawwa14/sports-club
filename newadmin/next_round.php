
<?php
include 'include/db_connect.php'; ;

$tournament_id = $_GET['tournament_id'] ?? null;
$current_round = $_GET['current_round'] ?? null;

if (!$tournament_id || !$current_round) {
    echo "Missing tournament ID or current round.";
    exit;
}

// Get winners from current round
$winner_query = "SELECT winner_id FROM matches WHERE tournament_id = ? AND round = ? AND winner_id IS NOT NULL";
$stmt = $conn->prepare($winner_query);
$stmt->bind_param("is", $tournament_id, $current_round);
$stmt->execute();
$result = $stmt->get_result();

$winner_ids = [];
while ($row = $result->fetch_assoc()) {
    $winner_ids[] = $row['winner_id'];
}
$stmt->close();

if (count($winner_ids) < 2) {
    echo "Not enough winners to generate next round.";
    exit;
}

shuffle($winner_ids); // Shuffle to randomize next round matchups

// Determine next round name
$round_names = ["First Round", "Quarterfinal", "Semifinal", "Final"];
$current_index = array_search($current_round, $round_names);
$next_round = $round_names[$current_index + 1] ?? null;

if (!$next_round) {
    echo "Final round already reached.";
    exit;
}

// Generate next round matches
for ($i = 0; $i < count($winner_ids); $i += 2) {
    if (!isset($winner_ids[$i + 1])) break;

    $team1_id = $winner_ids[$i];
    $team2_id = $winner_ids[$i + 1];

    $insert = "INSERT INTO matches (tournament_id, team1_id, team2_id, round, bracket_type)
               VALUES (?, ?, ?, ?, 'Single Elimination')";
    $stmt = $conn->prepare($insert);
    $stmt->bind_param("iiis", $tournament_id, $team1_id, $team2_id, $next_round);
    $stmt->execute();
    $stmt->close();
}

echo "<h3>$next_round Matches Generated Successfully!</h3>";
echo "<a href='view_result.php?tournament_id=$tournament_id'>View Results</a>";
?>
