<?php
require_once __DIR__ . '/../functions.php';
require_role('admin');
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!verify_csrf($_POST['_csrf']??'')) exit('Invalid CSRF');
    if(isset($_POST['add_alumni'])){
        $student_id = intval($_POST['student_id']); $year = intval($_POST['graduation_year']); $status = $_POST['employment_status']??'';
        if($student_id){ $pdo->prepare('INSERT INTO alumni (student_id,graduation_year,employment_status) VALUES (?,?,?)')->execute([$student_id,$year,$status]); }
    } elseif(isset($_POST['delete_alumni'])){
        $id = intval($_POST['alumni_id']); if($id) $pdo->prepare('DELETE FROM alumni WHERE alumni_id=?')->execute([$id]);
    }
}
$students = $pdo->query('SELECT student_id,student_number,first_name,last_name FROM students ORDER BY last_name')->fetchAll();
$alumni = $pdo->query('SELECT a.*, s.student_number, s.first_name, s.last_name FROM alumni a JOIN students s ON a.student_id=s.student_id ORDER BY a.alumni_id DESC')->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Alumni</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
  <div class="container">
    <h2>Alumni</h2>
    <?php if($err): ?><p class="error"><?php echo e($err); ?></p><?php endif; ?>
    <h3>Add Alumni</h3>
    <form method="post">
      <?php echo csrf_field(); ?>
      <select name="student_id"><?php foreach($students as $s): ?><option value="<?php echo $s['student_id']; ?>"><?php echo e($s['student_number'].' - '.$s['first_name'].' '.$s['last_name']); ?></option><?php endforeach; ?></select>
      <input name="graduation_year" placeholder="Graduation Year">
      <input name="employment_status" placeholder="Employment status">
      <button name="add_alumni" type="submit">Add</button>
    </form>

    <h3>Alumni List</h3>
    <table><thead><tr><th>ID</th><th>Student</th><th>Year</th><th>Employment</th><th>Action</th></tr></thead><tbody>
    <?php foreach($alumni as $a): ?>
      <tr>
        <td><?php echo $a['alumni_id']; ?></td>
        <td><?php echo e($a['student_number'].' - '.$a['first_name'].' '.$a['last_name']); ?></td>
        <td><?php echo e($a['graduation_year']); ?></td>
        <td><?php echo e($a['employment_status']); ?></td>
        <td>
          <form method="post" style="display:inline">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="alumni_id" value="<?php echo $a['alumni_id']; ?>">
            <button name="delete_alumni" onclick="return confirm('Delete?')">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?></tbody></table>
    <p><a href="dashboard.php">Back</a></p>
  </div>
</body>
</html>