-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 17, 2019 at 05:42 AM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 7.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `isibelipay_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `balance`
--

CREATE TABLE `balance` (
  `userid` int(11) NOT NULL,
  `balance` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `datelog` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `txid` varchar(11) NOT NULL,
  `txfrom` int(11) NOT NULL,
  `txto` int(11) NOT NULL,
  `txmuch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `wd_history`
--

CREATE TABLE `wd_history` (
  `datelog` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `wdid` varchar(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `wdwallet` varchar(34) NOT NULL,
  `wdmuch` int(11) NOT NULL,
  `wdtxid` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `balance`
--
ALTER TABLE `balance`
  ADD PRIMARY KEY (`userid`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`txid`),
  ADD KEY `txfrom` (`txfrom`),
  ADD KEY `txto` (`txto`);

--
-- Indexes for table `wd_history`
--
ALTER TABLE `wd_history`
  ADD PRIMARY KEY (`wdid`),
  ADD UNIQUE KEY `wd_txid` (`wdtxid`),
  ADD KEY `userid` (`userid`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `history`
--
ALTER TABLE `history`
  ADD CONSTRAINT `history_ibfk_1` FOREIGN KEY (`txfrom`) REFERENCES `balance` (`userid`),
  ADD CONSTRAINT `history_ibfk_2` FOREIGN KEY (`txto`) REFERENCES `balance` (`userid`);

--
-- Constraints for table `wd_history`
--
ALTER TABLE `wd_history`
  ADD CONSTRAINT `wd_history_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `balance` (`userid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
