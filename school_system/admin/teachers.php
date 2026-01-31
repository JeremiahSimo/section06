<?php
require_once __DIR__ . '/../functions.php';
require_role('admin');
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!verify_csrf($_POST['_csrf']??'')) exit('Invalid CSRF');
  if(isset($_POST['add_teacher'])){
    $first=trim($_POST['first_name']??''); $last=trim($_POST['last_name']??''); $email=trim($_POST['email']??''); $password=$_POST['password']??'pass123';
    if($first && $last && $email){ try{ create_user_with_profile($email,$password,'teacher',$first,$last); }catch(Exception $e){ $err=$e->getMessage(); } } else $err='All fields required';
  } elseif(isset($_POST['delete_teacher'])){
    $fid = intval($_POST['faculty_id']); if($fid){ $pdo->prepare('DELETE FROM faculty WHERE faculty_id=?')->execute([$fid]); }
  }
}
$teachers = $pdo->query('SELECT f.faculty_id,f.first_name,f.last_name,f.employment_status,u.email FROM faculty f JOIN users u ON f.user_id=u.user_id ORDER BY f.last_name')->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Manage Faculty</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
  <div class="container">
    <h2>Faculty</h2>
    <?php if($err): ?><p class="error"><?php echo htmlspecialchars($err); ?></p><?php endif; ?>
    <table><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Status</th></tr></thead><tbody>
    <?php foreach($teachers as $t): ?>
      <tr>
        <td><?php echo $t['faculty_id']; ?></td>
        <td><?php echo e($t['first_name'].' '.$t['last_name']); ?></td>
        <td><?php echo e($t['email']); ?></td>
        <td><?php echo e($t['employment_status']); ?></td>
        <td>
          <form method="post" style="display:inline">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="faculty_id" value="<?php echo $t['faculty_id']; ?>">
            <button name="delete_teacher" type="submit" onclick="return confirm('Delete this faculty?')">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody></table>
    <h3>Add Faculty</h3>
    <form method="post">
      <?php echo csrf_field(); ?>
      <input type="text" name="first_name" placeholder="First name" required>
      <input type="text" name="last_name" placeholder="Last name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password">
      <button name="add_teacher" type="submit">Add</button>
    </form>
    <p><a href="dashboard.php">Back</a></p>
  </div>
</body>
</html>