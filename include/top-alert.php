<?php
include_once('include/db_connect.php'); // DB connection

// Latest event fetch karein
$sql = "SELECT Event_Name, end_date, status FROM events ORDER BY id DESC LIMIT 1";
$result = mysqli_query($con, $sql);
$eventName = '';
$eventEndDate = '';
$eventStatus = '';

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $eventName = htmlspecialchars($row['Event_Name']);
    $eventEndDate = date('d M Y', strtotime($row['end_date'])); // formatted date
    $eventStatus = strtolower($row['status']); // active ya inactive
}
?>

<div style="background-color: #ffecb3; padding: 10px 15px; text-align: center; font-family: Arial, sans-serif; border-bottom: 2px solid #f0ad4e;">
    <?php if (!empty($eventName) && $eventStatus === "active"): ?>
        <span style="font-size: 16px; font-weight: bold; color: #333;">
            📢 Registration open for <span style="color: #d9534f;"><?php echo $eventName; ?></span>!
            <br>
            <span style="font-size: 14px; color: #555;">Ends on: <?php echo $eventEndDate; ?></span>
        </span>
        <a href="login.php" 
           style="display: inline-block; margin-left: 10px; padding: 5px 12px; background-color: #f05a28; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;">
           Join Now
        </a>
    <?php else: ?>
        <span style="font-size: 16px; font-weight: bold; color: #a94442;">
            ⚠ Event is temporarily not available
        </span>
    <?php endif; ?>
</div>