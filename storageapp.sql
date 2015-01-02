-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 02, 2015 at 01:12 PM
-- Server version: 5.5.24-log
-- PHP Version: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `livestorageapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

CREATE TABLE IF NOT EXISTS `attributes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Object_id` int(11) NOT NULL,
  `Name` text CHARACTER SET utf8 NOT NULL,
  `Value` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Object_id` (`Object_id`),
  KEY `Object_id_2` (`Object_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `conditions`
--

CREATE TABLE IF NOT EXISTS `conditions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  `Value` int(11) NOT NULL,
  `Discription` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `conditions`
--

INSERT INTO `conditions` (`ID`, `Name`, `Value`, `Discription`) VALUES
(8, 'Standard', 0, 'Standard vÃ¤rde');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE IF NOT EXISTS `locations` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  `City` text NOT NULL,
  `Adress` text NOT NULL,
  `Postal` int(11) NOT NULL,
  `Lastchange` datetime NOT NULL,
  `Changeby` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_3` (`ID`),
  KEY `ID` (`ID`),
  KEY `Changeby` (`Changeby`),
  KEY `ID_2` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`ID`, `Name`, `City`, `Adress`, `Postal`, `Lastchange`, `Changeby`) VALUES
(8, 'Standard plats', '', '', 0, '2015-01-02 13:11:30', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `objects`
--

CREATE TABLE IF NOT EXISTS `objects` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Type_id` int(11) NOT NULL,
  `Sum` int(11) NOT NULL,
  `Instorage` int(11) NOT NULL,
  `Condition_id` int(11) NOT NULL,
  `Assembly` int(11) NOT NULL,
  `Discription` text NOT NULL,
  `Location_id` int(11) NOT NULL,
  `Lastchange` datetime NOT NULL,
  `Changeby` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_2` (`ID`),
  KEY `Location_id` (`Location_id`),
  KEY `Condition_id` (`Condition_id`),
  KEY `Type_id` (`Type_id`),
  KEY `ID` (`ID`),
  KEY `Changeby` (`Changeby`),
  KEY `Changeby_2` (`Changeby`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `pictures`
--

CREATE TABLE IF NOT EXISTS `pictures` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Object_id` int(11) NOT NULL,
  `Mime` tinytext CHARACTER SET utf8,
  `Data` longblob NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Object_id` (`Object_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `user` varchar(100) NOT NULL,
  `Token` text NOT NULL,
  `Lastseen` datetime NOT NULL,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `types`
--

CREATE TABLE IF NOT EXISTS `types` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `types`
--

INSERT INTO `types` (`ID`, `Name`) VALUES
(7, 'Standard');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `Username` varchar(100) NOT NULL,
  `Name` text NOT NULL,
  `Password` text NOT NULL,
  `Salt` text NOT NULL,
  `Access` int(11) NOT NULL DEFAULT '1',
  `Email` text NOT NULL,
  PRIMARY KEY (`Username`),
  KEY `Username` (`Username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`Username`, `Name`, `Password`, `Salt`, `Access`, `Email`) VALUES
('admin', 'Admin Admin', 'd3c19f2db47d799dfe0c6f6e0fff83e686c94b60aa603824f76188046edbdb18', 'd3e8eeffda34c15c7e4221b83105a0eac538c747fb8a7cd4c5b4a9a4831948e9', 4, 'test@test.se');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attributes`
--
ALTER TABLE `attributes`
  ADD CONSTRAINT `attributes_ibfk_2` FOREIGN KEY (`Object_id`) REFERENCES `objects` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `locations`
--
ALTER TABLE `locations`
  ADD CONSTRAINT `locations_ibfk_2` FOREIGN KEY (`Changeby`) REFERENCES `users` (`Username`);

--
-- Constraints for table `objects`
--
ALTER TABLE `objects`
  ADD CONSTRAINT `objects_ibfk_1` FOREIGN KEY (`Type_id`) REFERENCES `types` (`ID`),
  ADD CONSTRAINT `objects_ibfk_2` FOREIGN KEY (`Condition_id`) REFERENCES `conditions` (`ID`),
  ADD CONSTRAINT `objects_ibfk_4` FOREIGN KEY (`Location_id`) REFERENCES `locations` (`ID`),
  ADD CONSTRAINT `objects_ibfk_5` FOREIGN KEY (`Changeby`) REFERENCES `users` (`Username`);

--
-- Constraints for table `pictures`
--
ALTER TABLE `pictures`
  ADD CONSTRAINT `pictures_ibfk_2` FOREIGN KEY (`Object_id`) REFERENCES `objects` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`Username`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
