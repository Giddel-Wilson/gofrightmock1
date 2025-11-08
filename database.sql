-- Database Schema for Cargo Tracking System
-- Create Database
CREATE DATABASE IF NOT EXISTS cargo;
USE cargo;

-- Admin Table
CREATE TABLE IF NOT EXISTS `admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `creation_date` date DEFAULT NULL,
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin (username: admin, password: admin)
INSERT INTO `admin` (`username`, `password`, `email`, `creation_date`) VALUES
('admin', '21232f297a57a5a743894a0e4a801fc3', 'admin@gofreight.com', CURDATE());

-- Customer Table
CREATE TABLE IF NOT EXISTS `customer` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `creation_date` date DEFAULT NULL,
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Driver Table
CREATE TABLE IF NOT EXISTS `driver` (
  `driver_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `vehicle_number` varchar(50) DEFAULT NULL,
  `creation_date` date DEFAULT NULL,
  PRIMARY KEY (`driver_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cargo Table
CREATE TABLE IF NOT EXISTS `cargo` (
  `cargo_id` int(11) NOT NULL AUTO_INCREMENT,
  `tracking_id` varchar(100) NOT NULL,
  `customer` varchar(100) DEFAULT NULL,
  `driver` varchar(100) DEFAULT NULL,
  `cargo_type` varchar(100) DEFAULT NULL,
  `cargo_weight` decimal(10,2) DEFAULT NULL,
  `pickup_location` varchar(255) DEFAULT NULL,
  `delivery_location` varchar(255) DEFAULT NULL,
  `distance` decimal(10,2) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `cleared` tinyint(1) DEFAULT 0,
  `payment_status` varchar(50) DEFAULT 'Unpaid',
  `pickup_date` date DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `creation_date` date DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cargo_id`),
  UNIQUE KEY `tracking_id` (`tracking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Mpesa Payment Table
CREATE TABLE IF NOT EXISTS `mpesa` (
  `mpesa_id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_code` varchar(100) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `cargo_id` int(11) DEFAULT NULL,
  `customer` varchar(100) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `creation_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mpesa_id`),
  UNIQUE KEY `transaction_code` (`transaction_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Location Updates Table (for tracking)
CREATE TABLE IF NOT EXISTS `location_updates` (
  `update_id` int(11) NOT NULL AUTO_INCREMENT,
  `cargo_id` int(11) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `location_name` varchar(255) DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`update_id`),
  KEY `cargo_id` (`cargo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data for testing
-- Insert sample customer
INSERT INTO `customer` (`username`, `password`, `email`, `creation_date`) VALUES
('testcustomer', '5f4dcc3b5aa765d61d8327deb882cf99', 'customer@test.com', CURDATE());

-- Insert sample driver
INSERT INTO `driver` (`username`, `password`, `email`, `phone`, `license_number`, `vehicle_number`, `creation_date`) VALUES
('testdriver', '5f4dcc3b5aa765d61d8327deb882cf99', 'driver@test.com', '0700000000', 'DL12345', 'KAA123B', CURDATE());
