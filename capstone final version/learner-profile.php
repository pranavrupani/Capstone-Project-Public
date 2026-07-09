<?php
// Start the session to access logged-in user data
session_start();

// Load the authentication config file
require_once 'config.php';

// Check if user is logged in - if not, send them to login page
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}
// If admin is viewing another learner, allow via ?view_id=ID. Otherwise
// redirect non-learners to admin dashboard.
if (isset($_GET['view_id'])) {
  // Only allow admins to view other learners' profiles
  if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
  }
  $view_id = (int)$_GET['view_id'];
  $view_user = getUserById($view_id);
  if (!$view_user) {
    header('Location: admin-profile.php');
    exit;
  }
} else {
  // Default behavior: learners see their own profile; admins get redirected
  if (isset($_SESSION['user_role']) && $_SESSION['user_role'] !== 'learner') {
    header('Location: admin-profile.php');
    exit;
  }
}

$profile_user_id = isset($view_user) ? (int)$view_user['id'] : (int)$_SESSION['user_id'];
$completed_lessons = getUserLessonCompletions($profile_user_id);
$completed_count = is_array($completed_lessons) ? count($completed_lessons) : 0;
$total_lessons = count(load_lessons_from_file());
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>My Profile - THE COSMIC HINDUISM PORTAL</title>
  <link rel="stylesheet" href="assets/style.css">
  <script src="assets/script.js" defer></script>
</head>
<body>
  <header class="site-header">
    <h1 class="site-title">THE COSMIC HINDUISM PORTAL</h1>
    <!-- Navigation menu for logged-in learners -->
    <nav class="nav-row">
      <a class="nav-btn" href="index.php">Home</a>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a class="nav-btn" href="course.php">Course</a>
      <?php endif; ?>
      <a class="nav-btn" href="deities.php">Deities</a>
      <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <a class="nav-btn" href="admin-profile.php">Dashboard</a>
      <?php else: ?>
        <a class="nav-btn" href="learner-profile.php">Profile</a>
      <?php endif; ?>
      <a class="nav-btn" href="logout.php">Logout</a>
    </nav>
  </header>

  <main class="main-area">
    <h2 class="page-title">Learner Profile</h2>

    <!-- Profile card showing personal information -->
    <div class="profile-card">
      <!-- Display welcome message with learner name -->
      <?php if (isset($view_user)): ?>
        <h3>Viewing: <?php echo htmlspecialchars($view_user['name']); ?> (learner)</h3>
      <?php else: ?>
        <h3>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h3>
      <?php endif; ?>
      
      <!-- Display learner information -->
      <div class="profile-info">
        <!-- Display the logged-in learner email -->
        <?php if (isset($view_user)): ?>
          <p><strong>Email:</strong> <?php echo htmlspecialchars($view_user['email']); ?></p>
          <p><strong>Role:</strong> <span style="text-transform: capitalize;"><?php echo htmlspecialchars($view_user['role']); ?></span></p>
        <?php else: ?>
          <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
          <!-- Display the role (learner) -->
          <p><strong>Role:</strong> <span style="text-transform: capitalize;"><?php echo $_SESSION['user_role']; ?></span></p>
        <?php endif; ?>
        <!-- Display account type label -->
        <p><strong>Account Type:</strong> Student</p>
      </div>

      <!-- Action buttons for learner -->
      <div class="profile-actions">
        <!-- Button to go to courses page -->
        <?php if (isset($view_user)): ?>
          <a class="small-btn" href="admin-profile.php">Back to Admin</a>
        <?php else: ?>
          <a class="small-btn" href="course.php">Continue Learning</a>
          <!-- Button to logout -->
          <a class="small-btn" href="logout.php">Logout</a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Learning progress section -->
    <div class="profile-section">
      <!-- Section heading -->
      <h3>Your Progress</h3>
      <!-- Display number of lessons completed -->
      <p>Lessons completed: <?php echo htmlspecialchars($completed_count); ?> of <?php echo htmlspecialchars($total_lessons); ?></p>
    </div>
  </main>

  <footer class="site-footer">
    <div class="footer-links">Copyright &copy; 2026 | Privacy | Terms of Use</div>
  </footer>
</body>
</html>

