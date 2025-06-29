<?php
require_once 'config/database.php';

$pdo = getDBConnection();

// Get hotel information
$stmt = $pdo->query("SELECT * FROM hotel LIMIT 1");
$hotel = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Access - <?php echo htmlspecialchars($hotel['name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville&family=League+Spartan&family=Poppins&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .admin-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 3em;
            text-align: center;
            max-width: 400px;
            width: 90%;
        }

        .admin-icon {
            font-size: 4rem;
            color: #516b5d;
            margin-bottom: 1em;
        }

        h1 {
            font-family: 'Libre Baskerville', sans-serif;
            color: #3c443f;
            margin-bottom: 0.5em;
        }

        p {
            color: #666;
            margin-bottom: 2em;
        }

        .btn-admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1em 2em;
            border: none;
            border-radius: 25px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .back-link {
            margin-top: 2em;
        }

        .back-link a {
            color: #516b5d;
            text-decoration: none;
            font-weight: 600;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-icon">🔐</div>
        <h1>Admin Access</h1>
        <p>Administrative panel for <?php echo htmlspecialchars($hotel['name']); ?></p>
        
        <a href="admin/login.php" class="btn-admin">Login to Admin Panel</a>
        
        <div class="back-link">
            <a href="index.php">← Back to Hotel Website</a>
        </div>
    </div>
</body>
</html> 