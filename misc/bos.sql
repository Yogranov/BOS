-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 11, 2021 at 07:35 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bos`
--

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `Id` int(11) NOT NULL COMMENT 'Client ID',
  `FirstName` text NOT NULL COMMENT 'Client First Name',
  `LastName` varchar(30) NOT NULL COMMENT 'Client Last Name',
  `PhoneNumber` varchar(12) NOT NULL COMMENT 'Client Phone Number',
  `Email` varchar(50) DEFAULT NULL COMMENT 'Client Email'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cookies`
--

CREATE TABLE `cookies` (
  `Id` int(11) NOT NULL,
  `UserId` int(3) NOT NULL,
  `Hash` varchar(64) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `families`
--

CREATE TABLE `families` (
  `Id` int(11) NOT NULL,
  `Name` varchar(60) CHARACTER SET utf8 NOT NULL,
  `Responsible` varchar(6) CHARACTER SET utf8 NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `monolog`
--

CREATE TABLE `monolog` (
  `channel` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `message` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `time` int(10) UNSIGNED DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `OrderId` int(11) NOT NULL COMMENT 'Order ID (PK)',
  `ClientId` int(11) NOT NULL COMMENT 'Client Id (FK)',
  `ShopId` int(4) NOT NULL,
  `SellerId` int(11) NOT NULL COMMENT 'Seller Id (FK)',
  `products` text DEFAULT NULL,
  `Remarks` text DEFAULT NULL COMMENT 'Order Remarks',
  `Timestamp` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Order Open Time',
  `Email` text DEFAULT NULL,
  `LastStatusUpdateTimestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `Barcode` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Name` text COLLATE utf8_unicode_ci NOT NULL,
  `Remark` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `Family` tinyint(2) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `regions`
--

CREATE TABLE `regions` (
  `Id` int(11) NOT NULL,
  `Name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Password` varchar(60) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Region Password',
  `Manager` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reminders`
--

CREATE TABLE `reminders` (
  `Id` int(11) NOT NULL COMMENT 'Reminder Id',
  `Remind` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Remind',
  `Timestamp` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Reminder  Time',
  `Seller` varchar(5) NOT NULL COMMENT 'Reminder Seller',
  `Shop` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sellers`
--

CREATE TABLE `sellers` (
  `Id` int(11) NOT NULL COMMENT 'Seller Id',
  `FirstName` varchar(20) NOT NULL COMMENT 'Seller First Name',
  `LastName` varchar(30) NOT NULL COMMENT 'Seller Last Name',
  `ShopId` int(11) NOT NULL COMMENT 'Seller Shop ID',
  `Email` varchar(50) DEFAULT NULL COMMENT 'Seller Email',
  `Status` enum('1','2') NOT NULL DEFAULT '1' COMMENT 'Seller Status'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shops`
--

CREATE TABLE `shops` (
  `Id` int(3) NOT NULL COMMENT 'Shop ID Primary',
  `Password` varchar(40) NOT NULL COMMENT 'Shop user password',
  `Name` varchar(20) NOT NULL COMMENT 'Shop Name',
  `Location` varchar(50) NOT NULL COMMENT 'Shop Location',
  `PhoneNumber` varchar(12) NOT NULL COMMENT 'Shop Number',
  `Manager` int(11) DEFAULT NULL COMMENT 'Shop Manager',
  `Email` varchar(50) NOT NULL COMMENT 'Shop Email',
  `Region` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `cookies`
--
ALTER TABLE `cookies`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `families`
--
ALTER TABLE `families`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`OrderId`),
  ADD KEY `Client ID` (`ClientId`),
  ADD KEY `Seller ID` (`SellerId`),
  ADD KEY `ShopId` (`ShopId`) USING BTREE;

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`Barcode`);

--
-- Indexes for table `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `regions_Id_uindex` (`Id`);

--
-- Indexes for table `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `SellerId` (`Seller`),
  ADD KEY `Shop Id` (`Shop`);

--
-- Indexes for table `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `SellerId` (`Id`),
  ADD KEY `ShopId` (`ShopId`);

--
-- Indexes for table `shops`
--
ALTER TABLE `shops`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `Id` (`Id`),
  ADD KEY `Manager` (`Manager`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Client ID';

--
-- AUTO_INCREMENT for table `cookies`
--
ALTER TABLE `cookies`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `families`
--
ALTER TABLE `families`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `OrderId` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Order ID (PK)';

--
-- AUTO_INCREMENT for table `regions`
--
ALTER TABLE `regions`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reminders`
--
ALTER TABLE `reminders`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Reminder Id';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
