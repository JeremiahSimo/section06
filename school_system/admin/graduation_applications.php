<?php
require_once __DIR__ . '/../functions.php';
require_role('admin');
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!verify_csrf($_POST['_csrf']??'')) exit('Invalid CSRF');
    if(isset($_POST['set_status'])){
        $id = intval($_POST['graduation_id']); $status = $_POST['status']??'pending';
        if($id) $pdo->prepare('UPDATE graduation_applications SET status=? WHERE graduation_id=?')->execute([$status,$id]);
    }
}
$apps = $pdo->query('SELECT g.*, s.student_number, s.first_name, s.last_name FROM graduation_applications g JOIN students s ON g.student_id=s.student_id ORDER BY g.graduation_id DESC')->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Graduation Applications</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
  <div class="container">
    <h2>Graduation Applications</h2>
    <table><thead><tr><th>ID</th><th>Student</th><th>Status</th><th>Action</th></tr></thead><tbody>
    <?php foreach($apps as $a): ?>
      <tr>
        <td><?php echo $a['graduation_id']; ?></td>
        <td><?php echo e($a['student_number'].' - '.$a['first_name'].' '.$a['last_name']); ?></td>
        <td><?php echo e($a['status']); ?></td>
        <td>
          <form method="post" style="display:inline">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="graduation_id" value="<?php echo $a['graduation_id']; ?>">
            <select name="status"><option value="pending">pending</option><option value="approved">approved</option><option value="rejected">rejected</option></select>
            <button name="set_status">Set</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?></tbody></table>
    <p><a href="dashboard.php">Back</a></p>
  </div>
</body>
</html>