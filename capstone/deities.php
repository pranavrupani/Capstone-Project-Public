<?php
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Deities - THE COSMIC HINDUISM PORTAL</title>
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
    <h2 class="page-title">Deities</h2>

    <!-- Search bar to find deities by name or category -->
    <div class="search-row">
      <input type="search" placeholder="Search gods by name, category, or mythology" class="search-input">
      <button class="small-btn">Search</button>
    </div>

    <!-- Grid of deity cards showing name, description, and link to details -->
    <div class="cards">
      <!-- Deity Card: Shiva -->
      <div class="card">
        <div class="image-placeholder"></div>
        <h3>Shiva</h3>
        <p>Shiva is the destroyer and transformer among the Trimurti.</p>
        <!-- Link to detailed page about this deity -->
        <a class="small-btn" href="deity-detail.php">View Details</a>
      </div>

      <!-- Deity Card: Ganesha -->
      <div class="card">
        <div class="image-placeholder"></div>
        <h3>Ganesha</h3>
        <p>Ganesha is the remover of obstacles and patron of arts and sciences.</p>
        <a class="small-btn" href="deity-detail.php">View Details</a>
      </div>

      <!-- Deity Card: Vishnu -->
      <div class="card">
        <div class="image-placeholder"></div>
        <h3>Vishnu</h3>
        <p>Vishnu preserves and protects the universe, often incarnating as avatars.</p>
        <a class="small-btn" href="deity-detail.php">View Details</a>
      </div>
    </div>
  </main>

  <footer class="site-footer">
    <div class="footer-links">Copyright &copy; 2026 | Privacy | Terms of Use</div>
  </footer>
</body>
</html>
