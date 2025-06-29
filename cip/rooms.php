<?php
require_once 'config/database.php';

$pdo = getDBConnection();

// Get hotel information
$stmt = $pdo->query("SELECT * FROM hotel LIMIT 1");
$hotel = $stmt->fetch();

// Get all available rooms with their details
$stmt = $pdo->query("
    SELECT r.*, 
           COUNT(res.id) as total_reservations,
           COUNT(CASE WHEN res.status IN ('pending', 'confirmed') THEN 1 END) as active_reservations
    FROM rooms r
    LEFT JOIN reservations res ON r.id = res.room_id
    WHERE r.is_available = 1
    GROUP BY r.id
    ORDER BY r.price_per_night
");
$rooms = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Rooms - <?php echo htmlspecialchars($hotel['name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville&family=League+Spartan&family=Poppins&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', serif;
            background-color: #f5f5f5;
            color: #3c443f;
        }

        h1, h2, h3 {
            font-family: 'Libre Baskerville', sans-serif;
        }

        /* Navbar - Consistent with index.php */
        .navbar {
            background-color: #846f84;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1em 2em;
        }

        .logo {
            font-family: 'League Spartan', sans-serif;
            font-size: 1.5em;
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #ffffff;
            font-weight: bold;
            transition: opacity 0.3s ease;
        }

        .logo:hover {
            opacity: 0.8;
        }

        .logo img {
            height: 40px;
            width: auto;
            margin-right: 10px;
            border-radius: 5px;
        }

        .logo-text {
            color: #ffffff;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 1.5em;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .nav-links li a {
            font-weight: 600;
            color: #fff;
            text-decoration: none;
            padding: 0.3em 0.7em;
            border-radius: 4px;
            transition: background 0.2s;
        }
        .nav-links li a.active,
        .nav-links li a:hover {
            background: #5b4b5b;
            color: #fff;
        }

        .btn-primary {
            background-color: #516b5d;
            color: #fff;
            padding: 0.8em 1.5em;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #3c443f;
        }

        /* Header Section - Consistent height with index.php hero */
        .header-section {
            background: linear-gradient(rgba(60, 68, 63, 0.7), rgba(60, 68, 63, 0.7)), 
                        url('https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80') center/cover no-repeat;
            height: 80vh;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
        }

        .header-section h1 {
            font-size: 3em;
            margin-bottom: 0.5em;
        }

        .header-section p {
            font-size: 1.2em;
            margin: 1em 0;
            max-width: 600px;
        }

        /* Rooms Grid */
        .rooms-container {
            max-width: 1200px;
            margin: 3em auto;
            padding: 0 2em;
        }

        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2em;
        }

        .room-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .room-image {
            height: 250px;
            background: url('https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80') center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }

        .room-info {
            padding: 2em;
        }

        .room-name {
            font-size: 1.5em;
            font-weight: bold;
            color: #3c443f;
            margin-bottom: 0.5em;
        }

        .room-description {
            color: #666;
            margin-bottom: 1em;
            line-height: 1.6;
        }

        .room-features {
            display: flex;
            gap: 1em;
            margin-bottom: 1.5em;
            flex-wrap: wrap;
        }

        .feature {
            background-color: #f0f0f0;
            padding: 0.3em 0.8em;
            border-radius: 15px;
            font-size: 0.9em;
            color: #5b4b5b;
        }

        .room-price {
            font-size: 1.8em;
            font-weight: bold;
            color: #516b5d;
            margin-bottom: 1em;
        }

        .btn-book-room {
            background-color: #516b5d;
            color: white;
            padding: 0.8em 1.5em;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-book-room:hover {
            background-color: #3c443f;
        }

        /* Footer */
        footer {
            background-color: #3c443f;
            color: #ffffff;
            text-align: center;
            padding: 1.5em 0;
            margin-top: 3em;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-section {
                height: 60vh;
            }
            
            .header-section h1 {
                font-size: 2em;
            }
            
            .rooms-grid {
                grid-template-columns: 1fr;
            }
            
            .navbar {
                flex-direction: column;
                gap: 1em;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <header class="navbar">
        <a href="index.php" class="logo">
            <img src="assets/images/logo.png" alt="Hotel Logo" style="height: 50px; width: auto;">
            <!--<span class="logo-text"><?php echo htmlspecialchars($hotel['name']); ?></span>-->
        </a>
        <nav>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="aboutus.php">About us</a></li>
                <li><a href="rooms.php"><u>Rooms</u></a></li>
                <li><a href="contacts.php">Contact</a></li>
            </ul>
        </nav>
    </header>

    <!-- Header Section -->
    <section class="header-section">
        <h1>Our Rooms</h1>
        <p>Discover our luxurious accommodations designed for your comfort and relaxation</p>
    </section>

    <!-- Rooms Grid -->
    <section class="rooms-container">
        <div class="rooms-grid">
            <?php foreach ($rooms as $room): ?>
            <div class="room-card">
                <div class="room-image">
                    <i class="fas fa-bed"></i>
                </div>
                <div class="room-info">
                    <div class="room-name"><?php echo htmlspecialchars($room['name']); ?></div>
                    <div class="room-description">
                        <?php echo htmlspecialchars($room['description'] ?? 'Luxurious room with modern amenities and comfortable furnishings.'); ?>
                    </div>
                    <div class="room-features">
                        <span class="feature"><?php echo $room['capacity']; ?> Guests</span>
                        <span class="feature"><?php echo $room['size']; ?> sq ft</span>
                        <?php if ($room['has_wifi']): ?>
                            <span class="feature">WiFi</span>
                        <?php endif; ?>
                        <?php if ($room['has_tv']): ?>
                            <span class="feature">TV</span>
                        <?php endif; ?>
                        <?php if ($room['has_ac']): ?>
                            <span class="feature">AC</span>
                        <?php endif; ?>
                    </div>
                    <div class="room-price">₱<?php echo number_format($room['price_per_night']); ?>/night</div>
                    <a href="bookingform.php?room_id=<?php echo $room['id']; ?>" class="btn-book-room">Book This Room</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 <?php echo htmlspecialchars($hotel['name']); ?>. All Rights Reserved.</p>
    </footer>
</body>
</html>