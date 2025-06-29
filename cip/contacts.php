<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Us - Grand Plaza Hotel</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville&family=Poppins&display=swap" rel="stylesheet"/>
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
    .navbar {
      background-color: #846f84;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1em 2em;
    }
    .logo {
      font-family: 'Libre Baskerville', serif;
      font-size: 1.7em;
      color: #fff;
      letter-spacing: 1px;
      font-weight: bold;
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
    .header-section {
      width: 100vw;
      position: relative;
      left: 50%;
      right: 50%;
      margin-left: -50vw;
      margin-right: -50vw;
      background: linear-gradient(rgba(60, 68, 63, 0.8), rgba(60, 68, 63, 0.8)), 
                  url('https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80') center/cover;
      color: white;
      text-align: center;
      padding: 4em 2em;
      border-radius: 0 0 18px 18px;
      margin-bottom: 2em;
      box-sizing: border-box;
    }

    .header-section h1 {
        font-size: 3em;
        margin-bottom: 0.5em;
    }

    .header-section p {
        font-size: 1.2em;
        max-width: 600px;
        margin: 0 auto;
    }

    .contact-section {
      background-color: #fff;
      color: #3c443f;
      padding: 2.5em 2em;
      font-family: 'Poppins', serif;
      box-shadow: 0 2px 8px rgba(90, 75, 91, 0.05);
      border-radius: 12px;
      margin: 0 auto 2em auto;
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
      width: 100vw;
      position: relative;
      left: 50%;
      right: 50%;
      margin-left: -50vw;
      margin-right: -50vw;
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
        padding: 2em 1em;
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
    <div class="logo">Grand Plaza Hotel</div>
    <nav>
      <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="aboutus.php">About us</a></li>
        <li><a href="rooms.php">Rooms</a></li>
        <li><a href="contacts.php"><u>Contact</u></a></li>
      </ul>
    </nav>
  </header>
  <!-- Header Section (full width) -->
  <section class="header-section">
    <h1>Contacts</h1>
    <p>We’d love to hear from you! Reach out for reservations, questions, or feedback.</p>
  </section>
  <div class="container">
    <!-- Contact Section -->
    <section class="contact-section">
      <div class="contact-container">
        <h1>Contact Us</h1>
        <p>Whether you have questions about your reservation or want to know more about our amenities, we’re here to help.</p>
        <div class="contact-info">
          <div>
            <h2>📍 Address</h2>
            <p>123 Luxury Lane, Downtown City, PH 1000</p>
          </div>
          <div>
            <h2>📞 Phone</h2>
            <p>+63 912 345 6789</p>
          </div>
          <div>
            <h2>📧 Email</h2>
            <p>info@grandplazahotel.com</p>
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
  <!-- Footer (full width) -->
  <footer>
    <p>&copy; 2025 Grand Plaza Hotel. All Rights Reserved.</p>
  </footer>
</body>
</html>