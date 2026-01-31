<?php
require_once __DIR__ . '/functions.php';
$err='';
$roles = get_roles();
if($_SERVER['REQUEST_METHOD']==='POST'){
    $first = trim($_POST['first_name']??'');
    $last = trim($_POST['last_name']??'');
    $email = trim($_POST['email']??'');
    $password = $_POST['password']??'';
    $role_id = $_POST['role_id']??null;
    // find role name
    $role_name = null;
    foreach($roles as $r) if($r['role_id']==$role_id) $role_name=$r['role_name'];
    if(!$first || !$last || !$email || !$password || !$role_name) $err='Fill all fields';
    else{
        // check existing email
        if(get_user_by_email($email)) $err='Email already registered';
        else{
            create_user_with_profile($email,$password,$role_name,$first,$last);
            header('Location: login.php'); exit;
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Register</title><link rel="stylesheet" href="css/style.css"></head>
<body>
  <div class="container">
    <h2>Register</h2>
    <?php if($err): ?><p class="error"><?php echo htmlspecialchars($err); ?></p><?php endif; ?>
    <form method="post">
      <label>First name</label>
      <input type="text" name="first_name" required>
      <label>Last name</label>
      <input type="text" name="last_name" required>
      <label>Email</label>
      <input type="email" name="email" required>
      <label>Password</label>
      <input type="password" name="password" required>
      <label>Role</label>
      <select name="role_id" required>
        <?php foreach($roles as $r): ?>
          <option value="<?php echo $r['role_id']; ?>"><?php echo htmlspecialchars($r['role_name']); ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit">Register</button>
    </form>
    <p><a href="login.php">Login</a> | <a href="index.php">Home</a></p>
  </div>
</body>
</html>