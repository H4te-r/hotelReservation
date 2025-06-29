<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reservation Form</title>
    <link rel="stylesheet" href="reservation.css" />
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville&family=League+Spartan&family=Poppins&display=swap" rel="stylesheet" />
    <style>
      body {
        margin: 0;
        font-family: 'Poppins', serif;
        background-color: #ffffff;
        color: #3c443f;
      }

      h1 {
        font-family: 'Libre Baskerville', sans-serif;
        text-align: center;
        color: #5b4b5b;
        margin-top: 1em;
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
        color: #ffffff;
      }

      .nav-links {
        display: flex;
        gap: 1.5em;
      }

      .nav-links li a {
        color: #ffffff;
        font-weight: 600;
      }

      /* Reservation Form */
      .reservation-section {
        max-width: 700px;
        margin: 2em auto;
        padding: 2em;
        background-color: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(91, 75, 91, 0.1);
      }

      .reservation-form {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5em;
      }

      .form-group {
        display: flex;
        flex-direction: column;
      }

      .form-group.full {
        grid-column: span 2;
      }

      label {
        margin-bottom: 0.5em;
        font-weight: bold;
        color: #3c443f;
      }

      input,
      select,
      textarea {
        padding: 0.8em;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-family: 'Poppins', serif;
      }

      textarea {
        resize: vertical;
        min-height: 100px;
      }

      .btn-primary {
        grid-column: span 2;
        background-color: #516b5d;
        color: #fff;
        padding: 0.9em;
        border: none;
        border-radius: 5px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s ease;
      }

      .btn-primary:hover {
        background-color: #3c443f;
      }

      /* Footer */
      footer {
        background-color: #3c443f;
        color: #ffffff;
        text-align: center;
        padding: 1em;
        margin-top: 3em;
      }

      /* Responsive */
      @media (max-width: 600px) {
        .reservation-form {
          grid-template-columns: 1fr;
        }

        .btn-primary {
          grid-column: span 1;
        }
      }
    </style>
  </head>
  <body>
    <!-- Header -->
    <header class="navbar">
      <div class="logo">Special Service</div>
      <nav>
        <ul class="nav-links">
          <li>
            <a href="homepage.php">Home</a>
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
    </header>
    <!-- Reservation Form -->
    <main class="reservation-section">
      <h1>Hotel Reservation Form</h1>
      <form class="reservation-form">
        <div class="form-group">
          <label for="bookingId">Booking ID</label>
          <input type="text" id="bookingId" required />
        </div>
        <div class="form-group">
          <label for="guestsName">Guests Name</label>
          <input type="text" id="guestsName" placeholder="Full Name" required />
        </div>
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" placeholder="Email" required />
        </div>
        <div class="form-group">
          <label for="checkin">Check-in Date</label>
          <input type="date" id="checkin" required />
        </div>
        <div class="form-group">
          <label for="checkout">Check-out Date</label>
          <input type="date" id="checkout" required />
        </div>
        <div class="form-group">
          <label for="guests">Number of Guests</label>
          <input type="number" id="guests" min="1" placeholder="e.g. 2" required />
        </div>
        <div class="form-group">
          <label for="room">Room Type</label>
          <select id="room" required>
            <option value="">-- Select Room Type --</option>
            <option value="deluxe">Deluxe Room</option>
            <option value="suite">Suite</option>
            <option value="family">Family Room</option>
          </select>
        </div>
        <div class="form-group full">
          <label for="requests">Special Requests</label>
          <textarea id="requests" placeholder="Any additional requests..."></textarea>
        </div>
        <button type="submit" class="btn-primary">Confirm Reservation</button>
      </form>
    </main>
    <!-- Footer -->
    <footer>
      <p>&copy; 2025 Special Service Hotel & Resort. All Rights Reserved.</p>
    </footer>
  </body>
</html>