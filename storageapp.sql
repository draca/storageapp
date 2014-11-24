-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 24, 2014 at 08:39 AM
-- Server version: 5.5.24-log
-- PHP Version: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `forshaga_storage`
--

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

CREATE TABLE IF NOT EXISTS `attributes` (
  `ID` int(11) NOT NULL,
  `Object_id` int(11) NOT NULL,
  `Name` text NOT NULL,
  `Discription` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Object_id` (`Object_id`),
  KEY `Object_id_2` (`Object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `objects`
--

INSERT INTO `objects` (`ID`, `Type_id`, `Sum`, `Instorage`, `Condition_id`, `Assembly`, `Discription`, `Location_id`, `Lastchange`, `Changeby`) VALUES
(1, 1, 10, 10, 1, 0, 'Höga stolar', 1, '2014-11-18 00:00:00', '1'),
(2, 1, 30, 15, 1, 0, '', 1, '2014-11-18 00:00:00', '1');

-- --------------------------------------------------------

--
-- Table structure for table `pictures`
--

CREATE TABLE IF NOT EXISTS `pictures` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Object_id` int(11) NOT NULL,
  `Data` text NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Object_id` (`Object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `user` int(11) NOT NULL,
  `Token` text NOT NULL,
  `Lastseen` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`user`, `Token`, `Lastseen`) VALUES
(0, 'af5945a39da11016dcce207223c336835f828c7571fb4f50bbaccc924b8f7209', '0000-00-00 00:00:00'),
(0, '768d2353dac46f69bdea00d846bd24e9acef75da25c26140bb2b8592805a1c2c', '0000-00-00 00:00:00'),
(0, '548c13a691c797ff27235ecf0e40e7a395ddb0e72fd8debe188b26f972da1881', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `types`
--

CREATE TABLE IF NOT EXISTS `types` (
  `ID` int(11) NOT NULL,
  `Name` text NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `Access` int(11) NOT NULL,
  `Email` text NOT NULL,
  PRIMARY KEY (`Username`),
  KEY `Username` (`Username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`Username`, `Name`, `Password`, `Salt`, `Access`, `Email`) VALUES
('admin', '', 'ef2d127de37b942baad06145e54b0c619a1f22327b2ebbcfbec78f5564afe39d', 'd3e8eeffda34c15c7e4221b83105a0eac538c747fb8a7cd4c5b4a9a4831948e9', 1, 'test@test.se');

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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
