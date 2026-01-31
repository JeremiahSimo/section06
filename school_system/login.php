<?php
require_once __DIR__ . '/functions.php';
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $email=trim($_POST['email']??'');
    $password=$_POST['password']??'';
    $user = get_user_by_email($email);
    if($user && password_verify($password,$user['password_hash'])){
        // enrich user with name
        $full = get_user_by_id($user['user_id']);
        $_SESSION['user'] = $full;
        // redirect by role
        $role = strtolower($full['role_name']);
        if($role==='admin') header('Location: /section06/school_system/admin/dashboard.php');
        elseif($role==='teacher' || $role==='faculty') header('Location: /section06/school_system/teacher/dashboard.php');
        else header('Location: /section06/school_system/student/dashboard.php');
        exit;
    } else {
        $err='Invalid credentials';
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Login</title><link rel="stylesheet" href="css/style.css"></head>
<body>
  <div class="container">
    <h2>Login</h2>
    <?php if($err): ?><p class="error"><?php echo htmlspecialchars($err); ?></p><?php endif; ?>
    <form method="post">
      <label>Email</label>
      <input type="email" name="email" required>
      <label>Password</label>
      <input type="password" name="password" required>
      <button type="submit">Login</button>
    </form>
    <p><a href="register.php">Register</a> | <a href="index.php">Home</a></p>
  </div>
</body>
</html>