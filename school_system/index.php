<?php
session_start();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>School Management System</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="container">
    <h1>School Management System</h1>
    <?php if(isset($_SESSION['user'])): ?>
      <p>Welcome, <?php echo htmlspecialchars($_SESSION['user']['name'] ?? $_SESSION['user']['email']); ?> (<?php echo htmlspecialchars($_SESSION['user']['role_name'] ?? 'user'); ?>)</p>
      <ul>
        <?php if(strtolower($_SESSION['user']['role_name'] ?? '')==='admin'): ?>
          <li><a href="admin/dashboard.php">Admin Dashboard</a></li>
        <?php endif; ?>
        <?php if(strtolower($_SESSION['user']['role_name'] ?? '')==='teacher' || strtolower($_SESSION['user']['role_name'] ?? '')==='faculty'): ?>
          <li><a href="teacher/dashboard.php">Teacher Dashboard</a></li>
        <?php endif; ?>
        <?php if(strtolower($_SESSION['user']['role_name'] ?? '')==='student'): ?>
          <li><a href="student/dashboard.php">Student Dashboard</a></li>
        <?php endif; ?>
      </ul>
      <p><a href="logout.php">Logout</a></p>
    <?php else: ?>
      <p><a href="login.php">Login</a> | <a href="register.php">Register</a></p>
    <?php endif; ?>
  </div>
</body>
</html>