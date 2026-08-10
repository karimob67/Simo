<?php
$host = 'localhost';
$dbname = 'simu';
$username = 'root';   // نام کاربری دیتابیس خود را وارد کنید
$password = '';       // رمز عبور دیتابیس خود را وارد کنید

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("خطا در اتصال به دیتابیس: " . $e->getMessage());
}
?>
