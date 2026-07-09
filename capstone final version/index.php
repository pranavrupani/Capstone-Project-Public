<?php
// Start session to keep user logged in
session_start();

//Home page for The Cosmic Hinduism Portal
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>THE COSMIC HINDUISM PORTAL - Home</title>
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
      <?php if (isset($_SESSION['user_id'])): ?>
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
          <a class="nav-btn" href="admin-profile.php">Dashboard</a>
        <?php else: ?>
          <a class="nav-btn" href="learner-profile.php">Profile</a>
        <?php endif; ?>
        <a class="nav-btn" href="logout.php">Logout</a>
      <?php else: ?>
        <a class="nav-btn" href="login.php">Login</a>
      <?php endif; ?>
    </nav>
  </header>

  <main class="main-area">
    <!-- Hero section with main call-to-action button -->
    <section class="center-block">
      <p style="text-align:center; margin-top:40px;">
        <!-- Button to navigate to the deities page and start exploring -->
        <a class="big-btn" href="deities.php">Click to begin</a>
      </p>
    </section>
  </main>

  <footer class="site-footer">
    <div class="footer-links">Copyright &copy; 2026 | Privacy | Terms of Use</div>
  </footer>
</body>
</html>
