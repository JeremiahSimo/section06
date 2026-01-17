-- Create the database
CREATE DATABASE IF NOT EXISTS `inventory_db2`;

-- Use the database
USE `inventory_db2`;

-- Create the products table
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `quantity` INT NOT NULL DEFAULT 0,
  `price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Optional: Insert some sample data
INSERT INTO `products` (`name`, `description`, `quantity`, `price`) VALUES
('Laptop', 'A high-performance laptop for development.', 10, 1200.50),
('Keyboard', 'A mechanical keyboard with RGB lighting.', 50, 75.00),
('Mouse', 'An ergonomic wireless mouse.', 75, 25.99);
