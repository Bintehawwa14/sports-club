<?php session_start();
include_once('../include/db_connect.php');

// for deleting event
if (isset($_GET['id'])) {
    $adminid = intval($_GET['id']); // SQL injection se bachne ke liye int banaya

    // Pehle event ka status check karo
    $check = mysqli_query($con, "SELECT status FROM events WHERE id = '$adminid' LIMIT 1");
      if ($status === 'active') {
        $checkActive = mysqli_query($con, "SELECT id FROM events WHERE status = 'active'");
        if (mysqli_num_rows($checkActive) > 0) {
            echo "<script>alert('only on event can be activated at a time.'); window.location='manage-events.php';</script>";
            exit;
        }}

    if (mysqli_num_rows($check) > 0) {
        $data = mysqli_fetch_assoc($check);

        if ($data['status'] === 'active') {
            echo "<script>alert('Active event can not be deleted!Deactivate if you want to delete'); window.location='manage-events.php';</script>";
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
    <!-- <link href="../css/styles.css" rel="stylesheet" /> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <?php include_once('includes/navbar.php');?>
    <div id="layoutSidenav">
        <?php include_once('includes/sidebar.php');?>
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
                                        <th>Event_Location</th>
                                        <th>Sport</th>
                                        <th>Action</th>
                                        <th>Activate/Deactivate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $ret = mysqli_query($con, "select * from events");
                                    $cnt = 1;
                                    while ($row = mysqli_fetch_array($ret)) { ?>
                                    <tr>
                                        <td><?php echo $cnt; ?></td>
                                        <td><?php echo $row['event_name']; ?></td>
                                        <td><?php echo $row['start_date']; ?></td>
                                        <td><?php echo $row['end_date']; ?></td>
                                        <td><?php echo $row['event_location']; ?></td>
                                        <td><?php echo $row['sport']; ?></td>
                                        <td>
                                            <!-- Edit button (pen icon) for each column -->
                                            <a href="edit-event.php?id=<?php echo $row['id']; ?>" style="margin-right: 10px;">
                                                <i class="fa fa-pen" aria-hidden="true" style="cursor: pointer; color: #007bff;"></i>
                                            </a>
                                            
                                            <!-- Delete button -->
                                             <a href="manage-events.php?id=<?php echo $row['id'];?>" onClick="return confirm('Do you really want to delete');"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                          
                                          <a href="close_event.php?id=<?php echo $row['id']; ?>" 
                            class="btn btn-sm" style= "background-color:white">
                                <i class="fas fa-times" aria-hidden="true" style="cursor: pointer; color: #007bff;"></i>
                            </a>

                                                                                    
                                    </td>
                                    <td>
                                    <?php if ($row['status'] == 'active') { ?>
                                        <a class="btn btn-sm btn-danger" 
                                        href="toggle-event.php?id=<?php echo $row['id']; ?>&status=inactive">
                                        <i class="fa fa-toggle-off"></i> Deactivate
                                        </a>
                                    <?php } else { ?>
                                        <a class="btn btn-sm btn-success" 
                                        href="toggle-event.php?id=<?php echo $row['id']; ?>&status=active">
                                        <i class="fa fa-toggle-on"></i> Activate
                                        </a>
                                    <?php } ?>
                                        </td>
                                    

                                    </tr>
                                    <?php $cnt = $cnt + 1; } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
    <script src="../js/datatables-simple-demo.js"></script>
    <style>
        /* Manage Users Page Styles */
        .container-fluid {
            width: 100%;
            max-width: 1000px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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
            padding: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 50px;
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

        .btn {
            padding: 8px 15px;
            background-color: #28a745;
            color: #fff;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn:hover {
            background-color: #218838;
        }

        .btn-danger:hover {
            background-color: #c82333;
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
                padding: 10px;
            }
        }
    </style>
</body>
</html>