<?php
require_once 'auth.php';
requireAuth();

$pdo = getDBConnection();
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'edit') {
        $name = $_POST['name'] ?? '';
        $address = $_POST['address'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $email = $_POST['email'] ?? '';
        $description = $_POST['description'] ?? '';
        $rating = $_POST['rating'] ?? 0.0;
        $image_url = $_POST['image_url'] ?? '';

        $stmt = $pdo->prepare("UPDATE hotel SET name = ?, address = ?, phone = ?, email = ?, description = ?, rating = ?, image_url = ? WHERE id = 1");
        if ($stmt->execute([$name, $address, $phone, $email, $description, $rating, $image_url])) {
            $message = "Hotel information updated successfully!";
        } else {
            $error = "Error updating hotel information.";
        }
    }
}

// Get hotel information
$stmt = $pdo->query("SELECT * FROM hotel LIMIT 1");
$hotel = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Information - Admin Panel</title>
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
        .hotel-preview {
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
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                            <a class="nav-link active" href="hotel.php">
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
                                <i class="fas fa-building me-2"></i>
                                Hotel Information
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

                        <!-- Hotel Preview -->
                        <div class="hotel-preview">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <i class="fas fa-building fa-5x text-primary mb-3"></i>
                                        <h3><?php echo htmlspecialchars($hotel['name']); ?></h3>
                                        <div class="rating mb-2">
                                            <?php for ($i = 0; $i < floor($hotel['rating']); $i++): ?>
                                                <i class="fas fa-star text-warning"></i>
                                            <?php endfor; ?>
                                            <span class="ms-2"><?php echo $hotel['rating']; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <p><strong>Address:</strong> <?php echo htmlspecialchars($hotel['address']); ?></p>
                                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($hotel['phone']); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($hotel['email']); ?></p>
                                    <p><strong>Description:</strong> <?php echo htmlspecialchars($hotel['description']); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Hotel Form -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-edit me-2"></i>
                                    Edit Hotel Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="edit">
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Hotel Name</label>
                                            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($hotel['name']); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Phone</label>
                                            <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($hotel['phone']); ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <textarea class="form-control" name="address" rows="2" required><?php echo htmlspecialchars($hotel['address']); ?></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($hotel['email']); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Rating</label>
                                            <input type="number" class="form-control" name="rating" step="0.1" min="0" max="5" value="<?php echo $hotel['rating']; ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" rows="4"><?php echo htmlspecialchars($hotel['description']); ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Image URL</label>
                                        <input type="url" class="form-control" name="image_url" value="<?php echo htmlspecialchars($hotel['image_url']); ?>">
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>
                                            Update Hotel Information
                                        </button>
                                        <a href="dashboard.php" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            Back to Dashboard
                                        </a>
                                    </div>
                                </form>
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