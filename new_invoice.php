<?php
include 'config.php';
include 'functions.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = $_POST['customer_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $payment_type = $_POST['payment_type'];
    $invoice_date = date('Y-m-d');
    
    // دریافت قیمت فروش کالا
    $product = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $product->execute([$product_id]);
    $prod = $product->fetch(PDO::FETCH_ASSOC);
    $unit_price = $prod['selling_price'];
    $total_amount = $unit_price * $quantity;
    
    // کاهش موجودی
    $new_stock = $prod['stock'] - $quantity;
    $pdo->prepare("UPDATE products SET stock = ? WHERE id = ?")->execute([$new_stock, $product_id]);
    
    // ثبت فاکتور
    $stmt = $pdo->prepare("INSERT INTO invoices (customer_id, invoice_date, total_amount, paid_amount, payment_type, status) VALUES (?, ?, ?, ?, ?, 'unpaid')");
    $paid_amount = ($payment_type == 'cash') ? $total_amount : 0;
    $stmt->execute([$customer_id, $invoice_date, $total_amount, $paid_amount, $payment_type]);
    $invoice_id = $pdo->lastInsertId();
    
    // اگر اقساطی است، اقساط را ایجاد کن
    if($payment_type == 'installment') {
        $installment_count = $_POST['installment_count'];
        $installment_amount = round($total_amount / $installment_count, 0);
        for($i = 1; $i <= $installment_count; $i++) {
            $due_date = date('Y-m-d', strtotime("+$i months"));
            $pdo->prepare("INSERT INTO installments (invoice_id, due_date, amount) VALUES (?, ?, ?)")
                ->execute([$invoice_id, $due_date, $installment_amount]);
        }
    } else {
        // نقدی - وضعیت پرداخت شده
        $pdo->prepare("UPDATE invoices SET status = 'paid' WHERE id = ?")->execute([$invoice_id]);
    }
    
    // بروزرسانی بدهی مشتری
    updateTotalDebt($customer_id, $pdo);
    updateCreditStatus($customer_id, $pdo);
    
    header("Location: invoices.php");
    exit;
}

// دریافت لیست مشتریان و کالاها
$customers = $pdo->query("SELECT * FROM customers ORDER BY name");
$products = $pdo->query("SELECT * FROM products WHERE stock > 0 ORDER BY name");
?>
<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاکتور جدید - سیمو</title>
    <link rel="stylesheet" href="https://cdn.rtlcss.com/bootstrap/v4.5.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script>
        function toggleInstallment() {
            var type = document.querySelector('input[name="payment_type"]:checked').value;
            document.getElementById('installment_div').style.display = (type == 'installment') ? 'block' : 'none';
        }
    </script>
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-white">ثبت فاکتور جدید</div>
        <div class="card-body">
            <form method="post" onchange="toggleInstallment()">
                <div class="form-group">
                    <label>مشتری</label>
                    <select name="customer_id" class="form-control" required>
                        <option value="">انتخاب کنید</option>
                        <?php while($c = $customers->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?= $c['id'] ?>"><?= $c['name'] ?> - <?= $c['phone'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>کالا</label>
                    <select name="product_id" class="form-control" required>
                        <option value="">انتخاب کنید</option>
                        <?php while($p = $products->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?= $p['id'] ?>"><?= $p['name'] ?> (موجودی: <?= $p['stock'] ?>) - قیمت: <?= number_format($p['selling_price']) ?> تومان</option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>تعداد</label>
                    <input type="number" name="quantity" class="form-control" min="1" required>
                </div>
                <div class="form-group">
                    <label>نوع پرداخت</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="payment_type" value="cash" checked onclick="toggleInstallment()">
                        <label class="form-check-label">نقد</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="payment_type" value="installment" onclick="toggleInstallment()">
                        <label class="form-check-label">قسط</label>
                    </div>
                </div>
                <div id="installment_div" style="display:none;">
                    <div class="form-group">
                        <label>تعداد اقساط</label>
                        <input type="number" name="installment_count" class="form-control" min="2" max="12" value="3">
                    </div>
                </div>
                <button type="submit" class="btn btn-warning">ثبت فاکتور</button>
                <a href="invoices.php" class="btn btn-secondary">بازگشت</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
