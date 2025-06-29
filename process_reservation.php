<?php
require_once 'config/database.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDBConnection();
        
        // Validate required fields
        $required_fields = ['room_id', 'guest_name', 'guest_email', 'guest_phone', 'check_in_date', 'check_out_date'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("All required fields must be filled.");
            }
        }
        
        $roomId = (int)$_POST['room_id'];
        $guestName = trim($_POST['guest_name']);
        $guestEmail = trim($_POST['guest_email']);
        $guestPhone = trim($_POST['guest_phone']);
        $checkInDate = $_POST['check_in_date'];
        $checkOutDate = $_POST['check_out_date'];
        $specialRequests = trim($_POST['special_requests'] ?? '');
        
        // Validate email
        if (!filter_var($guestEmail, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email address.");
        }
        
        // Validate dates
        $checkIn = new DateTime($checkInDate);
        $checkOut = new DateTime($checkOutDate);
        $today = new DateTime();
        
        if ($checkIn < $today) {
            throw new Exception("Check-in date cannot be in the past.");
        }
        
        if ($checkOut <= $checkIn) {
            throw new Exception("Check-out date must be after check-in date.");
        }
        
        // Calculate number of nights and total price
        $nights = $checkIn->diff($checkOut)->days;
        
        // Get room details
        $stmt = $pdo->prepare("SELECT r.*, h.name as hotel_name FROM rooms r JOIN hotels h ON r.hotel_id = h.id WHERE r.id = ? AND r.is_available = 1");
        $stmt->execute([$roomId]);
        $room = $stmt->fetch();
        
        if (!$room) {
            throw new Exception("Room not available or not found.");
        }
        
        $totalPrice = $room['price_per_night'] * $nights;
        
        // Check for date conflicts
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as conflicts 
            FROM reservations 
            WHERE room_id = ? 
            AND status IN ('pending', 'confirmed')
            AND (
                (check_in_date <= ? AND check_out_date > ?) OR
                (check_in_date < ? AND check_out_date >= ?) OR
                (check_in_date >= ? AND check_out_date <= ?)
            )
        ");
        $stmt->execute([$roomId, $checkOutDate, $checkInDate, $checkOutDate, $checkInDate, $checkInDate, $checkOutDate]);
        $conflicts = $stmt->fetch()['conflicts'];
        
        if ($conflicts > 0) {
            throw new Exception("This room is not available for the selected dates.");
        }
        
        // Insert reservation
        $stmt = $pdo->prepare("
            INSERT INTO reservations (room_id, guest_name, guest_email, guest_phone, check_in_date, check_out_date, total_price, special_requests, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
        ");
        
        if ($stmt->execute([$roomId, $guestName, $guestEmail, $guestPhone, $checkInDate, $checkOutDate, $totalPrice, $specialRequests])) {
            $reservationId = $pdo->lastInsertId();
            $message = "Reservation submitted successfully! Your reservation ID is: " . $reservationId;
        } else {
            throw new Exception("Error creating reservation. Please try again.");
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Result - Hotel Reservation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .result-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            padding: 40px;
        }
        .success-icon {
            color: #28a745;
            font-size: 4rem;
            margin-bottom: 20px;
        }
        .error-icon {
            color: #dc3545;
            font-size: 4rem;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="result-card text-center">
                    <?php if ($message): ?>
                        <i class="fas fa-check-circle success-icon"></i>
                        <h3 class="text-success mb-3">Reservation Successful!</h3>
                        <p class="lead mb-4"><?php echo htmlspecialchars($message); ?></p>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            We will contact you shortly to confirm your reservation.
                        </div>
                    <?php elseif ($error): ?>
                        <i class="fas fa-exclamation-triangle error-icon"></i>
                        <h3 class="text-danger mb-3">Reservation Failed</h3>
                        <p class="lead mb-4"><?php echo htmlspecialchars($error); ?></p>
                    <?php endif; ?>
                    
                    <div class="d-grid gap-2">
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>
                            Back to Home
                        </a>
                        <?php if ($message): ?>
                            <a href="admin/login.php" class="btn btn-outline-secondary">
                                <i class="fas fa-user-shield me-2"></i>
                                Admin Login
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 