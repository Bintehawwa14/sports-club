<?php
require '../include/db_connect.php';

if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $sql = "SELECT event_name, sport, start_date, end_date, event_location, status, close 
            FROM events WHERE id = $id";
    $result = mysqli_query($con, $sql);

    if($row = mysqli_fetch_assoc($result)){
        echo "<table>
                <tr>
                  <th>Event Name</th>
                  <td>{$row['event_name']}</td>
                </tr>
                <tr>
                  <th>Sport </th>
                  <td>{$row['sport']}</td>
                </tr>
                <tr>
                  <th>Start Date</th>
                  <td>".date("F d, Y", strtotime($row['start_date']))."</td>
                </tr>
                <tr>
                  <th>End Date </th>
                  <td>".date("F d, Y", strtotime($row['end_date']))."</td>
                </tr>
                <tr>
                  <th>Location </th>
                  <td>{$row['event_location']}</td>
                </tr>
                <tr>
                  <th>Status </th>
                  <td>{$row['status']}</td>
                </tr>
                <tr>
                  <th>Close </th>
                  <td>".($row['close'] ? "Yes " : "No ")."</td>
                </tr>
              </table>";
    } else {
        echo "<p>No details found.</p>";
    }
}
?>
