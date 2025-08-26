<?php
session_start();
require '../include/db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Event Details</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    /* same CSS jo aapne diya tha */
    :root{
      --bg:#f7f7fb; --card:#ffffff; --text:#1f2937; --muted:#6b7280;
      --primary:#4f46e5; --ring:rgba(79,70,229,.35); --border:#e5e7eb;
    }
    body{margin:0;font-family:system-ui;color:var(--text);background:var(--bg);}
    .container{max-width:900px;margin:48px auto;padding:0 16px;}
    .card{background:var(--card);border:1px solid var(--border);border-radius:16px;
          box-shadow:0 8px 24px rgba(0,0,0,.06);padding:20px;}
    h1{font-size:22px;margin:0 0 8px}
    .grid{display:grid;gap:12px;}
    label{display:block;font-size:14px;margin:8px 0;color:#374151}
    select{width:100%;padding:12px 14px;border-radius:10px;border:1px solid var(--border);}
    .btn{padding:10px 14px;border-radius:10px;cursor:pointer;font-weight:600}
    .btn-primary{background:var(--primary);color:#fff;border:none}
    table{width:100%;border-collapse:collapse;margin-top:16px;background:#fff;border-radius:12px;overflow:hidden}
    th,td{padding:12px 14px;border-bottom:1px solid var(--border);font-size:14px;text-align:left}
    th{background:#f9fafb;color:#374151;font-weight:700}
    tr:last-child td{border-bottom:none}
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <h1>Event Details </h1>

      <!-- Dropdown -->
      <div class="grid">
        <div class="row">
          <label for="eventSelect">Select Event</label>
          <select id="eventSelect" name="event_id">
            <option value="">— Select Event —</option>
            <?php
              $sql = "SELECT id, event_name FROM events ORDER BY start_date DESC";
              $result = mysqli_query($con, $sql);
              while($row = mysqli_fetch_assoc($result)){
                echo "<option value='{$row['id']}'>{$row['event_name']}</option>";
              }
            ?>
          </select>
        </div>
      </div>

      <!-- Results Section -->
      <div id="results">
        <p style="color:gray;margin-top:15px;">Choose an event to view details here. </p>
      </div>
    </div>
  </div>

  <script>
    document.getElementById("eventSelect").addEventListener("change", function(){
      let eventId = this.value;
      if(eventId){
        fetch("fetch_event_details.php?id=" + eventId)
          .then(res => res.text())
          .then(data => {
            document.getElementById("results").innerHTML = data;
          });
      } else {
        document.getElementById("results").innerHTML = "<p style='color:gray'>No event selected.</p>";
      }
    });
  </script>
</body>
</html>
