<?php
session_start();
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$pdo = getDBConnection();

// Get hotel information
$stmt = $pdo->query("SELECT * FROM hotel LIMIT 1");
$hotel = $stmt->fetch();

// Get all available rooms
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
                throw new Exception("All required fields must be filled.");
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
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $status = $_POST['status'] ?? 'pending';
        
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
            $total_price,
            $status
        ]);

        $message = "Reservation added successfully! Booking ID: " . $booking_id;
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Reservation - <?php echo htmlspecialchars($hotel['name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville&family=League+Spartan&family=Poppins&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-hotel"></i> Admin Panel</h3>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </div>
                <div class="nav-item">
                    <a href="hotel.php" class="nav-link">
                        <i class="fas fa-building"></i>
                        Hotel Info
                    </a>
                </div>
                <div class="nav-item">
                    <a href="rooms.php" class="nav-link">
                        <i class="fas fa-bed"></i>
                        Rooms
                    </a>
                </div>
                <div class="nav-item">
                    <a href="reservations.php" class="nav-link">
                        <i class="fas fa-calendar-check"></i>
                        Reservations
                    </a>
                </div>
                <div class="nav-item">
                    <a href="add_reservation.php" class="nav-link active">
                        <i class="fas fa-plus-circle"></i>
                        Add Reservation
                    </a>
                </div>
                
                <div class="nav-divider"></div>
                <div class="nav-item">
                    <a href="?logout=1" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navigation -->
            <div class="top-nav">
                <h1 class="page-title">Add New Reservation</h1>
                <div class="user-info">
                    <a href="reservations.php" class="btn btn-secondary">
                        <i class="fas fa-list"></i>
                        View All Reservations
                    </a>
                    <i class="fas fa-user"></i>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</span>
                </div>
            </div>

            <!-- Alerts -->
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?>">
                    <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Add Reservation Form -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus-circle"></i>
                        Create New Reservation
                    </h3>
                </div>
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Guest Name *</label>
                                <input type="text" name="guest_name" class="form-control" 
                                       value="<?php echo htmlspecialchars($_POST['guest_name'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone" class="form-control" 
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Number of Guests *</label>
                                <input type="number" name="num_guests" class="form-control" min="1" max="10" 
                                       value="<?php echo htmlspecialchars($_POST['num_guests'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Check-in Date *</label>
                                <input type="date" name="check_in_date" class="form-control" 
                                       value="<?php echo htmlspecialchars($_POST['check_in_date'] ?? ''); ?>" 
                                       min="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Check-out Date *</label>
                                <input type="date" name="check_out_date" class="form-control" 
                                       value="<?php echo htmlspecialchars($_POST['check_out_date'] ?? ''); ?>" 
                                       min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Room *</label>
                                <select name="room_id" class="form-select" required>
                                    <option value="">Select a Room</option>
                                    <?php foreach ($rooms as $room): ?>
                                        <option value="<?php echo $room['id']; ?>" 
                                                <?php echo (isset($_POST['room_id']) && $_POST['room_id'] == $room['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($room['room_type']); ?> - Room <?php echo htmlspecialchars($room['room_number']); ?> 
                                            ($<?php echo number_format($room['price_per_night'], 2); ?>/night)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="pending" <?php echo (isset($_POST['status']) && $_POST['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo (isset($_POST['status']) && $_POST['status'] === 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Special Requests</label>
                        <textarea name="special_requests" class="form-control" rows="3"><?php echo htmlspecialchars($_POST['special_requests'] ?? ''); ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Create Reservation
                        </button>
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Back to Dashboard
                        </a>
                    </div>
                </form>
            </div>

            <!-- Available Rooms Info -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bed"></i>
                        Available Rooms
                    </h3>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Room Number</th>
                                <th>Type</th>
                                <th>Capacity</th>
                                <th>Price/Night</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rooms as $room): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                                <td><?php echo htmlspecialchars($room['room_type']); ?></td>
                                <td><?php echo htmlspecialchars($room['capacity']); ?> guests</td>
                                <td>$<?php echo number_format($room['price_per_night'], 2); ?></td>
                                <td>
                                    <span class="badge badge-success">Available</span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 