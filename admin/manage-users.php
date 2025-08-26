<?php
session_start();
include_once('../include/db_connect.php');

// for deleting user
// for deleting user and their registrations
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    // Pehle role check karein
    $checkRole = mysqli_query($con, "SELECT role, email FROM users WHERE id='$user_id'");
    $row = mysqli_fetch_assoc($checkRole);

    if ($row && strtolower($row['role']) === 'admin') {
        echo "<script>alert('Admin cannot be deleted.');</script>";
    } else {
        // Delete all registrations first
        $email = $row['email'];
        mysqli_query($con, "DELETE FROM tabletennis_players WHERE email='$email'");
        mysqli_query($con, "DELETE FROM badminton_players WHERE email='$email'");
        // aapke baaki registration tables bhi yahan add karein

        // Ab user delete
        $sql = "DELETE FROM users WHERE id = $user_id";
        if (mysqli_query($con, $sql)) {
            echo "<script>alert(' successfully deleted!'); 
                  window.location.href='manage-users.php';</script>";
        } else {
            echo "Error: " . mysqli_error($con);
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
                <h1 class="mt-4">Manage users</h1>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        Registered User Details
                    </div>
                   
<div class="container my-4">
  <h2 class="mb-4">Manage Users</h2>
  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
      <thead class="table-primary">
        <tr>
          <th>Sno.</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Email Id</th>
          <th>Contact no.</th>
          <th>CNIC</th>
          <th>Club/College</th>
          <th>Reg. Date</th>
          <th>Role</th>
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
            <td><?= htmlspecialchars($row['cnic']) ?></td>
            <td><?= htmlspecialchars($row['club_college']) ?></td>
            <td><?= htmlspecialchars($row['posting_date']) ?></td>
            <td><?= htmlspecialchars($row['role']) ?></td>
  
            <td>
              <?php if (strtolower($row['role']) !== 'admin') { ?>
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
