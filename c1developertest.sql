-- phpMyAdmin SQL Dump
-- version 2.11.11.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 24, 2015 at 12:50 AM
-- Server version: 5.1.73
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `c1developertest`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresscache`
--

CREATE TABLE IF NOT EXISTS `addresscache` (
  `searched_address1` varchar(200) DEFAULT NULL,
  `searched_city` varchar(50) DEFAULT NULL,
  `searched_state` varchar(20) DEFAULT NULL,
  `validated_address1` varchar(200) DEFAULT NULL,
  `validated_city` varchar(50) DEFAULT NULL,
  `validated_state` varchar(20) DEFAULT NULL,
  `validated_zip` varchar(20) DEFAULT NULL,
  `error_status` varchar(50) DEFAULT NULL,
  UNIQUE KEY `searched_address1` (`searched_address1`,`searched_city`,`searched_state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


