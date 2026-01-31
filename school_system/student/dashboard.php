<?php
require_once __DIR__ . '/../functions.php';
require_role('student');
$stmt=$pdo->prepare('SELECT s.student_id FROM students s WHERE s.user_id=? LIMIT 1'); $stmt->execute([$_SESSION['user']['user_id']]); $sd=$stmt->fetch(); $student_id=$sd?$sd['student_id']:null;
$rows=[];
if($student_id){ $stmt=$pdo->prepare('SELECT c.title,c.course_code,g.grade,sec.semester,sec.academic_year FROM enrollments e JOIN sections sec ON e.section_id=sec.section_id JOIN courses c ON sec.course_id=c.course_id LEFT JOIN grades g ON g.enrollment_id=e.enrollment_id WHERE e.student_id=?'); $stmt->execute([$student_id]); $rows=$stmt->fetchAll(); }
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Student Dashboard</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
  <div class="container">
    <h2>Student Dashboard</h2>
    <h3>Your Enrollments</h3>
    <table><thead><tr><th>Course</th><th>Code</th><th>Semester</th><th>AY</th><th>Grade</th></tr></thead><tbody>
    <?php foreach($rows as $r): ?>
      <tr><td><?php echo htmlspecialchars($r['title']); ?></td><td><?php echo htmlspecialchars($r['course_code']); ?></td><td><?php echo htmlspecialchars($r['semester']); ?></td><td><?php echo htmlspecialchars($r['academic_year']); ?></td><td><?php echo htmlspecialchars($r['grade']); ?></td></tr>
    <?php endforeach; ?></tbody></table>
    <p><a href="../index.php">Home</a></p>
  </div>
</body>
</html>