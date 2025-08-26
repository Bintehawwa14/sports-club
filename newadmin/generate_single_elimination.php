<?php
include '../include/db_connect.php';

// Step 1: Get data from URL
$tournament_type = $_GET['tournament_type'];
$game_id = $_GET['game_id'];
$round = $_GET['round'];
$number_of_teams = $_GET['number_of_teams'];
$players_per_team = $_GET['players_per_team'];

// Step 2: Get latest tournament ID
$tournament_query = "SELECT id, tournament_type, game_id, round, number_of_teams, players_per_team FROM tournament WHERE tournament_type = ? AND game_id = ? AND round = ?";
$stmt = $con->prepare($tournament_query);
if (!$stmt) {
    die("Prepare failed: " . $con->error);
}
$stmt->bind_param("sii", $tournament_type, $game_id, $round);
$stmt->execute();
$tournament_result = $stmt->get_result();
$tournament = $tournament_result->fetch_assoc();

if (!$tournament) {
    echo "Tournament not found.";
    exit;
}
$tournament_id = $tournament['id'];

// Step 3: Get all team IDs for this tournament
$teams_query = "SELECT id FROM teams WHERE tournament_id = ?";
$stmt = $con->prepare($teams_query);
if (!$stmt) {
    die("Prepare failed: " . $con->error);
}
$stmt->bind_param("i", $tournament_id);
$stmt->execute();
$teams_result = $stmt->get_result();

$team_ids = [];
while ($row = $teams_result->fetch_assoc()) {
    $team_ids[] = $row['id'];
}

$total_teams = count($team_ids);

if ($total_teams < 2) {
    echo "Not enough teams to schedule matches.";
    exit;
}

shuffle($team_ids); // Step 4: Randomize teams

// Step 5: Create matches for the first round
$round_name = "First Round";

for ($i = 0; $i < $total_teams; $i += 2) {
    if (!isset($team_ids[$i + 1])) {
        // Odd team out – you can handle by giving a bye if you want
        break;
    }

    $team1_id = $team_ids[$i];
    $team2_id = $team_ids[$i + 1];

    $insert_match = "INSERT INTO matches (tournament_id, team1_id, team2_id, round, bracket_type)
                     VALUES (?, ?, ?, ?, 'Single Elimination')";
    $stmt = $con->prepare($insert_match);
    if (!$stmt) {
        die("Prepare failed: " . $con->error);
    }
    $stmt->bind_param("iiis", $tournament_id, $team1_id, $team2_id, $round_name);
    $stmt->execute();
}

echo "<h3>Matches Scheduled Successfully for $round_name!</h3>";
echo "<a href='view_result.php?tournament_id=$tournament_id'>View Results</a>";
?>