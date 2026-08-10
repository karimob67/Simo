<?php
include 'config.php';
include 'functions.php';

$stmt = $pdo->query("
    SELECT c.id, c.name, c.phone, i.amount, i.due_date 
    FROM installments i 
    JOIN invoices inv ON i.invoice_id = inv.id 
    JOIN customers c ON inv.customer_id = c.id 
    WHERE i.due_date < CURDATE() AND i.paid = FALSE AND i.reminder_sent = FALSE
");

$count = 0;
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $message = "سلام {$row['name']}، قسط شما به مبلغ {$row['amount']} تومان برای تاریخ {$row['due_date']} معوق شده است. لطفاً پرداخت نمایید.";
    // sendSms($row['phone'], $message);
    
    $pdo->prepare("INSERT INTO sms_logs (customer_id, type, message) VALUES (?, 'reminder', ?)")
        ->execute([$row['id'], $message]);
    
    $pdo->prepare("UPDATE installments SET reminder_sent = TRUE WHERE id = ?")
        ->execute([$row['id']]);
    $count++;
}

echo "<div class='alert alert-success'>$count پیامک یادآوری ارسال شد.</div>";
echo "<a href='index.php' class='btn btn-primary'>بازگشت به داشبورد</a>";
?>
