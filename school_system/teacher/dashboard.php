<?php
require_once __DIR__ . '/../functions.php';
require_role('teacher');
// find faculty id for this user
$stmt=$pdo->prepare('SELECT faculty_id FROM faculty WHERE user_id=? LIMIT 1'); $stmt->execute([$_SESSION['user']['user_id']]); $f=$stmt->fetch(); $faculty_id=$f?$f['faculty_id']:null;
$courses = [];
if($faculty_id){ $stmt=$pdo->prepare('SELECT sec.section_id,c.title,sec.schedule,sec.semester,sec.academic_year FROM sections sec JOIN courses c ON sec.course_id=c.course_id WHERE sec.faculty_id=?'); $stmt->execute([$faculty_id]); $courses=$stmt->fetchAll(); }
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Teacher Dashboard</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
  <div class="container">
    <h2>Teacher Dashboard</h2>
    <h3>Your Sections</h3>
    <ul><?php foreach($courses as $c): ?><li><?php echo htmlspecialchars($c['title'].' - '.$c['semester'].' '.$c['academic_year'].' ('.$c['schedule'].')'); ?> - <a href="/section06/school_system/admin/grades.php?section=<?php echo $c['section_id']; ?>">Grades</a></li><?php endforeach; ?></ul>
    <p><a href="../index.php">Home</a></p>
  </div>
</body>
</html>