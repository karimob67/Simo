<?php
include 'config.php';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $purchase_price = $_POST['purchase_price'];
    $selling_price = $_POST['selling_price'];
    $stock = $_POST['stock'];
    
    $stmt = $pdo->prepare("INSERT INTO products (name, purchase_price, selling_price, stock) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $purchase_price, $selling_price, $stock]);
    header("Location: products.php");
    exit;
}
?>
<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ثبت کالا - سیمو</title>
    <link rel="stylesheet" href="https://cdn.rtlcss.com/bootstrap/v4.5.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">ثبت کالای جدید</div>
        <div class="card-body">
            <form method="post">
                <div class="form-group">
                    <label>نام کالا</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>قیمت فاکتور (خرید)</label>
                    <input type="number" name="purchase_price" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>قیمت فروش</label>
                    <input type="number" name="selling_price" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>تعداد موجودی</label>
                    <input type="number" name="stock" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">ذخیره</button>
                <a href="products.php" class="btn btn-secondary">بازگشت</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
