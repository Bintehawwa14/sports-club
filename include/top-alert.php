<?php
// Fetch the latest event that is not closed
$sql_alert = "SELECT event_name, end_date FROM events WHERE is_closed='no' && status ='active' LIMIT 1";
$result_alert = mysqli_query($con, $sql_alert);

if ($result_alert && mysqli_num_rows($result_alert) > 0) {
    $row_alert = mysqli_fetch_assoc($result_alert);
    $eventName = htmlspecialchars($row_alert['event_name']);
    $eventEndDate = date('d M Y', strtotime($row_alert['end_date']));
    ?>
    <div style="background-color: #e3f2fd; padding: 12px 15px; text-align: center; font-family: Arial, sans-serif; border-bottom: 2px solid #64b5f6;">
        <span style="font-size: 16px; font-weight: bold; color: #0d47a1;">
            📢 Registration open for 
            <span style="color: #1976d2;"><?php echo $eventName; ?></span>!
            <br>
            <span style="font-size: 14px; color: #555;">Ends on: <?php echo $eventEndDate; ?></span>
        </span>
        <a href="login.php" 
           style="display: inline-block; margin-left: 10px; padding: 6px 14px; background-color: #42a5f5; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; transition: 0.3s;">
           Join Now
        </a>
    </div>
<?php
} // If no event with is_closed='no', nothing is displayed
?>
