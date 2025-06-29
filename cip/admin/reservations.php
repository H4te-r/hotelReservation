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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-confirmed { background: #d1edff; color: #0c5460; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .status-completed { background: #d4edda; color: #155724; }
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar">
                    <div class="p-4">
                        <h4 class="text-center mb-4">
                            <i class="fas fa-hotel me-2"></i>
                            Admin Panel
                        </h4>
                        <nav class="nav flex-column">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                            <a class="nav-link" href="hotel.php">
                                <i class="fas fa-building me-2"></i>
                                Hotel Info
                            </a>
                            <a class="nav-link" href="rooms.php">
                                <i class="fas fa-bed me-2"></i>
                                Rooms
                            </a>
                            <a class="nav-link active" href="reservations.php">
                                <i class="fas fa-calendar-check me-2"></i>
                                Reservations
                            </a>
                            <a class="nav-link" href="users.php">
                                <i class="fas fa-users me-2"></i>
                                Users
                            </a>
                            <hr class="my-3">
                            <a class="nav-link" href="?logout=1">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-0">
                <div class="main-content">
                    <!-- Top Navbar -->
                    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                        <div class="container-fluid">
                            <span class="navbar-brand">
                                <i class="fas fa-calendar-check me-2"></i>
                                Reservations Management
                            </span>
                        </div>
                    </nav>

                    <!-- Content -->
                    <div class="p-4">
                        <?php if ($message): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <?php echo htmlspecialchars($message); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Statistics -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="stats-card text-center">
                                    <h3 class="text-primary"><?php echo $statusStats['pending'] ?? 0; ?></h3>
                                    <p class="text-muted mb-0">Pending</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card text-center">
                                    <h3 class="text-info"><?php echo $statusStats['confirmed'] ?? 0; ?></h3>
                                    <p class="text-muted mb-0">Confirmed</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card text-center">
                                    <h3 class="text-success"><?php echo $statusStats['completed'] ?? 0; ?></h3>
                                    <p class="text-muted mb-0">Completed</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card text-center">
                                    <h3 class="text-danger"><?php echo $statusStats['cancelled'] ?? 0; ?></h3>
                                    <p class="text-muted mb-0">Cancelled</p>
                                </div>
                            </div>
                        </div>

                        <!-- Reservations List -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-list me-2"></i>
                                    All Reservations
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Guest</th>
                                                <th>Room</th>
                                                <th>Dates</th>
                                                <th>Total Price</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($reservations as $reservation): ?>
                                                <tr>
                                                    <td>
                                                        <strong>#<?php echo $reservation['id']; ?></strong>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <strong><?php echo htmlspecialchars($reservation['guest_name']); ?></strong><br>
                                                            <small class="text-muted">
                                                                <?php echo htmlspecialchars($reservation['guest_email']); ?><br>
                                                                <?php echo htmlspecialchars($reservation['guest_phone']); ?>
                                                            </small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <strong><?php echo htmlspecialchars($reservation['room_type']); ?></strong><br>
                                                            <small class="text-muted">
                                                                Room <?php echo htmlspecialchars($reservation['room_number']); ?>
                                                            </small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <strong>Check-in:</strong> <?php echo date('M d, Y', strtotime($reservation['check_in_date'])); ?><br>
                                                            <strong>Check-out:</strong> <?php echo date('M d, Y', strtotime($reservation['check_out_date'])); ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <strong>$<?php echo number_format($reservation['total_price'], 2); ?></strong>
                                                    </td>
                                                    <td>
                                                        <span class="status-badge status-<?php echo $reservation['status']; ?>">
                                                            <?php echo ucfirst($reservation['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            <?php echo date('M d, Y H:i', strtotime($reservation['created_at'])); ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="updateStatus(<?php echo $reservation['id']; ?>, '<?php echo $reservation['status']; ?>')">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteReservation(<?php echo $reservation['id']; ?>, '<?php echo htmlspecialchars($reservation['guest_name']); ?>')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
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
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Reservation Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="reservation_id" id="updateReservationId">
                        
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the reservation for "<span id="guestName"></span>"?</p>
                    <p class="text-danger"><small>This action cannot be undone.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="reservation_id" id="deleteReservationId">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateStatus(id, currentStatus) {
            document.getElementById('updateReservationId').value = id;
            document.querySelector('select[name="status"]').value = currentStatus;
            new bootstrap.Modal(document.getElementById('updateStatusModal')).show();
        }
        
        function deleteReservation(id, guestName) {
            document.getElementById('guestName').textContent = guestName;
            document.getElementById('deleteReservationId').value = id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
</body>
</html> 