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
        return 'user_not_found';
    }
    
    if (password_verify($password, $user['password'])) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_role'] = $user['role'];
        $_SESSION['admin_name'] = $user['full_name'];
        return true;
    } else {
        return false;
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
        $error = "User does not exist.";
    } else {
        $error = "Invalid username or password.";
    }
}

if (isset($_GET['logout'])) {
    logout();
}

?>
