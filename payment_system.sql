CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    phone VARCHAR(20),
    amount DECIMAL(10,2),
    txn_id VARCHAR(50) UNIQUE,
    status ENUM('Pending', 'Verified', 'Failed'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
