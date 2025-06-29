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
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_role'] = $user['role'];
        $_SESSION['admin_name'] = $user['full_name'];
        return true;
    }
    return false;
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

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (login($username, $password)) {
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Invalid username or password";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    logout();
}
?> 