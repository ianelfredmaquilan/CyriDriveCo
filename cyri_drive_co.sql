-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2026 at 06:46 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cyri_drive_co`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password_hash`, `email`, `created_at`) VALUES
(1, 'admin', '$2y$10$KMKSRSaCjE3BfE195PThaeqoP3dLXITgtdEreR1C3pn7QWL4366My', 'admin@cyridrive.com', '2026-05-24 03:18:20');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `number_of_days` int(11) NOT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `booking_status` varchar(20) DEFAULT 'Pending',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `pickup_time` time DEFAULT '09:00:00',
  `return_time` time DEFAULT '17:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `customer_id`, `vehicle_id`, `booking_date`, `number_of_days`, `price_per_day`, `total_amount`, `booking_status`, `start_date`, `end_date`, `pickup_time`, `return_time`) VALUES
(13, 11, 14, '2026-05-24 02:49:30', 4, 3400.00, 17000.00, 'Confirmed', '2026-05-12', '2026-05-08', '09:00:00', '17:00:00'),
(14, 11, 14, '2026-05-24 02:58:57', 4, 3400.00, 17000.00, 'Confirmed', '2026-05-12', '2026-05-08', '09:00:00', '17:00:00'),
(15, 11, 3, '2026-05-24 02:59:45', 17, 1700.00, 30600.00, 'Confirmed', '2026-05-13', '2026-05-30', '09:00:00', '17:00:00'),
(16, 11, 3, '2026-05-24 03:15:29', 13, 1700.00, 23800.00, 'Confirmed', '2026-06-11', '2026-06-24', '09:00:00', '17:00:00'),
(17, 11, 11, '2026-05-24 03:20:21', 7, 4000.00, 32000.00, 'Confirmed', '2026-05-24', '2026-05-31', '09:00:00', '17:00:00'),
(18, 11, 8, '2026-05-24 03:23:59', 2, 3000.00, 9000.00, 'Confirmed', '2026-05-28', '2026-05-30', '09:00:00', '17:00:00'),
(19, 11, 11, '2026-05-24 03:40:02', 7, 4000.00, 32000.00, 'Confirmed', '2026-05-24', '2026-05-31', '09:00:00', '17:00:00'),
(20, 11, 11, '2026-05-24 03:46:08', 12, 4000.00, 52000.00, 'Confirmed', '2026-06-17', '2026-06-29', '09:00:00', '17:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `phone_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `full_name`, `phone`, `email`, `password_hash`, `email_verified`, `phone_verified`, `created_at`) VALUES
(1, 'Renzo Inot', '09123456789', 'renzo@example.com', '$2y$10$n19yNufkTnwoKDh2unP7WemtXYeeD6FUbFcdt5qAYbSK4NcVxb5b.', 0, 0, '2026-05-16 13:38:01'),
(2, 'Carl Santos', '09123456789', 'carl@cyridrive.com', '$2y$10$n19yNufkTnwoKDh2unP7WemtXYeeD6FUbFcdt5qAYbSK4NcVxb5b.', 0, 0, '2026-05-16 14:43:55'),
(3, 'Vince Sabaan', '0975548143111', 'vincebaho@gmail.com', '$2y$10$n19yNufkTnwoKDh2unP7WemtXYeeD6FUbFcdt5qAYbSK4NcVxb5b.', 0, 0, '2026-05-16 14:48:16'),
(4, 'Ian Elfred Maquilan', '09158619838', 'ianelfredmaquilan@gmail.com', '$2y$10$n19yNufkTnwoKDh2unP7WemtXYeeD6FUbFcdt5qAYbSK4NcVxb5b.', 0, 0, '2026-05-20 12:38:27'),
(5, 'Sherlyn', '09084480104', 'rohrerz@gmail.com', '$2y$10$n19yNufkTnwoKDh2unP7WemtXYeeD6FUbFcdt5qAYbSK4NcVxb5b.', 0, 0, '2026-05-20 17:02:01'),
(6, 'Ian Godinez', '090822334455', 'iangodinez@gmail.com', '$2y$10$n19yNufkTnwoKDh2unP7WemtXYeeD6FUbFcdt5qAYbSK4NcVxb5b.', 0, 0, '2026-05-21 01:11:03'),
(7, 'fati godinez', '09996767868', 'godinezfati658@gmail.com', '$2y$10$n19yNufkTnwoKDh2unP7WemtXYeeD6FUbFcdt5qAYbSK4NcVxb5b.', 0, 0, '2026-05-21 02:44:56'),
(8, 'Joellyn M. Carlos', '09158619838', 'joellyn@gmail.com', '$2y$10$n19yNufkTnwoKDh2unP7WemtXYeeD6FUbFcdt5qAYbSK4NcVxb5b.', 0, 0, '2026-05-21 03:46:22'),
(11, 'Sher Lopez', '09084480104', 'lolomongpogi@gmail.com', '$2y$10$we01DiCAV9tTgFtzG7DKeuaofATB2/QmmI1mEbxxqavTW7XIWcFVa', 0, 0, '2026-05-24 02:46:39');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_status` varchar(20) DEFAULT 'Unpaid',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `booking_id`, `payment_method`, `amount_paid`, `payment_status`, `payment_date`) VALUES
(13, 13, 'GCash', 17000.00, 'Paid', '2026-05-24 02:49:30'),
(14, 14, 'GCash', 17000.00, 'Paid', '2026-05-24 02:58:57'),
(15, 15, 'GCash', 30600.00, 'Paid', '2026-05-24 02:59:45'),
(16, 16, 'Cash', 23800.00, 'Paid', '2026-05-24 03:15:29'),
(17, 17, 'Cash', 32000.00, 'Paid', '2026-05-24 03:20:21'),
(18, 18, 'Cash', 9000.00, 'Paid', '2026-05-24 03:23:59'),
(19, 19, 'Cash', 32000.00, 'Paid', '2026-05-24 03:40:02'),
(20, 20, 'Cash', 52000.00, 'Paid', '2026-05-24 03:46:08');

-- --------------------------------------------------------

--
-- Table structure for table `rentals`
--

CREATE TABLE `rentals` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `car` varchar(100) NOT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `days` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `booking_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rentals`
--

INSERT INTO `rentals` (`id`, `customer_name`, `car`, `price_per_day`, `days`, `total_price`, `booking_date`) VALUES
(1, 'TestUser', 'Toyota Vios', 1500.00, 2, 3000.00, '2026-05-16 22:26:40'),
(2, 'Carl Santos', 'Toyota Fortuner', 3000.00, 3, 9000.00, '2026-05-16 22:43:55'),
(3, 'Vince Sabaan', 'Nissan Sentra', 1700.00, 2, 3400.00, '2026-05-16 22:48:16'),
(4, 'Ian Elfred Maquilan', 'Ford Raptor', 4000.00, 69, 276000.00, '2026-05-20 20:38:27'),
(5, 'Ian Elfred Maquilan', 'Nissan Sentra', 1700.00, 12, 20400.00, '2026-05-20 22:10:32'),
(6, 'Sherlyn', 'Mitsubishi Mirage', 1400.00, 1, 1400.00, '2026-05-21 01:02:01'),
(7, 'Ian Godinez', 'Suzuki Desire', 1300.00, 4, 5200.00, '2026-05-21 09:11:03'),
(8, 'fati godinez', 'Mitsubishi Mirage', 1400.00, 1, 1400.00, '2026-05-21 10:44:56'),
(9, 'Joellyn M. Carlos', 'Mercedes Benz V Class', 5000.00, 23, 115000.00, '2026-05-21 11:46:22'),
(10, 'ian elfred', 'Mitsubishi Mirage', 1400.00, 25, 35000.00, '2026-05-22 15:34:45'),
(11, 'Ian Elfred Maquilan', 'Nissan Terra', 3100.00, 30, 93000.00, '2026-05-22 15:52:19'),
(12, 'Fati Godinez', 'Mitsubishi Mirage', 1400.00, 100, 140000.00, '2026-05-24 01:10:19'),
(13, 'Sher Lopez', 'Nissan Urvan', 3400.00, 4, 17000.00, '2026-05-24 10:49:30'),
(14, 'Sher Lopez', 'Nissan Urvan', 3400.00, 4, 17000.00, '2026-05-24 10:58:57'),
(15, 'Sher Lopez', 'Nissan Sentra', 1700.00, 17, 30600.00, '2026-05-24 10:59:45'),
(16, 'Sher Lopez', 'Nissan Sentra', 1700.00, 13, 23800.00, '2026-05-24 11:15:29'),
(17, 'Sher Lopez', 'Ford Raptor', 4000.00, 7, 32000.00, '2026-05-24 11:20:21'),
(18, 'Sher Lopez', 'Mitsubishi Montero', 3000.00, 2, 9000.00, '2026-05-24 11:23:59'),
(19, 'Sher Lopez', 'Ford Raptor', 4000.00, 7, 32000.00, '2026-05-24 11:40:02'),
(20, 'Sher Lopez', 'Ford Raptor', 4000.00, 12, 52000.00, '2026-05-24 11:46:08');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `vehicle_id` int(11) NOT NULL,
  `vehicle_name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `engine` varchar(100) DEFAULT NULL,
  `transmission` varchar(30) DEFAULT NULL,
  `seats` int(11) DEFAULT NULL,
  `fuel_type` varchar(30) DEFAULT NULL,
  `image_url` text DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`vehicle_id`, `vehicle_name`, `category`, `price_per_day`, `engine`, `transmission`, `seats`, `fuel_type`, `image_url`, `is_available`) VALUES
(1, 'Toyota Vios', 'Sedan', 1500.00, '1.3L', 'Automatic', 5, 'Gasoline', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQFuHcqPbXyNern0jXSoaVSs8wlqSkIR1Q_dQ&s', 1),
(2, 'Mitsubishi Mirage', 'Sedan', 1400.00, '1.2L', 'Manual', 5, 'Gasoline', 'https://hips.hearstapps.com/hmg-prod/images/2024-mitsubishi-mirage-g4-103-6508a36ae3654.jpg?crop=0.792xw:1.00xh;0.106xw,0&resize=1200:*', 1),
(3, 'Nissan Sentra', 'Sedan', 1700.00, '1.6L', 'Automatic', 5, 'Gasoline', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQjQ1oxexejkwAMwV6GtMUxDaEmqWvKY9J__Q&s', 1),
(4, 'Suzuki Desire', 'Sedan', 1300.00, '1.2L', 'Manual', 5, 'Gasoline', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRr9N6kWfaAcNbgjNbMI6wNM1HOt8PqYJK-8A&s', 1),
(5, 'Toyota Fortuner', 'SUV', 3000.00, '2.4L Diesel', 'Automatic', 7, 'Diesel', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTMKz0kZB5jUAQ2Bk1ikjznxdi7MutM2TFEdg&s', 1),
(6, 'Ford Everest', 'SUV', 3200.00, '2.0L Diesel', 'Automatic', 7, 'Diesel', 'https://d1hv7ee95zft1i.cloudfront.net/custom/car-model-photo/mobile/gallery/ford-everest-6757da49ce3ec.jpg', 1),
(7, 'Nissan Terra', 'SUV', 3100.00, '2.5L Diesel', 'Automatic', 7, 'Diesel', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRM4AI5zjpuYEB9rSERSM3bfRGl-p_33pBxmw&s', 1),
(8, 'Mitsubishi Montero', 'SUV', 3000.00, '2.4L Diesel', 'Automatic', 7, 'Diesel', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRD3tICpKmo9iO6ipFxgqOHzmoTSSIMVhR3JA&s', 1),
(9, 'Toyota Hilux', 'Pickup Truck', 2800.00, '2.4L Diesel', 'Manual', 5, 'Diesel', 'https://imgcdn.zigwheels.ph/large/gallery/exterior/30/813/toyota-hilux-front-angle-low-view-236132.jpg', 1),
(10, 'Nissan Navara', 'Pickup Truck', 2900.00, '2.5L Diesel', 'Automatic', 5, 'Diesel', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQReZ68dyjhtpOjSi6CTiNS_c7aaBuMoyyydg&s', 1),
(11, 'Ford Raptor', 'Pickup Truck', 4000.00, '2.0L Bi-Turbo', 'Automatic', 5, 'Diesel', 'https://d1hv7ee95zft1i.cloudfront.net/custom/car-model-photo/standard/ford-ranger-raptor-671aea0c12e9f.jpg', 1),
(12, 'Mitsubishi Strada', 'Pickup Truck', 2700.00, '2.4L Diesel', 'Manual', 5, 'Diesel', 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEghgrkqbrsU2QKM_aM_rj0waJRrWlC0Pd4HQSoQ38ivioE51P5UPJ4Pr_gHaV_asBuE3nso5GtwuRQESlntZ-pChOHUnTBT-fJmajKeebZnwj54lIlCDkVwdh3IMKZQG6UN2mf-689JBVrL/s1100/2020_mitsubishi_strada_athlete_4WD_00.jpg', 1),
(13, 'Toyota Hiace', 'Van', 3500.00, '2.8L Diesel', 'Manual', 15, 'Diesel', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQSmAqf7DNLwXUIGrCiEobIxANGHqO-zyCQHg&s', 1),
(14, 'Nissan Urvan', 'Van', 3400.00, '2.5L Diesel', 'Manual', 15, 'Diesel', 'https://d1hv7ee95zft1i.cloudfront.net/custom/car-model-photo/gallery/2023-nissan-urvan-nv350-cargo-65a0ba4c9619e.jpg', 1),
(15, 'Mercedes Benz V Class', 'Van', 5000.00, '2.0L Turbo', 'Automatic', 7, 'Diesel', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQO1-bqF3MAziDsWmrAYreePxd5mnLHRg0aYg&s', 1),
(16, 'Ford Transit', 'Van', 3600.00, '2.2L Diesel', 'Manual', 15, 'Diesel', 'https://i0.wp.com/travelupdate.ph/wp-content/uploads/2019/12/All-New-Ford-Transit-3_LO.jpg?resize=600%2C390&ssl=1', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `rentals`
--
ALTER TABLE `rentals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`vehicle_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `rentals`
--
ALTER TABLE `rentals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
