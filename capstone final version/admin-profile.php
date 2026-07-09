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
// Check if user is an admin - if not, send them to learner profile
if ($_SESSION['user_role'] != 'admin') {
  header('Location: learner-profile.php');
  exit;
}
// Get all users from the system
$users = getAllUsers();
// Count total number of users 
$user_count = 0;
$learner_count = 0;
foreach ($users as $u) {
  $user_count++;
  if ($u['role'] === 'learner') {
    $learner_count++;
  }
}
?>
<?php
// Handle admin actions: create, delete, update users and deities
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Create user
  if (isset($_POST['create_user'])) {
    // Map POST fields to clearly named local variables
    $create_name = trim($_POST['c_name'] ?? '');
    $create_email = trim($_POST['c_email'] ?? '');
    $create_password = $_POST['c_password'] ?? '';
    $create_role = $_POST['c_role'] ?? 'learner';

    // Basic validation and sanitization
    $create_name = strip_tags($create_name);
    $create_email = filter_var($create_email, FILTER_SANITIZE_EMAIL);

    if ($create_name !== '' && $create_email !== '' && $create_password !== '') {
      // Ensure email is valid before creating
      if (filter_var($create_email, FILTER_VALIDATE_EMAIL)) {
        addNewUser($create_name, $create_email, $create_password, $create_role);
      }
    }
    header('Location: admin-profile.php'); exit;
  }

  // ---------- Delete user ----------
  if (isset($_POST['delete_user'])) {
    $delete_user_id = (int)($_POST['delete_user']);
    if ($delete_user_id > 0) deleteUser($delete_user_id);
    header('Location: admin-profile.php'); exit;
  }

  // ---------- Update user ----------
  if (isset($_POST['update_user'])) {
    $update_user_id = (int)($_POST['u_id'] ?? 0);
    $update_name = trim($_POST['u_name'] ?? '');
    $update_email = trim($_POST['u_email'] ?? '');
    $update_role = $_POST['u_role'] ?? 'learner';
    $update_password = $_POST['u_password'] ?? null; 

    // Remove HTML and PHP tags from the name
    $update_name = strip_tags($update_name);
    // Sanitize the email to remove invalid characters
    $update_email = filter_var($update_email, FILTER_SANITIZE_EMAIL);

    if ($update_user_id && $update_name !== '' && $update_email !== '') {
      if (filter_var($update_email, FILTER_VALIDATE_EMAIL)) {
        updateUser($update_user_id, $update_name, $update_email, $update_role, $update_password);
      }
    }
    header('Location: admin-profile.php'); exit;
  }
  
  // ---------- Deity actions ----------
  // Create deity
  if (isset($_POST['create_deity'])) {
    $deity_name = trim($_POST['d_name'] ?? '');
    $deity_description = trim($_POST['d_description'] ?? '');
    $deity_mythology = trim($_POST['d_mythology'] ?? '');
    $deity_image = trim($_POST['d_image'] ?? '');

    // Sanitize text fields
    $deity_name = strip_tags($deity_name);
    $deity_description = strip_tags($deity_description);
    $deity_mythology = strip_tags($deity_mythology);

    // Image field 
    $deity_image = basename($deity_image);

    if ($deity_name !== '') createDeity($deity_name, $deity_description, $deity_mythology, $deity_image);
    header('Location: admin-profile.php'); exit;
  }

  // Delete deity
  if (isset($_POST['delete_deity'])) {
    $delete_deity_id = (int)$_POST['delete_deity'];
    if ($delete_deity_id > 0) deleteDeity($delete_deity_id);
    header('Location: admin-profile.php'); exit;
  }

  // Update deity
  if (isset($_POST['update_deity'])) {
    $update_deity_id = (int)($_POST['did'] ?? 0);
    $deity_name = trim($_POST['d_name'] ?? '');
    $deity_description = trim($_POST['d_description'] ?? '');
    $deity_mythology = trim($_POST['d_mythology'] ?? '');
    $deity_image = trim($_POST['d_image'] ?? '');

    $deity_name = strip_tags($deity_name);
    $deity_description = strip_tags($deity_description);
    $deity_mythology = strip_tags($deity_mythology);
    $deity_image = basename($deity_image);

    if ($update_deity_id && $deity_name !== '') updateDeity($update_deity_id, $deity_name, $deity_description, $deity_mythology, $deity_image);
    header('Location: admin-profile.php'); exit;
  }
}
// Refresh users list after any action
$users = getAllUsers();
// Refresh deities list
$deities = getAllDeities();

// If requested, load progress details for a specific user
$progress_view = null;
if (isset($_GET['view_progress'])) {
  $vp = (int)$_GET['view_progress'];
  $progress_rows = getUserLessonCompletions($vp);
  $progress_view = ['user' => getUserById($vp), 'rows' => $progress_rows];
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Dashboard - THE COSMIC HINDUISM PORTAL</title>
  <link rel="stylesheet" href="assets/style.css">
  <script src="assets/script.js" defer></script>
</head>
<body>
  <header class="site-header">
    <h1 class="site-title">THE COSMIC HINDUISM PORTAL</h1>
    <!-- Navigation menu for admin users-->
    <nav class="nav-row">
      <a class="nav-btn" href="index.php">Home</a>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a class="nav-btn" href="course.php">Course</a>
      <?php endif; ?>
      <a class="nav-btn" href="deities.php">Deities</a>
      <!-- Link to admin dashboard -->
      <a class="nav-btn" href="admin-profile.php">Dashboard</a>
      <!-- Link to logout -->
      <a class="nav-btn" href="logout.php">Logout</a>
    </nav>
  </header>

  <main class="main-area">
    <h2 class="page-title">Admin Dashboard</h2>

    <!-- Welcome section showing admin details -->
    <div class="profile-card">
      <!-- Display admin name -->
      <h3>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h3>
      
      <!-- Admin information display -->
      <div class="profile-info">
        <!-- Display the logged-in admin email -->
        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
        <!-- Display the role -->
        <p><strong>Role:</strong> <span style="text-transform: capitalize;"><?php echo $_SESSION['user_role']; ?></span></p>
        <!-- Display account type -->
        <p><strong>Account Type:</strong> Administrator</p>
      </div>

      <!-- Action buttons for admin -->
      <div class="profile-actions">
        <!-- Button to logout -->
        <a class="small-btn" href="logout.php">Logout</a>
      </div>
    </div>

    <!-- User progress section -->
    <div style="margin-top:30px;">
      <h3>User Progress</h3>
      <table class="simple-table">
        <thead><tr><th>Name</th><th>Email</th><th>Completed Lessons</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u):
            $comps = getUserLessonCompletions((int)$u['id']);
            $count = is_array($comps) ? count($comps) : 0;
        ?>
          <tr>
            <td><?php echo htmlspecialchars($u['name']); ?></td>
            <td><?php echo htmlspecialchars($u['email']); ?></td>
            <td><?php echo $count; ?></td>
            <td><a href="admin-profile.php?view_progress=<?php echo intval($u['id']); ?>">View</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if ($progress_view): ?>
      <div style="margin-top:20px; border:1px solid #eee; padding:12px;">
        <h3>Progress for <?php echo htmlspecialchars($progress_view['user']['name'] ?? 'User'); ?></h3>
        <?php if (empty($progress_view['rows'])): ?>
          <p>No lessons completed yet.</p>
        <?php else: ?>
          <ul>
            <?php foreach ($progress_view['rows'] as $pr):
                $lesson = getLessonById((int)$pr['lesson_id']);
            ?>
              <li><?php echo htmlspecialchars($lesson['title'] ?? ('Lesson '.$pr['lesson_id'])); ?>
                  <?php if (!empty($pr['completed_at'])): ?> - <?php echo htmlspecialchars($pr['completed_at']); ?><?php endif; ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
        <a class="small-btn" href="admin-profile.php">Close</a>
      </div>
    <?php endif; ?>

    <!-- Statistics section -->
    <div class="admin-dashboard">
      <h3>Statistics</h3>
      <!-- Display statistics in a two-column layout -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0;">
        <!-- Total users stat -->
        <div style="border: 1px solid #ddd; padding: 15px; border-radius: 5px;">
          <!-- Display the total user count -->
          <p style="font-size: 24px; font-weight: bold; color: #333;"><?php echo $user_count; ?></p>
          <!-- Label for this stat -->
          <p style="color: #666;">Total Users</p>
        </div>
        <!-- Active learners stat -->
        <div style="border: 1px solid #ddd; padding: 15px; border-radius: 5px;">
          <!-- Display the learner count -->
          <p style="font-size: 24px; font-weight: bold; color: #333;"><?php echo $learner_count; ?></p>
          <!-- Label for this stat -->
          <p style="color: #666;">Active Learners</p>
        </div>
      </div>

      <!-- Registered users table -->
      <h3>Registered Users</h3>
      <!-- Table showing all users in the system -->
      <!-- Add user form -->
      <div style="margin-bottom:20px; border:1px solid #eee; padding:10px;">
        <form method="post" style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
          <input name="c_name" placeholder="Name" required>
          <input name="c_email" placeholder="Email" required>
          <input name="c_password" placeholder="Password" required>
          <select name="c_role"><option value="learner">learner</option><option value="admin">admin</option></select>
          <button class="small-btn" type="submit" name="create_user">Create User</button>
        </form>
      </div>

      <?php
      // If edit_id is present, show edit form for that user
      if (isset($_GET['edit_id'])):
          $edit_user = getUserById((int)$_GET['edit_id']);
          if ($edit_user):
      ?>
      <div style="margin-bottom:20px; border:1px solid #eee; padding:10px;">
        <form method="post" style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
          <input name="u_name" placeholder="Name" required value="<?php echo htmlspecialchars($edit_user['name']); ?>">
          <input name="u_email" placeholder="Email" required value="<?php echo htmlspecialchars($edit_user['email']); ?>">
          <input name="u_password" placeholder="New password (leave blank to keep)" value="">
          <select name="u_role"><option value="learner" <?php if($edit_user['role']=='learner') echo 'selected'; ?>>learner</option><option value="admin" <?php if($edit_user['role']=='admin') echo 'selected'; ?>>admin</option></select>
          <input type="hidden" name="u_id" value="<?php echo intval($edit_user['id']); ?>">
          <button class="small-btn" type="submit" name="update_user">Save Changes</button>
          <a class="small-btn" href="admin-profile.php">Cancel</a>
        </form>
      </div>
      <?php
          endif;
      endif;
      ?>

      <table class="simple-table">
        <!-- Table header -->
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
          </tr>
        </thead>
        <!-- Table body - loop through and display each user -->
        <tbody>
          <?php
          // Loop through each user and display in a table row with actions
          foreach ($users as $user) {
              echo '<tr>';
              echo '<td>' . htmlspecialchars($user['name']) . '</td>';
              echo '<td>' . htmlspecialchars($user['email']) . '</td>';
              echo '<td style="text-transform: capitalize;">' . $user['role'] . '</td>';
              // Actions: View (open learner page), Edit (via GET) and Delete (POST)
              echo '<td>';
              // Show view link (opens learner profile in a new tab)
              echo '<a href="learner-profile.php?view_id=' . intval($user['id']) . '" target="_blank">View</a> ';
              echo '<a href="admin-profile.php?edit_id=' . intval($user['id']) . '">Edit</a> ';
              echo '<form method="post" style="display:inline; margin-left:8px;" onsubmit="return confirm(\'Delete user?\')">';
              echo '<input type="hidden" name="delete_user" value="' . intval($user['id']) . '">';
              echo '<button class="small-btn" type="submit">Delete</button>';
              echo '</form>';
              echo '</td>';
              echo '</tr>';
          }
          ?>
        </tbody>
      </table>
    </div>

    <!-- Deities management -->
    <div style="margin-top:30px;">
      <h3>Manage Deities</h3>
      <div style="margin-bottom:12px; border:1px solid #eee; padding:10px;">
        <form method="post" style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
          <input name="d_name" placeholder="Name" required>
          <input name="d_image" placeholder="Image filename (e.g. Shiva.jpg)">
          <input name="d_description" placeholder="Short description">
          <input name="d_mythology" placeholder="Mythology">
          <button class="small-btn" type="submit" name="create_deity">Create Deity</button>
        </form>
      </div>

      <?php if (isset($_GET['edit_deity'])):
        $edit_d = getDeityById((int)$_GET['edit_deity']);
        if ($edit_d):
      ?>
      <div style="margin-bottom:20px; border:1px solid #eee; padding:10px;">
        <form method="post" style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
          <input name="d_name" placeholder="Name" required value="<?php echo htmlspecialchars($edit_d['name']); ?>">
          <input name="d_image" placeholder="Image filename" value="<?php echo htmlspecialchars($edit_d['image_name']); ?>">
          <input name="d_description" placeholder="Short description" value="<?php echo htmlspecialchars($edit_d['description']); ?>">
          <input name="d_mythology" placeholder="Mythology" value="<?php echo htmlspecialchars($edit_d['mythology']); ?>">
          <input type="hidden" name="did" value="<?php echo intval($edit_d['deity_id']); ?>">
          <button class="small-btn" type="submit" name="update_deity">Save Deity</button>
          <a class="small-btn" href="admin-profile.php">Cancel</a>
        </form>
      </div>
      <?php endif; endif; ?>

      <table class="simple-table">
        <thead><tr><th>Name</th><th>Image</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($deities as $d): ?>
          <tr>
            <td><?php echo htmlspecialchars($d['name']); ?></td>
            <td><?php echo htmlspecialchars($d['image_name']); ?></td>
            <td>
              <a href="admin-profile.php?edit_deity=<?php echo intval($d['deity_id']); ?>">Edit</a>
              <form method="post" style="display:inline;margin-left:8px;" onsubmit="return confirm('Delete deity?')">
                <input type="hidden" name="delete_deity" value="<?php echo intval($d['deity_id']); ?>">
                <button class="small-btn" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>

  <footer class="site-footer">
    <div class="footer-links">Copyright &copy; 2026 | Privacy | Terms of Use</div>
  </footer>
</body>
</html>
