<?php
include 'config.php';
$id = $_GET['id'];
$invoice = $pdo->prepare("SELECT i.*, c.name as customer_name FROM invoices i LEFT JOIN customers c ON i.customer_id = c.id WHERE i.id = ?");
$invoice->execute([$id]);
$row = $invoice->fetch(PDO::FETCH_ASSOC);
if(!$row) die("فاکتور یافت نشد.");
?>
<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>جزییات فاکتور - سیمو</title>
    <link rel="stylesheet" href="https://cdn.rtlcss.com/bootstrap/v4.5.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">جزییات فاکتور #<?= $id ?></div>
        <div class="card-body">
            <p><strong>مشتری:</strong> <?= $row['customer_name'] ?></p>
            <p><strong>تاریخ:</strong> <?= $row['invoice_date'] ?></p>
            <p><strong>مبلغ کل:</strong> <?= number_format($row['total_amount']) ?> تومان</p>
            <p><strong>پرداخت شده:</strong> <?= number_format($row['paid_amount']) ?> تومان</p>
            <p><strong>نوع پرداخت:</strong> <?= $row['payment_type'] == 'cash' ? 'نقد' : 'قسط' ?></p>
            <p><strong>وضعیت:</strong> 
                <?php
                if($row['status'] == 'paid') echo '<span class="badge badge-success">پرداخت شده</span>';
                elseif($row['status'] == 'partial') echo '<span class="badge badge-warning">نیمه پرداخت</span>';
                else echo '<span class="badge badge-danger">پرداخت نشده</span>';
                ?>
            </p>
            <a href="installments.php?invoice_id=<?= $id ?>" class="btn btn-info">مدیریت اقساط</a>
            <a href="invoices.php" class="btn btn-secondary">بازگشت</a>
        </div>
    </div>
</div>
</body>
</html>
