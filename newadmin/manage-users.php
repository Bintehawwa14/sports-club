<?php
session_start();
include_once('../include/db_connect.php');

// for deleting user
if (isset($_GET['id'])) {
    $adminid = $_GET['id'];

    // Pehle role check karein
    $checkRole = mysqli_query($con, "SELECT role FROM users WHERE id='$adminid'");
    $row = mysqli_fetch_assoc($checkRole);

 if ($row && (strtolower($row['role']) === 'admin' || strtolower($row['role']) === 'newadmin')) {
    echo "<script>alert('Admin or New Admin cannot be deleted.');</script>";
} else {
    $msg = mysqli_query($con, "DELETE FROM users WHERE id='$adminid'");
    if ($msg) {
        echo "<script>alert('User deleted successfully');</script>";
    } else {
        echo "<script>alert('Error deleting user');</script>";
    }
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Your existing head content here -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

</head>
<body class="sb-nav-fixed">
<?php include_once('includes/navbar.php'); ?>
<div id="layoutSidenav">
    <?php include_once('includes/sidebar.php'); ?>
    <div id="layoutSidenav_content">
        <main>
             <div class="container-fluid px-4">
                    <h1 class="mt-4">Manage Users</h1>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            User's Details
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple">
      <thead>
        <tr>
          <th>Sno.</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Email Id</th>
          <th>Contact no.</th>
          <th>Reg. Date</th>
          <th>Role</th>
          <th>Approval Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $cnt = 1;
        $ret = mysqli_query($con, "SELECT * FROM users");
        while ($row = mysqli_fetch_assoc($ret)) {
        ?>
          <tr>
            <td><?= $cnt ?></td>
            <td><?= htmlspecialchars($row['fname']) ?></td>
            <td><?= htmlspecialchars($row['lname']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['contactno']) ?></td>
            <td><?= htmlspecialchars($row['posting_date']) ?></td>
            <td><?= htmlspecialchars($row['role']) ?></td>
            <td>
             <?php if (strtolower($row['role']) !== 'admin' && strtolower($row['role']) !== 'newadmin') { ?>
    <form method="post" action="update_approval.php" class="m-0 p-0">
      <input type="hidden" name="userid" value="<?= $row['id'] ?>">
      <select name="is_approved" class="form-select form-select-sm" onchange="this.form.submit()" style="width:110px;">
        <option value="0" <?= $row['is_approved'] == 0 ? 'selected' : '' ?>>Pending</option>
        <option value="1" <?= $row['is_approved'] == 1 ? 'selected' : '' ?>>Approved</option>
      </select>
    </form>
<?php } else { ?>
    <span class="text-muted">N/A</span>
<?php } ?>
            </td>
            <td>
         <?php if (strtolower($row['role']) !== 'admin' && strtolower($row['role']) !== 'newadmin') { ?>
    <a href="manage-users.php?id=<?= $row['id'] ?>" onclick="return confirm('Do you want to delete?')" class="text-danger fs-5">
      <i class="fas fa-trash"></i>
    </a>
<?php } else { ?>
    <span class="text-secondary">Delete not allowed</span>
<?php } ?>

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
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

</body>
</html>

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


