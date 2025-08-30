<?php include 'include/nav-bar.php';?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Badminton - Sport Details</title>
    <style>
        body {
          
    margin: 0;
    padding: 0;
    background-image: url(images/b.jpg);
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    position: relative;

            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f2f2f2;
        }
        h1 {
            text-align: center;
            color: #2c3e50;
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

    <h1>Badminton</h1>

    <div class="details">
        <h2>Game Details</h2>
        <p><strong>Members Required:</strong></p>
        <ul style="padding-left: 20px; font-size: 18px;">
            <li><strong>Singles:</strong> 1 player vs 1 player</li>
            <li><strong>Doubles:</strong> 2 players vs 2 players</li>
        </ul>
        <p><strong>Type of Game:</strong> Indoor/Outdoor Racket Sport</p>
        <p><strong>Equipment Needed:</strong> Racket, Shuttlecock, Net</p>
        <p><strong>Description:</strong> Badminton is a fast-paced racket sport that can be played individually (singles) or in pairs (doubles). It demands agility, quick reflexes, and excellent coordination. Points are scored by hitting the shuttlecock over the net into the opponent’s court.</p>

        <div class="requirements">
            <h2>Player Requirements</h2>
            <ul>
                <li><strong>Minimum Height:</strong> 5 feet (152 cm)</li>
                <li><strong>Weight Range:</strong> 45 kg – 75 kg</li>
                <li><strong>BMI Range:</strong> 18.5 – 24.9 (Healthy BMI)</li>
                <li><strong>Fitness Level:</strong> Excellent reflexes and quick footwork</li>
                <li><strong>Age Limit:</strong> 12 years and above</li>
            </ul>
        </div>
    </div>

    <a href="index.php" class="back-button">← Back to Home</a>

</body>
</html>
