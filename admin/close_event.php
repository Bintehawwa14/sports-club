<?php
session_start();
include_once('../include/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_id = $_POST['event_id'];
    $event_name = $_POST['event_name'];

    // Update event status
    $sql = "UPDATE events SET status='close' WHERE id='$event_id'";
    mysqli_query($con, $sql);

    // Save message in session
    $_SESSION['alert_message'] = "Registration is closed for $event_name";

    // Redirect back to index
    header("Location: ../index.php");
    exit();
}
?>
