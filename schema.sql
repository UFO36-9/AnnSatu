CREATE DATABASE IF NOT EXISTS annsatu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE annsatu;

CREATE TABLE IF NOT EXISTS menu_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description VARCHAR(500) NOT NULL,
    price DECIMAL(8,2) UNSIGNED NOT NULL,
    is_available BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS employees (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    email VARCHAR(190) NULL,
    fulfillment ENUM('pickup', 'delivery') NOT NULL,
    delivery_address VARCHAR(255) NULL,
    notes VARCHAR(500) NULL,
    total DECIMAL(9,2) UNSIGNED NOT NULL,
    status ENUM('received', 'preparing', 'ready', 'completed', 'cancelled') NOT NULL DEFAULT 'received',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_orders_created_at (created_at),
    INDEX idx_orders_status (status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    menu_item_id INT UNSIGNED NULL,
    item_name VARCHAR(150) NOT NULL,
    quantity TINYINT UNSIGNED NOT NULL,
    unit_price DECIMAL(8,2) UNSIGNED NOT NULL,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_menu FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reservations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    reservation_date DATE NOT NULL,
    reservation_time VARCHAR(20) NOT NULL,
    guests TINYINT UNSIGNED NOT NULL,
    status ENUM('pending', 'confirmed', 'seated', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_reservations_date_time (reservation_date, reservation_time)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS feedback (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NULL,
    message VARCHAR(1500) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_feedback_created_at (created_at)
) ENGINE=InnoDB;

INSERT INTO menu_items (id, name, category, description, price) VALUES
(1, 'Burrata & Heirloom Tomato', 'Starters', 'Creamy burrata, sun-ripened tomatoes, basil oil, grilled sourdough.', 13.00),
(2, 'Crispy Calamari', 'Starters', 'Lightly fried calamari, lemon, parsley, and our house marinara.', 15.00),
(3, 'Wild Mushroom Risotto', 'Mains', 'Arborio rice, woodland mushrooms, parmesan, a little white wine.', 24.00),
(4, 'Sunday Roast Chicken', 'Mains', 'Herb-roasted chicken, buttery mash, seasonal greens, pan jus.', 27.00),
(5, 'The AnnSatu Burger', 'Mains', 'Grass-fed beef, aged cheddar, onion jam, brioche, hand-cut fries.', 22.00),
(6, 'Seared Salmon', 'Mains', 'Crisp-skinned salmon, lemon butter, asparagus, spring peas.', 28.00),
(7, 'Olive Oil Cake', 'Desserts', 'Citrus-soaked cake, whipped mascarpone, seasonal berries.', 11.00),
(8, 'Warm Chocolate Tart', 'Desserts', 'Dark chocolate ganache, flaky sea salt, vanilla bean gelato.', 12.00),
(9, 'Garden Spritz', 'Drinks', 'Elderflower, fresh cucumber, sparkling wine, a little sunshine.', 14.00),
(10, 'House Lemonade', 'Drinks', 'Freshly squeezed lemons, garden mint, just enough sparkle.', 7.00)
ON DUPLICATE KEY UPDATE name = VALUES(name);
