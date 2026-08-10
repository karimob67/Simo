<?php
include 'config.php';
include 'functions.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $message = $_POST['message'];
    
    $customers = $pdo->query("SELECT id, phone FROM customers WHERE credit_status='good'");
    while($row = $customers->fetch(PDO::FETCH_ASSOC)) {
        // sendSms($row['phone'], $message);
        $pdo->prepare("INSERT INTO sms_logs (customer_id, type, message) VALUES (?, 'promotion', ?)")
            ->execute([$row['id'], $message]);
    }
    echo "<div class='alert alert-success'>پیامک‌های تبلیغاتی ارسال شد.</div>";
}
?>
<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ارسال تبلیغات - سیمو</title>
    <link rel="stylesheet" href="https://cdn.rtlcss.com/bootstrap/v4.5.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">ارسال پیامک تبلیغاتی به مشتریان خوش‌حساب</div>
        <div class="card-body">
            <form method="post">
                <div class="form-group">
                    <label>کالا</label>
                    <select name="product_id" class="form-control">
                        <?php
                        $products = $pdo->query("SELECT * FROM products");
                        while($p = $products->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='{$p['id']}'>{$p['name']} - قیمت: {$p['selling_price']} تومان</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>متن پیام</label>
                    <textarea name="message" class="form-control" rows="3">محصول جدید با تخفیف ویژه به انبار رسید. برای خرید تماس بگیرید.</textarea>
                </div>
                <button type="submit" class="btn btn-warning">ارسال به تمام مشتریان خوش‌حساب</button>
                <a href="index.php" class="btn btn-secondary">بازگشت</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
