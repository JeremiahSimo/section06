<?php
require_once __DIR__ . '/../functions.php';
require_role('admin');
$err='';
$if_code = '';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!verify_csrf($_POST['_csrf']??'')) exit('Invalid CSRF');
  if(isset($_POST['add_course'])){
    $code=trim($_POST['course_code']??''); $title=trim($_POST['title']??''); $units=intval($_POST['units']??0);
    if($code && $title){ $stmt=$pdo->prepare('INSERT INTO courses (course_code,title,units) VALUES (?,?,?)'); try{$stmt->execute([$code,$title,$units]);}catch(Exception $e){} }
  } elseif(isset($_POST['delete_course'])){
    $cid = intval($_POST['course_id']); if($cid){ $pdo->prepare('DELETE FROM courses WHERE course_id=?')->execute([$cid]); }
  }
}
$courses = $pdo->query('SELECT * FROM courses ORDER BY title')->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Manage Courses</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
  <div class="container">
    <h2>Courses</h2>
    <table><thead><tr><th>ID</th><th>Code</th><th>Title</th><th>Units</th></tr></thead><tbody>
    <?php foreach($courses as $c): ?>
      <tr>
        <td><?php echo $c['course_id']; ?></td>
        <td><?php echo e($c['course_code']); ?></td>
        <td><?php echo e($c['title']); ?></td>
        <td><?php echo e($c['units']); ?></td>
        <td>
          <form method="post" style="display:inline">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="course_id" value="<?php echo $c['course_id']; ?>">
            <button name="delete_course" type="submit" onclick="return confirm('Delete this course?')">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody></table>
    <h3>Add Course</h3>
    <form method="post">
      <?php echo csrf_field(); ?>
      <input type="text" name="course_code" placeholder="Course code" required>
      <input type="text" name="title" placeholder="Title" required>
      <input type="number" name="units" placeholder="Units">
      <button name="add_course" type="submit">Add</button>
    </form>
    <p><a href="dashboard.php">Back</a></p>
  </div>
</body>
</html>