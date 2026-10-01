-- Database: futsal_zone

CREATE DATABASE IF NOT EXISTS `futsal_zone`;
USE `futsal_zone`;

-- Table structure for users
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `fullname` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('user', 'admin') DEFAULT 'user',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table structure for courts
CREATE TABLE IF NOT EXISTS `courts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `type` VARCHAR(50) NOT NULL,
  `price_per_hour` DECIMAL(10, 2) NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `ball_quality` VARCHAR(100) NOT NULL,
  `facilities` TEXT NOT NULL
);

-- Table structure for bookings
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `court_id` INT NOT NULL,
  `booking_date` DATE NOT NULL,
  `start_time` TIME NOT NULL,
  `duration_hours` INT DEFAULT 1,
  `total_price` DECIMAL(10,2) NOT NULL,
  `status` ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'confirmed',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`court_id`) REFERENCES `courts`(`id`) ON DELETE CASCADE
);

-- Insert Default Courts
INSERT INTO `courts` (`id`, `name`, `type`, `price_per_hour`, `image`, `description`, `ball_quality`, `facilities`) VALUES
(1, 'Lapangan Vinyl A (International)', 'Matras / Vinyl Polypropylene', 120000, 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&w=800&q=80', 'Lapangan indoor standar internasional dengan bahan matras empuk anti slip, sangat cocok untuk turnamen.', 'Bola Specs / Molten Standar FIFA (Original)', 'Locker Room, AC Rest Area, Shower Air Hangat, Kantin, WiFi 100Mbps, Tribun Penonton'),
(2, 'Lapangan Rumput Sintetis B', 'Rumput Sintetis Premium', 100000, 'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?auto=format&fit=crop&w=800&q=80', 'Rumput sintetis halus dengan peredam benturan, nyaman untuk bermain santai maupun kompetitif bersama tim.', 'Bola Specs / Nike Futsal FIFA Approved', 'Locker Room, Kantin & Mini Market, Charging Station, Parkir Luas, Musholla'),
(3, 'Lapangan Parquet Kayu C', 'Hardwood Parquet Premium', 150000, 'https://images.unsplash.com/photo-1518091043644-c1d4457512c6?auto=format&fit=crop&w=800&q=80', 'Lapangan kayu parquet berkilau dengan gaya klasik modern. Laju bola sangat presisi dan cepat.', 'Bola Adidas Futsal Match Ball', 'AC VIP Rest Area, Sound System, Scoreboard Digital, Shower Warm Water, Free Drink');
