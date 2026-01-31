<?php
require_once __DIR__ . '/../functions.php';
require_role('admin');
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!verify_csrf($_POST['_csrf']??'')) exit('Invalid CSRF');
    if(isset($_POST['upload_doc'])){
        $student_id = intval($_POST['student_id']); $type = $_POST['document_type']??'';
        if(isset($_FILES['doc']) && $_FILES['doc']['error']===0){
            $fn = basename($_FILES['doc']['name']); $dst = __DIR__.'/../uploads/documents/'.time().'_'.$fn; move_uploaded_file($_FILES['doc']['tmp_name'],$dst);
            $pdo->prepare('INSERT INTO documents (student_id,document_type,issued_date) VALUES (?,?,?)')->execute([$student_id,$type,date('Y-m-d')]);
        }
    } elseif(isset($_POST['delete_doc'])){
        $id = intval($_POST['document_id']); if($id) $pdo->prepare('DELETE FROM documents WHERE document_id=?')->execute([$id]);
    }
}
$students = $pdo->query('SELECT student_id,student_number,first_name,last_name FROM students ORDER BY last_name')->fetchAll();
$docs = $pdo->query('SELECT d.*, s.student_number, s.first_name, s.last_name FROM documents d JOIN students s ON d.student_id=s.student_id ORDER BY d.document_id DESC')->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Documents</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
  <div class="container">
    <h2>Student Documents</h2>
    <form method="post" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <select name="student_id"><?php foreach($students as $s): ?><option value="<?php echo $s['student_id']; ?>"><?php echo e($s['student_number'].' - '.$s['first_name'].' '.$s['last_name']); ?></option><?php endforeach; ?></select>
      <input name="document_type" placeholder="Type (e.g., transcript)">
      <input type="file" name="doc">
      <button name="upload_doc" type="submit">Upload</button>
    </form>

    <h3>Documents</h3>
    <table><thead><tr><th>ID</th><th>Student</th><th>Type</th><th>Date</th><th>Action</th></tr></thead><tbody>
    <?php foreach($docs as $d): ?>
      <tr>
        <td><?php echo $d['document_id']; ?></td>
        <td><?php echo e($d['student_number'].' - '.$d['first_name'].' '.$d['last_name']); ?></td>
        <td><?php echo e($d['document_type']); ?></td>
        <td><?php echo e($d['issued_date']); ?></td>
        <td>
          <form method="post" style="display:inline">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="document_id" value="<?php echo $d['document_id']; ?>">
            <button name="delete_doc" onclick="return confirm('Delete?')">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?></tbody></table>
    <p><a href="dashboard.php">Back</a></p>
  </div>
</body>
</html>