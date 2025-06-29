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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms Management - Admin Panel</title>
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
        .btn-action {
            padding: 5px 10px;
            margin: 2px;
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
                            <a class="nav-link active" href="rooms.php">
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
                                <i class="fas fa-bed me-2"></i>
                                Rooms Management
                            </span>
                            <div class="navbar-nav ms-auto">
                                <a href="?action=add" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>
                                    Add Room
                                </a>
                            </div>
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

                        <!-- Add/Edit Room Form -->
                        <?php if (isset($_GET['action']) && ($_GET['action'] === 'add' || $_GET['action'] === 'edit')): ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-<?php echo $_GET['action'] === 'add' ? 'plus' : 'edit'; ?> me-2"></i>
                                        <?php echo $_GET['action'] === 'add' ? 'Add New Room' : 'Edit Room'; ?>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">
                                        <?php if ($editRoom): ?>
                                            <input type="hidden" name="id" value="<?php echo $editRoom['id']; ?>">
                                        <?php endif; ?>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Room Number</label>
                                                <input type="text" class="form-control" name="room_number" value="<?php echo $editRoom ? htmlspecialchars($editRoom['room_number']) : ''; ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Room Type</label>
                                                <input type="text" class="form-control" name="room_type" value="<?php echo $editRoom ? htmlspecialchars($editRoom['room_type']) : ''; ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Capacity</label>
                                                <input type="number" class="form-control" name="capacity" min="1" value="<?php echo $editRoom ? $editRoom['capacity'] : '2'; ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Price per Night</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" class="form-control" name="price_per_night" step="0.01" min="0" value="<?php echo $editRoom ? $editRoom['price_per_night'] : ''; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" name="description" rows="3"><?php echo $editRoom ? htmlspecialchars($editRoom['description']) : ''; ?></textarea>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Image URL</label>
                                            <input type="url" class="form-control" name="image_url" value="<?php echo $editRoom ? htmlspecialchars($editRoom['image_url']) : ''; ?>">
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>
                                                <?php echo $_GET['action'] === 'add' ? 'Add Room' : 'Update Room'; ?>
                                            </button>
                                            <a href="rooms.php" class="btn btn-secondary">
                                                <i class="fas fa-times me-2"></i>
                                                Cancel
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Rooms List -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-list me-2"></i>
                                    Rooms List
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
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
                                                    <td><?php echo $room['id']; ?></td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($room['room_number']); ?></strong>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($room['room_type']); ?></td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            <?php echo $room['capacity']; ?> people
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <strong>$<?php echo number_format($room['price_per_night'], 2); ?></strong>
                                                    </td>
                                                    <td>
                                                        <?php if ($room['is_available']): ?>
                                                            <span class="badge bg-success">Available</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Occupied</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="?action=edit&id=<?php echo $room['id']; ?>" class="btn btn-sm btn-primary btn-action">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-danger btn-action" onclick="deleteRoom(<?php echo $room['id']; ?>, '<?php echo htmlspecialchars($room['room_number']); ?>')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
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

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete room "<span id="roomNumber"></span>"?</p>
                    <p class="text-danger"><small>This action cannot be undone.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteRoomId">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteRoom(id, roomNumber) {
            document.getElementById('roomNumber').textContent = roomNumber;
            document.getElementById('deleteRoomId').value = id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
</body>
</html> 