<?php
// include '../include/db_connect.php';

// if (isset($_GET['id'])) {
//     $id = intval($_GET['id']);

//     // Event ko close mark karo
//     $update = mysqli_query($con, "UPDATE events SET is_closed='yes' WHERE id='$id'");

//     if ($update) {
//         header("Location: manage-events.php?msg=Event closed successfully");
//         exit;
//     } else {
//         echo "Error: " . mysqli_error($con);
//     }
// }
?>

<?php
include '../include/db_connect.php';

// Get today's date in YYYY-MM-DD format
$today = date('Y-m-d');

// Update all events where end_date = today and is_closed = 'no'
$update = $con->prepare("UPDATE events SET is_closed='yes', is_finished='yes' WHERE end_date=? AND is_closed='no'");
$update->bind_param("s", $today);

if ($update->execute()) {
    echo "Today's events have been closed successfully.";
} else {
    echo "Error: " . $con->error;
}
?>
