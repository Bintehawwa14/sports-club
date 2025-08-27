<?php
session_start();
include_once('../include/db_connect.php');

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Check if event ID is provided
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query = mysqli_query($con, "SELECT * FROM events WHERE id='$event_id'");
if (!$query) {
    die("Query failed: " . mysqli_error($con));
}
$event = mysqli_fetch_array($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Edit Event Page" />
    <meta name="author" content="" />
    <title>Edit Event</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('../images/alert.jpg');
            background-size: cover;
            background-position: top center;
            background-repeat: no-repeat;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        #layoutSidenav {
            display: flex;
            flex: 1;
        }

        #layoutSidenav_content {
            flex: 1;
            padding: 15px;
        }

        .container-fluid {
            max-width: 800px;
            margin: 0 auto;
        }

        .event-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            margin: 20px auto;
            width: 90%;
            max-width: 800px;
        }

        .event-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .event-header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #1a3c6d;
            margin: 0;
            letter-spacing: 0.3px;
        }

        .back-link {
            background-color: #6b7280;
            color: #ffffff;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .back-link:hover {
            background-color: #4b5563;
            transform: translateY(-1px);
        }

        .back-link:active {
            transform: translateY(0);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: 600;
            color: #1a3c6d;
            margin-bottom: 5px;
            display: block;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            color: #374151;
            background-color: #ffffff;
            box-sizing: border-box;
        }

        .form-group select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1rem;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
        }

        .submit-btn {
            background-color: #2563eb;
            color: #ffffff;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 500;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .submit-btn:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .alert {
            padding: 10px;
            background-color: #fef2f2;
            color: #b91c1c;
            margin: 15px auto;
            border-radius: 5px;
            text-align: center;
            font-size: 13px;
            max-width: 800px;
        }

        @media (max-width: 768px) {
            .event-container {
                width: 95%;
                padding: 15px;
                margin: 15px auto;
            }

            .event-header h1 {
                font-size: 20px;
            }

            .back-link,
            .submit-btn {
                padding: 7px 12px;
                font-size: 12px;
            }

            .form-group input,
            .form-group select {
                font-size: 13px;
                padding: 7px;
            }

            .container-fluid {
                padding: 10px;
            }
        }

        @media (max-width: 576px) {
            .event-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .back-link,
            .submit-btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <?php include_once('includes/navbar.php'); ?>
    <div id="layoutSidenav">
        <?php include_once('includes/sidebar.php'); ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <?php if ($event) { ?>
                    <div class="event-container">
                        <div class="event-header">
                            <h1>Edit Event: <?php echo htmlspecialchars($event['event_name']); ?></h1>
                            <a class="back-link" href="manage-events.php">Back to Events</a>
                        </div>
                        <form action="update-event.php" method="POST">
                            <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                            <div class="form-group">
                                <label for="event_name">Event Name</label>
                                <input type="text" id="event_name" name="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="event_date">Event Date</label>
                                <input type="date" id="event_date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="event_location">Event Location</label>
                                <input type="text" id="event_location" name="event_location" value="<?php echo htmlspecialchars($event['event_location']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="sport">Sport</label>
                                <input type="text" id="sport" name="sport" value="<?php echo htmlspecialchars($event['sport']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status" required>
                                    <option value="Active" <?php echo $event['status'] === 'Active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="Inactive" <?php echo $event['status'] === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    <option value="Cancelled" <?php echo $event['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($event['start_date']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="end_date">End Date</label>
                                <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($event['end_date']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="close">Close</label>
                                <input type="text" id="close" name="close" value="<?php echo htmlspecialchars($event['close']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="event_time">Event Time</label>
                                <input type="time" id="event_time" name="event_time" value="<?php echo htmlspecialchars($event['event_time']); ?>" required>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="submit-btn">Update Event</button>
                            </div>
                        </form>
                    </div>
                    <?php } else { ?>
                        <div class="alert">Event not found!</div>
                    <?php } ?>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>
</html>