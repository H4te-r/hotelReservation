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
                    <img src="../assets/images/logo.png" alt="Hotel Logo">
                    <span class="logo-text"><?php echo htmlspecialchars($hotel['name']); ?></span>
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
                <h1 class="page-title">Rooms Management</h1>
                <div class="user-info">
                    <a href="?action=add" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add Room
                    </a>
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

            <!-- Add/Edit Room Form -->
            <?php if (isset($_GET['action']) && ($_GET['action'] === 'add' || $_GET['action'] === 'edit')): ?>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-<?php echo $_GET['action'] === 'add' ? 'plus' : 'edit'; ?>"></i>
                            <?php echo $_GET['action'] === 'add' ? 'Add New Room' : 'Edit Room'; ?>
                        </h3>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">
                        <?php if ($editRoom): ?>
                            <input type="hidden" name="id" value="<?php echo $editRoom['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Room Number</label>
                                    <input type="text" name="room_number" class="form-control" 
                                           value="<?php echo $editRoom ? htmlspecialchars($editRoom['room_number']) : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Room Type</label>
                                    <select name="room_type" class="form-select" required>
                                        <option value="">Select Room Type</option>
                                        <option value="Standard" <?php echo ($editRoom && $editRoom['room_type'] === 'Standard') ? 'selected' : ''; ?>>Standard</option>
                                        <option value="Deluxe" <?php echo ($editRoom && $editRoom['room_type'] === 'Deluxe') ? 'selected' : ''; ?>>Deluxe</option>
                                        <option value="Suite" <?php echo ($editRoom && $editRoom['room_type'] === 'Suite') ? 'selected' : ''; ?>>Suite</option>
                                        <option value="Presidential" <?php echo ($editRoom && $editRoom['room_type'] === 'Presidential') ? 'selected' : ''; ?>>Presidential</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Capacity</label>
                                    <input type="number" name="capacity" class="form-control" 
                                           value="<?php echo $editRoom ? htmlspecialchars($editRoom['capacity']) : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Price per Night (₱)</label>
                                    <input type="number" name="price_per_night" class="form-control" step="0.01"
                                           value="<?php echo $editRoom ? htmlspecialchars($editRoom['price_per_night']) : ''; ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?php echo $editRoom ? htmlspecialchars($editRoom['description']) : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Image URL</label>
                            <input type="url" name="image_url" class="form-control" 
                                   value="<?php echo $editRoom ? htmlspecialchars($editRoom['image_url']) : ''; ?>">
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                <?php echo $_GET['action'] === 'add' ? 'Add Room' : 'Update Room'; ?>
                            </button>
                            <a href="rooms.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Rooms List -->
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
                                <th>Actions</th>
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
                                <td>$<?php echo number_format($room['price_per_night'], 2); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $room['is_available'] ? 'success' : 'danger'; ?>">
                                        <?php echo $room['is_available'] ? 'Available' : 'Occupied'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="?action=edit&id=<?php echo $room['id']; ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this room?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $room['id']; ?>">
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