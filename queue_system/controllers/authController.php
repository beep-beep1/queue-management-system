<?php
session_start();
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // Look for the user in the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header('Location: ../views/admin_dashboard.php');
        exit();
    } else {
        $_SESSION['error'] = "Invalid username or password";
        header('Location: ../views/login.php');
        exit();
    }
} else {
    header('Location: ../views/login.php');
    exit();
}
?>
