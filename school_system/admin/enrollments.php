<?php
require_once __DIR__ . '/../functions.php';
require_role('admin');
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['enroll'])){
    $student_id = $_POST['student_id']?:null; $section_id = $_POST['section_id']?:null; $status = $_POST['enrollment_status']??'enrolled';
    if($student_id && $section_id){ $stmt=$pdo->prepare('INSERT INTO enrollments (student_id,section_id,enrollment_status) VALUES (?,?,?)'); try{$stmt->execute([$student_id,$section_id,$status]);}catch(Exception $e){} }
}
$students = $pdo->query('SELECT student_id,student_number,first_name,last_name FROM students ORDER BY last_name')->fetchAll();
$sections = $pdo->query('SELECT s.section_id,c.title,s.semester,s.academic_year,f.first_name,f.last_name FROM sections s LEFT JOIN courses c ON s.course_id=c.course_id LEFT JOIN faculty f ON s.faculty_id=f.faculty_id ORDER BY s.section_id')->fetchAll();
$enrolls = $pdo->query('SELECT e.enrollment_id,e.enrollment_status,e.student_id,e.section_id,s.student_number,s.first_name,s.last_name,c.title FROM enrollments e JOIN students s ON e.student_id=s.student_id JOIN sections sec ON e.section_id=sec.section_id JOIN courses c ON sec.course_id=c.course_id')->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Enrollments</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
  <div class="container">
    <h2>Enrollments</h2>
    <form method="post">
      <select name="student_id"><?php foreach($students as $s): ?><option value="<?php echo $s['student_id']; ?>"><?php echo htmlspecialchars($s['student_number'].' - '.$s['first_name'].' '.$s['last_name']); ?></option><?php endforeach; ?></select>
      <select name="section_id"><?php foreach($sections as $sec): ?><option value="<?php echo $sec['section_id']; ?>"><?php echo htmlspecialchars($sec['title'].' ('.$sec['semester'].' '.$sec['academic_year'].') - '.($sec['first_name']?$sec['first_name'].' '.$sec['last_name']:'TBA')); ?></option><?php endforeach; ?></select>
      <select name="enrollment_status"><option>enrolled</option><option>waitlisted</option></select>
      <button name="enroll" type="submit">Enroll</button>
    </form>
    <h3>Current Enrollments</h3>
    <table><thead><tr><th>ID</th><th>Student</th><th>Course</th><th>Section</th><th>Status</th></tr></thead><tbody>
    <?php foreach($enrolls as $e): ?>
      <tr><td><?php echo $e['enrollment_id']; ?></td><td><?php echo htmlspecialchars($e['student_number'].' - '.$e['first_name'].' '.$e['last_name']); ?></td><td><?php echo htmlspecialchars($e['title']); ?></td><td><?php echo $e['section_id']; ?></td><td><?php echo htmlspecialchars($e['enrollment_status']); ?></td></tr>
    <?php endforeach; ?></tbody></table>
    <p><a href="dashboard.php">Back</a></p>
  </div>
</body>
</html>