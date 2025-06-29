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
                $name = $_POST['name'] ?? '';
                $address = $_POST['address'] ?? '';
                $phone = $_POST['phone'] ?? '';
                $email = $_POST['email'] ?? '';
                $description = $_POST['description'] ?? '';
                $rating = $_POST['rating'] ?? 0.0;
                $image_url = $_POST['image_url'] ?? '';

                $stmt = $pdo->prepare("INSERT INTO hotels (name, address, phone, email, description, rating, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$name, $address, $phone, $email, $description, $rating, $image_url])) {
                    $message = "Hotel added successfully!";
                } else {
                    $error = "Error adding hotel.";
                }
                break;

            case 'edit':
                $id = $_POST['id'] ?? '';
                $name = $_POST['name'] ?? '';
                $address = $_POST['address'] ?? '';
                $phone = $_POST['phone'] ?? '';
                $email = $_POST['email'] ?? '';
                $description = $_POST['description'] ?? '';
                $rating = $_POST['rating'] ?? 0.0;
                $image_url = $_POST['image_url'] ?? '';

                $stmt = $pdo->prepare("UPDATE hotels SET name = ?, address = ?, phone = ?, email = ?, description = ?, rating = ?, image_url = ? WHERE id = ?");
                if ($stmt->execute([$name, $address, $phone, $email, $description, $rating, $image_url, $id])) {
                    $message = "Hotel updated successfully!";
                } else {
                    $error = "Error updating hotel.";
                }
                break;

            case 'delete':
                $id = $_POST['id'] ?? '';
                $stmt = $pdo->prepare("DELETE FROM hotels WHERE id = ?");
                if ($stmt->execute([$id])) {
                    $message = "Hotel deleted successfully!";
                } else {
                    $error = "Error deleting hotel.";
                }
                break;
        }
    }
}

// Get hotels list
$stmt = $pdo->query("SELECT * FROM hotels ORDER BY name");
$hotels = $stmt->fetchAll();

// Get hotel for editing
$editHotel = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $editHotel = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotels Management - Hotel Reservation System</title>
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
                            <a class="nav-link active" href="hotels.php">
                                <i class="fas fa-building me-2"></i>
                                Hotels
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
                                <i class="fas fa-building me-2"></i>
                                Hotels Management
                            </span>
                            <div class="navbar-nav ms-auto">
                                <a href="?action=add" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>
                                    Add Hotel
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

                        <!-- Add/Edit Hotel Form -->
                        <?php if (isset($_GET['action']) && ($_GET['action'] === 'add' || $_GET['action'] === 'edit')): ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-<?php echo $_GET['action'] === 'add' ? 'plus' : 'edit'; ?> me-2"></i>
                                        <?php echo $_GET['action'] === 'add' ? 'Add New Hotel' : 'Edit Hotel'; ?>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">
                                        <?php if ($editHotel): ?>
                                            <input type="hidden" name="id" value="<?php echo $editHotel['id']; ?>">
                                        <?php endif; ?>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Hotel Name</label>
                                                <input type="text" class="form-control" name="name" value="<?php echo $editHotel ? htmlspecialchars($editHotel['name']) : ''; ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Phone</label>
                                                <input type="text" class="form-control" name="phone" value="<?php echo $editHotel ? htmlspecialchars($editHotel['phone']) : ''; ?>">
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Address</label>
                                            <textarea class="form-control" name="address" rows="2" required><?php echo $editHotel ? htmlspecialchars($editHotel['address']) : ''; ?></textarea>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" name="email" value="<?php echo $editHotel ? htmlspecialchars($editHotel['email']) : ''; ?>">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Rating</label>
                                                <input type="number" class="form-control" name="rating" step="0.1" min="0" max="5" value="<?php echo $editHotel ? $editHotel['rating'] : '0.0'; ?>">
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" name="description" rows="3"><?php echo $editHotel ? htmlspecialchars($editHotel['description']) : ''; ?></textarea>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Image URL</label>
                                            <input type="url" class="form-control" name="image_url" value="<?php echo $editHotel ? htmlspecialchars($editHotel['image_url']) : ''; ?>">
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>
                                                <?php echo $_GET['action'] === 'add' ? 'Add Hotel' : 'Update Hotel'; ?>
                                            </button>
                                            <a href="hotels.php" class="btn btn-secondary">
                                                <i class="fas fa-times me-2"></i>
                                                Cancel
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Hotels List -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-list me-2"></i>
                                    Hotels List
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Address</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>Rating</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($hotels as $hotel): ?>
                                                <tr>
                                                    <td><?php echo $hotel['id']; ?></td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($hotel['name']); ?></strong>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($hotel['address']); ?></td>
                                                    <td><?php echo htmlspecialchars($hotel['phone']); ?></td>
                                                    <td><?php echo htmlspecialchars($hotel['email']); ?></td>
                                                    <td>
                                                        <span class="badge bg-warning">
                                                            <?php echo $hotel['rating']; ?> ★
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="?action=edit&id=<?php echo $hotel['id']; ?>" class="btn btn-sm btn-primary btn-action">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-danger btn-action" onclick="deleteHotel(<?php echo $hotel['id']; ?>, '<?php echo htmlspecialchars($hotel['name']); ?>')">
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
                    <p>Are you sure you want to delete the hotel "<span id="hotelName"></span>"?</p>
                    <p class="text-danger"><small>This action cannot be undone.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteHotelId">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteHotel(id, name) {
            document.getElementById('hotelName').textContent = name;
            document.getElementById('deleteHotelId').value = id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
</body>
</html> 