<?php
require_once __DIR__ . '/../functions.php';
require_role('admin');
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!verify_csrf($_POST['_csrf']??'')) exit('Invalid CSRF');
    if(isset($_POST['create_bill'])){
        $student_id = intval($_POST['student_id']); $total = floatval($_POST['total_amount']); $status = $_POST['status']??'unpaid';
        if($student_id){ $pdo->prepare('INSERT INTO student_billing (student_id,total_amount,status) VALUES (?,?,?)')->execute([$student_id,$total,$status]); }
    } elseif(isset($_POST['record_payment'])){
        $billing_id = intval($_POST['billing_id']); $amount = floatval($_POST['amount_paid']); $method = $_POST['method']??'cash'; $date = $_POST['payment_date']?:date('Y-m-d');
        if($billing_id && $amount){ $pdo->prepare('INSERT INTO payments (billing_id,amount_paid,payment_date,method) VALUES (?,?,?,?)')->execute([$billing_id,$amount,$date,$method]); }
    }
}
$students = $pdo->query('SELECT student_id,student_number,first_name,last_name FROM students ORDER BY last_name')->fetchAll();
$bills = $pdo->query('SELECT sb.*, s.student_number, s.first_name, s.last_name FROM student_billing sb JOIN students s ON sb.student_id=s.student_id ORDER BY sb.billing_id DESC')->fetchAll();
$payments = $pdo->query('SELECT p.*, sb.billing_id, s.student_number FROM payments p JOIN student_billing sb ON p.billing_id=sb.billing_id JOIN students s ON sb.student_id=s.student_id ORDER BY p.payment_date DESC')->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Billing</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
  <div class="container">
    <h2>Student Billing</h2>
    <h3>Create Billing</h3>
    <form method="post">
      <?php echo csrf_field(); ?>
      <select name="student_id"><?php foreach($students as $s): ?><option value="<?php echo $s['student_id']; ?>"><?php echo e($s['student_number'].' - '.$s['first_name'].' '.$s['last_name']); ?></option><?php endforeach; ?></select>
      <input name="total_amount" placeholder="Total amount" required>
      <select name="status"><option value="unpaid">unpaid</option><option value="paid">paid</option></select>
      <button name="create_bill" type="submit">Create Bill</button>
    </form>

    <h3>Existing Bills</h3>
    <table><thead><tr><th>ID</th><th>Student</th><th>Total</th><th>Status</th></tr></thead><tbody>
    <?php foreach($bills as $b): ?><tr><td><?php echo $b['billing_id']; ?></td><td><?php echo e($b['student_number'].' - '.$b['first_name'].' '.$b['last_name']); ?></td><td><?php echo e($b['total_amount']); ?></td><td><?php echo e($b['status']); ?></td></tr><?php endforeach; ?>
    </tbody></table>

    <h3>Record Payment</h3>
    <form method="post">
      <?php echo csrf_field(); ?>
      <select name="billing_id"><?php foreach($bills as $b): ?><option value="<?php echo $b['billing_id']; ?>"><?php echo e($b['billing_id'].' - '.$b['student_number']); ?></option><?php endforeach; ?></select>
      <input name="amount_paid" placeholder="Amount" required>
      <input type="date" name="payment_date" value="<?php echo date('Y-m-d'); ?>">
      <input name="method" placeholder="Method" value="cash">
      <button name="record_payment" type="submit">Record</button>
    </form>

    <h3>Payments</h3>
    <table><thead><tr><th>ID</th><th>Billing</th><th>Amount</th><th>Date</th><th>Method</th></tr></thead><tbody>
    <?php foreach($payments as $p): ?><tr><td><?php echo $p['payment_id']; ?></td><td><?php echo e($p['student_number'].' (bill '.$p['billing_id'].')'); ?></td><td><?php echo e($p['amount_paid']); ?></td><td><?php echo e($p['payment_date']); ?></td><td><?php echo e($p['method']); ?></td></tr><?php endforeach; ?>
    </tbody></table>
    <p><a href="dashboard.php">Back</a></p>
  </div>
</body>
</html>