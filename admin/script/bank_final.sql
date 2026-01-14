-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 14, 2026 at 04:06 PM
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
  `loan_id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `loan_type` varchar(100) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `Status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `application_date` date DEFAULT NULL,
  PRIMARY KEY (`loan_id`),
  KEY `fk_loans_customer` (`customer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`loan_id`, `customer_id`, `loan_type`, `reason`, `amount`, `Status`, `application_date`) VALUES
(21, 8, 'Home', 'dqkwj', 1000.00, 'Rejected', '2026-01-14'),
(13, 8, 'Personal', 'jadka', 500.00, 'Approved', '2026-01-14'),
(23, 3, 'Personal', 'QDJKQWJ', 100.00, 'Approved', '2026-01-14');

-- --------------------------------------------------------

--
-- Table structure for table `loan_payments`
--

DROP TABLE IF EXISTS `loan_payments`;
CREATE TABLE IF NOT EXISTS `loan_payments` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `loan_id` int NOT NULL,
  `due_date` date NOT NULL,
  `payment_amount` decimal(12,2) DEFAULT '0.00',
  `status` enum('Pending','Paid') DEFAULT 'Pending',
  PRIMARY KEY (`payment_id`),
  KEY `loan_id` (`loan_id`)
) ENGINE=MyISAM AUTO_INCREMENT=93 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `loan_payments`
--

INSERT INTO `loan_payments` (`payment_id`, `loan_id`, `due_date`, `payment_amount`, `status`) VALUES
(48, 12, '2026-09-14', 1666.67, 'Pending'),
(47, 12, '2026-08-14', 1666.67, 'Pending'),
(46, 12, '2026-07-14', 1666.67, 'Pending'),
(45, 12, '2026-06-14', 1666.67, 'Pending'),
(44, 12, '2026-05-14', 1666.67, 'Pending'),
(43, 12, '2026-04-14', 1666.67, 'Pending'),
(42, 12, '2026-03-14', 1666.67, 'Pending'),
(41, 12, '2026-02-14', 1666.67, 'Pending'),
(40, 10, '2026-11-14', 2000.00, 'Pending'),
(39, 10, '2026-10-14', 2000.00, 'Pending'),
(38, 10, '2026-09-14', 2000.00, 'Pending'),
(37, 10, '2026-08-14', 2000.00, 'Paid'),
(36, 10, '2026-07-14', 2000.00, 'Paid'),
(35, 10, '2026-06-14', 2000.00, 'Paid'),
(34, 10, '2026-05-14', 2000.00, 'Paid'),
(33, 10, '2026-04-14', 2000.00, 'Paid'),
(32, 10, '2026-03-14', 2000.00, 'Paid'),
(31, 10, '2026-02-14', 2000.00, 'Paid'),
(30, 9, '2026-07-14', 166.65, 'Paid'),
(29, 9, '2026-06-14', 166.67, 'Paid'),
(28, 9, '2026-05-14', 166.67, 'Paid'),
(27, 9, '2026-04-14', 166.67, 'Paid'),
(26, 9, '2026-03-14', 166.67, 'Paid'),
(25, 9, '2026-02-14', 166.67, 'Paid'),
(49, 12, '2026-10-14', 1666.67, 'Pending'),
(50, 12, '2026-11-14', 1666.67, 'Pending'),
(51, 12, '2026-12-14', 1666.67, 'Pending'),
(52, 12, '2027-01-14', 1666.63, 'Pending'),
(53, 13, '2026-02-14', 83.33, 'Paid'),
(54, 13, '2026-03-14', 83.33, 'Paid'),
(55, 13, '2026-04-14', 83.33, 'Paid'),
(56, 13, '2026-05-14', 83.33, 'Paid'),
(57, 13, '2026-06-14', 83.33, 'Paid'),
(58, 13, '2026-07-14', 83.35, 'Pending'),
(59, 11, '2026-02-14', 1900.00, 'Pending'),
(60, 11, '2026-03-14', 1900.00, 'Pending'),
(61, 11, '2026-04-14', 1900.00, 'Pending'),
(62, 11, '2026-05-14', 1900.00, 'Pending'),
(63, 11, '2026-06-14', 1900.00, 'Pending'),
(64, 11, '2026-07-14', 1900.00, 'Pending'),
(65, 11, '2026-08-14', 1900.00, 'Pending'),
(66, 11, '2026-09-14', 1900.00, 'Pending'),
(67, 11, '2026-10-14', 1900.00, 'Pending'),
(68, 11, '2026-11-14', 1900.00, 'Pending'),
(69, 14, '2026-02-14', 3333.33, 'Pending'),
(70, 14, '2026-03-14', 3333.33, 'Pending'),
(71, 14, '2026-04-14', 3333.33, 'Pending'),
(72, 14, '2026-05-14', 3333.33, 'Pending'),
(73, 14, '2026-06-14', 3333.33, 'Pending'),
(74, 14, '2026-07-14', 3333.35, 'Pending'),
(75, 17, '2026-02-14', 33.33, 'Pending'),
(76, 17, '2026-03-14', 33.33, 'Pending'),
(77, 17, '2026-04-14', 33.33, 'Pending'),
(78, 17, '2026-05-14', 33.33, 'Pending'),
(79, 17, '2026-06-14', 33.33, 'Pending'),
(80, 17, '2026-07-14', 33.35, 'Pending'),
(81, 18, '2026-02-14', 333.33, 'Pending'),
(82, 18, '2026-03-14', 333.33, 'Pending'),
(83, 18, '2026-04-14', 333.33, 'Pending'),
(84, 18, '2026-05-14', 333.33, 'Pending'),
(85, 18, '2026-06-14', 333.33, 'Pending'),
(86, 18, '2026-07-14', 333.35, 'Pending'),
(87, 23, '2026-02-14', 16.67, 'Paid'),
(88, 23, '2026-03-14', 16.67, 'Paid'),
(89, 23, '2026-04-14', 16.67, 'Paid'),
(90, 23, '2026-05-14', 16.67, 'Paid'),
(91, 23, '2026-06-14', 16.67, 'Paid'),
(92, 23, '2026-07-14', 16.65, 'Paid');

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
('SAV0008c45', 8, 'Active', 'Regular', 2.50, 506.87, '2026-01-15', '2026-01-14 00:25:34'),
('SAV00081c6', 8, 'Active', 'Fixed', 3.50, 3466.54, '2026-01-15', '2026-01-14 00:25:52'),
('SAV0003147', 3, 'Active', 'Fixed', 3.50, 100.96, '2026-01-15', '2026-01-14 15:56:40'),
('SAV0008bbf', 8, 'Pending', 'Fixed', 3.50, 1000.00, NULL, '2026-01-14 02:53:59');

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
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `savings_transactions`
--

INSERT INTO `savings_transactions` (`transaction_id`, `savings_id`, `transaction_type`, `amount`, `balance_after`, `created_at`) VALUES
(1, 'SAV0008c45', 'Deposit', 1000.00, 1000.00, '2026-01-14 00:25:34'),
(2, 'SAV00081c6', 'Deposit', 1000.00, 1000.00, '2026-01-14 00:25:52'),
(3, 'SAV00081c6', 'Deposit', 2000.00, 3000.00, '2026-01-14 00:27:52'),
(4, 'SAV00085e7', 'Deposit', 1000.00, 1000.00, '2026-01-14 01:40:14'),
(5, 'SAV0008d17', 'Deposit', 1000.00, 1000.00, '2026-01-14 01:42:02'),
(6, 'SAV0008641', 'Deposit', 100.00, 100.00, '2026-01-14 01:43:30'),
(7, 'SAV0008fbc', 'Deposit', 900.00, 900.00, '2026-01-14 01:43:50'),
(8, 'SAV0008334', 'Deposit', 100.00, 100.00, '2026-01-14 01:48:53'),
(9, 'SAV0008c45', 'Deposit', 900.00, 1900.00, '2026-01-14 01:51:15'),
(10, 'SAV0008c45', 'Withdraw', 900.00, 1000.00, '2026-01-14 01:51:22'),
(11, 'SAV000892c', 'Deposit', 900.00, 900.00, '2026-01-14 01:54:20'),
(12, 'SAV00087ed', 'Deposit', 49999.00, 49999.00, '2026-01-14 01:56:41'),
(13, 'SAV0008295', 'Deposit', 500.00, 500.00, '2026-01-14 02:06:27'),
(14, 'SAV0008867', 'Deposit', 1000.00, 1000.00, '2026-01-14 02:06:43'),
(15, 'SAV0008004', 'Deposit', 100.00, 100.00, '2026-01-14 02:12:26'),
(16, 'SAV00081c6', 'Deposit', 401.00, 3401.00, '2026-01-14 02:13:21'),
(17, 'SAV0008d98', 'Deposit', 1000.00, 1000.00, '2026-01-14 02:28:25'),
(18, 'SAV00089f8', 'Deposit', 1000.00, 1000.00, '2026-01-14 02:35:23'),
(19, 'SAV0008231', 'Deposit', 1000.00, 1000.00, '2026-01-14 02:43:15'),
(20, 'SAV0008bbf', 'Deposit', 1000.00, 1000.00, '2026-01-14 02:53:59'),
(21, 'SAV0008c45', 'Withdraw', 500.00, 500.00, '2026-01-14 03:17:17'),
(22, 'SAV0008c45', 'Interest', 3.42, 503.42, '2026-01-14 12:31:40'),
(23, 'SAV00081c6', 'Interest', 32.61, 3433.61, '2026-01-14 12:31:40'),
(24, 'SAV0003147', 'Deposit', 100.00, 100.00, '2026-01-14 15:56:40'),
(25, 'SAV0008c45', 'Interest', 3.45, 506.87, '2026-01-14 16:00:01'),
(26, 'SAV00081c6', 'Interest', 32.93, 3466.54, '2026-01-14 16:00:01'),
(27, 'SAV0003147', 'Interest', 0.96, 100.96, '2026-01-14 16:00:01');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
  `transaction_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `transaction_type` enum('Cash In','Cash Out','Send Money','Received Money') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `balance_after` decimal(15,2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `icon` varchar(50) DEFAULT 'bi-cash',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`transaction_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `user_id`, `transaction_type`, `amount`, `balance_after`, `description`, `icon`, `created_at`) VALUES
(13, 8, 'Cash Out', 100.00, 8300.00, 'ATM Withdrawal', 'bi-arrow-down-circle', '2026-01-13 05:15:11'),
(11, 8, 'Cash Out', 500.00, 8500.00, 'ATM Withdrawal', 'bi-arrow-down-circle', '2026-01-13 05:00:27'),
(12, 8, 'Cash Out', 100.00, 8400.00, 'Cash Out via Counter', 'bi-wallet2', '2026-01-13 05:15:02'),
(10, 8, 'Cash Out', 1000.00, 9000.00, 'Cash Out via Counter', 'bi-wallet2', '2026-01-13 05:00:07'),
(14, 8, 'Cash Out', 100.00, 8200.00, 'Cash Out via Sari-Sari Store', 'bi-shop', '2026-01-13 05:15:23'),
(18, 3, 'Received Money', 100.00, 0.00, 'Received from Keneth James', 'bi-receive', '2026-01-13 05:43:14'),
(17, 8, 'Send Money', 100.00, 0.00, 'Sent to Juan', 'bi-send', '2026-01-13 05:43:14'),
(19, 8, 'Send Money', 100.00, 0.00, 'Sent to Juan', 'bi-send', '2026-01-13 05:50:12'),
(20, 3, 'Received Money', 100.00, 0.00, 'Received from Keneth James', 'bi-receive', '2026-01-13 05:50:12'),
(21, 8, 'Send Money', 100.00, 0.00, 'Sent to Juan', 'bi-send', '2026-01-13 06:01:47'),
(22, 3, 'Received Money', 100.00, 0.00, 'Received from Keneth James', 'bi-receive', '2026-01-13 06:01:47'),
(23, 8, 'Cash Out', 300.00, 7400.00, 'Transfer to Savings (SAV-001)', 'bi-piggy-bank', '2026-01-13 06:43:01'),
(24, 8, 'Cash Out', 5.00, 7395.00, 'Transfer to Savings (SAV-001)', 'bi-piggy-bank', '2026-01-13 06:43:20'),
(25, 8, 'Cash In', 400.93, 7795.93, 'Withdrawal from Savings (SAV-001)', 'bi-arrow-down-circle', '2026-01-13 06:44:44'),
(26, 8, 'Cash Out', 100.00, 7695.93, 'Initial deposit to Savings (SAV0008f11)', 'bi-piggy-bank', '2026-01-13 06:57:37'),
(27, 8, 'Cash In', 500.00, 8195.93, 'Withdrawal from Savings (SAV-001)', 'bi-arrow-down-circle', '2026-01-13 06:58:49'),
(28, 8, 'Cash Out', 195.93, 8000.00, 'Transfer to Savings (SAV-001)', 'bi-piggy-bank', '2026-01-13 07:06:55'),
(29, 8, 'Cash Out', 500.00, 7500.00, 'Transfer to Savings (SAV-001)', 'bi-piggy-bank', '2026-01-13 07:09:13'),
(30, 8, 'Cash In', 100000.00, 107500.00, 'Withdrawal from Savings (SAV-001)', 'bi-arrow-down-circle', '2026-01-13 07:13:33'),
(31, 8, 'Cash Out', 10000.00, 97500.00, 'Initial deposit to Savings (SAV0008f06)', 'bi-piggy-bank', '2026-01-13 12:33:46'),
(32, 8, 'Cash Out', 60000.00, 37500.00, 'Initial deposit to Savings (SAV0008952)', 'bi-piggy-bank', '2026-01-13 12:34:07'),
(33, 8, 'Cash In', 10000.00, 47500.00, 'Withdrawal from Savings (SAV0008952)', 'bi-arrow-down-circle', '2026-01-13 12:34:54'),
(34, 8, 'Cash Out', 1000.00, 46500.00, 'Initial deposit to Savings (SAV0008c45)', 'bi-piggy-bank', '2026-01-14 00:25:34'),
(35, 8, 'Cash Out', 1000.00, 45500.00, 'Initial deposit to Savings (SAV00081c6)', 'bi-piggy-bank', '2026-01-14 00:25:52'),
(36, 8, 'Cash Out', 2000.00, 43500.00, 'Transfer to Savings (SAV00081c6)', 'bi-piggy-bank', '2026-01-14 00:27:52'),
(37, 8, 'Cash Out', 1000.00, 41000.00, 'Initial deposit to Savings (SAV00085e7)', 'bi-piggy-bank', '2026-01-14 01:40:14'),
(38, 8, 'Cash Out', 1000.00, 40000.00, 'Initial deposit to Savings (SAV0008d17)', 'bi-piggy-bank', '2026-01-14 01:42:02'),
(39, 8, 'Cash Out', 100.00, 39900.00, 'Initial deposit to Savings (SAV0008641)', 'bi-piggy-bank', '2026-01-14 01:43:30'),
(40, 8, 'Cash Out', 900.00, 39000.00, 'Initial deposit to Savings (SAV0008fbc)', 'bi-piggy-bank', '2026-01-14 01:43:50'),
(41, 8, 'Cash Out', 100.00, 38900.00, 'Initial deposit to Savings (SAV0008334)', 'bi-piggy-bank', '2026-01-14 01:48:53'),
(42, 8, 'Cash Out', 900.00, 38000.00, 'Transfer to Savings (SAV0008c45)', 'bi-piggy-bank', '2026-01-14 01:51:15'),
(43, 8, 'Cash In', 900.00, 38900.00, 'Withdrawal from Savings (SAV0008c45)', 'bi-arrow-down-circle', '2026-01-14 01:51:22'),
(44, 8, 'Cash Out', 900.00, 38000.00, 'Initial deposit to Savings (SAV000892c)', 'bi-piggy-bank', '2026-01-14 01:54:20'),
(45, 8, 'Cash Out', 49999.00, 50001.00, 'Initial deposit to Savings (SAV00087ed)', 'bi-piggy-bank', '2026-01-14 01:56:41'),
(46, 8, 'Cash Out', 500.00, 49501.00, 'Initial deposit to Savings (SAV0008295)', 'bi-piggy-bank', '2026-01-14 02:06:27'),
(47, 8, 'Cash Out', 1000.00, 48501.00, 'Initial deposit to Savings (SAV0008867)', 'bi-piggy-bank', '2026-01-14 02:06:43'),
(48, 8, 'Cash Out', 100.00, 48401.00, 'Initial deposit to Savings (SAV0008004)', 'bi-piggy-bank', '2026-01-14 02:12:26'),
(49, 8, 'Cash Out', 401.00, 48000.00, 'Transfer to Savings (SAV00081c6)', 'bi-piggy-bank', '2026-01-14 02:13:21'),
(50, 8, 'Cash Out', 1000.00, 47000.00, 'Initial deposit to Savings (SAV0008d98)', 'bi-piggy-bank', '2026-01-14 02:28:25'),
(51, 8, 'Cash Out', 1000.00, 46000.00, 'Initial deposit to Savings (SAV00089f8)', 'bi-piggy-bank', '2026-01-14 02:35:23'),
(52, 8, 'Cash Out', 1000.00, 45000.00, 'Initial deposit to Savings (SAV0008231)', 'bi-piggy-bank', '2026-01-14 02:43:15'),
(53, 8, 'Cash Out', 1000.00, 44000.00, 'Initial deposit to Savings (SAV0008bbf)', 'bi-piggy-bank', '2026-01-14 02:53:59'),
(54, 8, 'Cash In', 500.00, 44500.00, 'Withdrawal from Savings (SAV0008c45)', 'bi-arrow-down-circle', '2026-01-14 03:17:17'),
(55, 8, 'Cash Out', 500.00, 44000.00, 'Cash Out via Counter', 'bi-wallet2', '2026-01-14 08:07:59'),
(56, 8, 'Cash Out', 300.00, 43700.00, 'Cash Out via Sari-Sari Store', 'bi-shop', '2026-01-14 08:08:37'),
(57, 8, 'Cash Out', 2000.00, 41700.00, 'Monthly Loan Payment', 'bi-cash-stack', '2026-01-14 08:31:34'),
(58, 8, 'Cash Out', 2000.00, 39700.00, 'Monthly Loan Payment', 'bi-cash-stack', '2026-01-14 08:33:13'),
(59, 8, 'Cash Out', 2000.00, 37700.00, 'Monthly Loan Payment', 'bi-cash-stack', '2026-01-14 08:37:56'),
(60, 8, 'Cash Out', 2000.00, 35700.00, 'Monthly Loan Payment', 'bi-cash-stack', '2026-01-14 08:47:35'),
(61, 8, 'Cash Out', 2000.00, 33700.00, 'Monthly Loan Payment', 'bi-cash-stack', '2026-01-14 08:48:05'),
(62, 8, 'Cash Out', 2000.00, 31700.00, 'Monthly Loan Payment', 'bi-cash-stack', '2026-01-14 08:53:15'),
(63, 8, 'Cash Out', 2000.00, 29700.00, 'Monthly Loan Payment', 'bi-cash-stack', '2026-01-14 08:58:06'),
(64, 8, 'Cash In', 19000.00, 48700.00, 'Loan Approved - Home', 'bi-cash-coin', '2026-01-14 13:08:38'),
(65, 8, 'Cash In', 20000.00, 68700.00, 'Loan Approved - Personal', 'bi-cash-coin', '2026-01-14 13:10:10'),
(66, 8, 'Cash In', 2000.00, 70700.00, 'Loan Approved - Personal', 'bi-cash-coin', '2026-01-14 13:22:54'),
(67, 8, 'Cash Out', 83.33, 70616.67, 'Monthly Loan Payment', 'bi-cash-stack', '2026-01-14 14:37:53'),
(68, 8, 'Cash Out', 83.33, 70533.34, 'Early Loan Payment', 'bi-cash-stack', '2026-01-14 14:50:11'),
(69, 8, 'Cash Out', 83.33, 70450.01, 'Early Loan Payment', 'bi-cash-stack', '2026-01-14 14:50:36'),
(70, 8, 'Cash Out', 83.33, 70366.68, 'Early Loan Payment', 'bi-cash-stack', '2026-01-14 14:51:46'),
(71, 8, 'Cash Out', 83.33, 70283.35, 'Early Loan Payment', 'bi-cash-stack', '2026-01-14 14:51:57'),
(72, 8, 'Cash Out', 283.35, 70000.00, 'Cash Out - Over the Counter - 7-Eleven', 'bi-wallet2', '2026-01-14 15:30:00'),
(73, 8, 'Cash Out', 500.00, 69500.00, 'Cash Out - Cash Machine - ETAP', 'bi-arrow-down-circle', '2026-01-14 15:30:11'),
(74, 8, 'Cash Out', 500.00, 69000.00, 'Cash Out - Sari-Sari Store - 09171234567', 'bi-shop', '2026-01-14 15:38:05'),
(75, 8, 'Cash Out', 500.00, 68500.00, 'Cash Out - Sari-Sari Store - 09171234567', 'bi-shop', '2026-01-14 15:38:30'),
(76, 8, 'Send Money', 500.00, 0.00, 'Sent to Juan', 'bi-send', '2026-01-14 15:40:31'),
(77, 3, 'Received Money', 500.00, 0.00, 'Received from Keneth James', 'bi-receive', '2026-01-14 15:40:31'),
(78, 8, 'Cash Out', 500.00, 67500.00, 'Cash Out - Sari-Sari Store - 09171234567', 'bi-shop', '2026-01-14 15:52:46'),
(79, 3, 'Received Money', 500.00, 1500.00, 'Received from Keneth James Rivera', 'bi-currency-exchange', '2026-01-14 15:52:46'),
(80, 8, 'Cash Out', 500.00, 67000.00, 'Cash Out - Sari-Sari Store - 09171234567', 'bi-shop', '2026-01-14 15:52:56'),
(81, 3, 'Received Money', 500.00, 2000.00, 'Received from Keneth James Rivera', 'bi-currency-exchange', '2026-01-14 15:52:56'),
(82, 8, 'Cash Out', 500.00, 66500.00, 'Cash Out - Sari-Sari Store - 09171234567', 'bi-shop', '2026-01-14 15:55:04'),
(83, 3, 'Received Money', 500.00, 2500.00, 'Received from Keneth James Rivera', 'bi-currency-exchange', '2026-01-14 15:55:04'),
(84, 8, 'Cash Out', 100.00, 66400.00, 'Cash Out - Sari-Sari Store - 09171234567', 'bi-shop', '2026-01-14 15:55:41'),
(85, 3, 'Received Money', 100.00, 2600.00, 'Received from Keneth James Rivera', 'bi-currency-exchange', '2026-01-14 15:55:41'),
(86, 3, 'Cash Out', 100.00, 2500.00, 'Initial deposit to Savings (SAV0003147)', 'bi-piggy-bank', '2026-01-14 15:56:40'),
(87, 3, 'Cash In', 100.00, 2600.00, 'Loan Approved - Personal', 'bi-cash-coin', '2026-01-14 16:00:19'),
(88, 3, 'Cash Out', 100.00, 2500.00, 'Full Loan Payment', 'bi-cash-stack', '2026-01-14 16:00:31');

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
(8, 'Keneth James', 'Esceuta', 'Rivera', 'riverakenethjames@gmail.com', '09940425690', 'Binan Laguna', '2005-11-05', 'ken123', 'profile/img_695487642ea7c.jpg', 'User', 'Approved', 66400.00),
(2, 'Abdul', 'Pacalundo', 'Disomimba', 'malik@test.com', '0912345678', 'Quezon City, Philippines', '1998-05-12', 'malik12345', 'profile/img_695487642ea7c.jpg', 'User', 'Rejected', 0.00),
(3, 'Juan', 'Santos', 'Dela Cruz', 'juan.delacruz@test.com', '09171234567', 'Manila, Philippines', '1995-03-18', 'juan12345', 'profile/img_695487642ea7c.jpg', 'User', 'Approved', 2500.00),
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
