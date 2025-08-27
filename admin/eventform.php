<?php
session_start();
include_once('../include/db_connect.php');

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = mysqli_real_escape_string($con, $_POST['event_name']);
    $start_date = !empty($_POST['start_date']) ? date('Y-m-d', strtotime($_POST['start_date'])) : null;
    $end_date = !empty($_POST['end_date']) ? date('Y-m-d', strtotime($_POST['end_date'])) : null;
    $event_time = !empty($_POST['event_time']) ? mysqli_real_escape_string($con, $_POST['event_time']) : null;
    $event_location = mysqli_real_escape_string($con, $_POST['event_location']);
    $event_date = !empty($_POST['event_date']) ? date('Y-m-d', strtotime($_POST['event_date'])) : null;
    $sport = !empty($_POST['sports']) ? mysqli_real_escape_string($con, $_POST['sports']) : '';

    $sql = "INSERT INTO events (event_name, start_date, end_date, event_date, event_location, event_time, sport) 
            VALUES ('$event_name', '$start_date', '$end_date', '$event_date', '$event_location', '$event_time', '$sport')";

    if (mysqli_query($con, $sql)) {
        echo "<script>alert('Event created successfully'); window.location='manage-events.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($con) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Event Creation Form" />
    <meta name="author" content="" />
    <title>Create Event</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('../images/volleyballform.jpg');
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

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
        }

        .sport-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .sport-button {
            padding: 8px 16px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            background-color: #f3f4f6;
            color: #374151;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .sport-button.selected {
            background-color: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
        }

        .sport-button:hover {
            background-color: #e5e7eb;
        }

        .sport-button.selected:hover {
            background-color: #1d4ed8;
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
            width: 100%;
        }

        .submit-btn:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .error-text {
            color: #b91c1c;
            font-size: 13px;
            margin-top: 5px;
            display: block;
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

            .sport-button {
                font-size: 13px;
                padding: 6px 12px;
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

            .sport-buttons {
                flex-direction: column;
                gap: 8px;
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
                    <div class="event-container">
                        <div class="event-header">
                            <h1>Create a New Event</h1>
                            <a class="back-link" href="manage-events.php">Back to Events</a>
                        </div>
                        <form action="eventform.php" method="post" onsubmit="return validateForm()">
                            <div class="form-group">
                                <label for="event_name">Event Name</label>
                                <input type="text" class="form-control" id="event_name" name="event_name" required oninput="checkEventName()">
                                <small id="eventNameError" class="error-text"></small>
                            </div>
                            <div class="form-group">
                                <label for="start_date">Registration Start</label>
                                <input type="date" class="form-control" name="start_date" id="start_date" min="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d', strtotime('+1 year')); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="end_date">Registration End</label>
                                <input type="date" class="form-control" name="end_date" id="end_date" min="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d', strtotime('+1 year')); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="event_date">Event Start</label>
                                <input type="date" class="form-control" name="event_date" id="event_date" min="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d', strtotime('+1 year')); ?>" required>
                                <small id="eventDateError" class="error-text"></small>
                            </div>
                            <div class="form-group">
                                <label for="event_time">Starting Time</label>
                                <input type="time" class="form-control" name="event_time" id="event_time" required>
                                <small class="error-text">Starting time must be 9:00 AM</small>
                            </div>
                            <div class="form-group">
                                <label for="event_location">Location</label>
                                <input type="text" class="form-control" id="event_location" name="event_location" value="FG Kharian Women Sports Club" readonly>
                            </div>
                            <div class="form-group">
                                <label for="sports">Games</label>
                                <div class="sport-buttons" id="sport-buttons">
                                    <div class="sport-button" data-sport="Volleyball">Volleyball</div>
                                    <div class="sport-button" data-sport="Badminton">Badminton</div>
                                    <div class="sport-button" data-sport="Table Tennis">Table Tennis</div>
                                    <div class="sport-button" data-sport="Cricket">Cricket</div>
                                </div>
                                <input type="hidden" name="sports" id="sports-input">
                                <small id="sportsError" class="error-text"></small>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="submit-btn">Create Event</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="../assets/demo/chart-area-demo.js"></script>
    <script src="../assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
    <script src="../js/datatables-simple-demo.js"></script>
    <script>
        function checkEventName() {
            const eventName = document.getElementById("event_name").value.trim();
            const namePattern = /^[A-Za-z0-9 ]+$/;
            const error = document.getElementById("eventNameError");

            if (!namePattern.test(eventName)) {
                error.textContent = "Event name must contain only letters, numbers, or spaces.";
                return false;
            } else {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "check_event.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (this.readyState === 4 && this.status === 200) {
                        error.textContent = this.responseText === "exists" ? "This event name already exists!" : "";
                    }
                };
                xhr.send("event_name=" + encodeURIComponent(eventName));
                return true;
            }
        }

        function updateSportsInput() {
            const buttons = document.querySelectorAll('.sport-button');
            const selectedSports = Array.from(buttons)
                .filter(button => button.classList.contains('selected'))
                .map(button => button.getAttribute('data-sport'));
            document.getElementById('sports-input').value = selectedSports.join(', ');
            document.getElementById('sportsError').textContent = selectedSports.length === 0 ? "Please select at least one sport." : "";
        }

        document.querySelectorAll('.sport-button').forEach(button => {
            button.addEventListener('click', () => {
                button.classList.toggle('selected');
                updateSportsInput();
            });
        });

        function validateForm() {
            const isNameValid = checkEventName();
            const eventDate = document.getElementById("event_date").value;
            const dateError = document.getElementById("eventDateError");
            const selectedDate = new Date(eventDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (selectedDate < today) {
                dateError.textContent = "Please select a valid future date.";
                return false;
            } else {
                dateError.textContent = "";
            }

            const selectedSports = document.getElementById('sports-input').value;
            if (!selectedSports) {
                document.getElementById('sportsError').textContent = "Please select at least one sport.";
                return false;
            } else {
                document.getElementById('sportsError').textContent = "";
            }

            return isNameValid && document.getElementById("eventNameError").textContent === "";
        }
    </script>
</body>
</html>