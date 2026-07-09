<?php
// Start session to keep user logged in
session_start();
require_once 'config.php';

// Only allow lessons access when logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php'); exit;
}

$course = isset($_GET['course']) ? (int)$_GET['course'] : 0;
if ($course) {
  $lessons = getLessonsByCourse($course);
}

// Admin: handle marking/unmarking lessons for learners
$admin_message = '';
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
  // load learners for admin controls
  $all_users = getAllUsers();
  $learners = array_filter($all_users, fn($u) => ($u['role'] ?? '') === 'learner');

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_action'])) {
    $action = $_POST['admin_action'];
    $target_lid = (int)($_POST['lesson_id'] ?? 0);
    $target_uid = (int)($_POST['learner_id'] ?? 0);
    if ($target_uid && $target_lid) {
      if ($action === 'mark') {
        markLessonComplete($target_uid, $target_lid);
        $admin_message = 'Marked complete.';
      } elseif ($action === 'unmark') {
        unmarkLessonComplete($target_uid, $target_lid);
        $admin_message = 'Marked incomplete.';
      } elseif ($action === 'reset') {
        clearUserCompletions($target_uid);
        $admin_message = 'Reset all progress for the learner.';
      }
    }
    // redirect to avoid POST resubmission and keep query params
    $qs = [];
    if ($course) $qs['course'] = $course;
    if (!empty($_POST['return_learner'])) $qs['learner_id'] = (int)$_POST['return_learner'];
    $loc = 'course.php' . (!empty($qs) ? ('?' . http_build_query($qs)) : '');
    header('Location: ' . $loc);
    exit;
  }

  // selected learner when admin is viewing lessons
  $view_learner_id = isset($_GET['learner_id']) ? (int)$_GET['learner_id'] : 0;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Mythology Courses - THE COSMIC HINDUISM PORTAL</title>
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
    <h2 class="page-title">Mythology Courses</h2>

    <div class="lesson-list">
      <?php if (!empty($admin_message)): ?>
        <div class="success-msg"><?php echo htmlspecialchars($admin_message); ?></div>
      <?php endif; ?>
      <?php if (!$course): ?>
        <!-- Show three course tiles -->
        <div class="lesson"><strong>Course 1:</strong> Introduction to Shiva
          <div class="lesson-actions"><a class="small-btn" href="course.php?course=1">View Lessons</a></div>
        </div>
        <div class="lesson"><strong>Course 2:</strong> Introduction to Ganesha
          <div class="lesson-actions"><a class="small-btn" href="course.php?course=2">View Lessons</a></div>
        </div>
        <div class="lesson"><strong>Course 3:</strong> Introduction to Vishnu
          <div class="lesson-actions"><a class="small-btn" href="course.php?course=3">View Lessons</a></div>
        </div>

        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
          <div style="margin-top:24px;">
            <h3>Manage Learners</h3>
            <table class="simple-table">
              <thead><tr><th>Name</th><th>Email</th><th>Progress</th><th>Actions</th></tr></thead>
              <tbody>
              <?php foreach ($learners as $ln):
                  $comps = getUserLessonCompletions((int)$ln['id']);
                  $count = is_array($comps) ? count($comps) : 0;
              ?>
                <tr>
                  <td><?php echo htmlspecialchars($ln['name']); ?></td>
                  <td><?php echo htmlspecialchars($ln['email']); ?></td>
                  <td><?php echo $count; ?></td>
                  <td>
                    <a href="course.php?learner_id=<?php echo intval($ln['id']); ?>">View Progress</a>
                    &nbsp;|&nbsp;
                    <a href="admin-profile.php?edit_id=<?php echo intval($ln['id']); ?>">Edit</a>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>

      <?php else: ?>
        <h3>Lessons for Course <?php echo intval($course); ?></h3>

        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
          <div style="margin-bottom:12px;">
            <form method="get" style="display:flex; gap:8px; align-items:center;">
              <input type="hidden" name="course" value="<?php echo intval($course); ?>">
              <label for="learner_id">Show for learner:</label>
              <select name="learner_id" id="learner_id">
                <option value="">-- select learner --</option>
                <?php foreach ($learners as $ln): ?>
                  <option value="<?php echo intval($ln['id']); ?>" <?php if(!empty($view_learner_id) && $view_learner_id===$ln['id']) echo 'selected'; ?>><?php echo htmlspecialchars($ln['name']); ?></option>
                <?php endforeach; ?>
              </select>
              <button class="small-btn" type="submit">Show</button>
            </form>
          </div>
        <?php endif; ?>

        <?php if (empty($lessons)): ?>
          <p>No lessons found for this course.</p>
        <?php else: ?>
          <?php foreach ($lessons as $l): ?>
            <div class="lesson">
              <strong><?php echo htmlspecialchars($l['title']); ?></strong>
              <div class="lesson-actions">
                <a class="small-btn" href="lesson.php?id=<?php echo intval($l['lesson_id']); ?>">Open</a>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin' && !empty($view_learner_id)): ?>
                  <?php if (isLessonComplete((int)$view_learner_id, (int)$l['lesson_id'])): ?>
                    <span style="margin-left:8px;color:green;">Completed</span>
                  <?php else: ?>
                    <span style="margin-left:8px;color:#666;">Not completed</span>
                  <?php endif; ?>
                <?php else: ?>
                  <?php if (isLessonComplete((int)$_SESSION['user_id'], (int)$l['lesson_id'])): ?>
                    <span style="margin-left:8px;color:green;">Completed</span>
                  <?php endif; ?>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
        <p><a class="small-btn" href="course.php">Back to courses</a></p>
      <?php endif; ?>
    </div>
  </main>

  <footer class="site-footer">
    <div class="footer-links">Copyright &copy; 2026 | Privacy | Terms of Use</div>
  </footer>
</body>
</html>
