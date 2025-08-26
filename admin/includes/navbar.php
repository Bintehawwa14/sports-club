 
 
 <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="custom-nav-link active" href="admin-profile.php">
    <div class="nav-icon"><i class="fas fa-tachometer-alt"></i></div>
    <span>Admin Profile</span>
</a>

          
          
           
        </nav>
       <style>
        body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f5f5f5;
}

  .navbar {
  

    position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1040;
  height: 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #343a40; 
  padding: 15px 40px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  
}
#layoutSidenav_content {
    padding-top: 50px; /* Enough space for fixed navbar */
}


.nav-links {
  list-style: none;
  display: flex;
  gap: 30px;
}

.nav-links li a {
  color: white;
  text-decoration: none;
  font-size: 16px;
  position: relative;
  transition: color 0.3s ease;
}

.nav-links li a::after {
  content: '';
  position: absolute;
  width: 0%;
  height: 2px;
  bottom: -5px;
  left: 0;
  background-color: white;
  transition: 0.3s ease;
}

.nav-links li a:hover {
  color:rgba(255, 217, 0, 0.66);
}

.nav-links li a:hover::after {
  width: 100%;
}
.custom-nav-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    color: #ffffff;
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
    border-left: 4px solid transparent;
    transition: all 0.3s ease;
    border-radius: 4px;
}

.custom-nav-link .nav-icon {
    font-size: 16px;
    color: #ffffffcc; /* Slightly lighter */
}

.custom-nav-link:hover {
    background-color: #ffffff1a;
    color: #ffd700;
    border-left: 4px solid #ffd700;
}

.custom-nav-link.active {
    background-color: #ffffff1a;
    color: #ffd700;
    border-left: 4px solid #ffd700;
}


</style>