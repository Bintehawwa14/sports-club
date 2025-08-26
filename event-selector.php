<?php
session_start();
    require 'include/db_connect.php';
    require 'include/nav-bar.php';
    ?>
    
    <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>View Event Schduling</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    :root{
      --bg:#f7f7fb;
      --card:#ffffff;
      --text:#1f2937;
      --muted:#6b7280;
      --primary:#4f46e5;
      --ring:rgba(79,70,229,.35);
      --border:#e5e7eb;
      --success:#16a34a;
      --danger:#dc2626;
    }
    *{box-sizing:border-box}
    body{
      margin:0; font-family:system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji","Segoe UI Emoji";
      color:var(--text); background:var(--bg);
    }
    .container{
      max-width:980px; margin:48px auto; padding:0 16px;
    }
    .card{
      background:var(--card); border:1px solid var(--border);
      border-radius:16px; box-shadow:0 8px 24px rgba(0,0,0,.06);
      padding:20px;
    }
    h1{font-size:22px; margin:0 0 8px}
    p.lead{margin:0 0 20px; color:var(--muted)}
    .grid{
      display:grid; gap:12px;
      grid-template-columns: 1fr;
    }
    @media(min-width:700px){
      .grid{ grid-template-columns: repeat(3, 1fr); }
    }
    label{display:block; font-size:14px; margin:8px 0; color:#374151}
    select{
      width:100%; padding:12px 14px; border-radius:10px; border:1px solid var(--border);
      background:#fff; color:var(--text); font-size:15px; outline:none;
      transition: box-shadow .15s ease, border-color .15s ease;
    }
    select:focus{ border-color:var(--primary); box-shadow:0 0 0 4px var(--ring); }
    .row{margin-top:4px}
    .actions{ display:flex; gap:8px; margin-top:10px; flex-wrap:wrap }
    .btn{
      border:1px solid var(--border); background:#fff; color:var(--text);
      padding:10px 14px; border-radius:10px; cursor:pointer; font-weight:600;
      transition: transform .05s ease, box-shadow .15s ease, border-color .15s ease;
    }
    .btn:hover{ box-shadow:0 4px 12px rgba(0,0,0,.06) }
    .btn:active{ transform:translateY(1px) }
    .btn-primary{ background:var(--primary); color:#fff; border-color:transparent }
    .btn-ghost{ background:transparent }
    .pill{
      display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:999px;
      background:#f3f4f6; color:#374151; font-size:13px; border:1px solid var(--border);
    }
    .divider{ height:1px; background:var(--border); margin:16px 0 }
    .empty{
      padding:18px; border:1px dashed var(--border); border-radius:12px;
      background:linear-gradient(180deg, #fafafa, #fff); color:var(--muted); text-align:center;
    }
    table{
      width:100%; border-collapse: collapse; margin-top:8px; background:#fff; border-radius:12px; overflow:hidden;
      border:1px solid var(--border);
    }
    th, td{ padding:12px 14px; border-bottom:1px solid var(--border); text-align:left; font-size:14px }
    th{ background:#f9fafb; color:#374151; font-weight:700 }
    tr:last-child td{ border-bottom:none }
    .badge{
      padding:4px 8px; border-radius:8px; font-size:12px; font-weight:600; display:inline-block;
    }
    .badge.ok{ background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0 }
    .badge.warn{ background:#fef2f2; color:#991b1b; border:1px solid #fecaca }
    .toolbar{
      display:flex; justify-content:space-between; align-items:center; gap:10px; margin-top:6px; flex-wrap:wrap;
    }
    .search{
      max-width:300px; width:100%; position:relative;
    }
    .search input{
      width:100%; padding:10px 12px; border:1px solid var(--border); border-radius:10px; outline:none;
    }
    .hint{ color:var(--muted); font-size:12px; margin-top:6px }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <h1>Event Schduling Details</h1>
      <p class="lead">Select <strong>Event</strong>, <strong>Sport</strong> and <strong>Round</strong> to view details instantly.</p>

      <!-- Selectors -->
      <div class="grid">
        <div class="row">
          <label for="EventSelect">Event Name</label>
          <select id="eventSelect" name="event_name" class="form-select" required>
          <option value="">— Select Event —</option>
          <?php

            // latest events fetch karna (maan lo events table me event_date column hai)
            $sql = "SELECT event_name FROM events ORDER BY start_date DESC LIMIT 5";  
            $result = mysqli_query($con, $sql);

            while($row = mysqli_fetch_assoc($result)){
                echo "<option value='{$row['event_name']}'>{$row['event_name']}</option>";
            }
          ?>
          </select>
        </div>
        <div class="row">
          <label for="sportSelect">Sport</label>
          <select id="sportSelect">
            <option value="">— Select Sport —</option>
            <option value="badminton">Badminton</option>
            <option value="cricket">Cricket</option>
            <option value="volleyball">Volleyball</option>
            <option value="tabletennis">Table Tennis</option>
          </select>
        </div>
        <div class="row">
          <label for="roundSelect">Round</label>
          <select id="roundSelect">
            <option value="">— Select Round —</option>
            <option value="1">First Round</option>
            <option value="2">Quarter Final</option>
            <option value="3">Semi Final</option>
            <option value="4">Final</option>
          </select>
      </div>
      <button type="button" id="fetchBtn"  class="btn btn-primary">show Results</button>
      <script>
document.getElementById("fetchBtn").addEventListener("click", function () {
  let eventName = document.getElementById("eventSelect").value;
  let sport = document.getElementById("sportSelect").value;
  let round = document.getElementById("roundSelect").value;

  if (eventName && sport && round) {
    fetch("fetch_matches.php?event=" + eventName + "&sport=" + sport + "&round=" + round)
      .then(response => response.text())
      .then(data => {
        document.getElementById("results").innerHTML = data;
      });
  } else {
    alert("Please select Event, Sport, and Round");
  }
});
</script>

     <button type="button" id="clearBtn" class="btn btn-primary">clear</button>

<script>
document.getElementById("clearBtn").addEventListener("click", function () {
  // Event dropdown reset
  document.getElementById("eventSelect").selectedIndex = 0;

  // Sport dropdown reset
  document.getElementById("sportSelect").selectedIndex = 0;

  // Round dropdown reset (agar hai)
  let round = document.getElementById("roundSelect");
  if(round){ round.selectedIndex = 0; }

  // Details section clear
  let details = document.getElementById("details-content");
  if(details){
    details.innerHTML = "";
  }
});
</script>
        
    

      <div class="divider"></div><br>

      <!-- Toolbar under results -->
      

      <!-- Results -->
      <div id="results" style="margin-top:10px;">
        <div class="empty">No data yet. Choose an event, sport, and round to see details here.</div>
      </div>
    </div>
  </div>
  </div>
   <?php require 'match_details.php';?>
</body>
</html>
  <?php require 'include/footer.php';?>
