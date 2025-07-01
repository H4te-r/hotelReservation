<?php
require_once 'config/database.php';

$pdo = getDBConnection();

// Get hotel information
$stmt = $pdo->query("SELECT * FROM hotel LIMIT 1");
$hotel = $stmt->fetch();

// Get available rooms
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

// Process form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $required_fields = ['guest_name', 'email', 'check_in_date', 'check_out_date', 'num_guests', 'room_id'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("All fields are required.");
            }
        }

        // Validate dates
        $check_in = new DateTime($_POST['check_in_date']);
        $check_out = new DateTime($_POST['check_out_date']);
        $today = new DateTime();
        
        if ($check_in < $today) {
            throw new Exception("Check-in date cannot be in the past.");
        }
        
        if ($check_out <= $check_in) {
            throw new Exception("Check-out date must be after check-in date.");
        }

        // Check if room is available for the selected dates
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as conflicting_reservations
            FROM reservations 
            WHERE room_id = ? 
            AND status IN ('pending', 'confirmed')
            AND (
                (check_in_date <= ? AND check_out_date > ?) OR
                (check_in_date < ? AND check_out_date >= ?) OR
                (check_in_date >= ? AND check_out_date <= ?)
            )
        ");
        $stmt->execute([
            $_POST['room_id'],
            $_POST['check_out_date'],
            $_POST['check_in_date'],
            $_POST['check_out_date'],
            $_POST['check_in_date'],
            $_POST['check_in_date'],
            $_POST['check_out_date']
        ]);
        
        $conflicts = $stmt->fetch()['conflicting_reservations'];
        if ($conflicts > 0) {
            throw new Exception("Selected room is not available for the chosen dates.");
        }

        // Generate booking ID
        $booking_id = 'BK' . date('Ymd') . rand(1000, 9999);

        // Calculate total price
        $check_in = new DateTime($_POST['check_in_date']);
        $check_out = new DateTime($_POST['check_out_date']);
        $nights = $check_out->diff($check_in)->days;
        
        // Get room price
        $stmt = $pdo->prepare("SELECT price_per_night FROM rooms WHERE id = ?");
        $stmt->execute([$_POST['room_id']]);
        $room_price = $stmt->fetch()['price_per_night'];
        $total_price = $room_price * $nights;

        // Insert reservation
        $stmt = $pdo->prepare("
            INSERT INTO reservations (
                booking_id, guest_name, email, phone, check_in_date, 
                check_out_date, num_guests, room_id, special_requests, total_price, status, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
        ");
        
        $stmt->execute([
            $booking_id,
            $_POST['guest_name'],
            $_POST['email'],
            $_POST['phone'] ?? '',
            $_POST['check_in_date'],
            $_POST['check_out_date'],
            $_POST['num_guests'],
            $_POST['room_id'],
            $_POST['special_requests'] ?? '',
            $total_price
        ]);

        $message = "Reservation submitted successfully! Your booking ID is: " . $booking_id;
        $messageType = 'success';
        
        // Clear form data after successful submission
        $_POST = array();
        
    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reservation Form - <?php echo htmlspecialchars($hotel['name']); ?></title>
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

      /* Navbar */    .navbar {
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
    
      /* Message styles */
      .message {
        padding: 1em;
        margin: 1em 0;
        border-radius: 5px;
        text-align: center;
      }

      .message.success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
      }

      .message.error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
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

      .phone-input-wrapper {
        display: flex;
        align-items: center;
        border: 1px solid #ccc;
        border-radius: 4px;
        overflow: hidden;
      }

      .country-prefix {
        background-color: #f5f5f5;
        padding: 8px 12px;
        border-right: 1px solid #ddd;
        font-weight: 500;
        color: #333;
        white-space: nowrap;
      }

      .phone-input-wrapper input {
        border: none;
        outline: none;
        padding: 8px 12px;
        flex: 1;
        font-size: inherit;
      }

      .phone-input-wrapper:focus-within {
        border-color: #007bff;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
      }

      .phone-input-wrapper input:invalid {
        border-left: 3px solidrgb(255, 255, 255);
      }

      .phone-input-wrapper input:invalid + .error-message {
        display: block;
      }

      .error-message {
        display: none;
        color: #dc3545;
        font-size: 0.875em;
        margin-top: 5px;
      }
      .btn-primary:hover {
        background-color: #3c443f;
      }

      /* Room info display */
      .room-info {
        background-color: #e8f5e8;
        padding: 1em;
        border-radius: 5px;
        margin-top: 0.5em;
        font-size: 0.9em;
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
      <a href="index.php" class="logo">
        <img src="assets/images/logo.png" alt="Hotel Logo" style="height: 50px; width: auto;">
        <!-- <span class="logo-text"><?php echo htmlspecialchars($hotel['name']); ?></span> -->
      </a>
      <nav>
        <ul class="nav-links">
          <li>
            <a href="index.php">Home</a>
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
    <!-- Reservation Form -->
    <main class="reservation-section">
      <h1>Hotel Reservation Form</h1>
      
      <?php if ($message): ?>
        <div class="message <?php echo $messageType; ?>">
          <?php echo htmlspecialchars($message); ?>
        </div>
      <?php endif; ?>

      <form class="reservation-form" method="POST" action="">
        <div class="form-group">
          <label for="guest_name">Guest Name</label>
          <input type="text" pattern="^[a-zA-Z ]*$" id="guest_name" name="guest_name" placeholder="Full Name" value="<?php echo htmlspecialchars($_POST['guest_name'] ?? ''); ?>" required />
        </div>
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required />
        </div>
        <div class="form-group">
          <label for="phone">Phone Number</label>
          <div class="phone-input-wrapper">
            <span class="country-prefix">+63</span>
            <input type="tel" id="phone" name="phone" pattern="9[0-9]{9}" inputmode="numeric" maxlength="10" placeholder="Phone Number" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required />
          </div>
        </div>
        <div class="form-group">
          <label for="check_in_date">Check-in Date</label>
          <input type="date" id="check_in_date" name="check_in_date" value="<?php echo htmlspecialchars($_POST['check_in_date'] ?? ''); ?>" required />
        </div>
        <div class="form-group">
          <label for="check_out_date">Check-out Date</label>
          <input type="date" id="check_out_date" name="check_out_date" value="<?php echo htmlspecialchars($_POST['check_out_date'] ?? ''); ?>" required />
        </div>
        <div class="form-group">
          <label for="num_guests">Number of Guests</label>
          <input type="number" id="num_guests" name="num_guests" min="1" max="10" placeholder="e.g. 2" value="<?php echo htmlspecialchars($_POST['num_guests'] ?? ''); ?>" required />
        </div>
        <div class="form-group">
          <label for="room_id">Room Type</label>
          <select id="room_id" name="room_id" required>
            <option value="">-- Select Room Type --</option>
            <?php foreach ($rooms as $room): ?>
              <option value="<?php echo $room['id']; ?>" 
                      data-price="<?php echo $room['price_per_night']; ?>"
                      data-capacity="<?php echo $room['capacity']; ?>"
                      <?php echo (isset($_POST['room_id']) && $_POST['room_id'] == $room['id']) || 
                                (isset($_GET['room_id']) && $_GET['room_id'] == $room['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($room['name']); ?> - ₱<?php echo number_format($room['price_per_night']); ?>/night
              </option>
            <?php endforeach; ?>
          </select>
          <div id="room-details" class="room-info" style="display: none;"></div>
        </div>
        <div class="form-group full">
          <label for="special_requests">Special Requests</label>
          <textarea id="special_requests" name="special_requests" placeholder="Any additional requests..."><?php echo htmlspecialchars($_POST['special_requests'] ?? ''); ?></textarea>
        </div>
        <button type="submit" class="btn-primary">Confirm Reservation</button>
      </form>
    </main>
    <!-- Footer -->
    <footer>
      <p>&copy; 2025 <?php echo htmlspecialchars($hotel['name']); ?>. All Rights Reserved.</p>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
      const phoneInput = document.getElementById('phone');
      const errorMessage = document.createElement('div');
      errorMessage.className = 'error-message';
      phoneInput.parentNode.parentNode.appendChild(errorMessage);
      
      phoneInput.addEventListener('input', function() {
        const value = this.value;
        
        if (value.length > 0 && !value.startsWith('9')) {
          this.setCustomValidity('Phone number must start with 9');
          errorMessage.textContent = 'Phone number must start with 9';
          errorMessage.style.display = 'block';
        } else if (value.length > 0 && value.length !== 10) {
          this.setCustomValidity('Phone number must be exactly 10 digits');
          errorMessage.textContent = 'Phone number must be exactly 10 digits';
          errorMessage.style.display = 'block';
        } else {
          this.setCustomValidity('');
          errorMessage.style.display = 'none';
        }
      });
      
      phoneInput.addEventListener('blur', function() {
        const value = this.value;
        if (value.length > 0 && value.length !== 10) {
          this.setCustomValidity('Phone number must be exactly 10 digits');
          errorMessage.textContent = 'Phone number must be exactly 10 digits';
          errorMessage.style.display = 'block';
        }
      });
    });

      // Filter rooms by number of guests
      const numGuestsInput = document.getElementById('num_guests');
      const roomSelect = document.getElementById('room_id');
      const allRoomOptions = Array.from(roomSelect.options).filter(opt => opt.value !== "");

      function filterRoomsByGuests() {
        const numGuests = parseInt(numGuestsInput.value, 10) || 1;
        // Remove all options except the placeholder
        roomSelect.innerHTML = '<option value="">-- Select Room Type --</option>';
        allRoomOptions.forEach(opt => {
          const capacity = parseInt(opt.getAttribute('data-capacity'), 10);
          if (capacity >= numGuests) {
            roomSelect.appendChild(opt);
          }
        });
      }

      numGuestsInput.addEventListener('input', filterRoomsByGuests);
      // Initial filter on page load
      filterRoomsByGuests();

      const phoneInput = document.getElementById('phone');
    
    // Remove non-digits and limit to 11 characters
    phoneInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    
    // Block non-numeric keys
    phoneInput.addEventListener('keypress', function(e) {
        if (!/[0-9]/.test(e.key) && !['Backspace', 'Delete', 'Tab'].includes(e.key)) {
            e.preventDefault();
        }
    });

      // Show room details when room is selected
      document.getElementById('room_id').addEventListener('change', function() {
        const roomDetails = document.getElementById('room-details');
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
          const price = selectedOption.getAttribute('data-price');
          const capacity = selectedOption.getAttribute('data-capacity');
          roomDetails.innerHTML = `
            <strong>Room Details:</strong><br>
            Price: ₱${parseInt(price).toLocaleString()}/night<br>
            Capacity: ${capacity} guests
          `;
          roomDetails.style.display = 'block';
        } else {
          roomDetails.style.display = 'none';
        }
      });

  // Set minimum date for check-in and check-out
  const today = new Date().toISOString().split('T')[0];
  document.getElementById('check_in_date').min = today;
  document.getElementById('check_out_date').min = today;

  // Update check-out minimum date when check-in changes
  document.getElementById('check_in_date').addEventListener('change', function () {
    document.getElementById('check_out_date').min = this.value;
    if (
      document.getElementById('check_out_date').value &&
      document.getElementById('check_out_date').value <= this.value
    ) {
      document.getElementById('check_out_date').value = '';
    }
  });

  // Auto-show room details if room is pre-selected
  window.addEventListener('load', function () {
    const roomSelect = document.getElementById('room_id');
    if (roomSelect.value) {
      roomSelect.dispatchEvent(new Event('change'));
    }

    // Restrict guest name input to letters and spaces only
    document.getElementById('guest_name').addEventListener('keypress', function (e) {
      const char = String.fromCharCode(e.which);
      if (!/^[a-zA-Z ]$/.test(char)) {
        e.preventDefault(); // Block the character from being entered
      }
    });

    // Clean pasted input for guest name
    document.getElementById('guest_name').addEventListener('input', function (e) {
      this.value = this.value.replace(/[^a-zA-Z ]/g, '');
    });
  });
</script>

  </body>
</html>