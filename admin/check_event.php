<?php
include_once('../include/db_connect.php');

if (isset($_POST['event_name'])) {
    $event_name = mysqli_real_escape_string($con, $_POST['event_name']);

    $query = "SELECT id FROM events WHERE event_name = '$event_name' LIMIT 1";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        echo "exists";
    } else {
        echo "available";
    }
}
?>
