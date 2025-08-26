<section id="layoutSidenav_content">
<div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div id="sidebar" class="custom-sidebar">
                
                <a class="custom-nav-link" href="dashboard.php">
                    <div class="nav-icon"><i class="fas fa-calendar-alt"></i></div>
                    <span> Dashboard</span>
                </a>

                <a class="custom-nav-link" href="profile.php">
                    <div class="nav-icon"><i class="fas fa-users"></i></div>
                    <span>profile</span>
                </a>
                <a class="custom-nav-link" href="get_event.php">
                    <div class="nav-icon"><i class="fas fa-calendar-alt"></i></div>
                    <span>Register for Latest Event</span>
                </a>
              
                <a class="custom-nav-link" href="change-password.php">
                    <div class="nav-icon"><i class="fas fa-calendar-alt"></i></div>
                    <span> change password</span>
                </a>
                <a class="custom-nav-link" href="logout.php">
                    <div class="nav-icon"><i class="fas fa-calendar-alt"></i></div>
                    <span> logout</span>
                </a>
                
                </div>


         </div>
         </nav>
         </div>
            



            
  <style>


#layoutSidenav_nav {
    width: 250px;
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    z-index: 1030;
    background-color: #343a40; /* dark background */
    color: white;
    overflow-y: auto;
}

.sb-sidenav {
    padding-top: 20px;
}

.sb-sidenav .nav-link {
    color: #cfd8dc;
    padding: 10px 20px;
    display: flex;
    align-items: center;
    transition: background-color 0.3s ease;
}

.sb-sidenav .nav-link:hover {
    background-color: #495057;
    color: #ffffff;
}

.sb-nav-link-icon {
    margin-right: 10px;
    display: flex;
    align-items: center;
}

.sb-sidenav-dark {
    background-color: #343a40;
}


#layoutSidenav_content {
  
    margin-left: 250px;
    padding: 20px;
}
.custom-sidebar {
    position: fixed;
    top: 56px; /* Height of the top navbar */
    bottom: 0;
    left: 0;
    width: 250px;
    overflow-y: auto;
    background-color: #343a40; /* or your sidebar bg color */
}

</style>
</section>