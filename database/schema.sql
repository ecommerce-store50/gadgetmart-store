-- GadgetMart database schema for MySQL/MariaDB on InfinityFree
-- Import this file in phpMyAdmin. This is SQL only; do not rename a PHP file to schema.sql.

CREATE TABLE IF NOT EXISTS settings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) NOT NULL UNIQUE,
  setting_value TEXT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  phone VARCHAR(40) NULL,
  password_hash VARCHAR(255) NOT NULL,
  address TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  icon VARCHAR(20) DEFAULT '📦',
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NULL,
  name VARCHAR(190) NOT NULL,
  slug VARCHAR(220) NOT NULL UNIQUE,
  description TEXT NULL,
  image VARCHAR(255) NULL,
  price DECIMAL(12,2) NOT NULL DEFAULT 0,
  old_price DECIMAL(12,2) NULL,
  stock INT NOT NULL DEFAULT 0,
  sku VARCHAR(80) NULL,
  rating DECIMAL(2,1) NOT NULL DEFAULT 5.0,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  is_bestseller TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS orders (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  customer_name VARCHAR(150) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  email VARCHAR(190) NULL,
  address TEXT NOT NULL,
  total DECIMAL(12,2) NOT NULL DEFAULT 0,
  status ENUM('pending','confirmed','packed','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  payment_method VARCHAR(30) NOT NULL DEFAULT 'cod',
  payment_status VARCHAR(30) NOT NULL DEFAULT 'unpaid',
  payment_reference VARCHAR(80) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS order_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NULL,
  product_name VARCHAR(190) NOT NULL,
  quantity INT NOT NULL,
  unit_price DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS stock_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  change_qty INT NOT NULL,
  reason VARCHAR(200) NOT NULL,
  order_id BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO settings (setting_key, setting_value) VALUES
('site_name','GadgetMart'),
('site_tagline','সেরা গ্যাজেটস সেরা দামে'),
('footer_description','আপনার একমাত্র গ্যাজেট ডেস্টিনেশন।'),
('phone','+880 1234-567890'),
('email','support@gadgetmart.com'),
('address','ঢাকা, বাংলাদেশ'),
('facebook_url','#'),
('instagram_url','#'),
('youtube_url','#'),
('twitter_url','#'),
('whatsapp_url','#'),
('delivery_fee','50'),
('coupon_code','SAVE10'),
('coupon_discount','10')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

INSERT INTO categories (name, icon) VALUES
('স্মার্টফোন','📱'),
('ল্যাপটপ','💻'),
('অডিও','🎧'),
('স্মার্টওয়াচ','⌚'),
('অ্যাকসেসরিজ','🔌');
