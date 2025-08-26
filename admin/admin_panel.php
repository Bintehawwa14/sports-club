<?php
// db_connection.php included for DB connection
include '../include/db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Match Scheduling Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
      <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <?php include_once('includes/navbar.php'); ?>
    <?php include_once('includes/sidebar.php');?>
        <div id="layoutSidenav_content">
    <style>
        body { font-family: Arial, sans-serif; background: #f3f3f3; padding: 20px; }
        h2 { color: #333; }
        form { margin-bottom: 20px; background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 0 8px rgba(0,0,0,0.1); }
        label, select, button { display: block; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; background: #fff; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #007bff; color: white; }
    </style>
</head>
<body>

<h2>Schedule Matches</h2>

<form method="POST" action="generate_single_elimination.php">
    <label for="id">Select round:</label>
    <select name="id" id="id" required>
          <option value="First Round">First Round</option>
          <option value="Quarterfinal">Quarterfinal</option>
          <option value="Semifinal">Semifinal</option>
          <option value="Final">Final</option>
        </select>
        <?php
        $eventQuery = $con->query("SELECT id, round FROM tournament");
        while ($row = $eventQuery->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['round']}</option>";
        }
        ?>
    </select>
    <button href="generate_single_elimination.php" type="submit">Generate Match Schedule</button>
    <div style="margin-top: 20px;">
        <button type="button" onclick="prevStep()">Back</button>
        <button type="button" onclick="nextStep()">Next</button>
      </div>
</form>

       
      
    </form>
  </div>
  <div id="layoutSidenav_content">
<h3>Scheduled Matches</h3>
<table>
    <tr>
        <th>Match ID</th>
        <th>Team 1</th>
        <th>Team 2</th>
        <th>Round</th>
        <th>Event</th>
    </tr>
   
</table>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
        <script src="../js/datatables-simple-demo.js"></script>

<?php if (isset($_GET['scheduled']) && $_GET['scheduled'] == 'true'): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Schedule Created!',
        text: 'Matches have been successfully scheduled.'
    });
</script>

<?php endif; ?>

</body>
</html>
