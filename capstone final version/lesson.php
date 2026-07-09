<?php
// Start session to track logged-in user and session data
session_start();
// Load configuration and helper functions
require_once 'config.php';

// Redirect unauthenticated users to the login page
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php'); exit;
}

// Read lesson id from query string and cast to integer to sanitize
$lesson_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// Fetch lesson data when a valid id is present
$lesson = $lesson_id ? getLessonById($lesson_id) : null;
$message = '';
// Handle POST form to mark a lesson complete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_complete']) && $lesson) {
  // Cast user id and lesson id to ints before calling DB helper
  markLessonComplete((int)$_SESSION['user_id'], (int)$lesson['lesson_id']);
  // Friendly message shown after marking complete
  $message = 'Marked complete.';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Lesson - THE COSMIC HINDUISM PORTAL</title>
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
    <h2 class="page-title">Lesson</h2>
    <?php if (!$lesson): ?>
      <p>Lesson not found.</p>
    <?php else: ?>
      <?php if ($message): ?><div class="success-msg"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
      <div class="profile-card">
        <?php // Escape HTML special characters to prevent XSS ?>
        <h3><?php echo htmlspecialchars($lesson['title']); ?></h3>
        <div class="profile-info">
          <?php // Escape HTML then convert newlines to <br> for display ?>
          <p><?php echo nl2br(htmlspecialchars($lesson['content'])); ?></p>
        </div>
        <div class="profile-actions">
          <?php if (isLessonComplete((int)$_SESSION['user_id'], (int)$lesson['lesson_id'])): ?>
            <span>Completed</span>
          <?php else: ?>
            <form method="post">
              <button class="small-btn" type="submit" name="mark_complete">Mark Complete</button>
            </form>
          <?php endif; ?>
          <?php // Ensure the course id is an integer ?>
          <a class="small-btn" href="course.php?course=<?php echo intval($lesson['course_id']); ?>">Back</a>
        </div>
      </div>
    <?php endif; ?>
  </main>
</body>
</html>
