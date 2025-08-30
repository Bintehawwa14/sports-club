<?php
include '../include/db_connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Event ko close mark karo
    $update = mysqli_query($con, "UPDATE events SET is_closed='yes' WHERE id='$id'");

    if ($update) {
        header("Location: manage-events.php?msg=Event closed successfully");
        exit;
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>
