<?php
include 'config.php';
include 'functions.php';

// حذف فاکتور
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // ابتدا اقساط را حذف می‌کنیم (یا به دلیل cascade در دیتابیس خودکار حذف می‌شوند)
    $pdo->prepare("DELETE FROM invoices WHERE id = ?")->execute([$id]);
    header("Location: invoices.php");
    exit;
}
?>
<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاکتورها - سیمو</title>
    <link rel="stylesheet" href="https://cdn.rtlcss.com/bootstrap/v4.5.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-white d-flex justify-content-between">
            <span>لیست فاکتورها</span>
            <a href="new_invoice.php" class="btn btn-light btn-sm">+ فاکتور جدید</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>مشتری</th>
                        <th>تاریخ</th>
                        <th>مبلغ کل</th>
                        <th>پرداخت شده</th>
                        <th>نوع</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $invoices = $pdo->query("
                        SELECT i.*, c.name as customer_name 
                        FROM invoices i 
                        LEFT JOIN customers c ON i.customer_id = c.id 
                        ORDER BY i.id DESC
                    ");
                    while($row = $invoices->fetch(PDO::FETCH_ASSOC)) {
                        $status_badge = '';
                        if($row['status'] == 'paid') $status_badge = '<span class="badge badge-success">پرداخت شده</span>';
                        elseif($row['status'] == 'partial') $status_badge = '<span class="badge badge-warning">نیمه پرداخت</span>';
                        else $status_badge = '<span class="badge badge-danger">پرداخت نشده</span>';
                        
                        echo "<tr>";
                        echo "<td>{$row['id']}</td>";
                        echo "<td>{$row['customer_name']}</td>";
                        echo "<td>{$row['invoice_date']}</td>";
                        echo "<td>" . number_format($row['total_amount']) . " تومان</td>";
                        echo "<td>" . number_format($row['paid_amount']) . " تومان</td>";
                        echo "<td>" . ($row['payment_type'] == 'cash' ? 'نقد' : 'قسط') . "</td>";
                        echo "<td>$status_badge</td>";
                        echo "<td>
                                <a href='invoice_detail.php?id={$row['id']}' class='btn btn-info btn-sm'><i class='fas fa-eye'></i></a>
                                <a href='invoices.php?delete={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"حذف شود؟\")'><i class='fas fa-trash'></i></a>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <a href="index.php" class="btn btn-secondary mt-3">بازگشت به داشبورد</a>
</div>
</body>
</html>
