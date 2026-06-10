<?php
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin - Manage Deities - THE COSMIC HINDUISM PORTAL</title>
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
    <h2 class="page-title">Admin - Manage Deities</h2>

    <!-- Form to add new deity -->
    <div class="admin-form">
      <!-- Show error message if validation fails -->
      <div class="error-msg">Please complete all required fields.</div>
      <!-- Show success message after saving -->
      <div class="success-msg">Deity saved successfully.</div>
      <form action="#" method="post">
        <!-- All fields marked with * are required -->
        <label>Name *</label>
        <input type="text" name="name" required>
        <label>Description *</label>
        <textarea name="description" required></textarea>
        <label>Mythology *</label>
        <input type="text" name="mythology" required>
        <label>Image Name</label>
        <input type="text" name="image">
        <p><button class="small-btn" type="submit">Save Deity</button></p>
      </form>
    </div>

    <!-- List of all deities currently in the database -->
    <h3>Deities</h3>
    <table class="simple-table admin-table">
      <thead>
        <tr><th>Name</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <!-- Each row shows a deity with edit and delete options -->
        <tr><td>Shiva</td><td><a href="#">Edit</a> | <a href="#">Delete</a></td></tr>
        <tr><td>Ganesha</td><td><a href="#">Edit</a> | <a href="#">Delete</a></td></tr>
        <tr><td>Vishnu</td><td><a href="#">Edit</a> | <a href="#">Delete</a></td></tr>
      </tbody>
    </table>
  </main>

  <footer class="site-footer">
    <div class="footer-links">Copyright &copy; 2026 | Privacy | Terms of Use</div>
  </footer>
</body>
</html>
