-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 19, 2026 at 10:32 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `riderent_prodb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'Admin',
  `address` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `full_name`, `username`, `email`, `phone`, `password`, `role`, `address`, `profile_image`, `status`, `created_at`) VALUES
(1, 'Ornima Hafizur', 'ornima_admin', 'ornima5170@gmail.com', '01710000001', 'ornima123', 'Super Admin', 'Dhaka', 'ornima.jpg', 'Active', '2026-07-12 14:29:35'),
(2, 'Sharafat Hossain', 'sharafat_admin', 'sharafatcox50@gmail.com', '01710000002', 'Hamim5756', 'Admin', 'Uttara', 'anisha.jpg', 'Active', '2026-07-12 14:29:35');

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `booking_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` int(11) NOT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `driver_fee` decimal(10,2) DEFAULT 0.00,
  `booking_status` enum('Pending','Confirmed','Completed','Cancelled','Driver_Requested') DEFAULT 'Pending',
  `payment_status` enum('Pending','Paid','Refunded') DEFAULT 'Pending',
  `payment_type` enum('Manual','Digital') DEFAULT 'Manual',
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `pickup_location` varchar(255) DEFAULT NULL,
  `dropoff_location` varchar(255) DEFAULT NULL,
  `special_requests` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`booking_id`, `customer_id`, `vehicle_id`, `driver_id`, `owner_id`, `booking_date`, `start_date`, `end_date`, `total_days`, `price_per_day`, `total_price`, `driver_fee`, `booking_status`, `payment_status`, `payment_type`, `payment_method`, `transaction_id`, `pickup_location`, `dropoff_location`, `special_requests`, `created_at`) VALUES
(1, 1, 1, 1, 1, '2026-07-17 06:52:30', '2026-07-20', '2026-07-22', 2, 4000.00, 8000.00, 1000.00, 'Confirmed', 'Paid', 'Digital', 'bKash', NULL, 'Uttara', NULL, NULL, '2026-07-17 06:52:30'),
(2, 2, 3, NULL, 2, '2026-07-17 06:52:30', '2026-07-25', '2026-07-27', 2, 5000.00, 10000.00, 0.00, 'Pending', 'Pending', 'Manual', NULL, NULL, 'Bashundhara', NULL, NULL, '2026-07-17 06:52:30'),
(3, 1, 5, 2, 1, '2026-07-17 06:52:30', '2026-08-01', '2026-08-03', 2, 7500.00, 15000.00, 1200.00, 'Completed', 'Paid', 'Digital', 'Nagad', NULL, 'Banani', NULL, NULL, '2026-07-17 06:52:30');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_1` varchar(20) NOT NULL,
  `phone_2` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `nid_number` varchar(50) DEFAULT NULL,
  `nid_photo` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `full_name`, `username`, `email`, `phone_1`, `phone_2`, `password`, `nid_number`, `nid_photo`, `address`, `profile_image`, `status`, `created_at`) VALUES
(1, 'Mahmudul Hasan', 'mahmud_customer', 'mahmud@gmail.com', '01710000001', '01810000001', 'mahmud123', '1234567891', 'nid_mahmud.jpg', 'Mirpur 10', NULL, 'Active', '2026-07-16 14:11:12'),
(2, 'Taslima Akter', 'taslima_customer', 'taslima@gmail.com', '01710000002', NULL, 'taslima123', '1234567892', 'nid_taslima.jpg', 'Uttara Sector 7', NULL, 'Active', '2026-07-16 14:11:12'),
(3, 'Shakib Al Hasan', 'shakib_customer', 'shakib@gmail.com', '01710000003', '01910000003', 'shakib123', '1234567893', 'nid_shakib.jpg', 'Gulshan 2', NULL, 'Active', '2026-07-16 14:11:12'),
(4, 'Nadia Sultana', 'nadia_customer', 'nadia@gmail.com', '01710000004', '01810000004', 'nadia123', '1234567894', 'nid_nadia.jpg', 'Banani 11', NULL, 'Active', '2026-07-16 14:11:12'),
(5, 'Rakib Hossain', 'rakib_customer', 'rakib@gmail.com', '01710000005', NULL, 'rakib123', '1234567895', 'nid_rakib.jpg', 'Dhanmondi 32', NULL, 'Active', '2026-07-16 14:11:12');

-- --------------------------------------------------------

--
-- Table structure for table `driver`
--

CREATE TABLE `driver` (
  `driver_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `nid_number` varchar(50) DEFAULT NULL,
  `nid_photo` varchar(255) DEFAULT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `license_photo` varchar(255) DEFAULT NULL,
  `experience_years` int(11) DEFAULT 0,
  `availability` enum('Available','Unavailable') DEFAULT 'Available',
  `rating` decimal(3,2) DEFAULT 0.00,
  `rating_count` int(11) DEFAULT 0,
  `address` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `driver`
--

INSERT INTO `driver` (`driver_id`, `full_name`, `username`, `email`, `phone`, `password`, `nid_number`, `nid_photo`, `license_number`, `license_photo`, `experience_years`, `availability`, `rating`, `rating_count`, `address`, `profile_image`, `status`, `created_at`) VALUES
(1, 'Rahim Uddin', 'rahim_driver', 'rahim.driver@gmail.com', '01712345678', 'rahim123', '3333333333', NULL, 'DL789012', NULL, 7, 'Available', 4.80, 0, 'Mirpur', NULL, 'Pending', '2026-07-16 14:53:44'),
(2, 'Sonia Akhter', 'sonia_driver', 'sonia.driver@gmail.com', '01812345678', 'sonia123', '4444444444', NULL, 'DL345678', NULL, 4, 'Unavailable', 4.20, 0, 'Uttara', NULL, 'Pending', '2026-07-16 14:53:44'),
(3, 'Kamal Hossain', 'kamal_driver', 'kamal.driver@gmail.com', '01912345678', 'kamal123', '5555555555', NULL, 'DL901234', NULL, 2, 'Available', 3.50, 0, 'Gulshan', NULL, 'Pending', '2026-07-16 14:53:44');

-- --------------------------------------------------------

--
-- Table structure for table `hub_inspection`
--

CREATE TABLE `hub_inspection` (
  `inspection_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `vehicle_id` int(11) NOT NULL,
  `inspection_type` enum('Pre-Rental','Post-Rental') NOT NULL,
  `fuel_level` varchar(20) DEFAULT NULL,
  `odometer_reading` int(11) DEFAULT NULL,
  `damage_photos` varchar(255) DEFAULT NULL,
  `damage_description` text DEFAULT NULL,
  `inspector_name` varchar(100) DEFAULT NULL,
  `hub_location` enum('Dhaka','Chattogram','Sylhet') DEFAULT 'Dhaka',
  `inspection_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Passed','Failed','Pending') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hub_inspection`
--

INSERT INTO `hub_inspection` (`inspection_id`, `booking_id`, `vehicle_id`, `inspection_type`, `fuel_level`, `odometer_reading`, `damage_photos`, `damage_description`, `inspector_name`, `hub_location`, `inspection_date`, `status`) VALUES
(1, 1, 1, 'Pre-Rental', 'Full', 15000, 'damage_1.jpg', 'No damage', 'Mr. Hasan', 'Dhaka', '2026-07-17 06:57:19', 'Passed'),
(2, 2, 3, 'Pre-Rental', 'Half', 22000, NULL, 'Minor scratch on left door', 'Mr. Karim', 'Dhaka', '2026-07-17 06:57:19', 'Pending'),
(3, 3, 5, 'Pre-Rental', 'Full', 18000, 'damage_2.jpg', 'Rear bumper scratch', 'Ms. Rina', 'Chattogram', '2026-07-17 06:57:19', 'Passed'),
(4, NULL, 7, 'Post-Rental', 'Empty', 25000, 'damage_3.jpg', 'Front windshield crack', 'Mr. Nayeem', 'Chattogram', '2026-07-17 06:57:19', 'Failed'),
(5, NULL, 9, 'Pre-Rental', 'Half', 12000, NULL, 'No damage found', 'Mr. Shafiq', 'Sylhet', '2026-07-17 06:57:19', 'Passed'),
(6, 4, 11, 'Post-Rental', 'Full', 30000, 'damage_4.jpg', 'Right side mirror broken', 'Ms. Sumi', 'Sylhet', '2026-07-17 06:57:19', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle`
--

CREATE TABLE `vehicle` (
  `vehicle_id` int(11) NOT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `vehicle_name` varchar(100) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `year` year(4) NOT NULL,
  `vehicle_type` varchar(30) NOT NULL,
  `fuel_type` varchar(30) NOT NULL,
  `transmission` varchar(30) NOT NULL,
  `seat_capacity` int(11) NOT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `location` varchar(100) NOT NULL,
  `availability` enum('Available','Booked','Maintenance') DEFAULT 'Available',
  `approval_status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `rating_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle`
--

INSERT INTO `vehicle` (`vehicle_id`, `owner_id`, `vehicle_name`, `brand`, `model`, `year`, `vehicle_type`, `fuel_type`, `transmission`, `seat_capacity`, `price_per_day`, `location`, `availability`, `approval_status`, `image`, `description`, `created_at`) VALUES
(1, 1, 'Toyota Premio', 'Toyota', 'Premio', '2020', 'Sedan', 'Petrol', 'Automatic', 5, 4000.00, 'Uttara', 'Available', 'Approved', 'premio.jpg', 'Comfortable family sedan', '2026-07-12 11:01:57'),
(2, 1, 'Honda Civic', 'Honda', 'Civic', '2021', 'Sedan', 'Petrol', 'Automatic', 5, 4000.00, 'Gulshan', 'Booked', 'Approved', 'civic.jpg', 'Luxury sedan with modern features', '2026-07-12 11:01:57'),
(3, 1, 'Toyota Noah', 'Toyota', 'Noah', '2019', 'Microbus', 'Petrol', 'Automatic', 8, 5000.00, 'Bashundhara', 'Available', 'Approved', 'noah.jpg', 'Spacious family microbus', '2026-07-12 11:01:57'),
(4, 1, 'BMW X5', 'BMW', 'X5', '2022', 'SUV', 'Diesel', 'Automatic', 5, 8000.00, 'Dhanmondi', 'Maintenance', 'Approved', 'x5.jpg', 'Premium luxury SUV', '2026-07-12 11:01:57'),
(5, 1, 'Audi A6', 'Audi', 'A6', '2021', 'Sedan', 'Petrol', 'Automatic', 5, 7500.00, 'Banani', 'Available', 'Approved', 'a6.jpg', 'Executive luxury sedan', '2026-07-12 11:01:57'),
(6, 2, 'Toyota Hiace', 'Toyota', 'Hiace', '2020', 'Van', 'Diesel', 'Manual', 12, 6500.00, 'Mirpur', 'Booked', 'Approved', 'hiace.jpg', 'Large passenger van', '2026-07-12 11:01:57'),
(7, 2, 'Honda Vezel', 'Honda', 'Vezel', '2022', 'SUV', 'Hybrid', 'Automatic', 5, 6000.00, 'Uttara', 'Available', 'Approved', 'vezel.jpg', 'Hybrid crossover SUV', '2026-07-12 11:01:57'),
(8, 2, 'Nissan X-Trail', 'Nissan', 'X-Trail', '2021', 'SUV', 'Petrol', 'Automatic', 5, 6200.00, 'Gulshan', 'Available', 'Approved', 'xtrail.jpg', 'Comfortable SUV', '2026-07-12 11:01:57'),
(9, 2, 'Toyota Axio', 'Toyota', 'Axio', '2019', 'Sedan', 'Petrol', 'Automatic', 5, 3200.00, 'Mohakhali', 'Available', 'Approved', 'axio.jpg', 'Affordable sedan', '2026-07-12 11:01:57'),
(10, 2, 'Mitsubishi Pajero', 'Mitsubishi', 'Pajero', '2020', 'SUV', 'Diesel', 'Automatic', 7, 8500.00, 'Banani', 'Maintenance', 'Approved', 'pajero.jpg', 'Powerful off-road SUV', '2026-07-12 11:01:57'),
(11, 3, 'Suzuki WagonR', 'Suzuki', 'WagonR', '2018', 'Hatchback', 'Petrol', 'Manual', 5, 2200.00, 'Farmgate', 'Available', 'Approved', 'wagonr.jpg', 'Compact city car', '2026-07-12 11:01:57'),
(12, 3, 'Hyundai Tucson', 'Hyundai', 'Tucson', '2023', 'SUV', 'Petrol', 'Automatic', 5, 7000.00, 'Uttara', 'Available', 'Approved', 'tucson.jpg', 'Modern SUV', '2026-07-12 11:01:57'),
(13, 3, 'Kia Sportage', 'Kia', 'Sportage', '2022', 'SUV', 'Petrol', 'Automatic', 5, 6800.00, 'Bashundhara', 'Booked', 'Approved', 'sportage.jpg', 'Stylish SUV', '2026-07-12 11:01:57'),
(14, 4, 'Toyota Corolla', 'Toyota', 'Corolla', '2021', 'Sedan', 'Petrol', 'Automatic', 5, 3600.00, 'Dhanmondi', 'Available', 'Approved', 'corolla.jpg', 'Reliable sedan', '2026-07-12 11:01:57'),
(15, 4, 'Mazda CX-5', 'Mazda', 'CX-5', '2023', 'SUV', 'Petrol', 'Automatic', 5, 7200.00, 'Gulshan', 'Available', 'Approved', 'cx5.jpg', 'Premium crossover SUV', '2026-07-12 11:01:57'),
(16, 4, 'Civic', 'Honda', 'Civic 2023', '2023', 'Sedan', 'Petrol', 'Automatic', 5, 3500.00, 'Uttara', 'Available', 'Approved', 'civic.jpg', 'Comfortable family car', '2026-07-12 13:49:52');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_owner`
--

CREATE TABLE `vehicle_owner` (
  `owner_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `nid_number` varchar(50) DEFAULT NULL,
  `nid_photo` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `total_rentals` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_owner`
--

INSERT INTO `vehicle_owner` (`owner_id`, `full_name`, `username`, `email`, `phone`, `password`, `nid_number`, `nid_photo`, `address`, `profile_image`, `status`, `total_rentals`, `created_at`) VALUES
(1, 'Masud Rana', 'masud_owner', 'masud@gmail.com', '01744444444', 'masud123', '1234567890', NULL, 'Banani', NULL, 'Pending', 0, '2026-07-16 14:31:33'),
(2, 'Ferdousi Begum', 'ferdousi_owner', 'ferdousi@gmail.com', '01755555555', 'ferdousi123', '0987654321', NULL, 'Dhanmondi', NULL, 'Pending', 0, '2026-07-16 14:31:33'),
(3, 'Kamal Uddin', 'kamal_owner', 'kamal.owner@gmail.com', '01766666666', 'kamal123', '1122334455', NULL, 'Uttara', NULL, 'Pending', 0, '2026-07-16 14:31:33'),
(4, 'Rokeya Sultana', 'rokeya_owner', 'rokeya@gmail.com', '01777777777', 'rokeya123', '5544332211', NULL, 'Gulshan', NULL, 'Pending', 0, '2026-07-16 14:31:33'),
(5, 'Jahangir Alam', 'jahangir_owner', 'jahangir@gmail.com', '01788888888', 'jahangir123', '6677889900', NULL, 'Mirpur', NULL, 'Pending', 0, '2026-07-16 14:31:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_type` enum('customer','driver','owner') NOT NULL,
  `user_id` int(11) NOT NULL,
  `target_type` enum('vehicle','driver') NOT NULL,
  `target_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `compare_vehicles`
--

CREATE TABLE `compare_vehicles` (
  `compare_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `driver_id` (`driver_id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `nid_number` (`nid_number`);

--
-- Indexes for table `driver`
--
ALTER TABLE `driver`
  ADD PRIMARY KEY (`driver_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `license_number` (`license_number`);

--
-- Indexes for table `hub_inspection`
--
ALTER TABLE `hub_inspection`
  ADD PRIMARY KEY (`inspection_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indexes for table `vehicle`
--
ALTER TABLE `vehicle`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `vehicle_owner`
--
ALTER TABLE `vehicle_owner`
  ADD PRIMARY KEY (`owner_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `target_id` (`target_id`);

--
-- Indexes for table `compare_vehicles`
--
ALTER TABLE `compare_vehicles`
  ADD PRIMARY KEY (`compare_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `driver`
--
ALTER TABLE `driver`
  MODIFY `driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hub_inspection`
--
ALTER TABLE `hub_inspection`
  MODIFY `inspection_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vehicle`
--
ALTER TABLE `vehicle`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `vehicle_owner`
--
ALTER TABLE `vehicle_owner`
  MODIFY `owner_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `compare_vehicles`
--
ALTER TABLE `compare_vehicles`
  MODIFY `compare_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `vehicle`
--
ALTER TABLE `vehicle`
  ADD CONSTRAINT `vehicle_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `vehicle_owner` (`owner_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
