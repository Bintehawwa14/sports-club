<?php
session_start();
include_once('../include/db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = $_POST['event_name'];
    $start_date = !empty($_POST['start_date']) ? date('Y-m-d', strtotime($_POST['start_date'])) : null;
    $end_date = !empty($_POST['end_date']) ? date('Y-m-d', strtotime($_POST['end_date'])) : null;
    $event_time = !empty($_POST['event_time']) ? $_POST['event_time'] : null; // ✅ new field
    $event_location = $_POST['event_location'];
    $sport = implode(", ", $_POST['sport'] ?? []);

    $sql = "INSERT INTO events (event_name, start_date, end_date, event_location, event_time,sport) 
            VALUES ('$event_name', '$start_date', '$end_date', '$event_location', '$event_time','$sport')";

    if (mysqli_query($con, $sql)) {
        echo "<script>alert('Event created successfully'); window.location='manage-events.php';</script>";
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
       
    <title>Event Creation Form</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <?php include_once('includes/navbar.php'); ?>
    <?php include_once('includes/sidebar.php');?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <div class="card mb-4 mx-auto event-card">
                        <div class="card-body">
                                  <h1 class="mt-4">Create a New Event</h1>
                    <form action="eventform.php" method="post" onsubmit="return validateForm()">

                     <div class="form-group">
                        <label for="event_name">Event Name</label>
                        <input type="text" class="form-control" id="event_name" name="event_name" required oninput="checkEventName()">
                        <small id="eventNameError" style="color: red;"></small>
                    </div>
                    
                       <label>Starting Date:</label>
                       <input type="date" class="form-control" name="start_date" id="start_date"
                        min="<?php echo date('Y-m-d'); ?>" 
                        max="<?php echo date('Y-m-d', strtotime('+1 year')); ?>" required>

                       <label>End Date:</label>
                       <input type="date" class="form-control" name="end_date" id="end_date"
                        min="<?php echo date('Y-m-d'); ?>" 
                        max="<?php echo date('Y-m-d', strtotime('+1 year')); ?>" required>

                        <label>Starting Time:</label>
                        <input type="time" class="form-control" name="event_time" id="event_time" required>
                        <small style="color: red;">Starting time must be 9:00 AM</small>


                        <label for="event_location">Location:</label>
                        <input type="text" id="event_location" name="event_location" value="FG Kharian Women Sports Club" class="form-control" readonly>
                


                        <label>Games:</label>
                         <div class="form-check">
                         <input type="checkbox" name="sport[]" value="Volleyball" class="form-check-input">
                         <label class="form-check-label">Volleyball</label>
                         </div>
                         <div class="form-check">
                         <input type="checkbox" name="sport[]" value="Badminton" class="form-check-input">
                        <label class="form-check-label">Badminton</label>
                        </div>
                        <div class="form-check">
                        <input type="checkbox" name="sport[]" value="Table Tennis" class="form-check-input">
                        <label class="form-check-label">Table Tennis</label>
                        </div>
                        <div class="form-check">
                        <input type="checkbox" name="sport[]" value="Cricket" class="form-check-input">
                        <label class="form-check-label">Cricket</label>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3" href="index.php">Create Event</button>
                        </form>
                        </div>
                    </div>
                </div>
            </main>
          
        </div>
    </div>
    <script>
 function checkEventName() {
    const eventName = document.getElementById("eventName").value.trim();
    const namePattern = /^[A-Za-z0-9 ]+$/; // allows letters, numbers, and spaces
    const error = document.getElementById("eventNameError");

    if (!namePattern.test(eventName)) {
        error.textContent = "Event name must contain only letters, numbers, or spaces.";
        return false;
    } else {
        error.textContent = "";
        return true;
    }

  }

  function validateForm() {
    const isNameValid = checkEventName();
    const eventDate = document.getElementById("eventDate").value;
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

    return isNameValid;
  }
</script>
</body>
</html>
<?php  ?>


<style>
   /* Page background and title styling */
body {
      
    margin: 0;
  padding: 0;
  background-image: url('../images/netballpage.jpg');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  font-family: Arial, sans-serif;
   
    
}

.container-fluid h1 {
    color: #2c3e50;
    margin-bottom: 30px;
    font-weight: 600;
    font-size: 28px;
    text-align: center;
}

/* Form card */
.event-card {
  background-color: rgba(255, 255, 255, 0.85);
  width: 400px;
  margin: 40px auto; /* was 100px, now closer to top */
  padding: 30px;
  border-radius: 15px;
  box-shadow: 0 0 15px rgba(0,0,0,0.4);
  text-align: center;
}

.card-body {
    padding: 30px 35px;
}

/* Labels */
form label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #333;
}

/* Input fields */
.form-control {
    width: 100%;
    margin-bottom: 20px;
    padding: 10px 15px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 15px;
    transition: border-color 0.3s ease;
}

.form-control:focus {
    border-color: #6fa8dc;
    box-shadow: 0 0 5px rgba(111, 168, 220, 0.5);
}

/* Checkbox styling */
.form-check {
    margin-bottom: 10px;
}

.form-check-input {
    margin-right: 10px;
    accent-color: #6fa8dc;
}

.form-check-label {
    font-size: 15px;
    color: #555;
}

/* Submit button */


    .btn {
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        width: 100%;
        cursor: pointer;
    }

    .btn:hover {
        background-color: #0056b3;
    }
</style>
<script>
function checkEventName() {
    const eventName = document.getElementById("event_name").value.trim();
    const error = document.getElementById("eventNameError");

    if (eventName.length === 0) {
        error.textContent = "Event name is required.";
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "check_event.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            if (this.responseText === "exists") {
                error.textContent = "This event name already exists!";
            } else {
                error.textContent = "";
            }
        }
    };
    xhr.send("event_name=" + encodeURIComponent(eventName));
}

function validateForm() {
    const error = document.getElementById("eventNameError").textContent;
    if (error !== "") {
        return false; // prevent submit
    }
    return true;
}
</script>
