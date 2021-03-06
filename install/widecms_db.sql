CREATE DATABASE  IF NOT EXISTS `[[database]]` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `[[database]]`;
-- MySQL dump 10.13  Distrib 5.5.49, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: cmswide
-- ------------------------------------------------------
-- Server version	5.5.49-0ubuntu0.14.04.1

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
-- Table structure for table `wd_files`
--

DROP TABLE IF EXISTS `wd_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wd_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file` varchar(100) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `thumbnails` varchar(4000) DEFAULT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wd_files`
--

LOCK TABLES `wd_files` WRITE;
/*!40000 ALTER TABLE `wd_files` DISABLE KEYS */;
INSERT INTO `wd_files` VALUES (1,'-love-linux.png','Perfil',NULL, NULL);
/*!40000 ALTER TABLE `wd_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wd_history`
--

DROP TABLE IF EXISTS `wd_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wd_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` varchar(255) NOT NULL,
  `app` varchar(45) DEFAULT NULL,
  `fk_user` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_wd_history_1_idx` (`fk_user`),
  CONSTRAINT `fk_wd_history_1` FOREIGN KEY (`fk_user`) REFERENCES `wd_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wd_users`
--

DROP TABLE IF EXISTS `wd_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wd_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(45) CHARACTER SET utf8 NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 NOT NULL,
  `password` varchar(100) CHARACTER SET utf8 NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '0 = disabled\n1 = enabled\n2 = root',
  `name` varchar(45) CHARACTER SET utf8 NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `image` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `dev_mode` int(11) NOT NULL DEFAULT '0' COMMENT '1 = true\n0 = false',
  `allow_dev` int(11) NOT NULL DEFAULT '0',
  `root` int(11) NOT NULL DEFAULT '0',
  `about` varchar(255) DEFAULT NULL,
  `recovery_token` varchar(255) DEFAULT NULL,
  `limit_recovery_token` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wd_users`
--

LOCK TABLES `wd_users` WRITE;
/*!40000 ALTER TABLE `wd_users` DISABLE KEYS */;
INSERT INTO `wd_users` VALUES (1,'[[login_user]]','[[email_user]]','[[password_user]]',1,'[[name_user]]','',now(),'-love-linux.png',1,1,1,'','','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `wd_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wd_users_perm`
--

DROP TABLE IF EXISTS `wd_users_perm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wd_users_perm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app` varchar(200) NOT NULL,
  `fk_user` int(11) NOT NULL,
  `page` varchar(100) DEFAULT NULL,
  `method` varchar(100) DEFAULT NULL,
  `status` int(11) NOT NULL COMMENT '1 = yes\n0 = no',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_wd_nav_perm_1_idx` (`app`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-06-26 19:30:02
