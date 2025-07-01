<?php
require_once 'auth.php';
requireAuth();

$pdo = getDBConnection();

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as total_rooms FROM rooms");
$totalRooms = $stmt->fetch()['total_rooms'];

$stmt = $pdo->query("SELECT COUNT(*) as total_reservations FROM reservations");
$totalReservations = $stmt->fetch()['total_reservations'];

$stmt = $pdo->query("SELECT COUNT(*) as pending_reservations FROM reservations WHERE status = 'pending'");
$pendingReservations = $stmt->fetch()['pending_reservations'];

// Calculate available rooms: Exclude rooms with confirmed future or current reservations
$today = date('Y-m-d');
$stmt = $pdo->query("
    SELECT COUNT(*) as available_rooms
    FROM rooms
    WHERE is_available = 1
    AND id NOT IN (
        SELECT room_id FROM reservations
        WHERE status = 'confirmed'
        AND check_out_date > '$today'
    )
");
$availableRooms = $stmt->fetch()['available_rooms'];

// Get hotel info
$stmt = $pdo->query("SELECT * FROM hotel LIMIT 1");
$hotel = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo htmlspecialchars($hotel['name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville&family=League+Spartan&family=Poppins&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <a href="../index.php" class="logo">
                    <img src="../assets/images/logo.png" alt="Hotel Logo" style="height: 60px; width: auto;">
                </a>
                <h3><i class="fas fa-hotel"></i> Admin Panel</h3>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-item">
                    <a href="dashboard.php" class="nav-link active">
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
                <h1 class="page-title">Dashboard</h1>
                <div class="user-info">
                    <i class="fas fa-user"></i>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</span>
                </div>
            </div>

            <!-- Hotel Information Card -->
            <div class="card">
                <div class="row">
                    <div class="col-md-8">
                        <h2 class="card-title"><?php echo htmlspecialchars($hotel['name']); ?></h2>
                        <p class="text-muted mb-2">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($hotel['address']); ?>
                        </p>
                        <p class="mb-0"><?php echo htmlspecialchars($hotel['description']); ?></p>
                    </div>
                    <div class="col-md-4 text-right">
                        <div class="rating">
                            <?php for ($i = 0; $i < floor($hotel['rating']); $i++): ?>
                                <i class="fas fa-star" style="color: #f093fb;"></i>
                            <?php endfor; ?>
                            <span class="ms-2"><?php echo $hotel['rating']; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Overview -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">System Overview</h3>
                </div>
                
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="fas fa-bed"></i>
                        </div>
                        <div class="stat-number"><?php echo $totalRooms; ?></div>
                        <div class="stat-label">Total Rooms</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-number"><?php echo $availableRooms; ?></div>
                        <div class="stat-label">Available Rooms</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-number"><?php echo $totalReservations; ?></div>
                        <div class="stat-label">Total Reservations</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon info">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-number"><?php echo $pendingReservations; ?></div>
                        <div class="stat-label">Pending Reservations</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="quick-actions-flex">
                    <a href="reservations.php" class="quick-action-btn btn-secondary">
                        <i class="fas fa-calendar-check"></i>
                        <span>View All Reservations</span>
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Reservations</h3>
                </div>
                <?php
                $stmt = $pdo->query("SELECT r.*, rm.room_type FROM reservations r LEFT JOIN rooms rm ON r.room_id = rm.id ORDER BY r.created_at DESC LIMIT 5");
                $recentReservations = $stmt->fetchAll();
                ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Guest Name</th>
                                <th>Room</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentReservations as $reservation): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reservation['booking_id']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['guest_name']); ?></td>
                                <td>
                                    <?php
                                        if (!empty($reservation['room_type'])) {
                                            echo htmlspecialchars($reservation['room_type']);
                                        } else {
                                            echo '<span class="text-muted">N/A</span>';
                                        }
                                    ?>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($reservation['check_in_date'])); ?></td>
                                <td><?php echo date('M d, Y', strtotime($reservation['check_out_date'])); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $reservation['status'] === 'confirmed' ? 'success' : 'warning'; ?>">
                                        <?php echo ucfirst($reservation['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="reservations.php" class="btn btn-primary">View All Reservations</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 