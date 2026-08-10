<?php
include 'config.php';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    
    $stmt = $pdo->prepare("INSERT INTO customers (name, phone, address) VALUES (?, ?, ?)");
    $stmt->execute([$name, $phone, $address]);
    header("Location: customers.php");
    exit;
}
?>
<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ثبت مشتری - سیمو</title>
    <link rel="stylesheet" href="https://cdn.rtlcss.com/bootstrap/v4.5.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">ثبت مشتری جدید</div>
        <div class="card-body">
            <form method="post">
                <div class="form-group">
                    <label>نام و نام خانوادگی</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>شماره موبایل</label>
                    <input type="text" name="phone" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>آدرس</label>
                    <textarea name="address" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-success">ذخیره</button>
                <a href="index.php" class="btn btn-secondary">بازگشت</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
