
<?php require 'include/db_connect.php';?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scheduled Matches</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Scheduled Matches</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Event Name</th>
                <th>Game</th>
                <th>Round</th>
                <th>Team 1</th>
                <th>Team 2</th>
                <th>Match Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $query = "SELECT event_name, game, round, team1_name, team2_name, match_date, match_status 
                  FROM matches 
                  WHERE match_status = 'Scheduled' 
                  ORDER BY match_date ASC";

        $result = $con->query($query);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        
                        <td>{$row['event_name']}</td>
                        <td>{$row['game']}</td>
                        <td>{$row['round']}</td>
                        <td>{$row['team1_name']}</td>
                        <td>{$row['team2_name']}</td>
                        <td>{$row['match_date']}</td>
                        <td>{$row['match_status']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='8' class='text-center'>No scheduled matches found</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>
</body>
    
</html>
