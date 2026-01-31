<?php
require_once __DIR__ . '/../functions.php';
require_role('admin');
$err='';
  if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!verify_csrf($_POST['_csrf']??'')) exit('Invalid CSRF');
  if(isset($_POST['add_admin'])){
    $first=trim($_POST['first_name']??''); $last=trim($_POST['last_name']??''); $email=trim($_POST['email']??''); $password=$_POST['password']??'pass123';
    if($first && $last && $email){ try{ create_user_with_profile($email,$password,'admin',$first,$last); }catch(Exception $e){ $err=$e->getMessage(); } } else $err='All fields required';
  }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Create Admin</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
  <div class="container">
    <h2>Create Admin</h2>
    <?php if($err): ?><p class="error"><?php echo htmlspecialchars($err); ?></p><?php endif; ?>
    <form method="post">
      <?php echo csrf_field(); ?>
      <input type="text" name="first_name" placeholder="First name" required>
      <input type="text" name="last_name" placeholder="Last name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password">
      <button name="add_admin" type="submit">Create Admin</button>
    </form>
    <p><a href="dashboard.php">Back to Dashboard</a></p>
  </div>
</body>
</html>