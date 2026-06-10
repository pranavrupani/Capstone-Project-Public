<?php
// login.php - Login and Register forms (no backend yet)
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login / Register - THE COSMIC HINDUISM PORTAL</title>
  <link rel="stylesheet" href="assets/style.css">
  <script src="assets/script.js" defer></script>
</head>
<body>
  <header class="site-header">
    <h1 class="site-title">THE COSMIC HINDUISM PORTAL</h1>
    <nav class="nav-row">
      <a class="nav-btn" href="index.php">Home</a>
      <a class="nav-btn" href="course.php">Course</a>
      <a class="nav-btn" href="calendar.php">Calendar</a>
      <a class="nav-btn" href="deities.php">Deities</a>
      <a class="nav-btn" href="login.php">Login</a>
    </nav>
  </header>

  <main class="main-area">
    <h2 class="page-title">Login / Register</h2>

    <!-- Container for both login and registration forms side-by-side -->
    <div class="forms-row">
      <!-- LEFT FORM: Login for existing users -->
      <div class="form-card">
        <h3>Login</h3>
        <!-- Login form - submits to this same page (or separate auth handler) -->
        <form action="#" method="post">
          <!-- Email field - required, must be valid email format -->
          <label>Email *</label>
          <input type="email" name="email" required>
          <!-- Password field - required, should be validated server-side -->
          <label>Password *</label>
          <input type="password" name="password" required>
          <!-- Submit button - sends form data via POST -->
          <p><button class="small-btn" type="submit">Login</button></p>
        </form>
      </div>

      <!-- RIGHT FORM: Registration for new users -->
      <div class="form-card">
        <h3>Register</h3>
        <!-- Registration form - creates new user account -->
        <form action="#" method="post">
          <!-- Full name field - required for user profile -->
          <label>Name *</label>
          <input type="text" name="name" required>
          <!-- Email field - required, should check for duplicates in database -->
          <label>Email *</label>
          <input type="email" name="email" required>
          <!-- Password field - required, should enforce strong password rules -->
          <!-- Password should be hashed before storing in database -->
          <label>Password *</label>
          <input type="password" name="password" required>
          <!-- Submit button - creates new user account if all validations pass -->
          <p><button class="small-btn" type="submit">Register</button></p>
        </form>
      </div>
    </div>
  </main>

  <footer class="site-footer">
    <div class="footer-links">Copyright &copy; 2026 | Privacy | Terms of Use</div>
  </footer>
</body>
</html>
