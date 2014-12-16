-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 12, 2014 at 01:50 PM
-- Server version: 5.5.24-log
-- PHP Version: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `storageapp`
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `attributes`
--

INSERT INTO `attributes` (`ID`, `Object_id`, `Name`, `Value`) VALUES
(1, 3, 'Sithojd', '1m'),
(2, 3, 'Farg', 'Vit'),
(3, 3, 'Bredd', '30cm'),
(4, 3, 'Bredd', '30cm');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `conditions`
--

INSERT INTO `conditions` (`ID`, `Name`, `Value`, `Discription`) VALUES
(1, 'Ny skick', 100, 'Möbeln är nästan ny'),
(2, 'Sliten', 80, 'MÃ¶beln Ã¤r sliten');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`ID`, `Name`, `City`, `Adress`, `Postal`, `Lastchange`, `Changeby`) VALUES
(1, 'Lintjärn S1', 'Forshaga', 'Europavägen 10', 66733, '2014-11-18 00:00:00', '1');

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
  `Discription` text CHARACTER SET ucs2 NOT NULL,
  `Location_id` int(11) NOT NULL,
  `Lastchange` datetime NOT NULL,
  `Changeby` varchar(100) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_2` (`ID`),
  KEY `Location_id` (`Location_id`),
  KEY `Condition_id` (`Condition_id`),
  KEY `Type_id` (`Type_id`),
  KEY `ID` (`ID`),
  KEY `Changeby` (`Changeby`),
  KEY `Changeby_2` (`Changeby`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `objects`
--

INSERT INTO `objects` (`ID`, `Type_id`, `Sum`, `Instorage`, `Condition_id`, `Assembly`, `Discription`, `Location_id`, `Lastchange`, `Changeby`) VALUES
(1, 1, 10, 10, 1, 0, 'Höga stolar', 1, '2014-11-18 00:00:00', '1'),
(2, 1, 30, 15, 1, 0, 'åäö', 1, '2014-11-18 00:00:00', '1'),
(3, 1, 40, 15, 1, 0, 'Ã¤r det text', 1, '2014-11-18 00:00:00', '1'),
(4, 1, 40, 15, 1, 0, 'Ã¤r det text', 1, '2014-11-18 00:00:00', '1');

-- --------------------------------------------------------

--
-- Table structure for table `pictures`
--

CREATE TABLE IF NOT EXISTS `pictures` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Object_id` int(11) NOT NULL,
  `Mime` tinytext CHARACTER SET utf8 NOT NULL,
  `Data` longblob NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Object_id` (`Object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`user`, `Token`, `Lastseen`) VALUES
('admin', '73c4e25849aef2a5e77fbdaa224e31c2e36280e51b0c0f0443bd44a2008d154d', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `types`
--

CREATE TABLE IF NOT EXISTS `types` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `types`
--

INSERT INTO `types` (`ID`, `Name`) VALUES
(1, 'Stol');

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
  ADD CONSTRAINT `attributes_ibfk_1` FOREIGN KEY (`Object_id`) REFERENCES `objects` (`ID`);

--
-- Constraints for table `objects`
--
ALTER TABLE `objects`
  ADD CONSTRAINT `objects_ibfk_1` FOREIGN KEY (`Type_id`) REFERENCES `types` (`ID`),
  ADD CONSTRAINT `objects_ibfk_2` FOREIGN KEY (`Condition_id`) REFERENCES `conditions` (`ID`),
  ADD CONSTRAINT `objects_ibfk_4` FOREIGN KEY (`Location_id`) REFERENCES `locations` (`ID`);

--
-- Constraints for table `pictures`
--
ALTER TABLE `pictures`
  ADD CONSTRAINT `pictures_ibfk_1` FOREIGN KEY (`Object_id`) REFERENCES `objects` (`ID`);

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`Username`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
