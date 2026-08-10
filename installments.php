<?php
include 'config.php';
include 'functions.php';

$invoice_id = $_GET['invoice_id'] ?? 0;

// پرداخت یک قسط
if(isset($_GET['pay_installment'])) {
    $inst_id = $_GET['pay_installment'];
    $pdo->prepare("UPDATE installments SET paid = TRUE, paid_date = CURDATE() WHERE id = ?")->execute([$inst_id]);
    
    // به‌روزرسانی مبلغ پرداخت شده در فاکتور
    $inst = $pdo->prepare("SELECT invoice_id, amount FROM installments WHERE id = ?");
    $inst->execute([$inst_id]);
    $inst_data = $inst->fetch(PDO::FETCH_ASSOC);
    $inv_id = $inst_data['invoice_id'];
    $amount = $inst_data['amount'];
    
    $pdo->prepare("UPDATE invoices SET paid_amount = paid_amount + ? WHERE id = ?")->execute([$amount, $inv_id]);
    
    // بررسی اینکه آیا کل فاکتور پرداخت شده است؟
    $inv = $pdo->prepare("SELECT total_amount, paid_amount FROM invoices WHERE id = ?");
    $inv->execute([$inv_id]);
    $inv_data = $inv->fetch(PDO::FETCH_ASSOC);
    if($inv_data['paid_amount'] >= $inv_data['total_amount']) {
        $pdo->prepare("UPDATE invoices SET status = 'paid' WHERE id = ?")->execute([$inv_id]);
    } else {
        $pdo->prepare("UPDATE invoices SET status = 'partial' WHERE id = ?")->execute([$inv_id]);
    }
    
    // بروزرسانی بدهی مشتری
    $cust_id = $pdo->prepare("SELECT customer_id FROM invoices WHERE id = ?");
    $cust_id->execute([$inv_id]);
    $cust = $cust_id->fetch(PDO::FETCH_ASSOC);
    updateTotalDebt($cust['customer_id'], $pdo);
    updateCreditStatus($cust['customer_id'], $pdo);
    
    header("Location: installments.php?invoice_id=$inv_id");
    exit;
}

// دریافت اطلاعات فاکتور و اقساط
$invoice = $pdo->prepare("SELECT i.*, c.name as customer_name FROM invoices i LEFT JOIN customers c ON i.customer_id = c.id WHERE i.id = ?");
$invoice->execute([$invoice_id]);
$inv = $invoice->fetch(PDO::FETCH_ASSOC);
if(!$inv) {
    die("فاکتور یافت نشد.");
}

$installments = $pdo->prepare("SELECT * FROM installments WHERE invoice_id = ? ORDER BY due_date");
$installments->execute([$invoice_id]);
?>
<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مدیریت اقساط - سیمو</title>
    <link rel="stylesheet" href="https://cdn.rtlcss.com/bootstrap/v4.5.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">مدیریت اقساط فاکتور #<?= $invoice_id ?></div>
        <div class="card-body">
            <h5>مشتری: <?= $inv['customer_name'] ?></h5>
            <h5>مبلغ کل: <?= number_format($inv['total_amount']) ?> تومان</h5>
            <h5>پرداخت شده: <?= number_format($inv['paid_amount']) ?> تومان</h5>
            <h5>باقیمانده: <?= number_format($inv['total_amount'] - $inv['paid_amount']) ?> تومان</h5>
            <hr>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>تاریخ سررسید</th>
                        <th>مبلغ</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $counter = 1;
                    while($row = $installments->fetch(PDO::FETCH_ASSOC)) {
                        $status = $row['paid'] ? 'پرداخت شده' : 'پرداخت نشده';
                        $btn = '';
                        if(!$row['paid']) {
                            $btn = "<a href='installments.php?pay_installment={$row['id']}&invoice_id={$invoice_id}' class='btn btn-success btn-sm' onclick='return confirm(\"پرداخت این قسط ثبت شود؟\")'>ثبت پرداخت</a>";
                        } else {
                            $btn = "<span class='badge badge-success'>پرداخت شده در {$row['paid_date']}</span>";
                        }
                        echo "<tr>";
                        echo "<td>$counter</td>";
                        echo "<td>{$row['due_date']}</td>";
                        echo "<td>" . number_format($row['amount']) . " تومان</td>";
                        echo "<td>$status</td>";
                        echo "<td>$btn</td>";
                        echo "</tr>";
                        $counter++;
                    }
                    ?>
                </tbody>
            </table>
            <a href="invoices.php" class="btn btn-secondary">بازگشت به فاکتورها</a>
        </div>
    </div>
</div>
</body>
</html>
