-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 25, 2013 at 03:47 PM
-- Server version: 5.5.29
-- PHP Version: 5.3.10-1ubuntu3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `fablabbcn`
--

-- --------------------------------------------------------

--
-- Table structure for table `feeds`
--

CREATE TABLE IF NOT EXISTS `feeds` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `descritpion` text COLLATE utf8_unicode_ci NOT NULL,
  `tags` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `exposure` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `latitude` float NOT NULL,
  `longitude` float NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `cosm_id` int(11) DEFAULT NULL,
  `cosm_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `wifi_ssid` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `wifi_pwd` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `feeds`
--

INSERT INTO `feeds` (`id`, `title`, `descritpion`, `tags`, `exposure`, `latitude`, `longitude`, `user_id`, `cosm_id`, `cosm_key`, `wifi_ssid`, `wifi_pwd`, `created`, `modified`) VALUES
(1, 'First Feed', '', '', '', 41.3881, 2.17626, 132, 105189, 'lnxn361AH2oaaBVczmJH6J9ecuOSAKxpMnNNeU0raVR3OD0g', 'Mywifi', 'wifipasword', '2013-02-17 20:08:37', '2013-02-17 20:08:37');

-- --------------------------------------------------------

--
-- Table structure for table `medias`
--

CREATE TABLE IF NOT EXISTS `medias` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ref` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ref_id` int(11) DEFAULT NULL,
  `file` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `position` int(11) DEFAULT '0',
  `modified` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `medias`
--

INSERT INTO `medias` (`id`, `ref`, `ref_id`, `file`, `position`, `modified`, `created`) VALUES
(1, 'User', 31, 'User/1-1.JPG', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 'Post', 3, 'Post/1-dezeen_No_Place_Like_Home_GPS_shoes_by_Dominic_Wilcox_1b.jpg', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 'Post', 21, 'Post/2-smart_citizen_board_big.png', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');


-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8_unicode_ci,
  `user_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `approved` tinyint(2) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `body`, `user_id`, `topic_id`, `media_id`, `approved`, `created`, `modified`) VALUES
(1, 'Gps shoes', '<p>This is really exciting! Not ? Next step for the clothes of the futures or "smart" clothes...</p><p><strong>I hope you enjoyed the post ;-)<br /></strong></p><p><img src="/img/Post/3-dezeen_No_Place_Like_Home_GPS_shoes_by_Dominic_Wilcox_1b.jpg" alt="" width="347" height="347" /></p>', 75, 5, 2, 0, '2012-11-14 17:57:08', '2012-11-15 13:18:19'),
(2, 'Smart Citizen Kit', '<p><img src="/img/Post/21-smart_citizen_board_big.png" alt="" /></p><p>The SCK is a electronic board based on Arduino (en.wikipedia.org/wiki/Arduino), equipped with the following sensors:</p><p>- Air Quality</p><p>- Temperature</p><p>- Sound</p><p>- Humidity</p><p>- Light Quantity</p><p>In addition, the board contains a solar charger that allows to connect it to photovoltaic panels to be installed anywhere. The board is equipped with a WiFi antenna that allows you to upload data from the sensors in real time to the online platform which is being developed withing the project and which will be based in Cosm (cosm.com/, previous Pachube) .</p><p>The Smart Citizen Kit is based on an Arduino shield developed ​​by Fab Lab Barcelona and Hangar. The version that will be funded through this campaign will remain compatible with Arduino, but it will be an autonomous board, which can be programmed from the same development environment (IDE). Improvements will include:</p><p>- Improved sensors calibration</p><p>- Lower power consumption</p><p>- Increased usability in planning and implementation</p><p>Computers have allowed us to create content, and Internet we can share it with. Now is the time to act in the physical world using both tools. Through the development and distribution of urban sensor kits, we will use devices to upload information from the environment to the web (as when we send messages to social networks, or we post videos or images) and thereby visualize and share values ​​such as air or noise pollution from wherever we want.</p>', 1, 3, 3, 0, '2012-11-12 16:59:51', '2012-11-30 17:34:38');

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--

CREATE TABLE IF NOT EXISTS `topics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `topics`
--

INSERT INTO `topics` (`id`, `name`, `created`, `modified`) VALUES
(1, 'News', NULL, '2012-11-12 03:27:26'),
(2, 'Tutorials', '2012-11-12 03:14:27', '2012-11-12 03:14:27'),
(3, 'Hardware', '2012-11-12 03:25:55', '2012-11-14 22:50:37'),
(4, 'Places', '2012-11-12 03:26:21', '2012-11-12 03:26:21'),
(5, 'Miscelaneous', '2012-11-12 03:27:00', '2012-11-12 03:27:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `role` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL,
  `media_id` int(11) DEFAULT NULL,
  `cosm_user` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cosm_token` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `city`, `country`, `website`, `email`, `email_verified`, `media_id`, `cosm_user`, `cosm_token`, `created`, `modified`) VALUES
(1, 'alex', '69cdc152f2f9415bdc0a193b38562c9beaba4bfc', 'admin', 'Barcelona', 'Spain', 'http:///magneticarchitecture.org', 'alexandre.dubor@gmail.com', 1, 1, 'smartcitizen', '######', '2012-09-28 19:41:59', '2013-02-10 15:12:04');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
