<?php
include_once('../include/db_connect.php');

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = intval($_GET['id']);
    $status = $_GET['status'] === 'active' ? 'active' : 'inactive';

    
        // Dusre sab ko inactive karo
        mysqli_query($con, "UPDATE events SET status = 'inactive'");


    // Selected event ka status update karo
    $query = "UPDATE events SET status = '$status' WHERE id = $id";
    
    if (mysqli_query($con, $query)) {
        header("Location: manage-events.php"); // wapas manage-event par
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }
} else {
    echo "Invalid request.";
}
?>
