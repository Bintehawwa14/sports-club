<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>FG Kharian Women Sports Club</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }

    body {
      background-color: #ffffff;
    }

    header {
      display: flex;
      justify-content: space-between;
      background-color: white;
      align-items: center;
      padding: 3px 40px;
      border-bottom: 1px solid #ddd;
      flex-wrap: wrap;
    }

    .logo-container {
      display: flex;
      align-items: center;
      gap: 200px;
      cursor: pointer;
    }

    .logo {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      transition: transform 0.2s;
    }

    .logo:hover {
      transform: scale(1.05);
    }

    .signin-btn {
      padding: 8px 16px;
      background-color: #f44336;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 14px;
      text-decoration: none;
      transition: background-color 0.3s;
    }

    .signin-btn:hover {
      background-color: #d63031;
    }

    .welcome-title {
      text-align: center;
      font-size: 28px;
      font-weight: bold;
      margin: 20px 0;
      color: #111;
    }

    nav {
      width: 100%;
      display: flex;
      justify-content: center;
      margin-top: 2px;
      
    }

    .nav-links {
      list-style: none;
      display: flex;
      gap: 50px;
      flex-wrap: wrap;
    }

    .nav-links li a {
      text-decoration: none;
      color: #f05a28;
      font-size: 16px;
      font-weight: 500;
      transition: color 0.3s;
    }

    .nav-links li a:hover {
      color: #ff7f50;
    }

    /* Modal Styles */
.modal {
  display: none;
  position: fixed;
  z-index: 999;
  left: 0;
  top: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.8);
  justify-content: center;
  align-items: center;
}

.modal-img {
  width: 500px;
  height: 500px;
  object-fit: contain;
  border-radius: 8px;
}

.close-btn {
  position: absolute;
  top: 20px;
  right: 30px;
  font-size: 30px;
  color: white;
  cursor: pointer;
  font-weight: bold;
}

</style>
</head>
<body>
  <header>
  <div class="logo-container">
    <img src="images/logo.png" alt="Logo" class="logo" onclick="openModal()">
  <nav>
    <ul class="nav-links">
      <li><a href="index.php">Home</a></li>
      <li><a href="Aboutus.php">About us</a></li>
      <li><a href="Sports.php">Sports</a></li>
      <li><a href="event-selector.php">Match details</a></li>
      <li><a href="matchesresult.php">Results</a></li>
      <li><a href="Contactus.php">Contact us</a></li>
    </ul>
  </nav>
</div>
  <a href="login.php" class="signin-btn">SIGNIN/SIGNUP</a>
</header>


<!-- Modal Image -->
<div id="logoModal" class="modal" onclick="closeModal()">
  <span class="close-btn" onclick="closeModal()">&times;</span>
  <img src="images/logo.png" alt="Logo Full View" class="modal-img">
</div>
  

  <script>
    function openModal() {
  document.getElementById("logoModal").style.display = "flex";
}

function closeModal() {
  document.getElementById("logoModal").style.display = "none";
}

  </script>

</body>
</html>
