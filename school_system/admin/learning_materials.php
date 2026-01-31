<?php
require_once __DIR__ . '/../functions.php';
require_role('admin');
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!verify_csrf($_POST['_csrf']??'')) exit('Invalid CSRF');
    if(isset($_POST['add_material'])){
        $section_id = $_POST['section_id']?:null; $title = $_POST['title']??''; $type = $_POST['material_type']??'file'; $url = null;
        if($type==='file' && isset($_FILES['file']) && $_FILES['file']['error']===0){
            $fn = basename($_FILES['file']['name']); $dst = __DIR__.'/../uploads/materials/'.time().'_'.$fn; move_uploaded_file($_FILES['file']['tmp_name'],$dst); $url = 'uploads/materials/'.basename($dst);
        } else { $url = $_POST['url']??''; }
        if($section_id && $title){ $pdo->prepare('INSERT INTO learning_materials (section_id,title,material_type,url) VALUES (?,?,?,?)')->execute([$section_id,$title,$type,$url]); }
    } elseif(isset($_POST['delete_material'])){
        $id = intval($_POST['material_id']); if($id) $pdo->prepare('DELETE FROM learning_materials WHERE material_id=?')->execute([$id]);
    }
}
$sections = $pdo->query('SELECT s.section_id,c.title,f.first_name,f.last_name FROM sections s LEFT JOIN courses c ON s.course_id=c.course_id LEFT JOIN faculty f ON s.faculty_id=f.faculty_id')->fetchAll();
$materials = $pdo->query('SELECT m.*, c.title as course, f.first_name, f.last_name FROM learning_materials m LEFT JOIN sections s ON m.section_id=s.section_id LEFT JOIN courses c ON s.course_id=c.course_id LEFT JOIN faculty f ON s.faculty_id=f.faculty_id ORDER BY m.material_id DESC')->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Learning Materials</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
  <div class="container">
    <h2>Learning Materials</h2>
    <h3>Add Material</h3>
    <form method="post" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <select name="section_id"><?php foreach($sections as $s): ?><option value="<?php echo $s['section_id']; ?>"><?php echo e($s['title'].' - '.($s['first_name']?$s['first_name'].' '.$s['last_name']:'TBA')); ?></option><?php endforeach; ?></select>
      <input name="title" placeholder="Title">
      <select name="material_type"><option value="file">File</option><option value="link">Link</option></select>
      <input type="file" name="file">
      <input name="url" placeholder="If link, paste URL here">
      <button name="add_material" type="submit">Add</button>
    </form>

    <h3>Materials</h3>
    <table><thead><tr><th>ID</th><th>Title</th><th>Course</th><th>Type</th><th>URL/File</th><th>Action</th></tr></thead><tbody>
    <?php foreach($materials as $m): ?>
      <tr>
        <td><?php echo $m['material_id']; ?></td>
        <td><?php echo e($m['title']); ?></td>
        <td><?php echo e($m['course'].' ('.($m['first_name']?$m['first_name'].' '.$m['last_name']:'TBA').')'); ?></td>
        <td><?php echo e($m['material_type']); ?></td>
        <td><?php if($m['material_type']==='file'): ?><a href="/<?php echo e($m['url']); ?>" target="_blank">Download</a><?php else: ?><a href="<?php echo e($m['url']); ?>" target="_blank">Link</a><?php endif; ?></td>
        <td>
          <form method="post" style="display:inline">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="material_id" value="<?php echo $m['material_id']; ?>">
            <button name="delete_material" onclick="return confirm('Delete?')">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?></tbody></table>
    <p><a href="dashboard.php">Back</a></p>
  </div>
</body>
</html>