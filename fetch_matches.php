<?php
include 'include/db_connect.php';

$event = $_GET['event'];
$sport = $_GET['sport'];
$round = $_GET['round'];

$sql = "SELECT * FROM matches 
        WHERE event_name='$event' 
        AND game='$sport' 
        AND round='$round'
        ORDER BY match_date ASC";

$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<table border='1' cellpadding='8'>
            <tr>
              
              <th>Event</th>
              <th>Sport</th>
              <th>Round</th>
              <th>Team A</th>
              <th>Team B</th>
              <th>Date</th>
            </tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
              
                <td>".$row['event_name']."</td>
                <td>".$row['game']."</td>
                <td>".$row['round']."</td>
                <td>".$row['team1_name']."</td>
                <td>".$row['team2_name']."</td>
                <td>".$row['match_date']."</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<div class='empty'>No matches found for selected filters.</div>";
}
?>

