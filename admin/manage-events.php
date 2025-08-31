
<?php
session_start();
require_once '../include/db_connect.php';

// Enable error logging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['user'])) {
    error_log("Session user not set. Redirecting to index.php");
    header("Location: index.php");
    exit();
}

// Handle event deletion with prepared statements
if (isset($_GET['id'])) {
    $event_id = intval($_GET['id']);

    // Check event status
    $checkStmt = mysqli_prepare($con, "SELECT status FROM events WHERE id = ?");
    mysqli_stmt_bind_param($checkStmt, "i", $event_id);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);

    if (mysqli_num_rows($checkResult) > 0) {
        $data = mysqli_fetch_assoc($checkResult);

        if ($data['status'] === 'active') {
            $checkActiveStmt = mysqli_query($con, "SELECT id FROM events WHERE status = 'active'");
            if (mysqli_num_rows($checkActiveStmt) > 0) {
                echo "<script>alert('Only one event can be activated at a time.'); window.location='manage-events.php';</script>";
                exit;
            }
        }

        if ($data['status'] === 'active') {
            echo "<script>alert('Active event cannot be deleted! Deactivate if you want to delete'); window.location='manage-events.php';</script>";
        } else {
            $deleteStmt = mysqli_prepare($con, "DELETE FROM events WHERE id = ?");
            mysqli_stmt_bind_param($deleteStmt, "i", $event_id);
            if (mysqli_stmt_execute($deleteStmt)) {
                echo "<script>alert('Data deleted successfully'); window.location='manage-events.php';</script>";
            } else {
                error_log("Error deleting event: " . mysqli_error($con));
                echo "<script>alert('Delete failed: " . mysqli_error($con) . "'); window.location='manage-events.php';</script>";
            }
            mysqli_stmt_close($deleteStmt);
        }
    } else {
        echo "<script>alert('Event not found'); window.location='manage-events.php';</script>";
    }
    mysqli_stmt_close($checkStmt);
}

// Fetch events
$eventQuery = "SELECT * FROM events";
$eventResult = mysqli_query($con, $eventQuery);
if (!$eventResult) {
    error_log("Error fetching events: " . mysqli_error($con));
    die("Error fetching events. Please check logs.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Manage Events Page" />
    <meta name="author" content="" />
    <title>Manage Events</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('../images/volleyballform.jpg');
            background-size: cover;
            background-position: center;
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
            max-width: 1200px;
            margin: 0 auto;
        }

        .users-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            margin: 20px auto;
            width: 95%;
            max-width: 1200px;
        }

        .users-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .users-header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #1a3c6d;
            margin: 0;
            letter-spacing: 0.3px;
        }

        .table-responsive {
            margin-top: 15px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
        }

        th {
            background-color: #f0f4f8;
            color: #1a3c6d;
            font-weight: 600;
        }

        td {
            color: #374151;
            background-color: #ffffff;
        }

        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .action-buttons a, .action-buttons button {
            font-size: 14px;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 60px;
            text-align: center;
        }

        .action-buttons .btn-edit {
            color: #fff;
            background-color: #007bff;
            border: none;
        }

        .action-buttons .btn-delete {
            color: #fff;
            background-color: #dc3545;
            border: none;
        }

        .action-buttons .btn-close {
            color: #fff;
            background-color: #6c757d;
            border: none;
        }

        .action-buttons .btn-activate {
            color: #fff;
            background-color: #dc3545;
            border: none;
        }

        .action-buttons .btn-deactivate {
            color: #fff;
            background-color: #28a745;
            border: none;
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
            .users-container {
                width: 95%;
                padding: 15px;
                margin: 15px auto;
            }

            .users-header h1 {
                font-size: 20px;
            }

            th, td {
                display: block;
                width: 100%;
                text-align: left;
                padding: 8px;
            }

            th {
                background-color: #e6eef8;
                border-bottom: none;
            }

            td {
                border-bottom: 1px solid #e0e0e0;
            }

            .container-fluid {
                padding: 10px;
            }

            .action-buttons {
                flex-direction: column;
                gap: 6px;
            }

            .action-buttons a, .action-buttons button {
                width: 100%;
                font-size: 12px;
                padding: 8px;
            }
        }

        @media (max-width: 576px) {
            .users-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <?php
    if (!file_exists('includes/navbar.php') || !file_exists('includes/sidebar.php')) {
        error_log("Navbar or sidebar file missing");
        echo "<div class='alert alert-danger text-center'>Error: Navigation files missing. Please check includes directory.</div>";
    } else {
        include_once('includes/navbar.php');
    ?>
    <div id="layoutSidenav">
        <?php include_once('includes/sidebar.php'); ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <div class="users-container">
                        <div class="users-header">
                            <h1>Manage Events</h1>
                        </div>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Sno.</th>
                                        <th>Event Name</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Event Date</th>
                                        <th>Location</th>
                                        <th>Sport</th>
                                        <th>Action</th>
                                        <th>Activate/Deactivate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cnt = 1;
                                    while ($row = mysqli_fetch_assoc($eventResult)) {
                                    ?>
                                        <tr>
                                            <td><?php echo $cnt; ?></td>
                                            <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                                            <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                                            <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                                            <td><?php echo htmlspecialchars($row['event_location']); ?></td>
                                            <td><?php echo htmlspecialchars($row['sport']); ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="edit-event.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">
                                                        <i class="fas fa-pen mr-1"></i> Edit
                                                    </a>
                                                    <a href="manage-events.php?id=<?php echo $row['id']; ?>" 
                                                       class="btn btn-delete" 
                                                       onclick="return confirm('Do you really want to delete');">
                                                        <i class="fas fa-trash mr-1"></i> Delete
                                                    </a>
                                                    <?php if ($row['is_closed'] == 'yes') { ?>
                                                        <button class="btn btn-close" disabled>
                                                            <i class="fas fa-times mr-1"></i> Closed
                                                        </button>
                                                    <?php } else { ?>
                                                        <a href="close_event.php?id=<?php echo $row['id']; ?>" 
                                                           class="btn btn-close" 
                                                           onclick="return confirm('Are you sure you want to close this event?');">
                                                            <i class="fas fa-times mr-1"></i> Close
                                                        </a>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <?php if ($row['status'] == 'active') { ?>
                                                        <a class="btn btn-deactivate" 
                                                           href="toggle-event.php?id=<?php echo $row['id']; ?>&status=inactive">
                                                           <i class="fas fa-toggle-on mr-1"></i> Activated
                                                        </a>
                                                    <?php } else { ?>
                                                        <a class="btn btn-activate" 
                                                           href="toggle-event.php?id=<?php echo $row['id']; ?>&status=active">
                                                           <i class="fas fa-toggle-off mr-1"></i> Deactivated
                                                        </a>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php
                                        $cnt++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php } ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
    <script src="../js/datatables-simple-demo.js"></script>
</body>
</html>
