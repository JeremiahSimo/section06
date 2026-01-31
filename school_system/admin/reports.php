<?php
require_once __DIR__ . '/../functions.php';
require_role('admin');
// Simple reports: students count, courses count, enrollments count
$counts = $pdo->query("SELECT (SELECT COUNT(*) FROM students) as students,(SELECT COUNT(*) FROM courses) as courses,(SELECT COUNT(*) FROM enrollments) as enrollments")->fetch();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Reports</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
  <div class="container">
    <h2>Reports</h2>
    <ul>
      <li>Students: <?php echo e($counts['students']); ?></li>
      <li>Courses: <?php echo e($counts['courses']); ?></li>
      <li>Enrollments: <?php echo e($counts['enrollments']); ?></li>
    </ul>
    <p><a href="export_students.php">Export students CSV</a></p>
    <p><a href="billing.php">Billing</a></p>
    <p><a href="dashboard.php">Back</a></p>
  </div>
</body>
</html>