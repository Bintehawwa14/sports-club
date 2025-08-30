<?php
session_start();
require '../include/db_connect.php';

// ✅ Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// ✅ Fetch registered cricket teams
// Fetch all teams per game
$cricketTeams = mysqli_query($con, "SELECT full_name, team_name, captain_name, vice_captain_name, email, game, is_approved 
FROM cricket_teams WHERE game='cricket'");


?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Registered players/Teams</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
      <?php include_once('includes/navbar.php');?>
        <div id="layoutSidenav">
         <?php include_once('includes/sidebar.php');?>
            <div id="layoutSidenav_content">
              <!-- Cricket Teams -->
<h3>Cricket Teams</h3>
<?php if (mysqli_num_rows($cricketTeams) > 0): ?>
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Full Name</th>
            <th>Team Name</th>
            <th>Captain</th>
            <th>Vice Captain</th>
            <th>Email</th>
            <th>Game</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($cricketTeams)) { ?>
            <tr>
                <td><?= htmlspecialchars($row['full_name']); ?></td>
                <td><?= htmlspecialchars($row['team_name']); ?></td>
                <td><?= htmlspecialchars($row['captain_name']); ?></td>
                <td><?= htmlspecialchars($row['vice_captain_name']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= htmlspecialchars($row['game']); ?></td>
               
                <td>
                    <!-- Approve dropdown -->
                    <form method="post" action="update_status.php">
                        <input type="hidden" name="game" value="cricket">
                        <input type="hidden" name="team_name" value="<?= htmlspecialchars($row['team_name']); ?>">
                        <!-- <select name="is_approved" onchange="this.form.submit()">
                            <option value="pending" <?= ($row['is_approved']=='pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?= ($row['is_approved']=='approved') ? 'selected' : ''; ?>>Approved</option>
                        </select> -->
                        <?= htmlspecialchars($row['is_approved']); ?>
                    </form>
                </td>
                
                <td>         
                    <a href="cteams_detail.php?team_name=<?php echo urlencode($row['team_name']); ?>" class="btn btn-primary btn-sm">Details</a>
                    <a href="delete.php?game=cricket&team_name=<?= urlencode($row['team_name']); ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Are you sure you want to delete this team?');">
                       🗑
                    </a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
           <a class="btn btn-sm btn-primary edit-link" href="cricket_scheduling.php">Schedule match</a>
<?php else: ?>
    <div class="alert alert-warning mt-3">No cricket teams registered yet.</div>
<?php endif; ?>
</div>
<style>
        body {
            background-color: #fff;
        }
        .container-fluid {
        width: 80%;
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

    .btn {
        padding: 8px 15px;
        margin-left: 20px;
        background-color: #007bff;
        color: #fff;
        font-size: 14px;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-danger {
        background-color: #dc3545;
    }

    .btn:hover {
        background-color: #007bff;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }
    </style>
</body>
</html>
