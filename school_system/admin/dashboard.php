<?php
require_once __DIR__ . '/../functions.php';
require_role('admin');
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Admin Dashboard</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
  <div class="container">
    <h2>Admin Dashboard</h2>
    <ul>
      <li><a href="students.php">Manage Students</a></li>
      <li><a href="teachers.php">Manage Faculty</a></li>
      <li><a href="courses.php">Manage Courses</a></li>
      <li><a href="sections.php">Manage Sections</a></li>
      <li><a href="enrollments.php">Enrollments</a></li>
      <li><a href="grades.php">Grades</a></li>
      <li><a href="attendance.php">Attendance</a></li>
      <li><a href="learning_materials.php">Learning Materials</a></li>
      <li><a href="documents.php">Documents</a></li>
      <li><a href="alumni.php">Alumni</a></li>
      <li><a href="graduation_applications.php">Graduation Apps</a></li>
      <li><a href="billing.php">Billing</a></li>
      <li><a href="applicants.php">Applicants</a></li>
      <li><a href="reports.php">Reports</a></li>
      <li><a href="export_students.php">Export Students CSV</a></li>
      <li><a href="create_admin.php">Create Admin</a></li>
    </ul>
    <p><a href="../index.php">Home</a></p>
  </div>
</body>
</html>