<?php


// Latest event fetch karein
$sql_alert = "SELECT Event_Name, end_date, status FROM events ORDER BY id DESC LIMIT 1";
$result_alert = mysqli_query($con, $sql_alert);
$eventName = '';
$eventEndDate = '';
$eventStatus = '';

if ($result_alert && mysqli_num_rows($result_alert) > 0) {
    $row_alert = mysqli_fetch_assoc($result_alert);
    $eventName = htmlspecialchars($row_alert['Event_Name']);
    $eventEndDate = date('d M Y', strtotime($row_alert['end_date']));
    $eventStatus = strtolower($row_alert['status']);
}

?>

<div style="background-color: #e3f2fd; padding: 12px 15px; text-align: center; font-family: Arial, sans-serif; border-bottom: 2px solid #64b5f6;">
    <?php if (!empty($eventName) && $eventStatus === "active"): ?>
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
    <?php else: ?>
        <span style="font-size: 16px; font-weight: bold; color: #1976d2;">
            ⚠ Event is temporarily not available
        </span>
    <?php endif; ?>
</div>

