<?php
require_once __DIR__ . '/../functions.php';
require_role('admin');
$err='';
  if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!verify_csrf($_POST['_csrf']??'')) exit('Invalid CSRF');
  if(isset($_POST['add_student'])){
    $first=trim($_POST['first_name']??''); $last=trim($_POST['last_name']??''); $email=trim($_POST['email']??''); $password=$_POST['password']??'pass123';
    if($first && $last && $email){ try{ create_user_with_profile($email,$password,'student',$first,$last); }catch(Exception $e){ $err=$e->getMessage(); } } else $err='All fields required';
  } elseif(isset($_POST['delete_student'])){
    $sid = intval($_POST['student_id']); if($sid){ $pdo->prepare('DELETE FROM students WHERE student_id=?')->execute([$sid]); }
  }
}
$students = $pdo->query('SELECT s.student_id,s.student_number,s.first_name,s.last_name,s.year_level,s.status,u.email FROM students s JOIN users u ON s.user_id=u.user_id ORDER BY s.last_name')->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Manage Students</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
  <div class="container">
    <h2>Students</h2>
    <?php if($err): ?><p class="error"><?php echo htmlspecialchars($err); ?></p><?php endif; ?>
    <table><thead><tr><th>ID</th><th>Student No</th><th>Name</th><th>Email</th><th>Year</th><th>Status</th></tr></thead><tbody>
    <?php foreach($students as $s): ?>
      <tr>
        <td><?php echo $s['student_id']; ?></td>
        <td><?php echo e($s['student_number']); ?></td>
        <td><?php echo e($s['first_name'].' '.$s['last_name']); ?></td>
        <td><?php echo e($s['email']); ?></td>
        <td><?php echo e($s['year_level']); ?></td>
        <td><?php echo e($s['status']); ?></td>
        <td>
          <form method="post" style="display:inline">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="student_id" value="<?php echo $s['student_id']; ?>">
            <button name="delete_student" type="submit" onclick="return confirm('Delete this student?')">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody></table>
    <h3>Add Student</h3>
    <form method="post">
      <?php echo csrf_field(); ?>
      <input type="text" name="first_name" placeholder="First name" required>
      <input type="text" name="last_name" placeholder="Last name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password">
      <button name="add_student" type="submit">Add</button>
    </form>
    <p><a href="dashboard.php">Back</a></p>
  </div>
</body>
</html>