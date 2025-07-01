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
            case 'add':
                $room_number = $_POST['room_number'] ?? '';
                $room_type = $_POST['room_type'] ?? '';
                $capacity = $_POST['capacity'] ?? '';
                $price_per_night = $_POST['price_per_night'] ?? '';
                $description = $_POST['description'] ?? '';
                $image_url = $_POST['image_url'] ?? '';

                $stmt = $pdo->prepare("INSERT INTO rooms (room_number, room_type, capacity, price_per_night, description, image_url) VALUES (?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$room_number, $room_type, $capacity, $price_per_night, $description, $image_url])) {
                    $message = "Room added successfully!";
                } else {
                    $error = "Error adding room.";
                }
                break;

            case 'edit':
                $id = $_POST['id'] ?? '';
                $room_number = $_POST['room_number'] ?? '';
                $room_type = $_POST['room_type'] ?? '';
                $capacity = $_POST['capacity'] ?? '';
                $price_per_night = $_POST['price_per_night'] ?? '';
                $description = $_POST['description'] ?? '';
                $image_url = $_POST['image_url'] ?? '';

                $stmt = $pdo->prepare("UPDATE rooms SET room_number = ?, room_type = ?, capacity = ?, price_per_night = ?, description = ?, image_url = ? WHERE id = ?");
                if ($stmt->execute([$room_number, $room_type, $capacity, $price_per_night, $description, $image_url, $id])) {
                    $message = "Room updated successfully!";
                } else {
                    $error = "Error updating room.";
                }
                break;

            case 'delete':
                $id = $_POST['id'] ?? '';
                $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
                if ($stmt->execute([$id])) {
                    $message = "Room deleted successfully!";
                } else {
                    $error = "Error deleting room.";
                }
                break;
        }
    }
}

// Get rooms list
$stmt = $pdo->query("SELECT * FROM rooms ORDER BY room_number");
$rooms = $stmt->fetchAll();

// Get room for editing
$editRoom = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $editRoom = $stmt->fetch();
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
    <title>Rooms Management - <?php echo htmlspecialchars($hotel['name']); ?></title>
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
                    <a href="rooms.php" class="nav-link active">
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
                <h1 class="page-title">Rooms Management</h1>
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

            <!-- Room List Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Rooms</h3>
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
                                <td>
                                    <strong><?php echo htmlspecialchars($room['room_number']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($room['room_type']); ?></td>
                                <td><?php echo htmlspecialchars($room['capacity']); ?> guests</td>
                                <td>₱<?php echo number_format($room['price_per_night'], 2); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $room['is_available'] ? 'success' : 'danger'; ?>">
                                        <?php echo $room['is_available'] ? 'Available' : 'Occupied'; ?>
                                    </span>
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