<?php
require_once __DIR__ . '/../functions.php';
require_role('admin');
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!verify_csrf($_POST['_csrf']??'')) exit('Invalid CSRF');
  if(isset($_POST['add_section'])){
    $course_id = $_POST['course_id']?:null; $faculty_id = $_POST['faculty_id']?:null; $room_id = $_POST['room_id']?:null; $schedule = $_POST['schedule']??''; $semester=$_POST['semester']??''; $ay=$_POST['academic_year']??'';
    if($course_id){ $stmt=$pdo->prepare('INSERT INTO sections (course_id,faculty_id,room_id,schedule,semester,academic_year) VALUES (?,?,?,?,?,?)'); $stmt->execute([$course_id,$faculty_id,$room_id,$schedule,$semester,$ay]); }
  } elseif(isset($_POST['delete_section'])){
    $sec = intval($_POST['section_id']); if($sec){ $pdo->prepare('DELETE FROM sections WHERE section_id=?')->execute([$sec]); }
  }
}
$courses = $pdo->query('SELECT * FROM courses ORDER BY title')->fetchAll();
$faculty = $pdo->query('SELECT faculty_id,first_name,last_name FROM faculty ORDER BY last_name')->fetchAll();
$rooms = $pdo->query('SELECT * FROM rooms ORDER BY room_name')->fetchAll();
$sections = $pdo->query('SELECT s.*, c.title, f.first_name, f.last_name, r.room_name FROM sections s LEFT JOIN courses c ON s.course_id=c.course_id LEFT JOIN faculty f ON s.faculty_id=f.faculty_id LEFT JOIN rooms r ON s.room_id=r.room_id ORDER BY s.section_id')->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Manage Sections</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
  <div class="container">
    <h2>Sections</h2>
    <table><thead><tr><th>ID</th><th>Course</th><th>Faculty</th><th>Room</th><th>Schedule</th><th>Sem</th><th>AY</th></tr></thead><tbody>
    <?php foreach($sections as $s): ?>
      <tr>
        <td><?php echo $s['section_id']; ?></td>
        <td><?php echo e($s['title']); ?></td>
        <td><?php echo e($s['first_name'].' '.$s['last_name']); ?></td>
        <td><?php echo e($s['room_name']); ?></td>
        <td><?php echo e($s['schedule']); ?></td>
        <td><?php echo e($s['semester']); ?></td>
        <td><?php echo e($s['academic_year']); ?></td>
        <td>
          <form method="post" style="display:inline">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="section_id" value="<?php echo $s['section_id']; ?>">
            <button name="delete_section" type="submit" onclick="return confirm('Delete this section?')">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody></table>
    <h3>Add Section</h3>
    <form method="post">
      <?php echo csrf_field(); ?>
      <select name="course_id">
        <?php foreach($courses as $c): ?>
          <option value="<?php echo $c['course_id']; ?>"><?php echo htmlspecialchars($c['title']); ?></option>
        <?php endforeach; ?>
      </select>
      <select name="faculty_id"><option value="">-- Faculty --</option><?php foreach($faculty as $f): ?><option value="<?php echo $f['faculty_id']; ?>"><?php echo htmlspecialchars($f['first_name'].' '.$f['last_name']); ?></option><?php endforeach; ?></select>
      <select name="room_id"><option value="">-- Room --</option><?php foreach($rooms as $r): ?><option value="<?php echo $r['room_id']; ?>"><?php echo htmlspecialchars($r['room_name']); ?></option><?php endforeach; ?></select>
      <input name="schedule" placeholder="Schedule">
      <input name="semester" placeholder="Semester">
      <input name="academic_year" placeholder="Academic Year">
      <button name="add_section" type="submit">Add</button>
    </form>
    <p><a href="dashboard.php">Back</a></p>
  </div>
</body>
</html>