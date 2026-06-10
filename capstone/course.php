<?php
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
      <a class="nav-btn" href="calendar.php">Calendar</a>
      <a class="nav-btn" href="deities.php">Deities</a>
      <a class="nav-btn" href="login.php">Login</a>
    </nav>
  </header>

  <main class="main-area">
    <h2 class="page-title">Mythology Courses</h2>

    <!-- Container for all available lessons -->
    <div class="lesson-list">
      <!-- Lesson 1: Introduction to Shiva -->
      <div class="lesson">
        <strong>Course 1:</strong> Introduction to Shiva 
        <div class="lesson-actions">
          <!-- Link to open the full lesson content -->
          <a class="small-btn" href="#">View Lesson</a>
        </div>
      </div>

      <!-- Lesson 2: Introduction to Ganesha -->
      <div class="lesson">
        <strong>Course 2:</strong> Introduction to Ganesha
        <div class="lesson-actions">
          <a class="small-btn" href="#">View Lesson</a>
        </div>
      </div>

      <!-- Lesson 3: Introduction to Vishnu -->
      <div class="lesson">
        <strong>Course 3:</strong> Introduction to Vishnu
        <div class="lesson-actions">
          <a class="small-btn" href="#">View Lesson</a>
        </div>
      </div>

      <!-- Display user progress showing courses completed out of total -->
      <p class="progress-text">Progress: <span id="completed-count">0</span> of 3 courses completed</p>
      <!-- Button to mark a course as complete (needs database integration) -->
      <p><button class="small-btn mark-complete">Mark Complete</button></p>
    </div>
  </main>

  <footer class="site-footer">
    <div class="footer-links">Copyright &copy; 2026 | Privacy | Terms of Use</div>
  </footer>
</body>
</html>
