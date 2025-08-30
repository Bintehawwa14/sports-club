```php
<?php
session_start();
include_once('../include/db_connect.php');

// Check if user logged in
if (!isset($_SESSION['userid'])) {
    header('location:logout.php');
    exit;
}

// Fetch all active events
$query = "SELECT id, event_name, start_date, end_date, sport, event_date, event_location 
          FROM events 
          WHERE status = 'active' AND is_closed='no'";

$result = mysqli_query($con, $query);

$events = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
    }
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
            background: url('../images/alert.jpg') no-repeat center top/cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 50px;
        }

        .event-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .event-card {
            background: rgba(30, 41, 59, 0.85);
            border-radius: 15px;
            padding: 20px;
            color: #f8fafc;
            box-shadow: 0 6px 15px rgba(0,0,0,0.5);
            text-align: center;
            backdrop-filter: blur(6px);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.6);
        }

        .event-card h2 {
            font-size: 1.4rem;
            margin-bottom: 12px;
            color: #60a5fa;
        }

        .event-card p {
            font-size: 0.95rem;
            margin: 6px 0;
        }

        .join-btn {
            padding: 8px 18px;
            background: linear-gradient(45deg, #3b82f6, #2563eb);
            border: none;
            border-radius: 25px;
            font-size: 0.9rem;
            color: white;
            cursor: pointer;
            margin-top: 12px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .join-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.4);
        }
    </style>
</head>
<body class="sb-nav-fixed">
<?php include_once('includes/navbar.php'); ?>
<div id="layoutSidenav">
<?php include_once('includes/sidebar.php'); ?>
<div id="layoutSidenav_content">
<main>
    <div class="event-container">
        <?php if (!empty($events)): ?>
            <?php foreach ($events as $event): ?>
                <div class="event-card">
                    <h2><?php echo htmlspecialchars($event['event_name']); ?></h2>
                    <p>📅 Starts: <?php echo date("M d, Y", strtotime($event['end_date'])); ?></p>
                    <p>🏅 Sports: <?php echo htmlspecialchars($event['sport']); ?></p>
                    <p>📍 Venue: <?php echo htmlspecialchars($event['event_location']); ?></p>
                    <p>📝 Reg: <?php echo date("M d", strtotime($event['start_date'])); ?> - <?php echo date("M d, Y", strtotime($event['end_date'])); ?></p>
                    <a href="join.php?event_id=<?php echo $event['id']; ?>&event_name=<?php echo urlencode($event['event_name']); ?>" class="join-btn">
                        Join Now
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="event-card">
                <h2>No active events available</h2>
            </div>
        <?php endif; ?>
    </div>
</main>
</div>
</div>
</body>
</html>
```