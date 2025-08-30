<?php
session_start();
require 'include/db_connect.php';
require 'include/nav-bar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>View Event Scheduling</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    :root {
      --bg: #f7f7fb;
      --card: #ffffff;
      --text: #1f2937;
      --muted: #6b7280;
      --primary: #4f46e5;
      --ring: rgba(79, 70, 229, .2);
      --border: #e5e7eb;
      --success: #16a34a;
      --danger: #dc2626;
      --hover: #f3f4f6;
      --shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
      color: var(--text);
      background: var(--bg);
      line-height: 1.5;
    }
    .container {
      max-width: 1100px;
      margin: 40px auto;
      padding: 0 24px;
    }
    .card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 20px;
      box-shadow: var(--shadow);
      padding: 32px;
      transition: transform 0.2s ease;
    }
    .card:hover {
      transform: translateY(-4px);
    }
    h1 {
      font-size: 28px;
      margin: 0 0 12px;
      font-weight: 700;
      color: var(--text);
    }
    p.lead {
      margin: 0 0 24px;
      color: var(--muted);
      font-size: 16px;
    }
    .grid {
      display: grid;
      gap: 16px;
      grid-template-columns: 1fr;
      justify-items: center; /* Centers items horizontally */
    }
    @media (min-width: 768px) {
      .grid {
        grid-template-columns: repeat(3, 1fr);
        justify-items: center; /* Centers items horizontally on medium screens and above */
      }
    }
    label {
      display: block;
      font-size: 14px;
      font-weight: 500;
      margin: 8px 0 4px;
      color: var(--text);
      text-align: center; /* Centers label text */
    }
    select {
      width: 80%; /* Reduces width to create space, adjustable as needed */
      padding: 12px 16px;
      border-radius: 12px;
      border: 1px solid var(--border);
      background: #fff;
      color: var(--text);
      font-size: 15px;
      outline: none;
      transition: all 0.2s ease;
      appearance: none;
      background-repeat: no-repeat;
      background-position: right 12px center;
      background-size: 18px;
      margin: 0 auto; /* Centers the select element */
    }
    select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px var(--ring);
    }
    .row {
      margin-top: 8px;
      text-align: center; /* Centers the row content */
    }
    .actions {
      display: flex;
      gap: 12px;
      margin-top: 16px;
      flex-wrap: wrap;
      justify-content: center; /* Centers the buttons */
    }
    .btn {
      padding: 12px 20px;
      border-radius: 12px;
      font-weight: 600;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.2s ease;
      border: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    .btn-primary {
      background: var(--primary);
      color: #fff;
    }
    .btn-primary:hover {
      background: #4338ca;
      box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }
    .btn-primary:active {
      transform: translateY(1px);
    }
    .btn-ghost {
      background: transparent;
      border: 1px solid var(--border);
      color: var(--text);
    }
    .btn-ghost:hover {
      background: var(--hover);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }
    .divider {
      height: 1px;
      background: var(--border);
      margin: 24px 0;
    }
    .empty {
      padding: 24px;
      border: 2px dashed var(--border);
      border-radius: 12px;
      background: #fafafa;
      color: var(--muted);
      text-align: center;
      font-size: 15px;
      font-style: italic;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 16px;
      background: var(--card);
      border-radius: 12px;
      overflow: hidden;
      border: 1px solid var(--border);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    th, td {
      padding: 14px 16px;
      text-align: left;
      font-size: 14px;
      border-bottom: 1px solid var(--border);
    }
    th {
      background: #f9fafb;
      color: var(--text);
      font-weight: 600;
      text-transform: uppercase;
      font-size: 13px;
    }
    tr:last-child td {
      border-bottom: none;
    }
    tr:hover {
      background: var(--hover);
    }
    .badge {
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 500;
      display: inline-block;
    }
    .badge.ok {
      background: #ecfdf5;
      color: #065f46;
      border: 1px solid #a7f3d0;
    }
    .badge.warn {
      background: #fef2f2;
      color: #991b1b;
      border: 1px solid #fecaca;
    }
    .toolbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      margin: 16px 0;
      flex-wrap: wrap;
    }
    .search {
      max-width: 320px;
      width: 100%;
      position: relative;
    }
    .search input {
      width: 100%;
      padding: 10px 16px;
      border: 1px solid var(--border);
      border-radius: 12px;
      outline: none;
      font-size: 14px;
      transition: all 0.2s ease;
    }
    .search input:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px var(--ring);
    }
    .hint {
      color: var(--muted);
      font-size: 12px;
      margin-top: 8px;
      font-style: italic;
    }
    #results {
      margin-top: 20px;
      animation: fadeIn 0.3s ease-in;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <h1>Event Scheduling Details</h1>
      <p class="lead">Select an <strong>Event</strong>, <strong>Sport</strong>, and <strong>Round</strong> to view details instantly.</p>

      <!-- Selectors -->
      <div class="grid">
        <div class="row">
          <label for="eventSelect">Event Name</label>
          <select id="eventSelect" name="event_name" class="form-select" required>
            <option value="">— Select Event —</option>
            <?php
              $sql = "SELECT event_name FROM events ORDER BY start_date DESC LIMIT 5";
              $result = mysqli_query($con, $sql);
              while ($row = mysqli_fetch_assoc($result)) {
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
      </div>

      <div class="actions">
        <button type="button" id="fetchBtn" class="btn btn-primary">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
          </svg>
          Show Results
        </button>
        <button type="button" id="clearBtn" class="btn btn-ghost">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854z"/>
          </svg>
          Clear
        </button>
      </div>

      <script>
        document.getElementById("fetchBtn").addEventListener("click", function () {
          let eventName = document.getElementById("eventSelect").value;
          let sport = document.getElementById("sportSelect").value;
          let round = document.getElementById("roundSelect").value;

          if (eventName && sport && round) {
            fetch("fetch_matches.php?event=" + encodeURIComponent(eventName) + "&sport=" + encodeURIComponent(sport) + "&round=" + encodeURIComponent(round))
              .then(response => response.text())
              .then(data => {
                document.getElementById("results").innerHTML = data;
              })
              .catch(error => {
                document.getElementById("results").innerHTML = '<div class="empty">Error fetching data. Please try again.</div>';
              });
          } else {
            alert("Please select Event, Sport, and Round");
          }
        });

        document.getElementById("clearBtn").addEventListener("click", function () {
          document.getElementById("eventSelect").selectedIndex = 0;
          document.getElementById("sportSelect").selectedIndex = 0;
          document.getElementById("roundSelect").selectedIndex = 0;
          document.getElementById("results").innerHTML = '<div class="empty">No data yet. Choose an event, sport, and round to see details here.</div>';
        });
      </script>

      <div class="divider"></div>

      <!-- Results -->
      <div id="results">
        <div class="empty">No data yet. Choose an event, sport, and round to see details here.</div>
      </div>
    </div>
  </div>
  <?php require 'match_details.php'; ?>
</body>
</html>
<?php require 'include/footer.php'; ?>