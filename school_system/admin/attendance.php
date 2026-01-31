<?php
require_once __DIR__ . '/../functions.php';
require_role('admin');
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!verify_csrf($_POST['_csrf']??'')) exit('Invalid CSRF');
    if(isset($_POST['add_attendance'])){
        $enroll_id = intval($_POST['enrollment_id']); $date = $_POST['attendance_date']??null; $status = $_POST['status']??'present';
        if($enroll_id && $date){ $pdo->prepare('INSERT INTO attendance (enrollment_id,attendance_date,status) VALUES (?,?,?)')->execute([$enroll_id,$date,$status]); }
    }
}
$enrolls = $pdo->query('SELECT e.enrollment_id,s.student_number,s.first_name,s.last_name,c.title FROM enrollments e JOIN students s ON e.student_id=s.student_id JOIN sections sec ON e.section_id=sec.section_id JOIN courses c ON sec.course_id=c.course_id')->fetchAll();
$records = $pdo->query('SELECT a.*, s.first_name,s.last_name,c.title FROM attendance a JOIN enrollments e ON a.enrollment_id=e.enrollment_id JOIN students s ON e.student_id=s.student_id JOIN sections sec ON e.section_id=sec.section_id JOIN courses c ON sec.course_id=c.course_id ORDER BY a.attendance_date DESC')->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Attendance</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
  <div class="container">
    <h2>Attendance</h2>
    <form method="post">
      <?php echo csrf_field(); ?>
      <select name="enrollment_id"><?php foreach($enrolls as $e): ?><option value="<?php echo $e['enrollment_id']; ?>"><?php echo e($e['student_number'].' - '.$e['first_name'].' '.$e['last_name'].' / '.$e['title']); ?></option><?php endforeach; ?></select>
      <input type="date" name="attendance_date" required>
      <select name="status"><option value="present">present</option><option value="absent">absent</option><option value="late">late</option></select>
      <button name="add_attendance" type="submit">Add</button>
    </form>

    <h3>Records</h3>
    <table><thead><tr><th>Date</th><th>Student</th><th>Course</th><th>Status</th></tr></thead><tbody>
    <?php foreach($records as $r): ?>
      <tr><td><?php echo e($r['attendance_date']); ?></td><td><?php echo e($r['first_name'].' '.$r['last_name']); ?></td><td><?php echo e($r['title']); ?></td><td><?php echo e($r['status']); ?></td></tr>
    <?php endforeach; ?></tbody></table>
    <p><a href="dashboard.php">Back</a></p>
  </div>
</body>
</html>