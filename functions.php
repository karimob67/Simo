<?php
// به‌روزرسانی وضعیت اعتباری مشتری
function updateCreditStatus($customerId, $pdo) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as late_count 
        FROM installments i 
        JOIN invoices inv ON i.invoice_id = inv.id 
        WHERE inv.customer_id = ? 
        AND i.due_date < CURDATE() 
        AND i.paid = FALSE
    ");
    $stmt->execute([$customerId]);
    $late = $stmt->fetch(PDO::FETCH_ASSOC)['late_count'];
    
    if($late > 3) {
        $status = 'bad';
    } elseif($late > 0) {
        $status = 'pending';
    } else {
        $status = 'good';
    }
    
    $pdo->prepare("UPDATE customers SET credit_status = ? WHERE id = ?")
        ->execute([$status, $customerId]);
}

// محاسبه کل بدهی مشتری
function updateTotalDebt($customerId, $pdo) {
    $stmt = $pdo->prepare("
        SELECT SUM(inv.total_amount - inv.paid_amount) as debt 
        FROM invoices inv 
        WHERE inv.customer_id = ? AND inv.status != 'paid'
    ");
    $stmt->execute([$customerId]);
    $debt = $stmt->fetch(PDO::FETCH_ASSOC)['debt'] ?? 0;
    
    $pdo->prepare("UPDATE customers SET total_debt = ? WHERE id = ?")
        ->execute([$debt, $customerId]);
}

// ارسال پیامک (ساختگی - با API واقعی جایگزین کنید)
function sendSms($phone, $message) {
    // کد API پیامک خود را اینجا قرار دهید (مثل کاوه‌نگار، پیامک‌دهی، وغیره)
    // مثال: file_get_contents("https://api.kavenegar.com/v1/.../sms/send.json?receptor=$phone&message=$message");
    return true;
}
?>
