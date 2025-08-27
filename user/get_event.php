<?php
session_start();
include_once('../include/db_connect.php');


// Check if user logged in
if (!isset($_SESSION['userid'])) {
    header('location:logout.php');
    exit;
}

// Fetch only latest active event
$query = "SELECT event_name, start_date, end_date, sport, event_date,event_location 
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

        <style>
body {
    margin: 0;
    padding: 0;
    background-image: url('../images/alert.jpg'); /* original bg */
    background-size: cover;
    background-position: top center;
    background-repeat: no-repeat;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    padding: 80px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.event-banner {
    position: relative;
    max-width: 700px;
    width: 100%;
    border-radius: 20px;
    background: rgba(30, 41, 59, 0.85); /* dark theme overlay */
    padding: 40px 30px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.6);
    backdrop-filter: blur(8px);
    text-align: center;
    color: #f8fafc; /* light text for dark bg */
    animation: fadeIn 1s ease forwards;
}

.event-banner h2 {
    font-size: 2.8rem;
    font-weight: 700;
    margin-bottom: 20px;
    color: #60a5fa; /* theme primary color */
    text-shadow: 1px 1px 4px rgba(0,0,0,0.6);
}

.event-banner p {
    font-size: 1.1rem;
    margin: 8px 0;
    line-height: 1.6;
}

.event-banner p i {
    margin-right: 8px;
    color: #3b82f6; /* accent for icons */
}

.join-btn {
    padding: 12px 28px;
    background: linear-gradient(45deg, #3b82f6, #2563eb); /* button gradient according to theme */
    border: none;
    border-radius: 50px;
    font-size: 1.1rem;
    color: white;
    cursor: pointer;
    margin-top: 20px;
    transition: all 0.3s ease;
    box-shadow: 0 6px 15px rgba(0,0,0,0.4);
}

.join-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.5);
}

.badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-top: 5px;
    color: white;
}

.badge.cricket { background-color: #10b981; }
.badge.volleyball { background-color: #f59e0b; }
.badge.badminton { background-color: #8b5cf6; }
.badge.tabletennis { background-color: #ef4444; }

@keyframes fadeIn {
    0% {opacity: 0; transform: translateY(20px);}
    100% {opacity: 1; transform: translateY(0);}
}

@media (max-width: 768px) {
    body { padding: 50px 20px; }
    .event-banner { padding: 30px 20px; }
    .event-banner h2 { font-size: 2rem; }
    .event-banner p { font-size: 1rem; }
    .join-btn { padding: 10px 20px; font-size: 1rem; }
}
</style>

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
                <p>📅 Event will start on : <?php echo date("F d, Y", strtotime($event['end_date'])); ?></p>
                <p>🏅 Sports included in this event are: <?php echo $event['sport']; ?></p>
                <p>📍 Venue: <?php echo $event['event_location']; ?></p>
                <p>📅 Registration is open from <?php echo date("F d, Y", 
                strtotime($event['start_date'])); ?> to <?php echo date("F d, Y", strtotime($event['end_date'])); ?>. 
                </p>
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
