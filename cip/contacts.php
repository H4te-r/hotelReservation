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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Us - <?php echo htmlspecialchars($hotel['name']); ?></title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville&family=League+Spartan&family=Poppins&display=swap" rel="stylesheet"/>
  <style>
    body {
      margin: 0;
      background: #f7f6f9;
      font-family: 'Poppins', serif;
      color: #3c443f;
    }
    h1, h2, h3 {
        font-family: 'Libre Baskerville', sans-serif;
    }
    .container {
      max-width: 1100px;
      margin: 0 auto;
      padding: 0 2em;
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

    .contact-section {
      background-color: #fff;
      color: #3c443f;
      padding: 2.5em 2em;
      font-family: 'Poppins', serif;
      box-shadow: 0 2px 8px rgba(90, 75, 91, 0.05);
      border-radius: 12px;
      margin: 2em auto 2em auto;
      max-width: 900px;
    }
    .contact-container {
      max-width: 800px;
      margin: auto;
      text-align: center;
    }
    .contact-container h1 {
      font-family: 'Libre Baskerville', serif;
      font-size: 2em;
      color: #5b4b5b;
      margin-bottom: 0.5em;
    }
    .contact-container p {
      font-size: 1.1em;
      margin-bottom: 2em;
    }
    .contact-info {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 2em;
      margin-bottom: 2.5em;
    }
    .contact-info h2 {
      color: #846f84;
      font-size: 1.2em;
      margin-bottom: 0.5em;
    }
    .map-container iframe {
      width: 100%;
      height: 300px;
      border: none;
      border-radius: 8px;
      box-shadow: 0 0 15px rgba(91, 75, 91, 0.1);
    }
    footer {
      background-color: #3c443f;
      color: #ffffff;
      text-align: center;
      padding: 1.5em 0;
      margin-top: 3em;
      font-size: 0.9em;
    }
    @media (max-width: 768px) {
      .navbar {
        flex-direction: column;
        gap: 1em;
      }
      .header-section {
        height: 60vh;
      }
      .header-section h1 {
        font-size: 2em;
      }
      .contact-section {
        padding: 1.5em 1em;
      }
    }
  </style>
</head>
<body>
  <!-- Navigation Bar -->
  <header class="navbar">
    <a href="index.php" class="logo">
      <img src="assets/images/logo.png" alt="Hotel Logo">
      <span class="logo-text"><?php echo htmlspecialchars($hotel['name']); ?></span>
    </a>
    <nav>
      <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="aboutus.php">About us</a></li>
        <li><a href="rooms.php">Rooms</a></li>
        <li><a href="contacts.php"><u>Contact</u></a></li>
      </ul>
    </nav>
  </header>
  <!-- Header Section -->
  <section class="header-section">
    <h1>Contacts</h1>
    <p>We'd love to hear from you! Reach out for reservations, questions, or feedback.</p>
  </section>
  <div class="container">
    <!-- Contact Section -->
    <section class="contact-section">
      <div class="contact-container">
        <h1>Contact Us</h1>
        <p>Whether you have questions about your reservation or want to know more about our amenities, we're here to help.</p>
        <div class="contact-info">
          <div>
            <h2>📍 Address</h2>
            <p>123 Main Street, Downtown, City</p>
          </div>
          <div>
            <h2>📞 Phone</h2>
            <p>+1-555-0123</p>
          </div>
          <div>
            <h2>📧 Email</h2>
            <p>info@grandplaza.com</p>
          </div>
          <div>
            <h2>🌐 Facebook</h2>
            <p><a href="https://facebook.com/grandplazahotel" target="_blank" style="color:#5b4b5b;text-decoration:underline;">facebook.com/grandplazahotel</a></p>
          </div>
        </div>
        <div class="map-container">
          <iframe src="https://maps.google.com/maps?q=manila%20city&t=&z=13&ie=UTF8&iwloc=&output=embed" allowfullscreen loading="lazy"></iframe>
        </div>
      </div>
    </section>
  </div>
  <!-- Footer -->
  <footer>
    <p>&copy; 2025 <?php echo htmlspecialchars($hotel['name']); ?>. All Rights Reserved.</p>
  </footer>
</body>
</html>