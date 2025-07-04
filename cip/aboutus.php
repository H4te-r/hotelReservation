<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>About Us - Grand Plaza Hotel</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville&family=League+Spartan&family=Poppins&display=swap" rel="stylesheet" />
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
      
      .about-section {
        background-color: #fff;
        color: #3c443f;
        padding: 2.5em 2em;
        font-family: 'Poppins', serif;
        box-shadow: 0 2px 8px rgba(90, 75, 91, 0.05);
        border-radius: 12px;
        margin: 2em auto 2em auto;
        max-width: 900px;
      }
      .about-content h1 {
        font-family: 'Libre Baskerville', serif;
        font-size: 2em;
        color: #5b4b5b;
        margin-bottom: 0.5em;
      }
      .about-content h2 {
        color: #846f84;
        font-size: 1.2em;
        margin-top: 1.5em;
      }
      .about-content p {
        margin-bottom: 1em;
      }
      .about-content ul {
        margin-left: 1em;
        padding-left: 1em;
        list-style-type: none;
      }
      .about-content li {
        margin-bottom: 0.5em;
      }
      .btn-primary {
        display: inline-block;
        background-color: #5b4b5b;
        color: #fff;
        padding: 0.8em 1.5em;
        border-radius: 5px;
        text-decoration: none;
        margin-top: 2em;
        font-weight: bold;
        transition: background-color 0.3s;
      }
      .btn-primary:hover {
        background-color: #3c443f;
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
        .about-section {
          padding: 1.5em 1em;
        }
      }
    </style>
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
            <a href="index.php">Home</a>
          </li>
          <li>
            <a href="aboutus.php"><u>About us</u></a>
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
    <!-- Header Section -->
    <section class="header-section">
      <h1>About us</h1>
      <p>Experience luxury, comfort, and elegance in the heart of the city.</p>
    </section>
    <div class="container">
      <!-- About Us Section -->
      <section class="about-section">
        <div class="about-content">
          <h1>Who We Are</h1>
          <p>Welcome to Grand Plaza Hotel — a luxury destination located in the heart of the city. Whether you're here for business or leisure, we offer exceptional accommodations with stunning city views, top-tier amenities, and warm hospitality.</p>
          <h2>Our Mission</h2>
          <p>To provide every guest with a world-class stay through unmatched service, stylish comfort, and a welcoming atmosphere that feels like home.</p>
          <h2>Our Vision</h2>
          <p>We envision Grand Plaza Hotel as the top choice for travelers seeking elegance, convenience, and a memorable hotel experience.</p>
          <h2>Why Choose Us?</h2>
          <ul>
            <li>✔ Prime downtown location</li>
            <li>✔ Affordable luxury rooms</li>
            <li>✔ 24/7 concierge & room service</li>
            <li>✔ High-speed Wi-Fi and smart facilities</li>
          </ul>
          <a href="bookingform.php" class="btn-primary">Book Your Stay</a>
        </div>
      </section>
    </div>
    <!-- Footer -->
    <footer>
      <p>&copy; 2025 Grand Plaza Hotel. All Rights Reserved.</p>
    </footer>
  </body>
</html>