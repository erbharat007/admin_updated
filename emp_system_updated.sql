-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 25, 2018 at 01:41 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `emp_system_updated`
--
CREATE DATABASE IF NOT EXISTS `emp_system_updated` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `emp_system_updated`;

-- --------------------------------------------------------

--
-- Table structure for table `activity_categories`
--

DROP TABLE IF EXISTS `activity_categories`;
CREATE TABLE IF NOT EXISTS `activity_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(255) NOT NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `activity_categories`
--

INSERT INTO `activity_categories` (`id`, `category`, `created_date`) VALUES
(2, 'Customer Visit', '2016-08-12 22:04:00'),
(3, 'Need Advice', '2016-08-12 22:05:40'),
(4, 'Site Visit', '2016-08-12 22:05:48'),
(5, 'Daily Activity', '2016-08-12 22:05:57'),
(6, 'Inspection', '2016-08-12 22:06:04'),
(7, 'Proposal Prepared', '2016-08-12 22:06:47'),
(8, 'Installation ', '2016-08-12 22:06:55'),
(9, 'Travelling', '2016-08-12 22:07:11'),
(10, 'Phone Calls', '2016-08-12 22:07:21'),
(11, 'Meeting with Customer', '2016-08-12 22:07:55');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE IF NOT EXISTS `admin_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` bigint(20) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `user_type` enum('finance','hr','superadmin') NOT NULL,
  `centralised_admin` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `branch_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  `last_password_change` datetime NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`user_id`, `first_name`, `last_name`, `email`, `password`, `phone`, `status`, `user_type`, `centralised_admin`, `branch_id`, `role_id`, `created_date`, `last_updated`, `last_password_change`) VALUES
(1, 'Super', 'Admin', 'erbharat.007@gmail.com', 'cade1ce46db56424983cf5b58db5741b', 123456789, 'active', 'superadmin', 'NO', 0, 0, '2016-10-04 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 'Divya', 'Jain', 'divya@gmail.com', '238eb7414ecc147fdf1964fc381624a3', 9990099911, 'active', 'hr', 'NO', 0, 4, '2016-10-04 06:20:44', '2016-10-04 06:23:25', '0000-00-00 00:00:00'),
(4, 'Priya', 'Sharma', 'priya@gmail.com', '238eb7414ecc147fdf1964fc381624a3', 9990099915, 'active', 'finance', 'NO', 0, 4, '2016-10-04 06:20:44', '2016-10-04 06:23:25', '2016-10-15 00:00:00'),
(5, 'Anu', 'Rastogi', 'anu@gmail.com', '238eb7414ecc147fdf1964fc381624a3', 9990099915, 'active', 'finance', 'NO', 0, 4, '2016-10-04 06:20:44', '2016-10-04 06:23:25', '2016-10-15 00:00:00'),
(6, 'Kalpesh', 'Jain', 'kalpesh@gmail.com', '238eb7414ecc147fdf1964fc381624a3', 9990099911, 'active', 'finance', 'NO', 6, 5, '2016-10-04 06:20:44', '2016-10-28 19:13:54', '0000-00-00 00:00:00'),
(7, 'Ratnesh', 'Babu', 'ratnesh@gmail.com', '238eb7414ecc147fdf1964fc381624a3', 12354334, 'active', 'finance', 'YES', 6, 4, '2016-10-24 19:25:38', '2016-10-28 19:16:44', '0000-00-00 00:00:00'),
(8, 'Shilpa', 'Sharma', 'shilpa@yahoo.com', '238eb7414ecc147fdf1964fc381624a3', 123678, 'inactive', 'finance', 'NO', 4, 3, '2016-11-12 19:25:38', '2016-11-12 19:16:44', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
CREATE TABLE IF NOT EXISTS `branches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `city_name` varchar(100) NOT NULL,
  `region` enum('North','South') NOT NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `city_name`, `region`, `created_date`) VALUES
(2, 'Hyderabad', 'South', '2016-08-06 08:28:09'),
(3, 'Faridabad', 'North', '2016-08-06 08:28:34'),
(4, 'Noida', 'North', '2016-08-08 06:04:33'),
(5, 'New Delhi', 'North', '2016-08-08 06:04:56'),
(6, 'Gurgaon', 'North', '2016-08-08 06:05:14'),
(8, 'Banglore', 'South', '2016-08-08 06:06:02');

-- --------------------------------------------------------

--
-- Table structure for table `calculated_da`
--

DROP TABLE IF EXISTS `calculated_da`;
CREATE TABLE IF NOT EXISTS `calculated_da` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) NOT NULL COMMENT 'Refers to the "tour_id" of table "tour_requests"',
  `tour_start_date` date NOT NULL,
  `tour_end_date` date NOT NULL,
  `total_travel_time_hrs` float NOT NULL,
  `total_travel_time_days` float NOT NULL,
  `total_tour_time_hrs` float NOT NULL,
  `total_tour_time_days` float NOT NULL,
  `total_balance_hours` float NOT NULL,
  `total_balance_days` float NOT NULL,
  `half_da_for_travel` float NOT NULL,
  `full_da_for_balance_days` float NOT NULL,
  `total_da` float NOT NULL,
  `approve_status_HOD1` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `HOD1_comment` text NOT NULL,
  `approved_by_HOD1_id` int(11) NOT NULL DEFAULT '0',
  `approve_status_HOD2` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `HOD2_comment` text NOT NULL,
  `approve_status_finance` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `finance_comment` text NOT NULL,
  `approved_by_finance_id` int(11) DEFAULT '0',
  `created_date` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `calculated_da`
--

INSERT INTO `calculated_da` (`id`, `tour_id`, `tour_start_date`, `tour_end_date`, `total_travel_time_hrs`, `total_travel_time_days`, `total_tour_time_hrs`, `total_tour_time_days`, `total_balance_hours`, `total_balance_days`, `half_da_for_travel`, `full_da_for_balance_days`, `total_da`, `approve_status_HOD1`, `HOD1_comment`, `approved_by_HOD1_id`, `approve_status_HOD2`, `HOD2_comment`, `approve_status_finance`, `finance_comment`, `approved_by_finance_id`, `created_date`, `last_updated`) VALUES
(1, 8, '2016-09-23', '2016-10-12', 59, 2.46, 457, 19.04, 398, 16.58, 369, 4974, 5343, 'approved', 'test comment 1', 0, 'pending', '', 'pending', '', 0, '2016-09-29 19:47:14', '2016-10-03 19:31:11'),
(2, 9, '2016-10-06', '2016-10-06', 1.95, 0.08, 1.95, 0.08, 0, 0, 13.6, 0, 13.6, 'pending', 'test reject', 0, 'pending', '', 'approved', 'Test Approved', 0, '2016-10-06 07:25:43', '2016-10-07 21:00:27'),
(4, 7, '2016-11-08', '2016-11-16', 5.68, 0.24, 199.75, 8.32, 194.07, 8.09, 36, 2427, 2463, 'pending', '', 0, 'pending', '', 'pending', '', 0, '2016-11-08 20:52:58', '2016-11-08 21:10:29');

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

DROP TABLE IF EXISTS `city`;
CREATE TABLE IF NOT EXISTS `city` (
  `city_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `country_id` bigint(20) NOT NULL DEFAULT '0' COMMENT 'Refers to the "id" of country table',
  `city_name` varchar(100) NOT NULL DEFAULT '',
  `is_metro_city` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`city_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `city`
--

INSERT INTO `city` (`city_id`, `country_id`, `city_name`, `is_metro_city`, `status`, `created_date`) VALUES
(1, 0, '', 0, 0, '2018-03-25 02:05:09');

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
CREATE TABLE IF NOT EXISTS `country` (
  `country_id` int(11) NOT NULL AUTO_INCREMENT,
  `country_name` varchar(100) NOT NULL,
  `iso_code_2` char(2) NOT NULL,
  `iso_code_3` char(3) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`country_id`, `country_name`, `iso_code_2`, `iso_code_3`, `status`, `created_date`) VALUES
(7, 'India', 'IN', 'IND', 1, '2018-03-23 01:50:42');

-- --------------------------------------------------------

--
-- Table structure for table `daily_activities`
--

DROP TABLE IF EXISTS `daily_activities`;
CREATE TABLE IF NOT EXISTS `daily_activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL COMMENT 'ID of employee (Refers to "employees" table)',
  `activity` text NOT NULL,
  `start_date_time` datetime NOT NULL,
  `end_date_time` datetime NOT NULL,
  `category_id` int(11) NOT NULL,
  `approve_status_HOD1` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `HOD1_comment` text NOT NULL,
  `approved_by_HOD1_id` int(11) NOT NULL DEFAULT '0',
  `approve_status_HOD2` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `HOD2_comment` text NOT NULL,
  `created_date` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=34 ;

--
-- Dumping data for table `daily_activities`
--

INSERT INTO `daily_activities` (`id`, `emp_id`, `activity`, `start_date_time`, `end_date_time`, `category_id`, `approve_status_HOD1`, `HOD1_comment`, `approved_by_HOD1_id`, `approve_status_HOD2`, `HOD2_comment`, `created_date`, `last_updated`) VALUES
(2, 9, 'test 123', '2016-08-16 00:00:00', '2016-08-17 00:00:00', -1, 'approved', 'test approve', 0, 'pending', '', '2016-08-15 22:00:57', '0000-00-00 00:00:00'),
(3, 9, 'Testing Meeting 1', '2016-08-16 02:00:00', '2016-08-16 03:00:00', 10, 'pending', '', 0, 'pending', '', '2016-08-15 22:02:38', '0000-00-00 00:00:00'),
(4, 9, 'Testing Meeting 2', '2016-08-16 02:00:00', '2016-08-16 03:00:00', 10, 'approved', '', 0, 'pending', '', '2016-08-15 22:05:45', '0000-00-00 00:00:00'),
(5, 9, 'Testing Meeting 4', '2016-08-16 02:00:00', '2016-08-16 03:06:00', 8, 'pending', '', 0, 'pending', '', '2016-08-15 22:09:12', '0000-00-00 00:00:00'),
(6, 9, 'Testing Meeting 5', '2016-08-16 02:00:00', '2016-08-16 03:00:00', 10, 'pending', '', 0, 'pending', '', '2016-08-15 22:15:47', '0000-00-00 00:00:00'),
(7, 9, 'Testing Meeting 6', '2016-08-16 02:00:00', '2016-08-16 03:00:00', 10, 'pending', '', 0, 'pending', '', '2016-08-15 22:21:32', '0000-00-00 00:00:00'),
(8, 9, 'Testing  8', '2016-08-16 02:00:00', '2016-08-16 06:21:00', 7, 'pending', '', 0, 'pending', '', '2016-08-15 22:22:08', '0000-00-00 00:00:00'),
(9, 9, 'Testing Meeting', '2016-08-16 02:00:00', '2016-08-16 03:00:00', 10, 'pending', '', 0, 'pending', '', '2016-08-15 22:22:47', '0000-00-00 00:00:00'),
(10, 10, 'test 123', '2016-08-16 00:00:00', '2016-08-17 00:00:00', -1, 'pending', '', 0, 'pending', '', '2016-08-15 22:00:57', '0000-00-00 00:00:00'),
(11, 9, 'test act 123', '2016-08-25 00:00:00', '2016-08-25 02:00:00', -1, 'pending', '', 0, 'pending', '', '2016-08-24 21:19:49', '0000-00-00 00:00:00'),
(12, 9, 'test act 123', '2016-08-25 00:00:00', '2016-08-25 02:00:00', -1, 'pending', '', 0, 'pending', '', '2016-08-24 21:20:17', '0000-00-00 00:00:00'),
(13, 9, 'test act 123', '2016-08-25 00:00:00', '2016-08-25 02:00:00', -1, 'pending', '', 0, 'pending', '', '2016-08-24 21:21:50', '0000-00-00 00:00:00'),
(14, 9, 'test act 123', '2016-08-25 00:00:00', '2016-08-25 02:00:00', -1, 'pending', '', 0, 'pending', '', '2016-08-24 21:23:51', '0000-00-00 00:00:00'),
(15, 9, 'tst', '2016-09-08 00:00:00', '2016-08-31 01:00:00', -1, 'approved', '', 0, 'pending', '', '2016-08-24 21:25:06', '0000-00-00 00:00:00'),
(16, 9, 'This is test 1', '2016-08-25 00:00:00', '2016-08-25 00:02:00', -1, 'pending', '', 0, 'pending', '', '2016-08-25 20:27:52', '0000-00-00 00:00:00'),
(17, 9, 'This is test 2', '2016-08-25 01:00:00', '0000-00-00 00:00:00', -1, 'pending', '', 0, 'pending', '', '2016-08-25 20:27:52', '0000-00-00 00:00:00'),
(18, 9, 'This is test 9', '2016-08-25 02:00:00', '2016-08-25 03:00:00', -1, 'pending', '', 0, 'pending', '', '2016-08-25 20:40:33', '0000-00-00 00:00:00'),
(19, 9, 'This is test 10', '2016-08-25 01:00:00', '2016-08-25 02:00:00', -1, 'pending', '', 0, 'pending', '', '2016-08-25 20:40:33', '0000-00-00 00:00:00'),
(20, 9, 'This is test 11', '2016-08-25 00:00:00', '2016-08-25 00:02:00', -1, 'pending', '', 0, 'pending', '', '2016-08-25 20:40:33', '0000-00-00 00:00:00'),
(23, 1, 'test', '2016-09-21 00:00:00', '2016-09-21 00:00:00', -1, 'pending', '', 0, 'pending', '', '2016-09-14 05:40:10', '0000-00-00 00:00:00'),
(24, 1, 'test', '2016-09-27 00:00:00', '2016-09-28 00:00:00', -1, 'pending', '', 0, 'pending', '', '2016-09-14 05:57:09', '0000-00-00 00:00:00'),
(25, 1, 'test', '2016-09-28 00:00:00', '2016-09-29 00:00:00', -1, 'pending', '', 0, 'pending', '', '2016-09-14 05:59:13', '0000-00-00 00:00:00'),
(26, 9, 'Test activity', '2016-12-29 00:00:00', '2016-12-29 03:10:00', 9, 'pending', '', 0, 'pending', '', '2016-12-28 19:23:22', '0000-00-00 00:00:00'),
(27, 9, 'Test activity 2', '2016-12-30 00:00:00', '2016-12-30 01:00:00', 10, 'approved', '', 0, 'pending', '', '2016-12-28 19:23:22', '0000-00-00 00:00:00'),
(28, 9, 'Test activity', '2016-12-29 00:00:00', '2016-12-29 13:30:00', 4, 'pending', '', 0, 'pending', '', '2016-12-28 19:27:51', '0000-00-00 00:00:00'),
(29, 9, 'Test activity 2', '2016-12-30 00:00:00', '2016-12-30 01:00:00', 10, 'pending', '', 0, 'pending', '', '2016-12-28 19:27:51', '0000-00-00 00:00:00'),
(30, 9, 'Testing 23', '2016-12-29 00:00:00', '2016-12-29 03:10:00', -1, 'rejected', '', 0, 'pending', '', '2016-12-28 19:37:19', '0000-00-00 00:00:00'),
(31, 9, 'Testing 23 45 test', '2016-12-29 03:00:00', '2016-12-29 14:10:00', -1, 'pending', '', 0, 'pending', '', '2016-12-28 19:37:19', '0000-00-00 00:00:00'),
(32, 9, 'Activity 1', '2016-12-29 17:00:00', '2016-12-29 18:01:00', -1, 'pending', '', 0, 'pending', '', '2016-12-28 19:39:01', '0000-00-00 00:00:00'),
(33, 9, 'Activity 2', '2016-12-29 18:02:00', '2016-12-29 19:00:00', -1, 'pending', '', 0, 'pending', '', '2016-12-28 19:39:01', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `da_res_details`
--

DROP TABLE IF EXISTS `da_res_details`;
CREATE TABLE IF NOT EXISTS `da_res_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) NOT NULL COMMENT 'Refers to the "tour_id" of table "tour_requests"',
  `start_date_time` datetime NOT NULL,
  `end_date_time` datetime NOT NULL,
  `reservation_from` varchar(255) NOT NULL,
  `reservation_to` varchar(255) NOT NULL,
  `travel_time` float NOT NULL,
  `created_date` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `da_res_details`
--

INSERT INTO `da_res_details` (`id`, `tour_id`, `start_date_time`, `end_date_time`, `reservation_from`, `reservation_to`, `travel_time`, `created_date`, `last_updated`) VALUES
(1, 8, '2016-09-23 09:00:00', '2016-09-23 10:00:00', 'Goa', 'Agra', 1, '2016-09-29 19:47:14', '2016-10-03 19:31:11'),
(2, 8, '2016-10-10 00:00:00', '2016-10-12 10:00:00', 'Beawar 5', 'Ajmer', 58, '2016-10-03 06:40:28', '2016-10-03 19:31:11'),
(3, 9, '2016-10-06 14:00:00', '2016-10-06 14:30:00', '', '', 0.5, '2016-10-06 07:25:43', '2016-10-07 21:00:27'),
(4, 9, '2016-10-06 14:30:00', '2016-10-06 15:57:00', '', '', 1.45, '2016-10-07 20:48:37', '2016-10-07 21:00:27'),
(7, 7, '2016-11-08 09:00:00', '2016-11-08 11:00:00', 'Delhi', 'Banglore', 2, '2016-11-08 20:52:58', '2016-11-08 21:10:29'),
(8, 7, '2016-11-15 10:00:00', '2016-11-15 11:00:00', 'Banglore', 'Delhi', 1, '2016-11-08 20:52:58', '2016-11-08 21:10:29'),
(9, 7, '2016-11-16 14:04:00', '2016-11-16 16:45:00', 'Delhi', 'Palam', 2.68, '2016-11-08 21:05:22', '2016-11-08 21:10:29');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `dept_id` int(11) NOT NULL AUTO_INCREMENT,
  `dept_name` varchar(255) NOT NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`dept_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`dept_id`, `dept_name`, `created_date`) VALUES
(15, 'Human Resource', '2016-07-14 22:57:10'),
(18, 'Business Development', '2016-07-15 05:34:40'),
(20, 'Finance', '2016-07-15 05:35:24'),
(21, 'Customer Relation', '2016-07-15 05:38:02'),
(22, 'Admin', '2016-08-08 05:54:27'),
(23, 'Manufacturig Department', '2016-08-08 06:07:57'),
(24, 'Research & Development', '2016-08-08 06:08:48');

-- --------------------------------------------------------

--
-- Table structure for table `designations`
--

DROP TABLE IF EXISTS `designations`;
CREATE TABLE IF NOT EXISTS `designations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `designation` varchar(255) NOT NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `designations`
--

INSERT INTO `designations` (`id`, `designation`, `created_date`) VALUES
(13, 'Project Manager', '2016-07-15 20:49:30'),
(14, 'HR Manager', '2016-07-15 20:50:12'),
(15, 'Chartered Accountant', '2016-07-15 20:50:29'),
(18, 'Admin Head', '2016-08-08 05:54:58'),
(19, 'Business Developer', '2016-08-08 06:09:40');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
CREATE TABLE IF NOT EXISTS `employees` (
  `emp_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `department_id` int(11) NOT NULL COMMENT 'ID of department',
  `designation_id` int(11) NOT NULL COMMENT 'ID of designation',
  `branch_id` int(11) NOT NULL,
  `address` text NOT NULL,
  `phone` bigint(20) NOT NULL,
  `emp_code` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `emp_category` enum('originator','authorizer','both') NOT NULL,
  `birth_date` date NOT NULL,
  `joining_date` date NOT NULL,
  `pan_number` varchar(255) NOT NULL,
  `passport_number` varchar(255) NOT NULL,
  `HOD_1_id` int(11) NOT NULL,
  `HOD_1_name` varchar(255) NOT NULL,
  `HOD_1_email` varchar(255) NOT NULL,
  `HOD_2_id` int(11) NOT NULL,
  `HOD_2_name` varchar(255) NOT NULL,
  `HOD_2_email` varchar(255) NOT NULL,
  `hr_id` int(11) NOT NULL,
  `hr_name` varchar(255) NOT NULL,
  `hr_email` int(255) NOT NULL,
  `role_id` int(11) NOT NULL COMMENT 'Refers to the ID of table "roles"',
  `bank_name` varchar(255) NOT NULL,
  `bank_ac_number` varchar(200) NOT NULL,
  `bank_ifsc_Code` varchar(20) NOT NULL,
  `bank_branch_address` text NOT NULL,
  `created_date` datetime NOT NULL,
  `last_password_change` datetime NOT NULL,
  PRIMARY KEY (`emp_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`emp_id`, `first_name`, `last_name`, `email`, `password`, `department_id`, `designation_id`, `branch_id`, `address`, `phone`, `emp_code`, `status`, `emp_category`, `birth_date`, `joining_date`, `pan_number`, `passport_number`, `HOD_1_id`, `HOD_1_name`, `HOD_1_email`, `HOD_2_id`, `HOD_2_name`, `HOD_2_email`, `hr_id`, `hr_name`, `hr_email`, `role_id`, `bank_name`, `bank_ac_number`, `bank_ifsc_Code`, `bank_branch_address`, `created_date`, `last_password_change`) VALUES
(1, 'Super', 'Admin', 'erbharat.007@gmail.com', 'cade1ce46db56424983cf5b58db5741b', 0, 0, 3, '', 0, '', 'active', 'both', '0000-00-00', '0000-00-00', '', '', 0, '', '', 0, '', '', 0, '', 0, 1, '', '', '', '', '0000-00-00 00:00:00', '2016-07-13 00:00:00'),
(9, 'Aniket', 'Jain', 'erbharat_007@yahoo.com', '238eb7414ecc147fdf1964fc381624a3', 15, 15, 4, 'Test Address', 123456780, 'ANI0012', 'active', 'originator', '2016-08-03', '2016-06-01', 'ANI009123', 'ANIT123IM', 1, '', '', 10, '', '', -1, '', 0, 5, '', '', '', '', '2016-08-06 20:36:51', '0000-00-00 00:00:00'),
(10, 'Akshay', 'Diwedi', 'akshay@gmail.com', '238eb7414ecc147fdf1964fc381624a3', 18, 19, 5, 'Test Address', 123098765, 'AKSHAY123', 'active', 'originator', '1986-05-06', '2016-07-13', 'TESTPAN11234', 'TESTPASS0122', 11, '', '', 12, '', '', 9, '', 0, 5, 'HDFC', '1230987655', 'HDFC12509', 'TEST ADDRESS', '2016-08-08 06:12:29', '0000-00-00 00:00:00'),
(11, 'Ajay', 'Kumar', 'ajay@gmail.com', '238eb7414ecc147fdf1964fc381624a3', 15, 15, 8, 'Test Address', 120965433, 'AJAY1209', 'active', 'originator', '1980-03-27', '2016-08-17', 'AJAY678', 'TESTAJAY12', 1, '', '', -1, '', '', -1, '', 0, 5, '', '', '', '', '2016-08-08 06:18:18', '0000-00-00 00:00:00'),
(12, 'Jayant', 'Pareek', 'jayant@gmail.com', '8b1b02c1c91463772c2bf6e3c828af99', 24, 19, 6, 'Sector-14, Gurgaon', 9898098975, 'ABC-234', 'active', 'originator', '1990-04-18', '2016-08-22', 'TESTPAN1234', '', 1, '', '', 1, '', '', -1, '', 0, 0, '', '', '', '', '2016-09-13 20:32:45', '0000-00-00 00:00:00'),
(13, 'Anmol', 'Pareek', 'anmol@gmail.com', '238eb7414ecc147fdf1964fc381624a3', 22, 15, 6, 'Test address', 12345, 'ABC123', 'inactive', 'originator', '2016-11-10', '2016-11-17', '', '', 1, '', '', -1, '', '', 1, '', 0, 0, '', '', '', '', '2016-11-03 17:47:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `features`
--

DROP TABLE IF EXISTS `features`;
CREATE TABLE IF NOT EXISTS `features` (
  `feature_id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL COMMENT 'Refers to the ID of table "services"',
  `feature_name` varchar(255) NOT NULL,
  `feature_url` varchar(255) NOT NULL,
  PRIMARY KEY (`feature_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `features`
--

INSERT INTO `features` (`feature_id`, `service_id`, `feature_name`, `feature_url`) VALUES
(1, 1, 'Add/Edit Registered Users', 'index.php?Option=Registration&SubOption=employee'),
(2, 3, 'Create Tour Request', 'index.php?Option=Tour&SubOption=createTourRequest'),
(3, 3, 'Tour Requests', 'index.php?Option=Tour&SubOption=tourRequests'),
(4, 2, 'Create Leave Application', 'index.php?Option=Leaves&SubOption=leaveApplication'),
(5, 2, 'Leave Requests', 'index.php?Option=Leaves&SubOption=leaveRequests'),
(6, 5, 'Add/Edit User Roles', 'index.php?Option=Users&SubOption=UserRole'),
(7, 5, 'Assign Roles to Employees', 'index.php?Option=Users&SubOption=AssignRole'),
(8, 4, 'Add/Edit Departments', 'index.php?Option=Setup&SubOption=Department'),
(9, 4, 'Add/Edit Designations', 'index.php?Option=Setup&SubOption=Designation'),
(10, 4, 'Add/Update Holiday Calendar (North)', 'index.php?Option=Setup&SubOption=holidayCalendarNorth'),
(11, 4, 'Leave Set up for Employees', 'index.php?Option=Setup&SubOption=empLeaveSetup'),
(12, 4, 'Add/Edit Company Branches', 'index.php?Option=Setup&SubOption=branches'),
(13, 4, 'Add/Update Holiday Calendar (South)', 'index.php?Option=Setup&SubOption=holidayCalendarSouth'),
(14, 4, 'Add/Edit Daily Activity Category', 'index.php?Option=Setup&SubOption=activityCategory'),
(15, 6, 'Create Daily Activity', 'index.php?Option=Activity&SubOption=dailyActivity'),
(16, 6, 'Daily Activity Requests', 'index.php?Option=Activity&SubOption=dailyActivityRequests'),
(17, 3, 'Local Conveyance Request/Reimbursement Form', 'index.php?Option=Tour&SubOption=localConveyance'),
(18, 3, 'Local Conveyance Requests', 'index.php?Option=Tour&SubOption=localConveyanceRequests'),
(19, 5, 'Assign Roles to Admin Users', 'index.php?Option=Users&SubOption=AssignRoleAdminUser'),
(20, 4, 'Add/Edit Country', 'index.php?Option=Setup&SubOption=Country'),
(21, 4, 'Add/Edit City', 'index.php?Option=Setup&SubOption=City');

-- --------------------------------------------------------

--
-- Table structure for table `holiday_calendar_north`
--

DROP TABLE IF EXISTS `holiday_calendar_north`;
CREATE TABLE IF NOT EXISTS `holiday_calendar_north` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `reason_of_holiday` text NOT NULL,
  `year` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=145 ;

--
-- Dumping data for table `holiday_calendar_north`
--

INSERT INTO `holiday_calendar_north` (`id`, `date`, `reason_of_holiday`, `year`) VALUES
(4, '2016-01-10', '', 2016),
(6, '2016-01-17', '', 2016),
(7, '2016-01-23', '', 2016),
(8, '2016-01-24', '', 2016),
(9, '2016-01-30', '', 2016),
(10, '2016-01-31', '', 2016),
(11, '2016-02-06', '', 2016),
(12, '2016-02-07', '', 2016),
(13, '2016-02-13', '', 2016),
(14, '2016-02-14', '', 2016),
(15, '2016-02-20', '', 2016),
(16, '2016-02-21', '', 2016),
(17, '2016-02-27', '', 2016),
(18, '2016-02-28', '', 2016),
(19, '2016-03-05', '', 2016),
(20, '2016-03-06', '', 2016),
(21, '2016-03-12', '', 2016),
(22, '2016-03-13', '', 2016),
(23, '2016-03-19', '', 2016),
(24, '2016-03-20', '', 2016),
(25, '2016-03-26', '', 2016),
(26, '2016-03-27', '', 2016),
(27, '2016-04-02', '', 2016),
(28, '2016-04-03', '', 2016),
(29, '2016-04-09', '', 2016),
(30, '2016-04-10', '', 2016),
(31, '2016-04-16', '', 2016),
(32, '2016-04-17', '', 2016),
(33, '2016-04-23', '', 2016),
(34, '2016-04-24', '', 2016),
(35, '2016-04-30', '', 2016),
(36, '2016-05-01', '', 2016),
(37, '2016-05-07', '', 2016),
(38, '2016-05-08', '', 2016),
(39, '2016-05-14', '', 2016),
(40, '2016-05-15', '', 2016),
(41, '2016-05-21', '', 2016),
(42, '2016-05-22', '', 2016),
(43, '2016-05-28', '', 2016),
(44, '2016-05-29', '', 2016),
(45, '2016-06-04', '', 2016),
(46, '2016-06-05', '', 2016),
(47, '2016-06-11', '', 2016),
(48, '2016-06-12', '', 2016),
(49, '2016-06-18', '', 2016),
(50, '2016-06-19', '', 2016),
(51, '2016-06-25', '', 2016),
(52, '2016-06-26', '', 2016),
(53, '2016-07-02', '', 2016),
(54, '2016-07-03', '', 2016),
(55, '2016-07-09', '', 2016),
(56, '2016-07-10', '', 2016),
(57, '2016-07-16', '', 2016),
(58, '2016-07-17', '', 2016),
(59, '2016-07-23', '', 2016),
(60, '2016-07-24', '', 2016),
(61, '2016-07-30', '', 2016),
(62, '2016-07-31', '', 2016),
(63, '2016-08-06', '', 2016),
(64, '2016-08-07', '', 2016),
(65, '2016-08-13', '', 2016),
(66, '2016-08-14', '', 2016),
(67, '2016-08-20', '', 2016),
(68, '2016-08-21', '', 2016),
(69, '2016-08-27', '', 2016),
(70, '2016-08-28', '', 2016),
(71, '2016-09-03', '', 2016),
(72, '2016-09-04', '', 2016),
(73, '2016-09-10', '', 2016),
(74, '2016-09-11', '', 2016),
(75, '2016-09-17', '', 2016),
(76, '2016-09-18', '', 2016),
(77, '2016-09-24', '', 2016),
(78, '2016-09-25', '', 2016),
(79, '2016-10-01', '', 2016),
(80, '2016-10-02', '', 2016),
(81, '2016-10-08', '', 2016),
(82, '2016-10-09', '', 2016),
(83, '2016-10-15', '', 2016),
(84, '2016-10-16', '', 2016),
(85, '2016-10-22', '', 2016),
(86, '2016-10-23', '', 2016),
(87, '2016-10-29', '', 2016),
(88, '2016-10-30', '', 2016),
(89, '2016-11-05', '', 2016),
(90, '2016-11-06', '', 2016),
(91, '2016-11-12', '', 2016),
(92, '2016-11-13', '', 2016),
(93, '2016-11-19', '', 2016),
(94, '2016-11-20', '', 2016),
(95, '2016-11-26', '', 2016),
(96, '2016-11-27', '', 2016),
(97, '2016-12-03', '', 2016),
(98, '2016-12-04', '', 2016),
(99, '2016-12-10', '', 2016),
(100, '2016-12-11', '', 2016),
(101, '2016-12-17', '', 2016),
(102, '2016-12-18', '', 2016),
(103, '2016-12-24', '', 2016),
(104, '2016-12-25', '', 2016),
(105, '2016-12-31', '', 2016),
(113, '2016-01-09', '', 2016),
(116, '2016-01-02', 'Collector Power', 2016),
(117, '2016-01-03', 'Normal Sunday', 2016),
(119, '2016-01-16', '', 2016),
(121, '2016-01-06', '', 2016),
(124, '2016-01-14', '', 2016),
(125, '2016-06-08', '', 2016),
(126, '2016-06-16', '', 2016),
(127, '2016-06-17', '', 2016),
(132, '2016-04-13', '', 2016),
(133, '2016-04-15', '', 2016),
(134, '2016-04-19', '', 2016),
(135, '2016-04-21', '', 2016),
(136, '2016-05-11', '', 2016),
(137, '2016-05-18', '', 2016),
(138, '2016-05-19', '', 2016),
(139, '2016-06-03', '', 2016),
(140, '2016-06-07', '', 2016),
(141, '2016-06-15', '', 2016),
(142, '2016-06-21', '', 2016),
(143, '2016-09-22', '', 2016),
(144, '2016-11-14', 'For Tsting', 2016);

-- --------------------------------------------------------

--
-- Table structure for table `holiday_calendar_south`
--

DROP TABLE IF EXISTS `holiday_calendar_south`;
CREATE TABLE IF NOT EXISTS `holiday_calendar_south` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `reason_of_holiday` text NOT NULL,
  `year` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `holiday_calendar_south`
--

INSERT INTO `holiday_calendar_south` (`id`, `date`, `reason_of_holiday`, `year`) VALUES
(1, '2016-02-07', 'Rose Day', 2016),
(2, '2016-03-12', '', 2016),
(3, '2016-03-16', 'holi', 2016),
(4, '2016-04-15', '', 2016),
(5, '2016-04-26', '', 2016),
(6, '2016-04-27', '', 2016),
(7, '2016-05-10', '', 2016),
(8, '2016-05-15', '', 2016),
(9, '2016-02-10', 'Chocolate day', 2016),
(10, '2016-02-18', '', 2016),
(11, '2016-02-16', '', 2016),
(12, '2016-02-25', '', 2016),
(13, '2016-02-26', '', 2016),
(14, '2016-09-20', '', 2016);

-- --------------------------------------------------------

--
-- Table structure for table `leave_applications`
--

DROP TABLE IF EXISTS `leave_applications`;
CREATE TABLE IF NOT EXISTS `leave_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL COMMENT 'ID of employee (Refers to "employees" table)',
  `leave_type_id` int(11) NOT NULL,
  `leave_from_date` date NOT NULL,
  `leave_to_date` date NOT NULL,
  `leaves_required` int(11) NOT NULL COMMENT 'in days',
  `half_day_leave` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `job_assignee_id` int(11) NOT NULL,
  `reason_of_leave` text NOT NULL,
  `leave_address` text NOT NULL,
  `leave_phone` int(11) NOT NULL,
  `medical_certificate` varchar(100) NOT NULL,
  `leave_balance` float NOT NULL,
  `approve_status_HOD1` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `HOD1_comment` text NOT NULL,
  `approved_by_HOD1_id` int(11) NOT NULL DEFAULT '0',
  `approve_status_HOD2` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `HOD2_comment` text NOT NULL,
  `created_date` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=36 ;

--
-- Dumping data for table `leave_applications`
--

INSERT INTO `leave_applications` (`id`, `emp_id`, `leave_type_id`, `leave_from_date`, `leave_to_date`, `leaves_required`, `half_day_leave`, `job_assignee_id`, `reason_of_leave`, `leave_address`, `leave_phone`, `medical_certificate`, `leave_balance`, `approve_status_HOD1`, `HOD1_comment`, `approved_by_HOD1_id`, `approve_status_HOD2`, `HOD2_comment`, `created_date`, `last_updated`) VALUES
(1, 4, 5, '2016-08-10', '2016-08-12', 2, 'NO', 8, 'Test application', 'Delhi, Ashok Vihar', 2147483647, '', 0, 'approved', '', 0, 'pending', '', '2016-07-24 10:08:25', '0000-00-00 00:00:00'),
(2, 4, 2, '2016-08-10', '2016-08-11', 1, 'NO', 8, 'test', 'test', 8987654, '', 0, 'approved', '', 0, 'pending', '', '2016-07-24 10:29:34', '0000-00-00 00:00:00'),
(3, 8, 3, '2016-09-06', '2016-09-08', 2, 'NO', 4, 'MBA Exam', 'test', 8987654, '', 0, 'approved', '', 0, 'pending', '', '2016-07-31 10:29:34', '0000-00-00 00:00:00'),
(4, 4, 2, '2016-10-05', '2016-10-07', 2, 'NO', 8, 'Diwali', 'Jaipur', 123455, '', 0, 'approved', '', 0, 'pending', '', '2016-08-02 22:14:26', '0000-00-00 00:00:00'),
(26, 9, 3, '2016-08-24', '2016-08-25', 2, 'NO', 11, 'Test Reason 1', 'Test Address 12', 9890, '', 0, 'rejected', '', 0, 'pending', '', '2016-08-19 07:31:41', '0000-00-00 00:00:00'),
(28, 9, 2, '2016-09-28', '2016-09-28', 1, 'NO', 11, 'Test', 'Test', 0, '', 0, 'approved', '', 0, 'pending', '', '2016-09-09 19:53:31', '0000-00-00 00:00:00'),
(29, 9, 1, '2016-09-21', '2016-09-23', 2, 'NO', 11, '', '', 0, '', 0, 'rejected', '', 0, 'pending', '', '2016-09-17 22:04:25', '0000-00-00 00:00:00'),
(30, 9, 1, '2016-11-21', '2016-11-22', 2, 'NO', 11, '', '', 0, '', 0, 'approved', 'Test Approve', 0, 'pending', '', '2016-09-17 22:06:17', '0000-00-00 00:00:00'),
(31, 9, 1, '2016-10-18', '2016-10-18', 1, 'YES', 11, '', '', 123, '', 0, 'approved', '', 0, 'pending', '', '2016-10-21 22:27:16', '0000-00-00 00:00:00'),
(32, 10, 1, '2016-10-24', '2016-11-07', 8, 'NO', 11, '', '', 0, '', 0, 'pending', '', 0, 'pending', '', '2016-09-17 22:06:17', '0000-00-00 00:00:00'),
(33, 10, 1, '2016-10-26', '2016-10-30', 3, 'NO', 11, '', '', 0, '', 0, 'pending', '', 0, 'pending', '', '2016-09-17 22:06:17', '0000-00-00 00:00:00'),
(34, 9, 2, '2016-11-23', '2016-11-23', 1, 'YES', 11, '', '', 0, '', 0, 'approved', 'Approved', 1, 'pending', '', '2016-11-15 05:18:53', '0000-00-00 00:00:00'),
(35, 9, 2, '2017-04-12', '2017-04-13', 2, 'NO', 11, '', '', 76666666, '', 0, 'cancelled', 'test cancellaed', 1, 'pending', '', '2017-04-02 20:37:02', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `leave_balance`
--

DROP TABLE IF EXISTS `leave_balance`;
CREATE TABLE IF NOT EXISTS `leave_balance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `leaves_availed` float NOT NULL DEFAULT '0',
  `opening_balance` float NOT NULL DEFAULT '0',
  `year` int(11) NOT NULL,
  `earned_type` enum('automatic','specified','NA') NOT NULL DEFAULT 'automatic',
  `last_updated` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  `updated_by_user_type` varchar(25) NOT NULL,
  `remaining_countable_days` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=67 ;

--
-- Dumping data for table `leave_balance`
--

INSERT INTO `leave_balance` (`id`, `emp_id`, `leave_type_id`, `leaves_availed`, `opening_balance`, `year`, `earned_type`, `last_updated`, `updated_by`, `updated_by_user_type`, `remaining_countable_days`) VALUES
(1, 1, 3, 7, 0, 2016, 'automatic', '0000-00-00 00:00:00', 0, '', 0),
(2, 4, 2, 1, 0, 2016, 'automatic', '0000-00-00 00:00:00', 0, '', 0),
(3, 4, 1, 2, 0, 2016, 'automatic', '0000-00-00 00:00:00', 0, '', 0),
(4, 9, 1, 11.5, 11.62, 2016, 'automatic', '2016-12-01 00:00:00', 0, '', 16),
(5, 9, 2, 10, 16, 2017, 'automatic', '0000-00-00 00:00:00', 0, '', 0),
(6, 9, 3, 2, 14, 2016, 'NA', '0000-00-00 00:00:00', 0, '', 0),
(7, 9, 4, 0, 20, 2016, 'automatic', '0000-00-00 00:00:00', 0, '', 0),
(8, 9, 5, 0, 15, 2016, 'NA', '0000-00-00 00:00:00', 0, '', 0),
(11, 10, 1, 0, 35.56, 2016, 'automatic', '2016-12-01 00:00:00', 0, '', 6),
(12, 10, 2, 0, 0, 2016, 'automatic', '2016-10-28 00:00:00', 0, '', 0),
(13, 10, 3, 0, 0, 2016, 'automatic', '2016-10-28 00:00:00', 0, '', 0),
(14, 10, 4, 0, 0, 2016, 'automatic', '2016-10-28 00:00:00', 0, '', 0),
(15, 10, 5, 0, 0, 2016, 'NA', '2016-10-28 00:00:00', 0, '', 0),
(18, 11, 1, 0, 6.64, 2016, 'automatic', '0000-00-00 00:00:00', 0, '', 0),
(19, 11, 2, 0, 0, 2016, 'automatic', '0000-00-00 00:00:00', 0, '', 0),
(20, 11, 3, 0, 0, 2016, 'automatic', '0000-00-00 00:00:00', 0, '', 0),
(21, 11, 4, 0, 0, 2016, 'automatic', '0000-00-00 00:00:00', 0, '', 0),
(22, 11, 5, 0, 0, 2016, 'automatic', '0000-00-00 00:00:00', 0, '', 0),
(23, 12, 1, 0, 99, 2016, 'NA', '2016-10-20 00:00:00', 0, '', 0),
(24, 12, 2, 0, 6, 2016, 'automatic', '2016-10-31 00:00:00', 0, '', 0),
(25, 12, 3, 0, 8, 2016, 'NA', '2016-10-31 00:00:00', 0, '', 0),
(26, 12, 4, 0, 19, 2016, 'NA', '2016-10-31 00:00:00', 0, '', 0),
(27, 12, 5, 0, 12, 2016, 'NA', '2016-10-31 00:00:00', 0, '', 0),
(28, 13, 1, 0, 0, 2016, 'automatic', '0000-00-00 00:00:00', 0, '', 0),
(29, 13, 2, 0, 0, 2016, 'automatic', '0000-00-00 00:00:00', 0, '', 0),
(30, 13, 3, 0, 0, 2016, 'automatic', '0000-00-00 00:00:00', 0, '', 0),
(31, 13, 4, 0, 0, 2016, 'automatic', '0000-00-00 00:00:00', 0, '', 0),
(32, 13, 5, 0, 0, 2016, 'automatic', '0000-00-00 00:00:00', 0, '', 0),
(33, 13, 1, 2, 12.1111, 2017, 'automatic', '2017-12-13 00:00:00', 0, 'CRON', 0),
(34, 13, 2, 8, 10, 2017, 'automatic', '2017-08-24 01:09:09', 1, 'admin_user', 0),
(35, 13, 3, 0, 0, 2017, 'automatic', '2017-08-24 01:09:09', 1, 'admin_user', 0),
(36, 13, 4, 0, 0, 2017, 'automatic', '2017-08-24 01:09:09', 1, 'admin_user', 0),
(37, 13, 5, 0, 0, 2017, 'automatic', '2017-08-24 01:09:09', 1, 'admin_user', 0),
(38, 9, 1, 11.5, 11.62, 2016, 'automatic', '2016-12-01 00:00:00', 0, '', 16),
(39, 9, 2, 20, 16, 2016, 'automatic', '0000-00-00 00:00:00', 0, '', 0),
(40, 9, 3, 2, 14, 2016, 'NA', '0000-00-00 00:00:00', 0, '', 0),
(41, 9, 4, 0, 20, 2016, 'automatic', '0000-00-00 00:00:00', 0, '', 0),
(42, 9, 5, 0, 15, 2016, 'NA', '0000-00-00 00:00:00', 0, '', 0),
(43, 12, 1, 0, 6, 2017, 'NA', '2017-08-26 16:08:31', 1, 'admin_user', 0),
(44, 12, 2, 0, 13, 2017, 'NA', '2017-08-26 16:08:11', 1, 'admin_user', 0),
(45, 12, 3, 0, 0, 2017, 'automatic', '2017-08-26 13:37:01', 1, 'admin_user', 0),
(46, 12, 4, 0, 0, 2017, 'NA', '2017-08-26 16:07:09', 1, 'admin_user', 0),
(47, 12, 5, 0, 5, 2017, 'automatic', '2017-08-26 16:06:03', 1, 'admin_user', 0),
(48, 10, 1, 0, 17.2111, 2017, 'automatic', '2017-12-13 00:00:00', 1, 'admin_user', 0),
(49, 10, 2, 0, 0, 2017, 'automatic', '2017-08-24 11:00:10', 1, 'admin_user', 0),
(50, 10, 3, 0, 0, 2017, 'automatic', '2017-08-24 11:00:10', 1, 'admin_user', 0),
(51, 10, 4, 0, 0, 2017, 'automatic', '2017-08-24 11:00:10', 1, 'admin_user', 0),
(52, 10, 5, 0, 0, 2017, 'automatic', '2017-08-24 11:00:10', 1, 'admin_user', 0),
(53, 13, 1, 0, 0, 2018, 'automatic', '2018-03-07 23:52:10', 1, 'admin_user', 0),
(54, 13, 2, 0, 0, 2018, 'NA', '2018-03-08 22:54:37', 1, 'admin_user', 0),
(55, 13, 3, 0, 0, 2018, 'NA', '2018-03-08 22:54:37', 1, 'admin_user', 0),
(56, 13, 4, 0, 0, 2018, 'NA', '2018-03-08 22:54:38', 1, 'admin_user', 0),
(57, 13, 5, 0, 0, 2018, 'automatic', '2018-03-07 23:52:10', 1, 'admin_user', 0),
(58, 13, 6, 0, 0, 2018, 'automatic', '2018-03-07 23:52:10', 1, 'admin_user', 0),
(59, 13, 7, 0, 0, 2018, 'NA', '2018-03-08 22:54:38', 1, 'admin_user', 0),
(60, 9, 2, 1, 10, 2018, 'automatic', '0000-00-00 00:00:00', 0, '', 0),
(61, 9, 5, 1, 10, 2018, 'automatic', '0000-00-00 00:00:00', 0, '', 0),
(62, 9, 3, 1, 10, 2018, 'automatic', '0000-00-00 00:00:00', 0, '', 0),
(63, 9, 1, 1, 10, 2018, 'automatic', '0000-00-00 00:00:00', 0, '', 0),
(64, 9, 4, 1, 10, 2018, 'automatic', '0000-00-00 00:00:00', 0, '', 0),
(65, 9, 6, 1, 10, 2018, 'automatic', '0000-00-00 00:00:00', 0, '', 0),
(66, 9, 7, 1, 10, 2018, 'automatic', '0000-00-00 00:00:00', 0, '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

DROP TABLE IF EXISTS `leave_types`;
CREATE TABLE IF NOT EXISTS `leave_types` (
  `leave_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `leave_type` varchar(200) NOT NULL,
  `leave_abbr` varchar(10) NOT NULL,
  `max_allowed` int(11) NOT NULL,
  PRIMARY KEY (`leave_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`leave_type_id`, `leave_type`, `leave_abbr`, `max_allowed`) VALUES
(1, 'Earned Leaves', 'EL', 10),
(2, 'Casual Leaves', 'CL', 10),
(3, 'Sick Leaves', 'SL', 18),
(4, 'Maternity Leaves', 'ML', 18),
(5, 'R Leaves', 'RH', 10),
(6, 'Leave without pay', 'LWP', 10),
(7, 'Compensatory Off', 'comp. off', 10);

-- --------------------------------------------------------

--
-- Table structure for table `local_conveyance_proofs`
--

DROP TABLE IF EXISTS `local_conveyance_proofs`;
CREATE TABLE IF NOT EXISTS `local_conveyance_proofs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conveyance_id` int(11) NOT NULL COMMENT 'Conveyance Request Id from table - "local_conveyance_requests"',
  `comments` text NOT NULL,
  `attachment` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `local_conveyance_proofs`
--

INSERT INTO `local_conveyance_proofs` (`id`, `conveyance_id`, `comments`, `attachment`) VALUES
(1, 1, 'Test comments', 'e-commerce2.jpg'),
(2, 3, 'Test Image 123', ''),
(3, 3, 'Test Image 12', ''),
(4, 3, 'Test Image 3', ''),
(5, 3, 'test 2', '10394601_670213519762998_7328022604981924456_n.jpg'),
(6, 4, 'test comments', '27595249_1477257258099.pdf'),
(7, 5, 'test  d', 'BSNL.JPg'),
(8, 8, 'r', '10394601_670213519762998_7328022604981924456_n.jpg'),
(9, 7, 'Test', '');

-- --------------------------------------------------------

--
-- Table structure for table `local_conveyance_requests`
--

DROP TABLE IF EXISTS `local_conveyance_requests`;
CREATE TABLE IF NOT EXISTS `local_conveyance_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL COMMENT 'ID of employee (Refers to "employees" table)',
  `type` enum('Advance','Reimbursement') NOT NULL,
  `date` date NOT NULL,
  `from` varchar(255) NOT NULL,
  `to` varchar(255) NOT NULL,
  `paid_by` enum('Company','Self') NOT NULL,
  `purpose` text NOT NULL,
  `travel_mode` varchar(255) NOT NULL,
  `amount` float NOT NULL,
  `start_meter_reading` float NOT NULL,
  `end_meter_reading` float NOT NULL,
  `total_kms` float NOT NULL,
  `approve_status_HOD1` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `HOD1_comment` text NOT NULL,
  `approved_by_HOD1_id` int(11) NOT NULL DEFAULT '0',
  `approve_status_HOD2` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `HOD2_comment` text NOT NULL,
  `approve_status_finance` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `finance_comment` text NOT NULL,
  `approved_by_finance_id` int(11) DEFAULT '0',
  `amount_deposited` float NOT NULL,
  `created_date` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `local_conveyance_requests`
--

INSERT INTO `local_conveyance_requests` (`id`, `emp_id`, `type`, `date`, `from`, `to`, `paid_by`, `purpose`, `travel_mode`, `amount`, `start_meter_reading`, `end_meter_reading`, `total_kms`, `approve_status_HOD1`, `HOD1_comment`, `approved_by_HOD1_id`, `approve_status_HOD2`, `HOD2_comment`, `approve_status_finance`, `finance_comment`, `approved_by_finance_id`, `amount_deposited`, `created_date`, `last_updated`) VALUES
(1, 9, 'Reimbursement', '2016-09-02', 'Gurgaon', 'Lodhi Road', 'Self', 'Meeting with Client', 'Own Car', 2000, 1200, 1242.4, 42.4, 'approved', '', 0, 'pending', '', 'approved', 'approved.', 0, 0, '2016-09-04 07:25:42', '0000-00-00 00:00:00'),
(3, 9, 'Advance', '2016-11-18', 'Gurgaon Sector 15', 'Cannught Palace B1', 'Company', 'Company Work 1', 'Own Car', 1200, 12061, 12070, 9, 'approved', '', 0, 'pending', '', 'rejected', '', 0, 0, '2016-10-11 07:26:30', '2016-10-11 22:03:27'),
(4, 9, 'Advance', '2016-11-02', 'Gurgaon', 'Lodhi Road', 'Company', 'test ing', 'tst', 76, 0, 0, 0, 'pending', '', 0, 'pending', '', 'pending', '', 0, 0, '2016-11-15 20:32:10', '2016-11-15 20:43:10'),
(5, 9, 'Advance', '2016-11-23', 'Gurgaon', 'Lodhi Road', 'Company', 'Test purpose', 'Bike', 800, 0, 0, 0, 'pending', '', 0, 'pending', '', 'pending', '', 0, 0, '2016-11-16 18:39:53', '2016-11-16 18:49:15'),
(6, 9, 'Advance', '2016-11-15', 'Gurgaon Sector 15', 'Lodhi Road', 'Company', 'Testing', 'Bike', 900, 0, 0, 0, 'pending', '', 0, 'pending', '', 'pending', '', 0, 0, '2016-11-18 05:27:42', '0000-00-00 00:00:00'),
(7, 9, 'Advance', '2016-12-15', 'Gurgaon', 'Lodhi Road', 'Company', 'Test', 'Test', 700, 12, 19, 7, 'pending', '', 0, 'pending', '', 'pending', '', 0, 0, '2016-12-26 20:36:18', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `status`) VALUES
(1, 'superadmin', 1),
(4, 'HOD', 1),
(5, 'Requestor', 1);

-- --------------------------------------------------------

--
-- Table structure for table `role_details`
--

DROP TABLE IF EXISTS `role_details`;
CREATE TABLE IF NOT EXISTS `role_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL COMMENT 'Refers to the ID of table "roles"',
  `feature_id` int(11) NOT NULL COMMENT 'Refers to the ID of table "features"',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=224 ;

--
-- Dumping data for table `role_details`
--

INSERT INTO `role_details` (`id`, `role_id`, `feature_id`) VALUES
(49, 3, 1),
(50, 3, 4),
(51, 3, 5),
(52, 3, 2),
(53, 3, 3),
(54, 4, 1),
(55, 4, 4),
(56, 4, 5),
(57, 4, 2),
(58, 4, 3),
(199, 5, 4),
(200, 5, 17),
(201, 5, 19),
(202, 5, 2),
(203, 5, 15),
(204, 1, 1),
(205, 1, 4),
(206, 1, 5),
(207, 1, 17),
(208, 1, 18),
(209, 1, 3),
(210, 1, 2),
(211, 1, 14),
(212, 1, 13),
(213, 1, 12),
(214, 1, 11),
(215, 1, 10),
(216, 1, 9),
(217, 1, 8),
(218, 1, 7),
(219, 1, 6),
(220, 1, 19),
(221, 1, 15),
(222, 1, 16),
(223, 4, 18);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `service_id` int(11) NOT NULL AUTO_INCREMENT,
  `service_name` varchar(255) NOT NULL,
  `display_order` int(11) NOT NULL,
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `service_name`, `display_order`) VALUES
(1, 'Manage Registrations', 2),
(2, 'Manage Leaves', 3),
(3, 'Manage Tours', 4),
(4, 'General Set-up', 1),
(5, 'Manage Users Roles', 6),
(6, 'Manage Activity', 5);

-- --------------------------------------------------------

--
-- Table structure for table `tour_requests`
--

DROP TABLE IF EXISTS `tour_requests`;
CREATE TABLE IF NOT EXISTS `tour_requests` (
  `tour_id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL COMMENT 'ID of employee (Refers to "employees" table)',
  `tour_start_date` datetime NOT NULL,
  `tour_end_date` datetime NOT NULL,
  `tour_place` varchar(255) NOT NULL,
  `tour_customer` varchar(255) NOT NULL,
  `tour_transport` varchar(255) NOT NULL,
  `tour_purpose` text NOT NULL,
  `reservation_start_date` date NOT NULL,
  `reservation_return_date` date NOT NULL,
  `reservation_from` varchar(255) NOT NULL,
  `reservation_to` varchar(255) NOT NULL,
  `reservation_mode` varchar(255) NOT NULL,
  `reservation_details` text NOT NULL,
  `tickets_purchased_by` enum('Company','Self') NOT NULL,
  `tickets_purchased_by_amount` float NOT NULL,
  `hotel_accommodation_by` enum('Company','Self') NOT NULL,
  `hotel_accommodation_by_amount` float NOT NULL,
  `local_conveyance_paid_by` enum('Company','Self') NOT NULL,
  `local_conveyance_amount` float NOT NULL,
  `required_da_amount_perday` float NOT NULL,
  `total_days` int(11) NOT NULL,
  `total_da_amount` float NOT NULL,
  `remarks` text NOT NULL,
  `approve_status_HOD1` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `HOD1_comment` text NOT NULL,
  `approved_by_HOD1_id` int(11) NOT NULL DEFAULT '0',
  `approve_status_HOD2` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `HOD2_comment` text NOT NULL,
  `approve_status_finance` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `finance_comment` text NOT NULL,
  `approved_by_finance_id` int(11) DEFAULT '0',
  `amount_deposited` float NOT NULL,
  `created_date` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`tour_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `tour_requests`
--

INSERT INTO `tour_requests` (`tour_id`, `emp_id`, `tour_start_date`, `tour_end_date`, `tour_place`, `tour_customer`, `tour_transport`, `tour_purpose`, `reservation_start_date`, `reservation_return_date`, `reservation_from`, `reservation_to`, `reservation_mode`, `reservation_details`, `tickets_purchased_by`, `tickets_purchased_by_amount`, `hotel_accommodation_by`, `hotel_accommodation_by_amount`, `local_conveyance_paid_by`, `local_conveyance_amount`, `required_da_amount_perday`, `total_days`, `total_da_amount`, `remarks`, `approve_status_HOD1`, `HOD1_comment`, `approved_by_HOD1_id`, `approve_status_HOD2`, `HOD2_comment`, `approve_status_finance`, `finance_comment`, `approved_by_finance_id`, `amount_deposited`, `created_date`, `last_updated`) VALUES
(2, 9, '2016-10-10 00:00:00', '2016-10-12 00:00:00', 'Banglore', 'Mr. Akshat Jain', 'Train', 'Test Meeting''s', '2016-10-10', '2016-10-13', 'New Delhi', 'Banglore', 'Test Mode 1', 'Flight', 'Company', 6000, 'Self', 9000, 'Company', 3000, 0, 0, 0, '', 'approved', '', 0, 'pending', '', 'approved', 'approved', 7, 0, '2016-08-27 22:00:49', '2016-08-28 08:53:57'),
(7, 9, '2016-09-28 00:00:00', '2016-09-30 00:00:00', 'Banglore', 'Miss Disha Jain', 'Flight', 'Customer Visit', '2016-09-28', '2016-09-30', 'Gurgaon', 'Banglore', 'Flight', 'Hotel Leela', 'Self', 10000, 'Self', 5000, 'Company', 2500, 300, 2, 600, '', 'approved', '', 0, 'pending', '', 'rejected', '', 0, 0, '2016-09-15 04:46:28', '2016-09-15 04:48:12'),
(8, 9, '2016-09-05 00:00:00', '2016-09-15 00:00:00', 'Mumbai', 'Miss Disha Jain', 'Train', 'Test New', '0000-00-00', '0000-00-00', '', '', '', '', 'Self', 100, 'Company', 5000, '', 0, 300, 10, 3000, '', 'pending', 'test comments super', 1, 'pending', '', 'pending', '', 0, 0, '2016-09-24 20:04:12', '2016-09-25 07:53:04'),
(9, 9, '2016-10-21 00:00:00', '2016-10-21 00:00:00', 'Banglore', 'Miss Disha Jain', 'Train', 'Test', '0000-00-00', '0000-00-00', '', '', '', '', '', 0, 'Company', 2000, 'Company', 3000, 340, 1, 340, '', 'approved', 'test', 0, 'pending', '', 'approved', 'Finance comments', 0, 0, '2016-10-05 21:50:28', '2016-10-05 22:50:15'),
(10, 9, '2016-10-26 00:00:00', '2016-10-26 00:00:00', 'Banglore', 'Miss Disha Jain', 'Bus', 'mb m', '0000-00-00', '0000-00-00', '', '', '', '', 'Self', 1000, 'Self', 1000, 'Self', 2, 200, 1, 200, 'Test remarks 21', 'pending', '', 0, 'pending', '', 'pending', '', 0, 0, '2016-10-16 12:22:44', '2016-10-29 22:44:30'),
(11, 9, '2016-12-07 00:00:00', '2016-12-07 00:00:00', 'Mumbai', 'Mr. Akshat Jain', 'Bus', 'Test', '0000-00-00', '0000-00-00', '', '', '', '', 'Company', 2000, 'Self', 3000, 'Self', 2000, 122, 1, 122, '', 'approved', 'Again Approved', 1, 'pending', '', 'approved', 'APproved From finance', 4, 0, '2016-12-26 20:41:08', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `tour_res_details`
--

DROP TABLE IF EXISTS `tour_res_details`;
CREATE TABLE IF NOT EXISTS `tour_res_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) NOT NULL COMMENT 'Refers to the "tour_id" of table "tour_requests"',
  `reservation_start_date` date NOT NULL,
  `reservation_return_date` date NOT NULL,
  `reservation_from` varchar(255) NOT NULL,
  `reservation_to` varchar(255) NOT NULL,
  `reservation_mode` varchar(255) NOT NULL,
  `reservation_details` text NOT NULL,
  `created_date` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `tour_res_details`
--

INSERT INTO `tour_res_details` (`id`, `tour_id`, `reservation_start_date`, `reservation_return_date`, `reservation_from`, `reservation_to`, `reservation_mode`, `reservation_details`, `created_date`, `last_updated`) VALUES
(1, 8, '2016-09-07', '2016-09-15', 'Jaipur', 'Chandigarh', 'Taxi', 'New ', '2016-09-24 20:09:45', '2016-09-25 07:53:04'),
(2, 8, '2016-09-13', '2016-09-13', 'Jaipur', 'Jodhpur', 'Bus', 'new details', '2016-09-24 20:18:52', '2016-09-25 07:53:04'),
(3, 8, '2016-09-21', '2016-09-29', '', '', 'Flight', 'New New', '2016-09-24 21:46:05', '2016-09-25 07:53:04'),
(4, 9, '2016-10-13', '2016-10-13', 'Jaipur', 'Delhi', 'Bus', 'Test 2', '2016-10-05 21:50:29', '2016-10-05 22:50:15'),
(5, 10, '2016-10-14', '2016-10-14', 's', 'd', 'Bus', ', ', '2016-10-16 12:22:44', '2016-10-29 22:44:30'),
(6, 11, '2016-12-15', '2016-12-15', 'Jaipur', 'Delhi', 'Train', 'Test', '2016-12-26 20:41:08', '0000-00-00 00:00:00');

DELIMITER $$
--
-- Events
--
DROP EVENT `table_maintain_customer`$$
CREATE DEFINER=`root`@`localhost` EVENT `table_maintain_customer` ON SCHEDULE EVERY 1 MONTH STARTS '2015-09-17 05:00:00' ON COMPLETION NOT PRESERVE ENABLE DO ANALYZE TABLE `customer`$$

DELIMITER ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
