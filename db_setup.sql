-- Ve

CREATE DATABASE IF NOT EXISTS `velvet_vogue_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `velvet_vogue_db`;


-- Table structure for table `users`

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('customer', 'admin') DEFAULT 'customer',
  `phone` VARCHAR(20) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Initial User Seeds (Passwords: 'admin123' and 'user123')
INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `phone`, `address`) VALUES
(1, 'John Finlo (Admin)', 'admin@velvetvogue.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe11yR4fGv6jD1K2qO9/7o2a1XfN2s2Sm', 'admin', '0771234567', 'Velvet Vogue HQ, Colombo 03'),
(2, 'Sarah Jenkins', 'sarah@example.com', '$2y$10$89Wd2XmK1N4DqO5pL7v2e.eS9jD2fG3h4i5j6k7l8m9n0o1p2q3r4', 'customer', '0779876543', '123 Main Street, Kandy');


-- Table structure for table `categories`

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL,
  `slug` VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `categories` (`id`, `name`, `slug`) VALUES
(1, 'Casual Wear', 'casual-wear'),
(2, 'Formal Wear', 'formal-wear'),
(3, 'Outerwear', 'outerwear'),
(4, 'Accessories', 'accessories');

-- Table structure for table `products`

CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `category_id` INT NOT NULL,
  `gender` ENUM('Men', 'Women', 'Unisex') NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `description` TEXT NOT NULL,
  `sizes` VARCHAR(100) DEFAULT 'S, M, L, XL',
  `colors` VARCHAR(100) DEFAULT 'Black, Navy, Beige',
  `image_url` VARCHAR(255) NOT NULL,
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_new` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `products` (`id`, `title`, `category_id`, `gender`, `price`, `description`, `sizes`, `colors`, `image_url`, `is_featured`, `is_new`) VALUES
(1, 'Velvet Touch Evening Blazer', 2, 'Men', 24500.00, 'Tailored burgundy slim-fit velvet blazer designed for high-profile formal events and evening celebrations.', 'S, M, L, XL', 'Burgundy, Midnight Black', 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=600&auto=format&fit=crop&q=80', 1, 1),
(2, 'Silk Satin Formal Gown', 2, 'Women', 32000.00, 'Elegant floor-length silk satin evening dress with front split and delicate waist detailing.', 'S, M, L', 'Emerald Green, Ruby Red, Black', 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?w=600&auto=format&fit=crop&q=80', 1, 1),
(3, 'Urban Oversized Denim Jacket', 1, 'Unisex', 16500.00, 'Vintage washed heavy-duty denim jacket featuring raw edge hems and signature brass buttons.', 'M, L, XL', 'Classic Blue, Black', 'https://images.unsplash.com/photo-1576995853123-5a10305d93c0?w=600&auto=format&fit=crop&q=80', 1, 0),
(4, 'Minimalist Linen Casual Shirt', 1, 'Men', 6800.00, 'Breathable 100% organic linen shirt with mandarin collar, ideal for casual weekend wear.', 'S, M, L, XL', 'White, Sage Green, Beige', 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=600&auto=format&fit=crop&q=80', 0, 1),
(5, 'High-Waist Pleated Trousers', 1, 'Women', 8900.00, 'Sophisticated tailored high-waist trousers with wide legs for a modern smart-casual look.', 'S, M, L', 'Beige, Charcoal Grey, Cream', 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=600&auto=format&fit=crop&q=80', 0, 0),
(6, 'Classic Trench Coat', 3, 'Women', 36500.00, 'Double-breasted water-resistant trench coat with waist belt and custom lining.', 'S, M, L', 'Khaki, Black', 'https://images.unsplash.com/photo-1544441893-675973e31985?w=600&auto=format&fit=crop&q=80', 1, 0),
(7, 'Premium Leather Crossbody Bag', 4, 'Unisex', 14200.00, 'Handcrafted genuine leather shoulder bag with adjustable strap and metallic hardware.', 'One Size', 'Tan Brown, Black', 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600&auto=format&fit=crop&q=80', 0, 1),
(8, 'Structured Slim Suit Trousers', 2, 'Men', 9500.00, 'Flat-front formal suit pants made from wrinkle-resistant poly-viscose stretch blend.', 'S, M, L, XL', 'Navy Blue, Charcoal', 'https://images.unsplash.com/photo-1479064555552-3ef4979f8908?w=600&auto=format&fit=crop&q=80', 0, 0);


-- Table structure for table `orders`

CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_email` VARCHAR(100) NOT NULL,
  `customer_phone` VARCHAR(20) NOT NULL,
  `shipping_address` TEXT NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `payment_method` VARCHAR(50) DEFAULT 'Card Payment',
  `status` ENUM('Pending', 'Processing', 'Completed', 'Cancelled') DEFAULT 'Processing',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample Order
INSERT INTO `orders` (`id`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `total_amount`, `payment_method`, `status`) VALUES
(1001, 2, 'Sarah Jenkins', 'sarah@example.com', '0779876543', '123 Main Street, Kandy', 32000.00, 'Credit Card', 'Completed');


-- Table structure for table `order_items`

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `size` VARCHAR(20) DEFAULT 'M',
  `color` VARCHAR(50) DEFAULT 'Standard',
  `quantity` INT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `size`, `color`, `quantity`, `price`) VALUES
(1, 1001, 2, 'M', 'Ruby Red', 1, 32000.00);


-- Table structure for table `inquiries`

CREATE TABLE IF NOT EXISTS `inquiries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `subject` VARCHAR(150) NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `inquiries` (`id`, `name`, `email`, `subject`, `message`) VALUES
(1, 'Alex Perera', 'alex@gmail.com', 'Sizing Guidance', 'Hi, I would like to know if the Velvet Touch Evening Blazer fits true to size or if I should order one size up? Thanks!');
