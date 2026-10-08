-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: pkts_karate
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Import into InfinityFree database: if0_42734174_pkts_karate
-- Select the database in phpMyAdmin BEFORE importing
-- Do NOT run CREATE DATABASE or USE statements

--
-- Table structure for table `admin_users`
--

DROP TABLE IF EXISTS `admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL,
  `role` varchar(50) DEFAULT 'admin',
  `avatar` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_users`
--

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
INSERT INTO `admin_users` VALUES (1,'admin','$2b$10$nIXW3alRUtVx66bQmAJB3eWtJumnSMaqU63nFfrsvNZlivbnv6T3W','admin@pktskarate.com','Super Admin',NULL,'Active','2026-08-18 05:07:13','2026-07-24 04:00:30'),(3,'Batman_Admin','$2y$10$lhRjktw3kpgcpXTE05hiJe8U4Z8Ycm.yiXUBF0oTOTcVrq2vKALaK','batman@pktskarate.com','Super Admin',NULL,'Active','2026-08-19 06:32:35','2026-08-18 22:32:16'),(4,'dojo_manager','$2y$10$.oX4kgojtEhN0pv4jNF1Ye1KsdJYMCaH23IObeE1wmxwvwbDNjAzC','manager@pktskarate.com','Dojo Manager',NULL,'Active',NULL,'2026-08-19 08:00:31');
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `status` varchar(50) DEFAULT 'unread',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_messages`
--

LOCK TABLES `contact_messages` WRITE;
/*!40000 ALTER TABLE `contact_messages` DISABLE KEYS */;
INSERT INTO `contact_messages` VALUES (4,'Juan Dela Cruz','juan@gmail.com','+63 917 555 1234','Trial Class Schedule Inquiry','Good day Sensei, I would like to ask if you have weekend classes available for my 8 year old son?','unread','2026-08-19 08:00:31'),(5,'Maria Santos','maria.santos@yahoo.com','+63 918 444 9876','Adult Beginners Karate','Hello! Do you offer beginner classes for adults with no prior martial arts background?','read','2026-08-19 08:00:31'),(6,'Carlos Reyes','carlos.reyes@hotmail.com','+63 920 333 5555','Equipment Size Inquiry','Hi, is the Kata Gi Canvas uniform available in 170 CM size at the Pasig dojo branch?','replied','2026-08-19 08:00:31');
/*!40000 ALTER TABLE `contact_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_activity_logs`
--

DROP TABLE IF EXISTS `customer_activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_activity_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int DEFAULT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `activity_type` enum('registration','login','logout') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_activity_logs`
--

LOCK TABLES `customer_activity_logs` WRITE;
/*!40000 ALTER TABLE `customer_activity_logs` DISABLE KEYS */;
INSERT INTO `customer_activity_logs` VALUES (1,1,'John Doe','john@example.com','registration','127.0.0.1','New customer account created','2026-08-17 08:21:38'),(2,1,'John Doe','john@example.com','login','127.0.0.1','Customer logged into portal','2026-08-17 08:21:38'),(3,1,'John Doe','john@example.com','logout','127.0.0.1','Customer logged out','2026-08-17 08:21:38'),(4,2,'Glenn Rabe','glennrabe004@gmail.com','login','::1','Customer logged into portal','2026-08-17 21:40:21');
/*!40000 ALTER TABLE `customer_activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_payment_summary`
--

DROP TABLE IF EXISTS `customer_payment_summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_payment_summary` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_orders` int DEFAULT '0',
  `paid_orders` int DEFAULT '0',
  `unpaid_orders` int DEFAULT '0',
  `total_due` decimal(10,2) DEFAULT '0.00',
  `total_paid` decimal(10,2) DEFAULT '0.00',
  `last_payment_date` datetime DEFAULT NULL,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `customer_payment_summary_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_payment_summary`
--

LOCK TABLES `customer_payment_summary` WRITE;
/*!40000 ALTER TABLE `customer_payment_summary` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_payment_summary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `dob` date DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Active',
  `language` varchar(10) DEFAULT 'en',
  `currency` varchar(10) DEFAULT 'PHP',
  `newsletter` tinyint(1) DEFAULT '1',
  `loyalty_points` int DEFAULT '150',
  `size_preference` varchar(50) DEFAULT NULL,
  `favorite_categories` varchar(255) DEFAULT NULL,
  `notification_preferences` varchar(255) DEFAULT 'email,sms',
  `two_factor` tinyint(1) DEFAULT '0',
  `login_history` text,
  `shipping_addresses` text,
  `billing_addresses` text,
  `payment_methods` text,
  `wishlist` text,
  `ticket_history` text,
  `message_history` text,
  `linked_social` text,
  `last_login` datetime DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total_due` decimal(10,2) DEFAULT '0.00',
  `total_paid` decimal(10,2) DEFAULT '0.00',
  `pending_payment_count` int DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,'Demo','User','demo.google@pktskarate.local','$2y$10$pkrxAmd.j7m7QYTfe/3Fi.fQmGMxHDwMCYvWxw3jhCSpjrB6EWWgu','',NULL,NULL,'Active','en','PHP',1,150,NULL,NULL,'email,sms',0,'[{\"time\":\"2026-08-17 02:53:29\",\"ip\":\"::1\",\"device\":\"Chrome (Windows 11)\"}]','[{\"id\":1,\"name\":\"Pasig Address\",\"address\":\"Robinson Metro East, Pasig, Metro Manila, 1600\",\"default\":true}]','[{\"id\":1,\"name\":\"Billing Address\",\"address\":\"Robinson Metro East, Pasig, Metro Manila, 1600\",\"default\":true}]','[{\"id\":1,\"brand\":\"Visa\",\"last4\":\"4322\",\"exp\":\"12\\/28\",\"default\":true}]',NULL,'[{\"id\":\"TCK-20485\",\"subject\":\"Dojo class schedule inquiry\",\"status\":\"Resolved\",\"date\":\"2026-08-12\"}]','[{\"sender\":\"support\",\"msg\":\"Welcome to JKS Pasig PKTS Karate Dojo! What is your query?\",\"time\":\"02:53\"},{\"sender\":\"customer\",\"msg\":\"I wanted to ask if uniform size 150cm fits a 10 year old.\",\"time\":\"02:53\"},{\"sender\":\"support\",\"msg\":\"Yes, 150cm is designed for kids roughly 9-11 years old. You can purchase directly.\",\"time\":\"02:53\"}]',NULL,'2026-08-17 10:53:29',NULL,NULL,'2026-08-17 02:52:37',0.00,0.00,0),(2,'Glenn','Rabe','glennrabe004@gmail.com','$2y$10$vlIQdz7LUL31V9Alij7rT.FHYWoY6PLOfhEkzWTkIxch1n.WS75ry','0938483743',NULL,'../uploads/avatar_2_1787039252.jpg','Active','en','PHP',1,150,NULL,NULL,'email,sms',0,'[{\"time\":\"2026-08-17 10:30:48\",\"ip\":\"::1\",\"device\":\"Chrome (Windows 11)\"}]','[{\"id\":1,\"name\":\"Pasig Address\",\"address\":\"Robinson Metro East, Pasig, Metro Manila, 1600\",\"default\":true}]','[{\"id\":1,\"name\":\"Billing Address\",\"address\":\"Robinson Metro East, Pasig, Metro Manila, 1600\",\"default\":true}]','[{\"id\":1,\"brand\":\"Visa\",\"last4\":\"4322\",\"exp\":\"12\\/28\",\"default\":true}]',NULL,'[{\"id\":\"TCK-20485\",\"subject\":\"Dojo class schedule inquiry\",\"status\":\"Resolved\",\"date\":\"2026-08-12\"}]','[{\"sender\":\"support\",\"msg\":\"Welcome to JKS Pasig PKTS Karate Dojo! What is your query?\",\"time\":\"10:30\"},{\"sender\":\"customer\",\"msg\":\"I wanted to ask if uniform size 150cm fits a 10 year old.\",\"time\":\"10:30\"},{\"sender\":\"support\",\"msg\":\"Yes, 150cm is designed for kids roughly 9-11 years old. You can purchase directly.\",\"time\":\"10:30\"}]',NULL,'2026-08-19 16:00:43',NULL,NULL,'2026-08-17 08:07:01',0.00,0.00,0);
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `size` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,'Kumite Gi Super Light Air Cool Red and Blue','M',5000.00,1);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_number` varchar(100) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(150) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_address` text NOT NULL,
  `customer_notes` text,
  `payment_method` varchar(100) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` varchar(50) DEFAULT 'unpaid',
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,'PKTS-1786954577-525','Glenn','glennrabe004@gmail.com','09483438343','eerrere','heehhhe','Cash on Delivery (COD)',5000.00,'paid','delivered','2026-08-17 08:16:17');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_history`
--

DROP TABLE IF EXISTS `payment_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `customer_id` int DEFAULT NULL,
  `customer_email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `old_status` enum('paid','unpaid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'unpaid',
  `new_status` enum('paid','unpaid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'paid',
  `payment_method` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_payment_history_order` (`order_id`),
  KEY `idx_payment_history_status` (`new_status`),
  KEY `idx_payment_history_date` (`created_at`),
  CONSTRAINT `payment_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_history`
--

LOCK TABLES `payment_history` WRITE;
/*!40000 ALTER TABLE `payment_history` DISABLE KEYS */;
INSERT INTO `payment_history` VALUES (1,1,NULL,NULL,5000.00,'paid','unpaid',NULL,NULL,'2026-08-17 21:39:55'),(2,1,NULL,NULL,5000.00,'unpaid','paid',NULL,NULL,'2026-08-17 21:39:57');
/*!40000 ALTER TABLE `payment_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `secondary_image` varchar(255) DEFAULT NULL,
  `description` text,
  `sizes` varchar(255) DEFAULT '140 CM, 150 CM, 160 CM, 170 CM, 180 CM',
  `status` varchar(50) DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Kata Gi Canvas Red and Blue','Uniform',6500.00,'PROD/Uniform1.jpeg',NULL,'High performance heavy weight canvas kata gi with red/blue shoulder embroidery.','140 CM, 150 CM, 160 CM, 170 CM, 180 CM, 185 CM, 190 CM','active','2026-07-24 04:00:30'),(2,'Kumite Gi Super Light Air Cool Red and Blue','Uniform',5000.00,'PROD/Uniform2.jpeg',NULL,'Ultra lightweight air-cool breathable kumite uniform for fast tournament sparring.','S, M, L, XL, XXL','active','2026-07-24 04:00:30'),(3,'Shureido Competition Kata Gi','Uniform',7200.00,'PROD/Uniform3.jpeg',NULL,'Master grade Japanese cut karate uniform engineered for crisp snap and durabilty.','150 CM, 160 CM, 170 CM, 180 CM','active','2026-07-24 04:00:30'),(4,'WKF Approved Shin Guard & Instep','Equipment',2800.00,'PROD/Eqp1.jpg',NULL,'Ergonomic high-density foam padding for maximum shin and foot protection.','S, M, L, XL','active','2026-07-24 04:00:30'),(5,'Red and Blue Karate Sparring Mitts','Equipment',2200.00,'PROD/Eqp2.jpg',NULL,'Official WKF style sparring gloves with secure wrist wrap support.','S, M, L','active','2026-07-24 04:00:30'),(6,'Head Guard Protector with Face Shield','Equipment',3500.00,'PROD/Eqp3.jpeg',NULL,'Shock absorbing head protection with optional clear face guard for youth/adults.','M, L','active','2026-07-24 04:00:30'),(7,'PKTS Official Dojo Duffle Bag','Bags',2400.00,'PROD/Bag1.jpg',NULL,'Spacious water-resistant gear bag with dedicated gi compartment and ventilation.','Standard','active','2026-07-24 04:00:30'),(8,'PKTS Pro Karate Equipment Backpack','Bags',2900.00,'PROD/Bag2.jpg',NULL,'Multi-zipper heavy-duty gear backpack with external belt holder straps.','Standard','active','2026-07-24 04:00:30');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_settings`
--

LOCK TABLES `site_settings` WRITE;
/*!40000 ALTER TABLE `site_settings` DISABLE KEYS */;
INSERT INTO `site_settings` VALUES ('announcement_banner','🥋 Free Trial Class Available for New Students! Book Yours Today.'),('contact_email','info@pktskarate.com'),('contact_phone','+63 917 123 4567'),('dojo_address','Robinson Metro East, Pasig, Metro Manila, 1600'),('hero_headline','TRAIN WITH PURPOSE. MASTER THE ART.'),('hero_subtext','Authentic Japan Karate Shoto-Federation (JKS) Martial Arts Training in Pasig City.'),('site_title','PKTS Karate Dojo - Pasig City');
/*!40000 ALTER TABLE `site_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trial_bookings`
--

DROP TABLE IF EXISTS `trial_bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trial_bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `guardian_first_name` varchar(100) NOT NULL,
  `guardian_last_name` varchar(100) NOT NULL,
  `student_first_name` varchar(100) NOT NULL,
  `student_last_name` varchar(100) NOT NULL,
  `student_age` int NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `zip` varchar(10) NOT NULL,
  `trial_date` date NOT NULL,
  `time_slot` varchar(150) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trial_bookings`
--

LOCK TABLES `trial_bookings` WRITE;
/*!40000 ALTER TABLE `trial_bookings` DISABLE KEYS */;
/*!40000 ALTER TABLE `trial_bookings` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-24 15:29:51
