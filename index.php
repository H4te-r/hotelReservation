<?php
require_once 'config/database.php';

$pdo = getDBConnection();

// Get single hotel information
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

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as total_rooms FROM rooms WHERE is_available = 1");
$totalRooms = $stmt->fetch()['total_rooms'];

$stmt = $pdo->query("SELECT COUNT(*) as total_reservations FROM reservations WHERE status = 'confirmed'");
$totalReservations = $stmt->fetch()['total_reservations'];

$stmt = $pdo->query("SELECT COUNT(*) as available_rooms FROM rooms WHERE is_available = 1");
$availableRooms = $stmt->fetch()['available_rooms'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($hotel['name']); ?> - Room Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .hero-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: white;
            padding: 80px 0;
            margin-bottom: 50px;
        }
        .hotel-info {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }
        .hotel-image {
            height: 300px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 4rem;
        }
        .hotel-details {
            padding: 30px;
        }
        .rating {
            color: #ffc107;
            font-size: 1.2rem;
        }
        .room-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 30px;
        }
        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        .room-image {
            height: 200px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }
        .room-info {
            padding: 25px;
        }
        .price-tag {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
        }
        .btn-book {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-book:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .navbar {
            background: rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(10px);
        }
        .navbar-brand {
            font-weight: 700;
            color: white !important;
        }
        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            transition: color 0.3s ease;
        }
        .nav-link:hover {
            color: white !important;
        }
        .stats {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            color: white;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-hotel me-2"></i>
                <?php echo htmlspecialchars($hotel['name']); ?>
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="admin/login.php">
                    <i class="fas fa-user-shield me-2"></i>
                    Admin Login
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 mb-4">
                <i class="fas fa-hotel me-3"></i>
                Welcome to <?php echo htmlspecialchars($hotel['name']); ?>
            </h1>
            <p class="lead mb-4">Experience luxury and comfort in the heart of the city</p>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="stats">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <h3><?php echo $totalRooms; ?></h3>
                                <p>Total Rooms</p>
                            </div>
                            <div class="col-md-4">
                                <h3><?php echo $availableRooms; ?></h3>
                                <p>Available Rooms</p>
                            </div>
                            <div class="col-md-4">
                                <h3><?php echo $totalReservations; ?></h3>
                                <p>Confirmed Bookings</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Hotel Information -->
    <section class="container mb-5">
        <div class="hotel-info">
            <div class="row">
                <div class="col-md-4">
                    <div class="hotel-image">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="hotel-details">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h2 class="mb-1"><?php echo htmlspecialchars($hotel['name']); ?></h2>
                            <div class="rating">
                                <?php for ($i = 0; $i < floor($hotel['rating']); $i++): ?>
                                    <i class="fas fa-star"></i>
                                <?php endfor; ?>
                                <?php if ($hotel['rating'] - floor($hotel['rating']) > 0): ?>
                                    <i class="fas fa-star-half-alt"></i>
                                <?php endif; ?>
                                <span class="ms-1"><?php echo $hotel['rating']; ?></span>
                            </div>
                        </div>
                        
                        <p class="text-muted mb-3">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            <?php echo htmlspecialchars($hotel['address']); ?>
                        </p>
                        
                        <p class="mb-3"><?php echo htmlspecialchars($hotel['description']); ?></p>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fas fa-phone me-1"></i>
                                    <?php echo htmlspecialchars($hotel['phone']); ?>
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fas fa-envelope me-1"></i>
                                    <?php echo htmlspecialchars($hotel['email']); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Rooms Section -->
    <section class="container mb-5">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center text-white mb-5">
                    <i class="fas fa-bed me-2"></i>
                    Available Rooms
                </h2>
            </div>
        </div>
        
        <div class="row">
            <?php foreach ($rooms as $room): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="room-card">
                        <div class="room-image">
                            <i class="fas fa-bed"></i>
                        </div>
                        <div class="room-info">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h4 class="mb-1"><?php echo htmlspecialchars($room['room_type']); ?></h4>
                                <div class="price-tag">
                                    $<?php echo number_format($room['price_per_night']); ?>
                                </div>
                            </div>
                            
                            <p class="text-muted mb-3">
                                <i class="fas fa-door-open me-2"></i>
                                Room <?php echo htmlspecialchars($room['room_number']); ?>
                            </p>
                            
                            <p class="mb-3"><?php echo htmlspecialchars($room['description']); ?></p>
                            
                            <div class="row mb-3">
                                <div class="col-6">
                                    <small class="text-muted">
                                        <i class="fas fa-users me-1"></i>
                                        <?php echo $room['capacity']; ?> people
                                    </small>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Available
                                    </small>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button class="btn btn-primary btn-book" onclick="makeReservation(<?php echo $room['id']; ?>, '<?php echo htmlspecialchars($room['room_type']); ?>', <?php echo $room['price_per_night']; ?>, '<?php echo htmlspecialchars($room['room_number']); ?>')">
                                    <i class="fas fa-calendar-check me-2"></i>
                                    Book Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Reservation Modal -->
    <div class="modal fade" id="reservationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-plus me-2"></i>
                        Make Reservation
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="reservationForm" method="POST" action="process_reservation.php">
                        <input type="hidden" name="room_id" id="roomId">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Guest Name</label>
                                <input type="text" class="form-control" name="guest_name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="guest_email" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" name="guest_phone" required>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Check-in Date</label>
                                <input type="date" class="form-control" name="check_in_date" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Check-out Date</label>
                                <input type="date" class="form-control" name="check_out_date" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Special Requests</label>
                            <textarea class="form-control" name="special_requests" rows="3"></textarea>
                        </div>
                        
                        <div class="alert alert-info">
                            <strong>Room Details:</strong>
                            <div id="roomDetails"></div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check me-2"></i>
                                Confirm Reservation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function makeReservation(roomId, roomType, pricePerNight, roomNumber) {
            document.getElementById('roomId').value = roomId;
            document.getElementById('roomDetails').innerHTML = `
                <strong>Room Type:</strong> ${roomType}<br>
                <strong>Room Number:</strong> ${roomNumber}<br>
                <strong>Price per Night:</strong> $${pricePerNight}
            `;
            
            new bootstrap.Modal(document.getElementById('reservationModal')).show();
        }
        
        // Set minimum dates for check-in and check-out
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            const checkInInput = document.querySelector('input[name="check_in_date"]');
            const checkOutInput = document.querySelector('input[name="check_out_date"]');
            
            checkInInput.min = today;
            checkOutInput.min = today;
            
            checkInInput.addEventListener('change', function() {
                checkOutInput.min = this.value;
            });
        });
    </script>
</body>
</html> 