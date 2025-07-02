<?php
session_start();
require_once '../config/database.php';

// Check if user is already logged in
function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Login function
function login($username, $password) {
    $pdo = getDBConnection();

    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user) {
        return 'user_not_found'; // Custom return value for user not found
    }
    
    if (password_verify($password, $user['password'])) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_role'] = $user['role'];
        $_SESSION['admin_name'] = $user['full_name'];
        return true; // Login successful
    }
    return false; // Incorrect password
}

// Logout function
function logout() {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Require authentication for protected pages
function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

$error = ''; // Initialize error variable

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $loginResult = login($username, $password);

    if ($loginResult === true) {
        header('Location: dashboard.php');
        exit();
    } elseif ($loginResult === 'user_not_found') {
        $error = "User does not exist.";
    } else {
        $error = "Invalid username or password."; // This will now specifically mean incorrect password
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    logout();
}
?>

<?php if (!empty($error)): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label for="username">Username:</label><br>
    <input type="text" id="username" name="username" required><br>
    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" required><br><br>
    <button type="submit" name="login">Login</button>
</form>
