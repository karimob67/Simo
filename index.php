<?php
include 'config.php';
session_start();
?>
<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سیمو - مدیریت فروشگاه</title>
    <link rel="stylesheet" href="https://cdn.rtlcss.com/bootstrap/v4.5.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container-fluid">
    <!-- هدر -->
    <nav class="navbar navbar-light bg-white shadow-sm mb-4">
        <span class="logo"><i class="fas fa-store"></i> سیمو</span>
        <span class="navbar-text">
            <i class="fas fa-user-circle"></i> خوش آمدید
        </span>
    </nav>

    <?php
    // آمار کلی
    $totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $totalCustomers = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
    $totalDebt = $pdo->query("SELECT SUM(total_debt) FROM customers")->fetchColumn();
    $todaySales = $pdo->query("SELECT SUM(total_amount) FROM invoices WHERE DATE(invoice_date)=CURDATE()")->fetchColumn();
    ?>

    <!-- کارت‌های آماری -->
    <div class="row">
        <div class="col-md-3">
            <div class="card card-stats bg-white p-3">
                <h6>کل کالاها</h6>
                <h2><?= $totalProducts ?></h2>
                <small><i class="fas fa-box"></i> موجودی انبار</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats bg-white p-3">
                <h6>مشتریان</h6>
                <h2><?= $totalCustomers ?></h2>
                <small><i class="fas fa-users"></i> ثبت شده</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats bg-white p-3">
                <h6>مجموع بدهی</h6>
                <h2><?= number_format($totalDebt) ?> تومان</h2>
                <small><i class="fas fa-credit-card"></i> معوقات</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats bg-white p-3">
                <h6>فروش امروز</h6>
                <h2><?= number_format($todaySales) ?> تومان</h2>
                <small><i class="fas fa-chart-line"></i> امروز</small>
            </div>
        </div>
    </div>

    <!-- منوی سریع -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <a href="add_product.php" class="btn btn-primary"><i class="fas fa-plus"></i> کالای جدید</a>
                    <a href="add_customer.php" class="btn btn-success"><i class="fas fa-user-plus"></i> مشتری جدید</a>
                    <a href="new_invoice.php" class="btn btn-warning"><i class="fas fa-file-invoice"></i> فاکتور جدید</a>
                    <a href="customers.php" class="btn btn-info"><i class="fas fa-address-book"></i> لیست مشتریان</a>
                    <a href="send_promotion.php" class="btn btn-secondary"><i class="fas fa-bullhorn"></i> ارسال تبلیغات</a>
                    <a href="send_reminders.php" class="btn btn-danger"><i class="fas fa-bell"></i> یادآوری اقساط</a>
                </div>
            </div>
        </div>
    </div>

    <!-- لیست مشتریان بدحساب و آخرین پیامک‌ها -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">مشتریان بدحساب</div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php
                        $badCustomers = $pdo->query("SELECT * FROM customers WHERE credit_status='bad' LIMIT 5");
                        while($row = $badCustomers->fetch(PDO::FETCH_ASSOC)) {
                            echo "<li class='list-group-item d-flex justify-content-between'>";
                            echo $row['name'] . " - " . $row['phone'];
                            echo "<span class='badge badge-danger'>بدهی: " . number_format($row['total_debt']) . " تومان</span>";
                            echo "</li>";
                        }
                        if($badCustomers->rowCount() == 0) echo "<li class='list-group-item'>همه مشتریان خوش‌حساب هستند ✅</li>";
                        ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">آخرین پیامک‌ها</div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php
                        $smsLogs = $pdo->query("SELECT * FROM sms_logs ORDER BY sent_at DESC LIMIT 5");
                        while($row = $smsLogs->fetch(PDO::FETCH_ASSOC)) {
                            echo "<li class='list-group-item'>";
                            echo $row['message'];
                            echo "<small class='d-block text-muted'>" . $row['sent_at'] . "</small>";
                            echo "</li>";
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
