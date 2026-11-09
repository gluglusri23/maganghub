CREATE DATABASE IF NOT EXISTS gametopup CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gametopup;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS transaction_details;
DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS payment_methods;
DROP TABLE IF EXISTS games;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE games (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description TEXT NULL,
  icon VARCHAR(20) DEFAULT '🎮',
  theme_color CHAR(7) DEFAULT '#5936bd',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE products (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  game_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  price INT UNSIGNED NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_products_game FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE payment_methods (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  description VARCHAR(255) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE transactions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  invoice VARCHAR(30) NOT NULL UNIQUE,
  user_id BIGINT UNSIGNED NOT NULL,
  payment_method_id BIGINT UNSIGNED NOT NULL,
  total INT UNSIGNED NOT NULL,
  status ENUM('pending','paid','processing','completed','cancelled') NOT NULL DEFAULT 'pending',
  player_id VARCHAR(80) NOT NULL,
  server_id VARCHAR(40) NULL,
  paid_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_transactions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_transactions_payment FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE transaction_details (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  transaction_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NOT NULL,
  game_id BIGINT UNSIGNED NOT NULL,
  product_name VARCHAR(120) NOT NULL,
  game_name VARCHAR(100) NOT NULL,
  quantity INT UNSIGNED NOT NULL DEFAULT 1,
  unit_price INT UNSIGNED NOT NULL,
  subtotal INT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_details_transaction FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_details_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_details_game FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO games (name,description,icon,theme_color) VALUES
('Mobile Legends','Top up Diamond Mobile Legends.','⚔️','#5a42c7'),
('Free Fire','Top up Diamond Free Fire.','🔥','#db5a32'),
('PUBG Mobile','Top up UC PUBG Mobile.','🎯','#b28b35');

INSERT INTO products (game_id,name,price) VALUES
(1,'86 Diamonds',20000),(1,'172 Diamonds',39000),(1,'257 Diamonds',58000),
(2,'70 Diamonds',10000),(2,'140 Diamonds',20000),(2,'355 Diamonds',50000),
(3,'60 UC',15000),(3,'325 UC',75000),(3,'660 UC',145000);

INSERT INTO payment_methods (name,description) VALUES
('QRIS (Simulasi)','Pembayaran QRIS demo, tanpa transaksi uang sungguhan.'),
('Transfer Bank (Simulasi)','Transfer bank demo, tanpa transaksi uang sungguhan.'),
('E-Wallet (Simulasi)','Dompet digital demo, tanpa transaksi uang sungguhan.');

-- Buat akun admin setelah import:
-- Jalankan perintah PHP berikut dari terminal pada folder project:
-- php -r "echo password_hash('Admin123!', PASSWORD_DEFAULT), PHP_EOL;"
-- Salin hasil hash, lalu jalankan:
-- INSERT INTO users(name,email,password,role) VALUES('Administrator','admin@gametopup.test','PASTE_HASH_DI_SINI','admin');
