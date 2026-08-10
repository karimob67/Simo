<?php
include 'config.php';
$id = $_GET['id'];
$product = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$product->execute([$id]);
$row = $product->fetch(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $purchase_price = $_POST['purchase_price'];
    $selling_price = $_POST['selling_price'];
    $stock = $_POST['stock'];
    
    $stmt = $pdo->prepare("UPDATE products SET name=?, purchase_price=?, selling_price=?, stock=? WHERE id=?");
    $stmt->execute([$name, $purchase_price, $selling_price, $stock, $id]);
    header("Location: products.php");
    exit;
}
?>
<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ویرایش کالا - سیمو</title>
    <link rel="stylesheet" href="https://cdn.rtlcss.com/bootstrap/v4.5.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-white">ویرایش کالا</div>
        <div class="card-body">
            <form method="post">
                <div class="form-group">
                    <label>نام کالا</label>
                    <input type="text" name="name" class="form-control" value="<?= $row['name'] ?>" required>
                </div>
                <div class="form-group">
                    <label>قیمت فاکتور</label>
                    <input type="number" name="purchase_price" class="form-control" value="<?= $row['purchase_price'] ?>" required>
                </div>
                <div class="form-group">
                    <label>قیمت فروش</label>
                    <input type="number" name="selling_price" class="form-control" value="<?= $row['selling_price'] ?>" required>
                </div>
                <div class="form-group">
                    <label>موجودی</label>
                    <input type="number" name="stock" class="form-control" value="<?= $row['stock'] ?>" required>
                </div>
                <button type="submit" class="btn btn-warning">به‌روزرسانی</button>
                <a href="products.php" class="btn btn-secondary">بازگشت</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
