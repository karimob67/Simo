<?php
include 'config.php';
$customer_id = $_GET['customer_id'];
$customer = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$customer->execute([$customer_id]);
$cust = $customer->fetch(PDO::FETCH_ASSOC);
if(!$cust) die("مشتری یافت نشد.");

$invoices = $pdo->prepare("SELECT * FROM invoices WHERE customer_id = ? ORDER BY id DESC");
$invoices->execute([$customer_id]);
?>
<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاکتورهای مشتری - سیمو</title>
    <link rel="stylesheet" href="https://cdn.rtlcss.com/bootstrap/v4.5.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">فاکتورهای <?= $cust['name'] ?></div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>تاریخ</th>
                        <th>مبلغ کل</th>
                        <th>پرداخت شده</th>
                        <th>نوع</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $invoices->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['invoice_date'] ?></td>
                            <td><?= number_format($row['total_amount']) ?> تومان</td>
                            <td><?= number_format($row['paid_amount']) ?> تومان</td>
                            <td><?= $row['payment_type'] == 'cash' ? 'نقد' : 'قسط' ?></td>
                            <td><?= $row['status'] ?></td>
                            <td>
                                <a href="invoice_detail.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">مشاهده</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <a href="customers.php" class="btn btn-secondary">بازگشت به مشتریان</a>
        </div>
    </div>
</div>
</body>
</html>
