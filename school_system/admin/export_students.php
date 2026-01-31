<?php
require_once __DIR__ . '/../functions.php';
require_role('admin');
$students = $pdo->query('SELECT s.student_number,s.first_name,s.last_name,s.year_level,s.status,u.email FROM students s JOIN users u ON s.user_id=u.user_id')->fetchAll();
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="students.csv"');
$out = fopen('php://output','w');
fputcsv($out,['student_number','first_name','last_name','year_level','status','email']);
foreach($students as $s) fputcsv($out,[$s['student_number'],$s['first_name'],$s['last_name'],$s['year_level'],$s['status'],$s['email']]);
fclose($out);
exit;
?>