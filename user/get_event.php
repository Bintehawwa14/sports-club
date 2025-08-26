<?php
session_start();
include_once('../include/db_connect.php');


// Check if user logged in
if (!isset($_SESSION['userid'])) {
    header('location:logout.php');
    exit;
}

// Fetch only latest active event
$query = "SELECT event_name, start_date, end_date, sport, event_location 
          FROM events 
          WHERE status = 'active'
          ORDER BY start_date DESC 
          LIMIT 1";

$result = mysqli_query($con, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    $event = false; // no event found
} else {
    $event = mysqli_fetch_assoc($result);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url('../images/alert.jpg');
            background-size: cover;
            background-position: top center;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
            padding: 80px;
        }

        .event-banner::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.5); /* fade overlay */
        }

        .event-content {
            position: relative;
            color: white;
            text-align: center;
            padding: 20px;
            z-index: 1;
        }

        .event-content h2 {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .event-content p {
            font-size: 1.2rem;
            margin: 5px 0;
        }

        .join-btn {
            padding: 10px 25px;
            background-color: #ff9800;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            color: white;
            cursor: pointer;
            margin-top: 15px;
            transition: 0.3s;
        }

        .join-btn:hover {
            background-color: #e68900;
        }
    </style>
</head>
<body class="sb-nav-fixed">
<?php include_once('includes/navbar.php'); ?>
<div id="layoutSidenav">
<?php include_once('includes/sidebar.php'); ?>
<div id="layoutSidenav_content">
<main>
    <div class="event-banner">
        <div class="event-content">
            <?php if ($event): ?>
                <h2><?php echo $event['event_name']; ?></h2>
                <p>🏅 Sport: <?php echo $event['sport']; ?></p>
                <p>📍 Location: <?php echo $event['event_location']; ?></p>
                <p>📅 Start Date: <?php echo date("F d, Y", strtotime($event['start_date'])); ?></p>
                <p>📅 End Date: <?php echo date("F d, Y", strtotime($event['end_date'])); ?></p>
                <button class="join-btn" onclick="window.location.href='join.php'">Join Now</button>
            <?php else: ?>
                <h2>Temporary this event is not available </h2>
                
            <?php endif; ?>
        </div>
    </div>
</main>
</div>
</div>
</body>
</html>
