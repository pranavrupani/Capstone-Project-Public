<?php
// Start session to store user data after login
session_start();

// Load the authentication config file
require_once 'config.php';

// Initialize error message variable
$login_error = '';
$register_error = '';
$register_success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Distinguish between login and register by submit button name
  if (isset($_POST['register'])) {
    // Registration flow
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($name === '' || $email === '' || $password === '') {
      $register_error = 'Please fill all registration fields.';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $register_error = 'Please provide a valid email address.';
    } else if (strlen($password) < 6) {
      $register_error = 'Password must be at least 6 characters.';
    } else {
      // Try to add user (default role: learner)
      if (addNewUser($name, $email, $password, 'learner')) {
        $register_success = 'Registration successful. Please login.';
      } else {
        $register_error = 'Email already registered.';
      }
    }
  } else {
    // Login flow
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($email === '' || $password === '') {
      $login_error = 'Please enter both email and password.';
    } else {
      // Get user row from DB by email
      $user = getUserByEmail($email);
      if ($user && password_verify($password, $user['password'])) {
        // Successful login: store minimal info in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];

        // Redirect based on role
        if ($user['role'] === 'admin') {
          header('Location: admin-profile.php');
        } else {
          header('Location: learner-profile.php');
        }
        exit;
      } else {
        $login_error = 'Invalid email or password.';
      }
    }
  }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login - THE COSMIC HINDUISM PORTAL</title>
  <link rel="stylesheet" href="assets/style.css">
  <script src="assets/script.js" defer></script>
</head>
<body>
  <header class="site-header">
    <h1 class="site-title">THE COSMIC HINDUISM PORTAL</h1>
    <nav class="nav-row">
      <a class="nav-btn" href="index.php">Home</a>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a class="nav-btn" href="course.php">Course</a>
      <?php endif; ?>
      <a class="nav-btn" href="deities.php">Deities</a>
      <a class="nav-btn" href="login.php">Login</a>
    </nav>
  </header>

  <main class="main-area">
    <h2 class="page-title">Login</h2>

    <!-- Login and Registration forms -->
    <div class="forms-row">
      <!-- LOGIN -->
      <div class="form-card">
        <h3>Login</h3>
        <?php if ($login_error): ?>
          <div class="error-msg"><?php echo htmlspecialchars($login_error); ?></div>
        <?php endif; ?>
        <form action="login.php" method="post">
          <label>Email *</label>
          <input type="email" name="email" required>
          <label>Password *</label>
          <input type="password" name="password" required>
          <p><button class="small-btn" type="submit" name="login">Login</button></p>
        </form>
      </div>

      <!-- REGISTER -->
      <div class="form-card">
        <h3>Register</h3>
        <?php if ($register_error): ?>
          <div class="error-msg"><?php echo htmlspecialchars($register_error); ?></div>
        <?php endif; ?>
        <?php if ($register_success): ?>
          <div class="success-msg"><?php echo htmlspecialchars($register_success); ?></div>
        <?php endif; ?>
        <form action="login.php" method="post">
          <label>Name *</label>
          <input type="text" name="name" required>
          <label>Email *</label>
          <input type="email" name="email" required>
          <label>Password *</label>
          <input type="password" name="password" required>
          <p><button class="small-btn" type="submit" name="register">Register</button></p>
        </form>
      </div>
    </div>
  </main>

  <footer class="site-footer">
    <div class="footer-links">Copyright &copy; 2026 | Privacy | Terms of Use</div>
  </footer>
</body>
</html>
