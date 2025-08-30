<?php include 'include/nav-bar.php';?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cricket - Sport Details</title>
    <style>
        body {
            margin: 0;
    padding: 0;
    background-image: url(images/cricketform.jpg);
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    position: relative;
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f2f2f2;
        }
        h1{
            font-size: 48px;
            color: #e9d51e; /* ya white agar background dark ho */
            text-align: center;
            margin-top: 50px;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 2px 2px 5px rgba(228, 241, 35, 0.2);
        }
        .images {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 30px;
            flex-wrap: wrap;
        }
        .images img {
            width: 300px;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        .details {
            max-width: 700px;
            margin: 20px auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .details h2 {
            margin-bottom: 15px;
            color: #34495e;
        }
        .details p {
            font-size: 18px;
            margin: 10px 0;
        }
        .requirements {
            margin-top: 20px;
        }
        .requirements ul {
            list-style-type: disc;
            padding-left: 20px;
        }
        .requirements li {
            margin-bottom: 8px;
            font-size: 17px;
        }
        .back-button {
            display: block;
            width: 170px;
            margin: 30px auto 0;
            text-align: center;
            padding: 12px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .back-button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

    <h1 > Cricket</h1>

    
    <div class="details">
        <h2>Game Details</h2>
        <p><strong>Members Required:</strong> 11 players per team</p>
        <p><strong>Type of Game:</strong> Outdoor Team Sport</p>
        <p><strong>Equipment Needed:</strong> Bat, Ball, Wickets</p>
        <p><strong>Description:</strong> Cricket is a popular outdoor sport played between two teams of eleven players each. The game involves batting, bowling, and fielding. It teaches teamwork, patience, and strategy.</p>

        <div class="requirements">
            <h2>Player Requirements</h2>
            <ul>
                <li><strong>Minimum Height:</strong> 5 feet 2 inches (157 cm)</li>
                <li><strong>Weight Range:</strong> 50 kg – 85 kg</li>
                <li><strong>BMI Range:</strong> 18.5 – 24.9 (Healthy BMI)</li>
                <li><strong>Fitness Level:</strong> Good stamina and agility</li>
                <li><strong>Age Limit:</strong> 15 years and above</li>
            </ul>
        </div>
    </div>

    <a href="index.php" class="back-button">← Back to Home</a>

</body>
</html>
