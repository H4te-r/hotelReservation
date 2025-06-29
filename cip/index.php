<?php
require_once 'config/database.php';

$pdo = getDBConnection();

// Get hotel information
$stmt = $pdo->query("SELECT * FROM hotel LIMIT 1");
$hotel = $stmt->fetch();

// Get room statistics
$stmt = $pdo->query("SELECT COUNT(*) as total_rooms FROM rooms WHERE is_available = 1");
$totalRooms = $stmt->fetch()['total_rooms'];

$stmt = $pdo->query("SELECT COUNT(*) as confirmed_reservations FROM reservations WHERE status = 'confirmed'");
$confirmedReservations = $stmt->fetch()['confirmed_reservations'];

$stmt = $pdo->query("SELECT COUNT(*) as available_rooms FROM rooms WHERE is_available = 1");
$availableRooms = $stmt->fetch()['available_rooms'];
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($hotel['name']); ?> - Luxury Hotel & Resort</title>
    <style>
      /* Fonts and Resets */
      body {
        margin: 0;
        font-family: 'Poppins', serif;
        color: #ffffff;
        background-color: #ffffff;
      }

      h1,
      h2,
      h3 {
        font-family: 'Libre Baskerville', sans-serif;
      }

      a {
        text-decoration: none;
        color: #ffffff;
      }

      ul {
        list-style: none;
        margin: 0;
        padding: 0;
      }

      /* Navbar */
      .navbar {
        background-color: #846f84;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1em 2em;
      }

      .logo {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: #ffffff;
        font-family: 'League Spartan', sans-serif;
        font-size: 1.5em;
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

      /* Hero Section */
      .hero {
        background: linear-gradient(rgba(60, 68, 63, 0.7), rgba(60, 68, 63, 0.7)), url('https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80') center/cover no-repeat;
        height: 80vh;
        position: relative;
      }

      .hero-overlay {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
      }

      .hero-overlay h1 {
        font-size: 3em;
        margin-bottom: 0.5em;
      }

      .hero-overlay p {
        font-size: 1.2em;
        margin: 1em 0;
        max-width: 600px;
      }

      /* Stats Section */
      .stats-section {
        background-color: #f5f5f5;
        padding: 3em 2em;
        color: #3c443f;
      }

      .stats-container {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2em;
      }

      .stat-card {
        text-align: center;
        padding: 2em;
        background: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      }

      .stat-number {
        font-size: 2.5em;
        font-weight: bold;
        color: #516b5d;
        margin-bottom: 0.5em;
      }

      .stat-label {
        font-size: 1.1em;
        color: #5b4b5b;
      }

      /* Buttons */
      .btn-primary {
        background-color: #516b5d;
        color: #fff;
        padding: 0.8em 1.5em;
        border: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s ease;
      }

      .btn-primary:hover {
        background-color: #3c443f;
      }

      .btn-secondary {
        background-color: #5b4b5b;
        color: #fff;
        padding: 0.8em 1.5em;
        border: none;
        border-radius: 5px;
        margin-top: 1em;
        font-weight: bold;
        transition: background-color 0.3s ease;
      }

      .btn-secondary:hover {
        background-color: #846f84;
      }

      /* About Section */
      .about-section {
        background-color: #3c443f;
        padding: 4em 2em;
        text-align: center;
      }

      .about-content {
        max-width: 800px;
        margin: 0 auto;
      }

      .about-content h2 {
        font-size: 2.5em;
        margin-bottom: 1em;
      }

      .about-content p {
        font-size: 1.1em;
        line-height: 1.6;
        margin-bottom: 2em;
      }

      /* Footer */
      footer {
        background-color: #3c443f;
        text-align: center;
        padding: 1.5em 0;
        font-size: 0.9em;
      }

      /* Responsive */
      @media (max-width: 768px) {
        .hero-overlay h1 {
          font-size: 2em;
        }
        
        .stats-container {
          grid-template-columns: 1fr;
        }
        
        .navbar {
          flex-direction: column;
          gap: 1em;
        }
      }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville&family=League+Spartan&family=Poppins&display=swap" rel="stylesheet">
  </head>
  <body>
    <!-- Navigation Bar -->
    <header class="navbar">
      <a href="index.php" class="logo">
        <img src="assets/images/logo.png" alt="Hotel Logo" style="height: 50px; width: auto;">
        <!--<span class="logo-text"><?php echo htmlspecialchars($hotel['name']); ?></span>-->
      </a>
      <nav>
        <ul class="nav-links">
          <li>
            <a href="index.php"><u>Home</u></a>
          </li>
          <li>
            <a href="aboutus.php">About us</a>
          </li>
          <li>
            <a href="rooms.php">Rooms</a>
          </li>
          <li>
            <a href="contacts.php">Contact</a>
          </li>
        </ul>
      </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-overlay">
        <h1><?php echo htmlspecialchars($hotel['name']); ?></h1>
        <p><?php echo htmlspecialchars($hotel['description'] ?? 'Experience luxury and comfort like never before.'); ?></p>
        <a href="bookingform.php" class="btn-secondary">Book Now</a>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
      <div class="stats-container">
        <div class="stat-card">
          <div class="stat-number"><?php echo $totalRooms; ?></div>
          <div class="stat-label">Total Rooms</div>
        </div>
        <div class="stat-card">
          <div class="stat-number"><?php echo $availableRooms; ?></div>
          <div class="stat-label">Available Rooms</div>
        </div>
        <div class="stat-card">
          <div class="stat-number"><?php echo $confirmedReservations; ?></div>
          <div class="stat-label">Confirmed Bookings</div>
        </div>
        <div class="stat-card">
          <div class="stat-number">4.5★</div>
          <div class="stat-label">Guest Rating</div>
        </div>
      </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
      <div class="about-content">
        <h2>Welcome to Luxury</h2>
        <p>
          <?php echo htmlspecialchars($hotel['description'] ?? 'Discover the perfect blend of comfort, luxury, and exceptional service. Our hotel offers world-class amenities, stunning accommodations, and unforgettable experiences for every guest.'); ?>
        </p>
        <a href="bookingform.php" class="btn-primary">Make Your Reservation</a>
      </div>
    </section>

    <!-- Footer -->
    <footer>
      <p>&copy; 2025 <?php echo htmlspecialchars($hotel['name']); ?>. All Rights Reserved.</p>
    </footer>
  </body>
</html>