-- Progettazione Web 
DROP DATABASE if exists f1_runner; 
CREATE DATABASE f1_runner; 
USE f1_runner; 
-- MySQL dump 10.13  Distrib 5.7.28, for Win64 (x86_64)
--
-- Host: localhost    Database: f1_runner
-- ------------------------------------------------------
-- Server version	5.7.28

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
-- Table structure for table `cars`
--

DROP TABLE IF EXISTS `cars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `price` int(11) NOT NULL,
  `speed` int(11) NOT NULL,
  `reliability` int(11) NOT NULL,
  `pitcrew` int(11) NOT NULL,
  `img` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cars`
--

LOCK TABLES `cars` WRITE;
/*!40000 ALTER TABLE `cars` DISABLE KEYS */;
INSERT INTO `cars` VALUES (1,'Haas',0,1,1,1,'haas.png'),(2,'Alpine',0,1,1,1,'alpine.png'),(3,'Stake-Sauber',0,1,1,1,'stake.png'),(4,'Racing Bulls',250,2,1,2,'racing_bulls.png'),(5,'Williams',350,2,2,2,'williams.png'),(7,'Aston Martin',500,3,2,2,'aston_martin.png'),(8,'Mercedes Benz',750,3,3,3,'mercedes.png'),(9,'Ferrari',1000,3,4,4,'ferrari.png'),(10,'Red Bull',1250,4,5,4,'red_bull.png'),(11,'McLaren',1500,5,4,4,'mclaren.png');
/*!40000 ALTER TABLE `cars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `drivers`
--

DROP TABLE IF EXISTS `drivers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `drivers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `talent` decimal(3,1) NOT NULL DEFAULT '1.0',
  `img` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `drivers`
--

LOCK TABLES `drivers` WRITE;
/*!40000 ALTER TABLE `drivers` DISABLE KEYS */;
INSERT INTO `drivers` VALUES (1,'Ollie','Bearman',0,1.0,'bearman.png'),(2,'Franco','Colapinto',0,1.0,'colapinto.png'),(3,'Gabriel','Bortoleto',0,1.0,'bortoleto.png'),(4,'Liam','Lawson',25,1.1,'lawson.png'),(5,'Isack','Hadjar',25,1.1,'hadjar.png'),(6,'Estaban','Ocon',50,1.1,'ocon.png'),(7,'Lance','Stroll',50,1.1,'stroll.png'),(8,'Pierre','Gasly',100,1.2,'gasly.png'),(9,'Nico','Hulkenberg',100,1.2,'hulkenberg.png'),(10,'Alexander','Albon',250,1.3,'albon.png'),(11,'Carlos','Sainz',500,1.5,'sainz.png'),(12,'Kimi','Antonelli',500,1.5,'antonelli.png'),(13,'Fernando','Alonso',500,1.5,'alonso.png'),(14,'George','Russell',750,1.6,'russell.png'),(15,'Lando','Norris',750,1.6,'norris.png'),(16,'Oscar','Piastri',1000,1.7,'piastri.png'),(17,'Lewis','Hamilton',1500,1.8,'hamilton.png'),(18,'Charles','Leclerc',2000,2.0,'leclerc.png'),(19,'Max','Verstappen',3000,2.5,'verstappen.png'),(20,'Yuki','Tsunoda',250,1.3,'tsunoda.png');
/*!40000 ALTER TABLE `drivers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `games`
--

DROP TABLE IF EXISTS `games`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `earned_coins` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `games_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `games`
--

LOCK TABLES `games` WRITE;
/*!40000 ALTER TABLE `games` DISABLE KEYS */;
INSERT INTO `games` VALUES (1,1,140,0,'2026-01-22 23:22:52'),(2,1,1260,7,'2026-01-22 23:34:57'),(3,1,140,0,'2026-01-22 23:41:16'),(4,1,24,0,'2026-01-23 00:19:12'),(5,1,2268,13,'2026-01-23 00:34:31'),(6,1,1764,105,'2026-01-23 00:36:52'),(7,1,1680,100,'2026-01-23 00:38:49'),(8,1,308,18,'2026-01-23 00:43:38'),(9,1,308,18,'2026-01-23 00:48:36'),(10,1,560,33,'2026-01-23 00:49:16'),(11,1,140,8,'2026-01-23 00:49:27'),(12,1,224,13,'2026-01-23 00:49:49'),(13,1,308,18,'2026-01-23 00:50:12'),(14,1,224,13,'2026-01-23 00:50:32'),(15,1,252,14,'2026-01-23 00:50:53'),(16,1,196,10,'2026-01-23 00:51:12'),(17,1,392,22,'2026-01-23 00:57:50'),(18,1,1680,100,'2026-01-23 01:16:25'),(19,1,168,0,'2026-01-23 01:20:59'),(20,1,1540,8,'2026-01-23 15:41:24'),(21,1,2604,15,'2026-01-23 15:43:15'),(22,1,4218,30,'2026-01-23 15:55:47'),(23,1,20,0,'2026-01-23 17:47:48'),(24,1,20,0,'2026-01-23 21:47:53'),(25,1,30,0,'2026-01-23 21:48:24'),(26,1,50,0,'2026-01-23 21:50:37'),(27,1,20,0,'2026-01-23 21:50:50'),(28,1,20,0,'2026-01-23 21:51:06'),(29,1,20,0,'2026-01-23 21:51:17'),(30,1,20,0,'2026-01-23 21:51:25'),(31,1,20,0,'2026-01-23 21:57:07'),(32,1,20,0,'2026-01-23 21:57:29'),(33,1,100,1,'2026-01-23 22:03:10'),(34,1,20,0,'2026-01-23 22:06:45'),(35,1,20,0,'2026-01-23 22:06:56'),(36,1,100,1,'2026-01-23 22:07:16'),(37,1,100,1,'2026-01-23 22:07:30'),(38,1,50,0,'2026-01-23 22:14:51'),(39,1,162,1,'2026-01-23 23:23:47'),(40,1,162,1,'2026-01-23 23:26:13'),(42,1,4550,85,'2026-01-24 00:58:16'),(43,1,400,7,'2026-01-24 00:58:47'),(44,1,42,0,'2026-01-24 00:59:14'),(45,1,42,0,'2026-01-24 00:59:24'),(46,1,84,0,'2026-01-24 01:18:14'),(47,1,168,1,'2026-01-24 01:37:31'),(48,1,2700,51,'2026-01-25 18:51:15'),(49,1,2900,55,'2026-01-25 18:52:27'),(50,1,750,13,'2026-01-25 19:16:25'),(51,1,3200,60,'2026-01-25 19:18:04'),(52,1,2850,53,'2026-01-25 19:20:51'),(53,1,600,11,'2026-01-25 19:21:55'),(54,1,3300,62,'2026-01-26 00:45:25'),(55,1,2700,51,'2026-01-26 20:59:39'),(56,1,1100,20,'2026-01-26 21:01:09'),(57,1,1100,20,'2026-01-26 21:03:34'),(58,1,700,13,'2026-01-26 21:31:23'),(59,1,450,7,'2026-01-26 21:31:53'),(60,1,24,0,'2026-01-26 21:32:28'),(61,1,24,0,'2026-01-26 21:32:41'),(62,1,24,0,'2026-01-26 21:32:52'),(65,1,2900,55,'2026-01-26 22:01:50'),(66,1,1050,19,'2026-01-26 22:37:36'),(67,1,400,7,'2026-01-26 22:53:13'),(68,1,24,0,'2026-01-26 22:54:22'),(70,1,24,0,'2026-01-26 22:56:35'),(71,1,24,0,'2026-01-26 22:59:01'),(72,1,24,0,'2026-01-26 23:01:01'),(73,1,24,0,'2026-01-26 23:01:14'),(76,1,1350,24,'2026-01-27 15:14:23'),(77,1,500,9,'2026-01-27 15:15:04'),(78,1,1650,30,'2026-01-27 15:32:29'),(79,1,700,13,'2026-01-27 16:57:51'),(80,1,3050,57,'2026-01-27 20:37:21'),(81,1,2612,49,'2026-01-27 20:59:43'),(82,1,570,9,'2026-01-27 23:34:09'),(83,1,1995,36,'2026-01-27 23:41:32'),(84,1,2470,45,'2026-01-28 20:10:06'),(85,1,997,17,'2026-01-30 18:07:50');
/*!40000 ALTER TABLE `games` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `garage_cars`
--

DROP TABLE IF EXISTS `garage_cars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `garage_cars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_car` int(11) NOT NULL,
  `lvl_speed` int(11) NOT NULL DEFAULT '0',
  `lvl_reliability` int(11) NOT NULL DEFAULT '0',
  `lvl_pitcrew` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `garage_cars_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `garage_cars`
--

LOCK TABLES `garage_cars` WRITE;
/*!40000 ALTER TABLE `garage_cars` DISABLE KEYS */;
INSERT INTO `garage_cars` VALUES (1,1,1,5,5,5),(2,1,10,5,5,5),(3,1,2,1,2,5),(4,1,7,1,1,1),(5,1,9,0,0,0);
/*!40000 ALTER TABLE `garage_cars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `garage_drivers`
--

DROP TABLE IF EXISTS `garage_drivers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `garage_drivers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_driver` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `garage_drivers_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `garage_drivers`
--

LOCK TABLES `garage_drivers` WRITE;
/*!40000 ALTER TABLE `garage_drivers` DISABLE KEYS */;
INSERT INTO `garage_drivers` VALUES (1,1,1),(2,1,19);
/*!40000 ALTER TABLE `garage_drivers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `coins` int(11) NOT NULL DEFAULT '0',
  `current_car_id` int(11) DEFAULT NULL,
  `current_driver_id` int(11) DEFAULT NULL,
  `best_score` int(11) DEFAULT '0',
  `security_question` int(11) DEFAULT '0',
  `security_answer` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin@admin.com','admin','$2y$10$C32UuVZXoW1v0Kf08dRNBen4LipE.ZpamJzFGN6HjNdrAqLdRIgyO',38907,10,19,4550,1,'$2y$10$8jn/knlmyYzfmB2cGKXRLuZbUpRkl7DDhifWBQ4u7VGUxJsfZnnAe'),(2,'player1@player.com','player1','$2y$10$glTx44Z06KluXE3l0jivYOhYfBSA46HFCW3DwcPXw01Ef3AYMPcIy',0,NULL,NULL,0,3,'$2y$10$51SHooiZ58Ml/5rLpX70b.4jwckl5qUgV.n0KB9f5KPhn9rm1f9Aa');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-30 20:19:34
