<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
        font-family: 'League Spartan', sans-serif;
        font-size: 1.5em;
      }

      .nav-links {
        display: flex;
        gap: 1.5em;
      }

      .nav-links li a {
        font-weight: 600;
      }

      /* Hero Section */
      .hero {
        background: url('hotel-room.jpg') center/cover no-repeat;
        height: 80vh;
        position: relative;
      }

      .hero-overlay {
        background-color: rgba(60, 68, 63, 0.7);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
      }

      .hero-overlay h1 {
        font-size: 3em;
      }

      .hero-overlay p {
        font-size: 1.2em;
        margin: 1em 0;
      }

      /* Buttons */
      .btn-primary {
        background-color: #516b5d;
        color: #fff;
        padding: 0.8em 1.5em;
        border: none;
        border-radius: 5px;
        font-weight: bold;
      }

      .btn-secondary {
        background-color: #5b4b5b;
        color: #fff;
        padding: 0.8em 1.5em;
        border: none;
        border-radius: 5px;
        margin-top: 1em;
        font-weight: bold;
      }

      /* Booking Form */
      .booking-form {
        background-color: #f5f5f5;
        padding: 2em;
        display: flex;
        justify-content: center;
      }

      .booking-form form {
        display: flex;
        gap: 1em;
      }

      .booking-form input,
      .booking-form select {
        padding: 0.7em;
        border: 1px solid #ccc;
        border-radius: 4px;
      }

      /* Footer */
      footer {
        background-color: #3c443f;
        text-align: center;
        padding: 1.5em 0;
        font-size: 0.9em;
      }
    </style>
  </head>
  <body>
    <!DOCTYPE html>
    <html lang="en">
      <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Marimar Hotel & Resort</title>
        <link rel="stylesheet" href="style.css">
        <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville&family=League+Spartan&family=Poppins&display=swap" rel="stylesheet">
      </head>
      <body>
        <!-- Navigation Bar -->
        <header class="navbar">
          <div class="logo">Special Service</div>
          <nav>
            <ul class="nav-links">
              <li>
                <a href="homepage.php"><u>Home</u></a>
              </li>
              <li>
                <a href="#">About us</a>
              </li>
              <li>
                <a href="#">Rooms</a>
              </li>
              <li>
                <a href="#">Contact</a>
              </li>
            </ul>
          </nav>
          <a href="#" class="btn-primary">Gallery</a>
        </header>
        <!-- Hero Section -->
        <section class="hero">
          <div class="hero-overlay">
            <h1>Special Service Hotel</h1>
            <p>Experience luxury and comfort like never before.</p>
            <a href="bookingform.php" class="btn-secondary">Book Now</a>
          </div>
        </section>
        <!-- Footer -->
        <footer>
          <p>&copy; 2025 Special Service Hotel & Resort. All Rights Reserved.</p>
        </footer>
      </body>
    </html>