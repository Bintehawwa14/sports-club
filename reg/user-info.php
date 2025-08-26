<?php
if (!isset($_SESSION)) {
    session_start();
}
include_once('../include/db_connect.php');

if (isset($_SESSION['userid'])) {
    $userid = $_SESSION['userid'];
    $query = "SELECT fname, lname, email, contactno FROM users WHERE id = '$userid' LIMIT 1";
    $result = mysqli_query($con, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        $user_fullname = $row['fname'] . " " . $row['lname'];
        $user_email = $row['email'];
        $user_contact = $row['contactno'];
    } else {
        $user_fullname = "";
        $user_email = "";
        $user_contact = "";
    }
} else {
    $user_fullname = "";
    $user_email = "";
    $user_contact = "";
}
?>
