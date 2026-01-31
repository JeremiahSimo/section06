<?php
require_once __DIR__ . '/../functions.php';
require_role('admin');
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['assign_grade'])){
    $enroll_id = $_POST['enroll_id']; $grade = $_POST['grade']; $remarks = $_POST['remarks']?:null;
    // insert or update grades table
    $exists = $pdo->prepare('SELECT grade_id FROM grades WHERE enrollment_id=?'); $exists->execute([$enroll_id]);
    if($exists->fetch()){
        $u=$pdo->prepare('UPDATE grades SET grade=?,remarks=? WHERE enrollment_id=?'); $u->execute([$grade,$remarks,$enroll_id]);
    } else {
        $i=$pdo->prepare('INSERT INTO grades (enrollment_id,grade,remarks) VALUES (?,?,?)'); $i->execute([$enroll_id,$grade,$remarks]);
    }
}
$enrolls = $pdo->query('SELECT e.enrollment_id,e.enrollment_status,s.student_number,s.first_name,s.last_name,c.title,g.grade,g.remarks FROM enrollments e JOIN students s ON e.student_id=s.student_id JOIN sections sec ON e.section_id=sec.section_id JOIN courses c ON sec.course_id=c.course_id LEFT JOIN grades g ON g.enrollment_id=e.enrollment_id')->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Assign Grades</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
  <div class="container">
    <h2>Assign Grades</h2>
    <table><thead><tr><th>ID</th><th>Student</th><th>Course</th><th>Grade</th><th>Remarks</th><th>Action</th></tr></thead><tbody>
    <?php foreach($enrolls as $e): ?>
      <tr>
        <td><?php echo $e['enrollment_id']; ?></td>
        <td><?php echo htmlspecialchars($e['student_number'].' - '.$e['first_name'].' '.$e['last_name']); ?></td>
        <td><?php echo htmlspecialchars($e['title']); ?></td>
        <td><?php echo htmlspecialchars($e['grade']); ?></td>
        <td><?php echo htmlspecialchars($e['remarks']); ?></td>
        <td>
          <form method="post" style="display:inline">
            <input type="hidden" name="enroll_id" value="<?php echo $e['enrollment_id']; ?>">
            <input name="grade" placeholder="Grade" value="<?php echo htmlspecialchars($e['grade']); ?>">
            <input name="remarks" placeholder="Remarks" value="<?php echo htmlspecialchars($e['remarks']); ?>">
            <button name="assign_grade">Set</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody></table>
    <p><a href="dashboard.php">Back</a></p>
  </div>
</body>
</html>