<?php
session_start();
include_once('../include/db_connect.php');

// For deleting event
if (isset($_GET['id'])) {
    $adminid = intval($_GET['id']); // Prevent SQL injection

    // Check event status
    $check = mysqli_query($con, "SELECT status FROM events WHERE id = '$adminid' LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        $data = mysqli_fetch_assoc($check);

        if ($data['status'] === 'active') {
            $checkActive = mysqli_query($con, "SELECT id FROM events WHERE status = 'active'");
            if (mysqli_num_rows($checkActive) > 0) {
                echo "<script>alert('Only one event can be activated at a time.'); window.location='manage-events.php';</script>";
                exit;
            }
        }

        if ($data['status'] === 'active') {
            echo "<script>alert('Active event cannot be deleted! Deactivate if you want to delete'); window.location='manage-events.php';</script>";
        } else {
            $msg = mysqli_query($con, "DELETE FROM events WHERE id = '$adminid'");
            if ($msg) {
                echo "<script>alert('Data deleted successfully'); window.location='manage-events.php';</script>";
            } else {
                echo "<script>alert('Delete failed'); window.location='manage-events.php';</script>";
            }
        }
    } else {
        echo "<script>alert('Event not found'); window.location='manage-events.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Manage Events</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <style>
        /* Manage Events Page Styles */
        .container-fluid {
            width: 100%;
            max-width: 1200px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        body {
            margin: 0;
            padding: 0;
            background-image: url('../images/volleyballform.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
        }

        .card {
            border: none;
            border-radius: 8px;
            margin-top: 20px;
        }

        .card-header {
            background-color: #f7f7f7;
            color: #333;
            padding: 15px;
            font-size: 18px;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
        }

        .card-body {
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            font-size: 16px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        td {
            background-color: #fff;
            color: #555;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        /* Action Buttons Container */
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

        .action-buttons .btn-activate, .action-buttons .btn-deactivate {
            min-width: 100px;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .container-fluid {
                width: 95%;
                padding: 15px;
            }

            h1 {
                font-size: 24px;
            }

            th, td {
                font-size: 14px;
                padding: 8px;
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

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }

        @media (max-width: 576px) {
            th, td {
                font-size: 12px;
                padding: 6px;
            }

            .action-buttons a, .action-buttons button {
                font-size: 11px;
                padding: 6px;
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
                    <h1 class="mt-4">Manage Events</h1>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Events Details
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>Sno.</th>
                                        <th>Event_Name</th>
                                        <th>Start_Date</th>
                                        <th>End_Date</th>
                                        <th>Event Date</th>
                                        <th>Event_Location</th>
                                        <th>Sport</th>
                                        <th>Action</th>
                                        <th>Activate/Deactivate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $ret = mysqli_query($con, "SELECT * FROM events");
                                    $cnt = 1;
                                    while ($row = mysqli_fetch_array($ret)) { ?>
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
                                                    <i class="fa fa-pen" aria-hidden="true"></i> Edit
                                                </a>
                                                <a href="manage-events.php?id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-delete" 
                                                   onclick="return confirm('Do you really want to delete');">
                                                    <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                                </a>
                                               <?php if ($row['is_closed'] == 'yes') { ?>
                                            <button class="btn btn-sm btn-danger" disabled>
                                                <i class="fas fa-times"></i> Closed
                                            </button>
                                        <?php } else { ?>
                                            <a href="close_event.php?id=<?php echo $row['id']; ?>" 
                                            class="btn btn-sm btn-warning" 
                                            onclick="return confirm('Are you sure you want to close this event?');">
                                                <i class="fas fa-times"></i> Close
                                            </a>
                                        <?php } ?>

                                            </div>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <?php if ($row['status'] == 'active') { ?>
                                                    <a class="btn btn-deactivate btn-danger" 
                                                       href="toggle-event.php?id=<?php echo $row['id']; ?>&status=inactive">
                                                       <i class="fa fa-toggle-off"></i> Deactivate
                                                    </a>
                                                <?php } else { ?>
                                                    <a class="btn btn-activate btn-success" 
                                                       href="toggle-event.php?id=<?php echo $row['id']; ?>&status=active">
                                                       <i class="fa fa-toggle-on"></i> Activate
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php $cnt++; } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
    <script src="../js/datatables-simple-demo.js"></script>
</body>
</html>