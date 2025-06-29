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
    <title>Hotel Information - <?php echo htmlspecialchars($hotel['name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville&family=League+Spartan&family=Poppins&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
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
                    <a href="hotel.php" class="nav-link active">
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
                <div class="nav-item">
                    <a href="add_reservation.php" class="nav-link">
                        <i class="fas fa-plus-circle"></i>
                        Add Reservation
                    </a>
                </div>
                <div class="nav-item">
                    <a href="users.php" class="nav-link">
                        <i class="fas fa-users"></i>
                        Users
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
                <h1 class="page-title">Hotel Information</h1>
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

            <!-- Hotel Preview -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Current Hotel Information</h3>
                </div>
                <div class="row">
                    <div class="col-md-4 text-center">
                        <i class="fas fa-building" style="font-size: 4em; color: #516b5d; margin-bottom: 1em;"></i>
                        <h3><?php echo htmlspecialchars($hotel['name']); ?></h3>
                        <div class="rating mb-2">
                            <?php for ($i = 0; $i < floor($hotel['rating']); $i++): ?>
                                <i class="fas fa-star" style="color: #f093fb;"></i>
                            <?php endfor; ?>
                            <span class="ms-2"><?php echo $hotel['rating']; ?></span>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <p><strong>Address:</strong> 123 Luxury Lane, Downtown City, PH 1000 </p>
                        <p><strong>Phone:</strong>  +63 912 345 6789</p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($hotel['email']); ?></p>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($hotel['description']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Edit Hotel Form -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i>
                        Edit Hotel Information
                    </h3>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="edit">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Hotel Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($hotel['name']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($hotel['phone']); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2" required><?php echo htmlspecialchars($hotel['address']); ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($hotel['email']); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Rating</label>
                                <input type="number" name="rating" class="form-control" step="0.1" min="0" max="5" value="<?php echo htmlspecialchars($hotel['rating']); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($hotel['description']); ?></textarea>
                    </div>
                    
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Update Hotel Information
                        </button>
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Back to Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 