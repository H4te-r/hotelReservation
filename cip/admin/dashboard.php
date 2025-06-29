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

$stmt = $pdo->query("SELECT COUNT(*) as available_rooms FROM rooms WHERE is_available = 1");
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
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        .bg-primary-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .bg-success-gradient {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .bg-warning-gradient {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .bg-info-gradient {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        .hotel-info-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
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
                            <a class="nav-link active" href="dashboard.php">
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
                            <a class="nav-link" href="reservations.php">
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
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </span>
                            <div class="navbar-nav ms-auto">
                                <span class="navbar-text">
                                    Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!
                                </span>
                            </div>
                        </div>
                    </nav>

                    <!-- Dashboard Content -->
                    <div class="p-4">
                        <!-- Hotel Information -->
                        <div class="hotel-info-card">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h2 class="mb-2"><?php echo htmlspecialchars($hotel['name']); ?></h2>
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        <?php echo htmlspecialchars($hotel['address']); ?>
                                    </p>
                                    <p class="mb-0"><?php echo htmlspecialchars($hotel['description']); ?></p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="rating">
                                        <?php for ($i = 0; $i < floor($hotel['rating']); $i++): ?>
                                            <i class="fas fa-star text-warning"></i>
                                        <?php endfor; ?>
                                        <span class="ms-2"><?php echo $hotel['rating']; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h3 class="mb-3">System Overview</h3>
                            </div>
                        </div>

                        <!-- Statistics Cards -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6 col-lg-3">
                                <div class="stat-card">
                                    <div class="d-flex align-items-center">
                                        <div class="stat-icon bg-primary-gradient me-3">
                                            <i class="fas fa-bed"></i>
                                        </div>
                                        <div>
                                            <h3 class="mb-1"><?php echo $totalRooms; ?></h3>
                                            <p class="text-muted mb-0">Total Rooms</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <div class="stat-card">
                                    <div class="d-flex align-items-center">
                                        <div class="stat-icon bg-success-gradient me-3">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <div>
                                            <h3 class="mb-1"><?php echo $availableRooms; ?></h3>
                                            <p class="text-muted mb-0">Available Rooms</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <div class="stat-card">
                                    <div class="d-flex align-items-center">
                                        <div class="stat-icon bg-warning-gradient me-3">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                        <div>
                                            <h3 class="mb-1"><?php echo $totalReservations; ?></h3>
                                            <p class="text-muted mb-0">Total Reservations</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <div class="stat-card">
                                    <div class="d-flex align-items-center">
                                        <div class="stat-icon bg-info-gradient me-3">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div>
                                            <h3 class="mb-1"><?php echo $pendingReservations; ?></h3>
                                            <p class="text-muted mb-0">Pending Reservations</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="fas fa-bolt me-2"></i>
                                            Quick Actions
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <a href="hotel.php?action=edit" class="btn btn-primary w-100">
                                                    <i class="fas fa-edit me-2"></i>
                                                    Edit Hotel Info
                                                </a>
                                            </div>
                                            <div class="col-md-3">
                                                <a href="rooms.php?action=add" class="btn btn-success w-100">
                                                    <i class="fas fa-plus me-2"></i>
                                                    Add Room
                                                </a>
                                            </div>
                                            <div class="col-md-3">
                                                <a href="reservations.php" class="btn btn-warning w-100">
                                                    <i class="fas fa-eye me-2"></i>
                                                    View Reservations
                                                </a>
                                            </div>
                                            <div class="col-md-3">
                                                <a href="../index.php" class="btn btn-info w-100">
                                                    <i class="fas fa-external-link-alt me-2"></i>
                                                    View Guest Page
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 