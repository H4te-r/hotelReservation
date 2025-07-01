<?php
require_once 'auth.php';
requireAuth();

$pdo = getDBConnection();
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_status':
                $reservationId = $_POST['reservation_id'] ?? '';
                $status = $_POST['status'] ?? '';
                
                $stmt = $pdo->prepare("UPDATE reservations SET status = ? WHERE id = ?");
                if ($stmt->execute([$status, $reservationId])) {
                    $message = "Reservation status updated successfully!";
                } else {
                    $error = "Error updating reservation status.";
                }
                break;

            case 'delete':
                $reservationId = $_POST['reservation_id'] ?? '';
                $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
                if ($stmt->execute([$reservationId])) {
                    $message = "Reservation deleted successfully!";
                } else {
                    $error = "Error deleting reservation.";
                }
                break;
        }
    }
}

// Get reservations with room details
$stmt = $pdo->query("
    SELECT r.*, 
           rm.room_type, rm.room_number, rm.price_per_night
    FROM reservations r
    JOIN rooms rm ON r.room_id = rm.id
    ORDER BY r.created_at DESC
");
$reservations = $stmt->fetchAll();

// Get status counts
$stmt = $pdo->query("SELECT status, COUNT(*) as count FROM reservations GROUP BY status");
$statusCounts = $stmt->fetchAll();
$statusStats = [];
foreach ($statusCounts as $stat) {
    $statusStats[$stat['status']] = $stat['count'];
}

// Get hotel info
$stmt = $pdo->query("SELECT * FROM hotel LIMIT 1");
$hotel = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations Management - <?php echo htmlspecialchars($hotel['name']); ?></title>
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
                    <!-- <span class="logo-text"><?php echo htmlspecialchars($hotel['name']); ?></span> -->
                </a>
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
                    <a href="reservations.php" class="nav-link active">
                        <i class="fas fa-calendar-check"></i>
                        Reservations
                    </a>
                </div>
                <div class="nav-item">
                    <a href="add_reservation.php" class="nav-link">
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
                <h1 class="page-title">Reservations Management</h1>
                <div class="user-info">
                    <i class="fas fa-user"></i>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</span>
                </div>
            </div>

            <!-- Alerts -->
            <?php if ($message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Statistics -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Reservation Statistics</h3>
                </div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-number"><?php echo $statusStats['pending'] ?? 0; ?></div>
                        <div class="stat-label">Pending</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon info">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-number"><?php echo $statusStats['confirmed'] ?? 0; ?></div>
                        <div class="stat-label">Confirmed</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div class="stat-number"><?php echo $statusStats['paid'] ?? 0; ?></div>
                        <div class="stat-label">Paid</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon danger">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-number"><?php echo $statusStats['cancelled'] ?? 0; ?></div>
                        <div class="stat-label">Cancelled</div>
                    </div>
                </div>
            </div>

            <!-- Reservations Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Reservations</h3>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Guest Name</th>
                                <th>Email</th>
                                <th>Room</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Guests</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reservation['booking_id']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['guest_name']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['email']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($reservation['room_type']); ?>
                                    <br><small class="text-muted">Room #<?php echo htmlspecialchars($reservation['room_number']); ?></small>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($reservation['check_in_date'])); ?></td>
                                <td><?php echo date('M d, Y', strtotime($reservation['check_out_date'])); ?></td>
                                <td><?php echo htmlspecialchars($reservation['num_guests']); ?></td>
                                <td>₱<?php echo number_format($reservation['total_price'], 2); ?></td>
                                <td>
                                    <span class="badge badge-<?php 
                                        echo $reservation['status'] === 'confirmed' ? 'success' : 
                                            ($reservation['status'] === 'pending' ? 'warning' : 
                                            ($reservation['status'] === 'cancelled' ? 'danger' : 'info')); 
                                    ?>">
                                        <?php echo ucfirst($reservation['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <!-- Status Update Form -->
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="pending" <?php echo $reservation['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="confirmed" <?php echo $reservation['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                <option value="completed" <?php echo $reservation['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                <option value="cancelled" <?php echo $reservation['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                <option value="paid" <?php echo $reservation['status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                                            </select>
                                        </form>
                                        
                                        <!-- Delete Form -->
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this reservation?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
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