<?php require 'include/nav-bar.php';?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Game Maker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2c4250;
            --secondary-color: #e74c3c;
            --accent-color: #3498db;
            --text-color: #333;
            --light-bg: #f9f9f9;
            --white: #ffffff;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-image: url(images/c.jpg);
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
            background-color: white;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        
        h2 {
            color: var(--primary-color);
            font-size: 2rem;
            margin: 40px 0 20px;
            border-bottom: 2px solid var(--accent-color);
            padding-bottom: 10px;
            text-align: center;

        }
        
        h3 {
            color: var(--secondary-color);
            font-size: 1.5rem;
            margin: 30px 0 15px;
            text-align: center;
        }
        
        p {
            margin-bottom: 15px;
            font-size: 1.1rem;
            text-align: center;
            
        }
        
        .mission-section {
            background-color: var(--white);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }
        
        .values-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        .value-icon {
            font-size: 3.5rem; /* icon size */
            color: #0d6efd;    /* default blue color */
            transition: transform 0.3s, color 0.3s;
            }

        .value-icon:hover {
            transform: scale(1.2);
            color: #ff9800; /* hover color */
            }

        .value-card {
            background-color: var(--white);
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .value-card:hover {
            transform: translateY(-5px);
        }
        
        .team-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .team-member {
            background-color: var(--white);
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .team-member h3 {
            margin: 15px 0 5px;
        }
        
        .team-member p {
            color: var(--primary-color);
            font-weight: 500;
            margin: 0;
        }
        
        .divider {
            height: 1px;
            background-color: #ddd;
            margin: 40px 0;
        }
        

    </style>
</head>
<body>
    <div class="container">
        <section class="mission-section">
            <h2>The Game Maker</h2>
            <h3>FG KHARIAN WOMEN SPORTS CLUB</h3>

        </section>
    </div>

    
    
    <div class="container">
        <section class="mission-section">
            <h2>Our Mission</h2>
            <p>At The Game Maker, we believe that sports have the power to unite students, build character, and create lasting memories. Our mission is to make sports event organization accessible, efficient, and enjoyable for everyone.</p>
            <p> To simplify sports event organization, promote fair play, and bring students together through engaging and well-managed tournaments.
</p>
        </section>
                <section class="mission-section">
           <h2>Who We Are</h2>
        <p>
          <strong>The Game Make</strong> is a modern sports event management platform designed to make organizing games effortless. 
          Whether it’s cricket, volleyball, badminton, or netball, we provide an all-in-one solution to register teams, schedule matches, 
          track scores, and announce results.A sloution for the students of <strong>Health and Physical Education Department of FG DEGREE COLLEGE KHARIAN CANTT.</strong></p>
        </section>

        </section>
        <section class="mission-section">
        <h2>What We Offer</h2>
        <ul>
          <li>Easy team and player registration</li>
          <li>Match scheduling with updates</li>
          <li>Separate result management for each sport</li>
        </ul>
        </section>

        <section class="mission-section">
            <h2>Our Values</h2>
            <p>These core values guide everything we do and shape how we serve the sports community at FG Degree College Kharian Cantt.</p>
            
            <div class="values-container">
                <div class="value-card">
                    <h3>Excellence</h3>
                     <i class="bi bi-award value-icon" style="color:#ffc107;"></i>
                    <p>We aim for excellence in organizing and managing every sports event, ensuring smooth registration, fair play, and accurate results.</p>
                </div>
                
                <div class="value-card">
                    <h3>Community</h3>
                     <i class="bi bi-people value-icon" style="color:#28a745;"></i>
                    <p>We believe in the power of sports communities and work to strengthen these bonds.</p>
                </div>
                
                <div class="value-card">
                    <h3>Innovation</h3>
                    <i class="bi bi-lightbulb value-icon" style="color:#2894a7;"></i>
                    <p>We believe in the strength of our college sports community and work to connect students, teams, and staff through healthy competition and teamwork.</p>
                </div>
                
                <div class="value-card">
                    <h3>Passion</h3>
                    <i class="bi bi-heart value-icon" style="color:#ec190a;"></i>
                    <p>Our passion for sports inspires us to create a platform that supports table tennis, volleyball, cricket, and badminton with equal dedication.</p>
                </div>
            </div>
        </section>
        
        <div class="divider"></div>
        
        <section class="mission-section">
                <h2>Meet Our Team</h2>
                <p>Our diverse team of sports enthusiasts and event management professionals.</p>
            
                <div class="team-container">
                    <div class="team-member">
                        <img src="https://via.placeholder.com/150" alt="Team Member">
                        <h3>Sir Talha</h3>
                        <p>Sports Manager</p>
                    </div>
                    
                    <div class="team-member">
                        <img src="https://via.placeholder.com/150" alt="Team Member">
                        <h3>Sir Kashif</h3>
                        <p>Sports Enthusiasts</p>
                    </div>
                    
                    <div class="team-member">
                        <img src="https://via.placeholder.com/150" alt="Team Member">
                        <h3>Ma'am Asma</h3>
                        <p>Sports Teacher</p>
                    </div>
                </div>
            </div>
        </section>
    
    <?php require 'include/footer.php';?>
</body>
</html>