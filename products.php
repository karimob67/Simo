<?php
include 'config.php';
// حذف کالا
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
    header("Location: products.php");
    exit;
}
?>
<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مدیریت کالاها - سیمو</title>
    <link rel="stylesheet" href="https://cdn.rtlcss.com/bootstrap/v4.5.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between">
            <span>لیست کالاها</span>
            <a href="add_product.php" class="btn btn-light btn-sm">+ کالای جدید</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>نام کالا</th>
                        <th>قیمت فاکتور</th>
                        <th>قیمت فروش</th>
                        <th>موجودی</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $products = $pdo->query("SELECT * FROM products ORDER BY id DESC");
                    while($row = $products->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>{$row['id']}</td>";
                        echo "<td>{$row['name']}</td>";
                        echo "<td>" . number_format($row['purchase_price']) . " تومان</td>";
                        echo "<td>" . number_format($row['selling_price']) . " تومان</td>";
                        echo "<td>{$row['stock']}</td>";
                        echo "<td>
                                <a href='edit_product.php?id={$row['id']}' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i></a>
                                <a href='products.php?delete={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"حذف شود؟\")'><i class='fas fa-trash'></i></a>
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
