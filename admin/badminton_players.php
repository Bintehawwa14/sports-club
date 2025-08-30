<?php session_start();
include_once('../include/db_connect.php');

$badmintonTeams = mysqli_query($con, "SELECT fullName,email,role,teamName,player1,dob1,height1,
weight1,chronic_illness1,allergies1,medications1,surgeries1,
previous_injuries1,height2,weight2,chronic_illness2,allergies2,
medications2,surgeries2,previous_injuries2,player2,dob2,category,game, is_approved 
FROM badminton_players");
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
               
<!-- Badminton Teams -->
<h3>Badminton Teams</h3>
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Full Name</th>
            <th>Team Name</th>
            <th>Email</th>
            <th>Player 1</th>
            <th>Player 2</th>
            <th>Role</th>
            <th>Game</th>
            <th>Status</th>
            
        </tr>
    </thead>
    <tbody>
          <?php 
        mysqli_data_seek($badmintonTeams, 0);
        while($row = mysqli_fetch_assoc($badmintonTeams)) 
            if(strtolower($row['role']) == 'team') {
                $status = isset($row['is_approved']) ? $row['is_approved'] : 'pending';
        ?>
            <tr>
                <td><?= htmlspecialchars($row['fullName']); ?></td>
                <td><?= htmlspecialchars($row['teamName']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= htmlspecialchars($row['player1']); ?></td>
                <td><?= htmlspecialchars($row['player2']); ?></td>
                <td><?= htmlspecialchars($row['role']); ?></td>
                <td><?= htmlspecialchars($row['game']); ?></td>
                <td>
                    <!-- Approve dropdown -->
                    <form method="post" action="update_status.php">
                        <input type="hidden" name="game" value="badminton">
                        <input type="hidden" name="team_name" value="<?= htmlspecialchars($row['teamName']); ?>">
                        <!-- <select name="is_approved" onchange="this.form.submit()">
                            <option value="pending" <?= ($row['is_approved']=='pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?= ($row['is_approved']=='approved') ? 'selected' : ''; ?>>Approved</option>
                        </select> -->
                        <?= htmlspecialchars($row['is_approved']); ?>
                    </form>
                </td>
                
               
            </tr>
        
        <?php } ?>
    </tbody>
</table>

<!-- Badminton Players-->
<h3>Badminton Players</h3>
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Full Name</th>
            <th>Email</th>
            <th>Player</th>
            <th>Role</th>
            <th>Game</th>
            <th>Status</th>
            
        </tr>
    </thead>
    <tbody>
         <?php 
        mysqli_data_seek($badmintonTeams, 0);
        while($row = mysqli_fetch_assoc($badmintonTeams)) 
            if(strtolower($row['role']) == 'player') {
                $status = isset($row['is_approved']) ? $row['is_approved'] : 'pending';
        ?>
            <tr>
                <td><?= htmlspecialchars($row['fullName']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= htmlspecialchars($row['player1']); ?></td>
                <td><?= htmlspecialchars($row['role']); ?></td>
                <td><?= htmlspecialchars($row['game']); ?></td>
                <td>
                    <form method="post" action="update_status.php">
                        <input type="hidden" name="game" value="badminton">
                        <input type="hidden" name="team_name" value="<?= htmlspecialchars($row['teamName']); ?>">
                        <!-- <select name="is_approved" onchange="this.form.submit()">
                            <option value="pending" <?= ($row['is_approved']=='pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?= ($row['is_approved']=='approved') ? 'selected' : ''; ?>>Approved</option>
                        </select> -->
                        <?= htmlspecialchars($row['is_approved']); ?>
                    </form>
                </td>
               
            </tr>
        <?php } ?>
    </tbody>
</table>
                                  
                                </table>
                            <a class="btn btn-sm btn-primary edit-link" href="badminton_scheduling.php">Single Elimination</a>
                            <a class="btn btn-sm btn-primary edit-link" href="badminton_double.php">Double Elimination</a>
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
     .edit-link {
        font-size: 14px;
        color: #fff;
        background-color: #007bff;
        padding: 8px 15px;
        text-decoration: none;
        border-radius: 5px;
    }

    .edit-link:hover {
        background-color: #0056b3;
    }

</style>

</body>
</html>
<?php  ?>