<?php
session_start();
require_once 'config.php';

$deities = getAllDeities();
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
    <h2 class="page-title">Deities</h2>

    <div class="cards">
      <?php if (empty($deities)): ?>
        <p>No deities found.</p>
      <?php else: ?>
        <?php foreach ($deities as $d):
          $img = findDeityImagePath($d['image_name'], $d['name']);
        ?>
        <div class="card">
          <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($d['name']); ?>">
          <h3><?php echo htmlspecialchars($d['name']); ?></h3>
          <p><?php echo htmlspecialchars($d['description']); ?></p>
          <?php if (isset($_SESSION['user_id'])): ?>
            <a class="small-btn" href="deity-detail.php?id=<?php echo intval($d['deity_id']); ?>">View Details</a>
          <?php else: ?>
            <a class="small-btn" href="login.php">Login to view</a>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>

  <footer class="site-footer">
    <div class="footer-links">Copyright &copy; 2026 | Privacy | Terms of Use</div>
  </footer>
</body>
</html>
