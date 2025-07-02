<?php
session_start();
require_once '../config/database.php';

function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function login($username, $password) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // If no user found with that username, return 'user_not_found'
        // This takes precedence over any password check if the user doesn't exist.
        return 'user_not_found'; 
    }
    
    // If user exists, verify the password
    if (password_verify($password, $user['password'])) {
        // If username is correct and password matches
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_role'] = $user['role'];
        $_SESSION['admin_name'] = $user['full_name'];
        return true; // Login successful
    } else {
        // If username is correct but password does not match
        return 'password_wrong'; // Specific signal for incorrect password
    }
}

function logout() {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}

function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $loginResult = login($username, $password);

    if ($loginResult === true) {
        header('Location: dashboard.php');
        exit();
    } elseif ($loginResult === 'user_not_found') {
        // If the username was not found in the database
        $error = "User doesn't exist."; // Display this message
    } elseif ($loginResult === 'password_wrong') {
        // If the username was found, but the password was incorrect
        $error = "Password is wrong."; // Display this message
    } else {
        // Fallback for any other unexpected login result
        $error = "An unexpected error occurred during login.";
    }
}

if (isset($_GET['logout'])) {
    logout();
}

?>
