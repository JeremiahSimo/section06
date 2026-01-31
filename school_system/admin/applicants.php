<?php
require_once __DIR__ . '/../functions.php';
require_role('admin');
$apps = $pdo->query('SELECT a.*, p.name as program FROM applicants a LEFT JOIN programs p ON a.applied_program=p.program_id ORDER BY a.applicant_id DESC')->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Applicants</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
  <div class="container">
    <h2>Applicants</h2>
    <table><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Program</th><th>Status</th></tr></thead><tbody>
    <?php foreach($apps as $a): ?>
      <tr><td><?php echo e($a['applicant_id']); ?></td><td><?php echo e($a['first_name'].' '.$a['last_name']); ?></td><td><?php echo e($a['email']); ?></td><td><?php echo e($a['program']); ?></td><td><?php echo e($a['application_status']); ?></td></tr>
    <?php endforeach; ?></tbody></table>
    <p><a href="dashboard.php">Back</a></p>
  </div>
</body>
</html>