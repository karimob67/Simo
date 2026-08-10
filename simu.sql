CREATE DATABASE IF NOT EXISTS simu;
USE simu;

-- جدول کالاها
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    purchase_price DECIMAL(15,2) NOT NULL,
    selling_price DECIMAL(15,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- جدول مشتریان
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    phone VARCHAR(15) UNIQUE NOT NULL,
    address TEXT,
    credit_status ENUM('good','bad','pending') DEFAULT 'good',
    total_debt DECIMAL(15,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- جدول فاکتورهای فروش
CREATE TABLE invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    invoice_date DATE NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL,
    paid_amount DECIMAL(15,2) DEFAULT 0,
    payment_type ENUM('cash','installment') NOT NULL,
    status ENUM('paid','unpaid','partial') DEFAULT 'unpaid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
);

-- جدول اقساط
CREATE TABLE installments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT,
    due_date DATE NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    paid BOOLEAN DEFAULT FALSE,
    paid_date DATE,
    reminder_sent BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
);

-- جدول پیامک‌ها
CREATE TABLE sms_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    type ENUM('reminder','promotion') NOT NULL,
    message TEXT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

-- نمونه داده (اختیاری)
INSERT INTO products (name, purchase_price, selling_price, stock) VALUES
('یخچال سامسونگ', 15000000, 18500000, 5),
('اجاق گاز پارس', 7000000, 9500000, 8),
('ماشین لباسشویی ال جی', 12000000, 15500000, 3);
