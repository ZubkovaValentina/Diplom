-- MySQL dump 10.16  Distrib 10.1.22-MariaDB, for osx10.12 (x86_64)
--
-- Host: localhost    Database: remont_avto
-- ------------------------------------------------------
-- Server version	10.1.22-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `client`
--

DROP TABLE IF EXISTS `client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client` (
  `key_client` int(8) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `mobile_phone` int(11) NOT NULL,
  `series_p` int(10) NOT NULL,
  PRIMARY KEY (`key_client`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detail`
--

DROP TABLE IF EXISTS `detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detail` (                                                          
  `key_detail` int(10) NOT NULL AUTO_INCREMENT,
  `name_detail` varchar(255) NOT NULL,
  `manufacturer` varchar(255) NOT NULL,
  `car_model` varchar(255) DEFAULT NULL,
  `price` int(10) unsigned NOT NULL,
  `kolvo` int(8) NOT NULL,
  `key_provider` int(11) DEFAULT NULL,
  PRIMARY KEY (`key_detail`),
  KEY `key_provider` (`key_provider`),
  CONSTRAINT `detail_ibfk_1` FOREIGN KEY (`key_provider`) REFERENCES `provider` (`key_provider`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employee`
--

DROP TABLE IF EXISTS `employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee` (
  `key_employee` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `INN` int(15) NOT NULL,
  `position` varchar(255) NOT NULL,
  `birthday` date NOT NULL,
  `children` tinyint(3) unsigned NOT NULL,
  `education` varchar(255) NOT NULL,
  `sex` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`key_employee`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invoice`
--

DROP TABLE IF EXISTS `invoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice` (
  `key_invoice` int(10) NOT NULL AUTO_INCREMENT,
  `invoice_number` int(20) NOT NULL,
  `date` date NOT NULL,
  `key_client` int(10) NOT NULL,
  `sum` decimal(10,2) NOT NULL,
  `accepted` char(20) NOT NULL,
  `passed` char(20) NOT NULL,
  `detail` varchar(32) NOT NULL,
  PRIMARY KEY (`key_invoice`),
  KEY `invoice_ibfk_1` (`key_client`),
  CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`key_client`) REFERENCES `client` (`key_client`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `my_order`
--

DROP TABLE IF EXISTS `my_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `my_order` (
  `key_order` int(10) NOT NULL AUTO_INCREMENT,
  `date` datetime NULL,
  `key_client` int(10) DEFAULT NULL,
  `accepted` int(10) unsigned DEFAULT NULL,
  `passed` int(10) unsigned DEFAULT NULL,                    
  PRIMARY KEY (`key_order`),
  KEY `key_client` (`key_client`),
  KEY `accepted` (`accepted`),
  KEY `passed` (`passed`),
  CONSTRAINT `my_order_ibfk_1` FOREIGN KEY (`key_client`) REFERENCES `client` (`key_client`) ON DELETE SET NULL,
  CONSTRAINT `my_order_ibfk_2` FOREIGN KEY (`accepted`) REFERENCES `employee` (`key_employee`) ON DELETE SET NULL,
  CONSTRAINT `my_order_ibfk_3` FOREIGN KEY (`passed`) REFERENCES `employee` (`key_employee`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_detail`
--

DROP TABLE IF EXISTS `order_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_detail` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `key_order` int(10) DEFAULT NULL,
  `key_detail` int(10) DEFAULT NULL,                                               
  PRIMARY KEY (`id`),
  KEY `key_order` (`key_order`),
  KEY `key_detail` (`key_detail`),
  CONSTRAINT `order_detail_ibfk_1` FOREIGN KEY (`key_order`) REFERENCES `my_order` (`key_order`) ON DELETE SET NULL,
  CONSTRAINT `order_detail_ibfk_2` FOREIGN KEY (`key_detail`) REFERENCES `detail` (`key_detail`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_product`
--

DROP TABLE IF EXISTS `order_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_product` (
  `key_order` int(10) NOT NULL AUTO_INCREMENT,
  `order_number` int(20) NOT NULL,
  `date` date NOT NULL,
  `sum` decimal(10,2) NOT NULL,
  `key_client` int(10) NOT NULL,
  `sum_detail` decimal(10,2) NOT NULL,
  `sum_work` decimal(10,2) NOT NULL,
  PRIMARY KEY (`key_order`),
  KEY `order_product_ibfk_1` (`key_client`),
  CONSTRAINT `order_product_ibfk_1` FOREIGN KEY (`key_client`) REFERENCES `client` (`key_client`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_service`
--

DROP TABLE IF EXISTS `order_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_service` (
  `id` int(10) NOT NULL AUTO_INCREMENT,                                           
  `key_order` int(10) DEFAULT NULL,
  `key_service` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `key_order` (`key_order`),
  KEY `key_service` (`key_service`),
  CONSTRAINT `order_service_ibfk_1` FOREIGN KEY (`key_order`) REFERENCES `my_order` (`key_order`) ON DELETE SET NULL,
  CONSTRAINT `order_service_ibfk_2` FOREIGN KEY (`key_service`) REFERENCES `service` (`key_service`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `provider`
--

DROP TABLE IF EXISTS `provider`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `provider` (
  `key_provider` int(11) NOT NULL AUTO_INCREMENT,
  `name_organization` varchar(255) NOT NULL,
  `provider_address` varchar(255) NOT NULL,
  `mobile_phone` int(11) NOT NULL,
  `fax` int(11) NOT NULL,
  `INN` int(10) NOT NULL,
  PRIMARY KEY (`key_provider`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `service`
--

DROP TABLE IF EXISTS `service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service` (
  `key_service` int(10) NOT NULL AUTO_INCREMENT,
  `name_service` varchar(255) NOT NULL,
  `price` int(10) unsigned NOT NULL,
  PRIMARY KEY (`key_service`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-04-18 21:11:27
