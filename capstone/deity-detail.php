<?php
// Displays detailed information about a specific deity
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Deity Details - THE COSMIC HINDUISM PORTAL</title>
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
    <h2 class="page-title">Deity Details</h2>

    <!-- Container for deity information with image and details side by side -->
    <div class="detail-row">
      <!-- Left side: Deity image -->
      <div class="detail-image">
        <div class="image-placeholder large"></div>
      </div>
      <!-- Right side: Deity information (name, description, mythology) -->
      <div class="detail-info">
        <!-- Deity name (should come from database) -->
        <p><strong>Name:</strong> Shiva</p>
        <!-- Full description of the deity -->
        <p><strong>Description:</strong> Shiva is worshipped as the supreme being who creates, protects and transforms the universe.</p>
        <!-- Mythology/tradition information -->
        <p><strong>Mythology:</strong> Hinduism — part of the Trimurti alongside Brahma and Vishnu.</p>
        <!-- Link to go back to the deities list -->
        <p><a class="small-btn" href="deities.php">Back to Deities</a></p>
      </div>
    </div>
  </main>

  <footer class="site-footer">
    <div class="footer-links">Copyright &copy; 2026 | Privacy | Terms of Use</div>
  </footer>
</body>
</html>
