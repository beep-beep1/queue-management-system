<?php
session_start();
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Queue Management</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <!-- Theme Toggle Button -->
  <button class="theme-toggle" id="themeToggle">Toggle Theme</button>

  <div class="login-container">
    <h2 class="text-center">Queue Management Login</h2>
    <?php if ($error): ?>
      <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST" action="../controllers/authController.php">
      <div class="mb-3">
        <input type="text" name="username" placeholder="Username" class="form-control" required>
      </div>
      <div class="mb-3">
        <input type="password" name="password" placeholder="Password" class="form-control" required>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Login</button>
      </div>
    </form>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Theme Toggle Script -->
  <script>
    // Check saved theme preference
    const currentTheme =
