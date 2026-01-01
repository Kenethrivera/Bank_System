-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 01, 2026 at 09:57 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bank_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

DROP TABLE IF EXISTS `loans`;
CREATE TABLE IF NOT EXISTS `loans` (
  `loan_id` int NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `loan_type` varchar(100) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `Status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `application_date` date DEFAULT NULL,
  PRIMARY KEY (`loan_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`loan_id`, `customer_name`, `loan_type`, `amount`, `Status`, `application_date`) VALUES
(3, 'Pedro Reyes', 'Auto Loan', 25000.00, 'Pending', '2025-02-28'),
(2, 'Maria Santos', 'Housing Loan', 300000.00, 'Pending', '2025-03-05'),
(1, 'Juan Dela Cruz', 'Personal Loan', 50000.00, 'Pending', '2025-03-12'),
(4, 'Kene', 'loan', 2000.00, 'Approved', '2026-01-01'),
(6, 'james', 'loan', 2000.00, 'Approved', '2026-01-01'),
(7, 'escueta', 'loan', 2000.00, 'Rejected', '2026-01-01'),
(8, 'rivera', 'loan', 2000.00, 'Pending', '2026-01-01'),
(9, 'Pogi', 'Personal', 20000.00, 'Pending', '2025-12-17'),
(10, 'Pogi', 'Personal', 20000.00, 'Rejected', '2025-12-17'),
(11, 'Panget', 'Business', 21.00, 'Rejected', '2025-12-17'),
(12, 'dqkjdjqk', 'Auto', 200001.00, 'Approved', '2025-12-17'),
(13, 'Johan Guigayoma', 'Auto', 500000.00, 'Pending', '2025-12-31');

-- --------------------------------------------------------

--
-- Table structure for table `savings_accounts`
--

DROP TABLE IF EXISTS `savings_accounts`;
CREATE TABLE IF NOT EXISTS `savings_accounts` (
  `savings_id` varchar(10) NOT NULL,
  `ID` int NOT NULL,
  `status` enum('Active','Pending','Frozen','Closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'Active',
  `savings_type` varchar(100) DEFAULT NULL,
  `interest_rate` decimal(5,2) DEFAULT NULL,
  `balance` decimal(15,2) DEFAULT '0.00',
  `last_interest_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`savings_id`),
  KEY `fk_savings_user` (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `savings_accounts`
--

INSERT INTO `savings_accounts` (`savings_id`, `ID`, `status`, `savings_type`, `interest_rate`, `balance`, `last_interest_date`, `created_at`) VALUES
('SAV-001', 1, 'Active', 'Special', 0.10, 100041.10, '2025-12-31', '2025-12-30 06:00:48'),
('SAV-002', 2, 'Pending', 'Fixed', 0.08, 5000.00, '2025-12-29', '2025-12-05 02:00:00'),
('SAV-003', 3, 'Closed', 'Regular', 0.05, 20000.00, '2025-12-28', '2025-12-10 03:00:00'),
('SAV-004', 4, 'Frozen', 'Regular', 0.05, 15000.00, '2025-12-27', '2025-11-15 04:00:00'),
('SAV-005', 5, 'Active', 'Special', 0.10, 200054.79, '2025-12-31', '2025-12-30 08:24:53'),
('SAV-006', 1, 'Active', 'Special', 0.10, 1000273.97, '2025-12-31', '2025-12-30 08:25:14'),
('SAV-007', 5, 'Pending', 'Fixed', 0.08, 200000.00, NULL, '2025-12-31 01:42:50'),
('SAV-008', 9, 'Active', 'Fixed', 0.08, 1111.00, NULL, '2026-01-01 21:16:35'),
('SAV-009', 3, 'Active', 'Special', 0.10, 1000000.00, NULL, '2026-01-01 21:16:49');

-- --------------------------------------------------------

--
-- Table structure for table `savings_transactions`
--

DROP TABLE IF EXISTS `savings_transactions`;
CREATE TABLE IF NOT EXISTS `savings_transactions` (
  `transaction_id` int NOT NULL AUTO_INCREMENT,
  `savings_id` varchar(50) NOT NULL,
  `transaction_type` enum('Deposit','Withdraw','Interest') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `balance_after` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`transaction_id`),
  KEY `savings_id` (`savings_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `savings_transactions`
--

INSERT INTO `savings_transactions` (`transaction_id`, `savings_id`, `transaction_type`, `amount`, `balance_after`, `created_at`) VALUES
(13, 'SAV-004', 'Interest', 2.05, 15002.05, '2025-12-27 04:00:00'),
(12, 'SAV-004', 'Withdraw', 10000.00, 15000.00, '2025-12-01 02:00:00'),
(11, 'SAV-004', 'Deposit', 10000.00, 25000.00, '2025-11-20 01:00:00'),
(10, 'SAV-003', 'Interest', 2.74, 20002.74, '2025-12-28 06:00:00'),
(9, 'SAV-003', 'Withdraw', 5000.00, 20000.00, '2025-12-15 02:00:00'),
(7, 'SAV-002', 'Deposit', 500.00, 7500.00, '2025-12-08 03:00:00'),
(8, 'SAV-003', 'Deposit', 5000.00, 25000.00, '2025-12-11 01:00:00'),
(6, 'SAV-002', 'Withdraw', 1000.00, 7000.00, '2025-12-07 02:00:00'),
(5, 'SAV-002', 'Deposit', 3000.00, 8000.00, '2025-12-06 01:00:00'),
(4, 'SAV-001', 'Interest', 13.70, 100013.70, '2025-12-30 06:02:00'),
(3, 'SAV-001', 'Withdraw', 7000.00, 100000.00, '2025-12-10 03:00:00'),
(2, 'SAV-001', 'Deposit', 2000.00, 107000.00, '2025-12-05 02:00:00'),
(1, 'SAV-001', 'Deposit', 5000.00, 105000.00, '2025-12-02 01:30:00'),
(14, 'SAV-001', 'Interest', 27.40, 100041.10, '2025-12-30 16:00:00'),
(15, 'SAV-005', 'Interest', 54.79, 200054.79, '2025-12-30 16:00:00'),
(16, 'SAV-006', 'Interest', 273.97, 1000273.97, '2025-12-30 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_accounts`
--

DROP TABLE IF EXISTS `user_accounts`;
CREATE TABLE IF NOT EXISTS `user_accounts` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(150) NOT NULL,
  `MiddleName` varchar(150) DEFAULT NULL,
  `LastName` varchar(150) NOT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `Phone` varchar(15) DEFAULT NULL,
  `Address` varchar(150) NOT NULL,
  `Birthdate` date NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Img` varchar(150) NOT NULL,
  `Role` varchar(10) DEFAULT 'User',
  `Status` varchar(10) DEFAULT 'Pending',
  `Balance` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `Phone` (`Phone`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_accounts`
--

INSERT INTO `user_accounts` (`ID`, `FirstName`, `MiddleName`, `LastName`, `Email`, `Phone`, `Address`, `Birthdate`, `Password`, `Img`, `Role`, `Status`, `Balance`) VALUES
(8, 'Keneth James', 'Esceuta', 'Rivera', 'riverakenethjames@gmail.com', '09940425690', 'Binan Laguna', '2005-11-05', 'ken123', 'profile/img_695487642ea7c.jpg', 'User', 'Pending', 0.00),
(2, 'Abdul', 'Pacalundo', 'Disomimba', 'malik@test.com', '0912345678', 'Quezon City, Philippines', '1998-05-12', 'malik12345', 'profile/img_695487642ea7c.jpg', 'User', 'Rejected', 0.00),
(3, 'Juan', 'Santos', 'Dela Cruz', 'juan.delacruz@test.com', '09171234567', 'Manila, Philippines', '1995-03-18', 'juan12345', 'profile/img_695487642ea7c.jpg', 'User', 'Approved', 0.00),
(4, 'Maria', 'Reyes', 'Gonzales', 'maria.gonzales@test.com', '09281234567', 'Cebu City, Philippines', '1997-11-05', 'maria12345', 'profile/img_695487642ea7c.jpg', 'User', 'Approved', 0.00),
(5, 'Joshua', 'Lim', 'Tan', 'joshua.tan@test.com', '09061234567', 'Davao City, Philippines', '2000-01-22', 'joshua12345', 'profile/img_695487642ea7c.jpg', 'User', 'Approved', 0.00),
(6, 'Angela', 'Cruz', 'Navarro', 'angela.navarro@test.com', '09391234567', 'Baguio City, Philippines', '1996-07-30', 'angela12345', 'profile/img_695487642ea7c.jpg', 'User', 'Pending', 0.00),
(7, 'Mark', 'Villanueva', 'Flores', 'mark.flores@test.com', '09151234567', 'Laguna, Philippines', '1994-09-14', 'mark12345', 'profile/img_695487642ea7c.jpg', 'User', 'Pending', 0.00),
(9, 'adadj', 'kdjq', 'ekqjqk', '123@panget.com', '123144141', 'laguna', '2005-11-05', '123456', 'profile/img_695487d5e4536.jpg', 'User', 'Pending', 0.00);

DELIMITER $$
--
-- Events
--
DROP EVENT IF EXISTS `daily_interest_accrual`$$
CREATE DEFINER=`root`@`localhost` EVENT `daily_interest_accrual` ON SCHEDULE EVERY 1 DAY STARTS '2025-12-31 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    INSERT INTO savings_transactions (
        savings_id,
        transaction_type,
        amount,
        balance_after,
        created_at
    )
    SELECT
        savings_id,
        'Interest',
        ROUND(balance * (interest_rate / 365), 2) AS interest_amount,
        ROUND(balance + (balance * (interest_rate / 365)), 2) AS new_balance,
        NOW()
    FROM savings_accounts
    WHERE status = 'Active'
      AND (last_interest_date IS NULL OR last_interest_date < CURRENT_DATE);

    UPDATE savings_accounts
    SET
        balance = ROUND(balance + (balance * (interest_rate / 365)), 2),
        last_interest_date = CURRENT_DATE
    WHERE status = 'Active'
      AND (last_interest_date IS NULL OR last_interest_date < CURRENT_DATE);
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
