<?php
// Start session 
session_start();
// Load DB connection
require_once 'config.php';

// Read deity id from query string and coerce to integer to avoid injection
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// Fetch deity record when a valid id is provided
$deity = $id ? getDeityById($id) : null;
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
    <h2 class="page-title">Deity Details</h2>

    <?php if (!$deity): ?>
      <p>Deity not found.</p>
    <?php else: ?>
      <div class="detail-row">
        <div class="detail-image">
          <?php // Determine the image path ?>
          <?php $img = findDeityImagePath($deity['image_name'], $deity['name']); ?>
          <?php // Escape URL and alt text to prevent XSS and ensure valid HTML attributes ?>
          <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($deity['name']); ?>">
        </div>
        <div class="detail-info">
          <?php // Escape all user-visible fields to prevent injection of HTML/JS ?>
          <p><strong>Name:</strong> <?php echo htmlspecialchars($deity['name']); ?></p>
          <p><strong>Description:</strong> <?php echo htmlspecialchars($deity['description']); ?></p>
          <p><strong>Mythology:</strong> <?php echo htmlspecialchars($deity['mythology']); ?></p>
          <p><a class="small-btn" href="deities.php">Back to Deities</a></p>
        </div>
      </div>
    <?php endif; ?>
  </main>

  <footer class="site-footer">
    <div class="footer-links">Copyright &copy; 2026 | Privacy | Terms of Use</div>
  </footer>
</body>
</html>
