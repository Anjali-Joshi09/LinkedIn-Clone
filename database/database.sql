-- MySQL dump 10.13  Distrib 8.0.46, for Win64 (x86_64)
--
-- Host: localhost    Database: linkedin_admin
-- ------------------------------------------------------
-- Server version	8.0.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int unsigned DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `user_id` (`user_id`),
  KEY `idx_type` (`type`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `activity_log_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_log`
--

LOCK TABLES `activity_log` WRITE;
/*!40000 ALTER TABLE `activity_log` DISABLE KEYS */;
INSERT INTO `activity_log` VALUES (1,1,NULL,'user_login','User logged in','127.0.0.1','2026-05-17 14:03:33'),(2,1,NULL,'profile_updated','Updated profile','127.0.0.1','2026-05-17 14:03:33'),(3,1,NULL,'job_applied','Applied for analyst role','127.0.0.1','2026-05-17 14:03:33'),(4,1,201,'job_posted','Created new job','127.0.0.1','2026-05-17 14:03:33'),(5,1,NULL,'post_created','Published new post','127.0.0.1','2026-05-17 14:03:33'),(6,1,NULL,'comment_added','Commented on post','127.0.0.1','2026-05-17 14:03:33'),(7,1,NULL,'ticket_created','Created support ticket','127.0.0.1','2026-05-17 14:03:33'),(8,1,201,'company_verified','Company verification complete','127.0.0.1','2026-05-17 14:03:33'),(9,1,NULL,'password_changed','Changed account password','127.0.0.1','2026-05-17 14:03:33'),(10,1,NULL,'message_sent','Sent recruiter message','127.0.0.1','2026-05-17 14:03:33'),(11,1,NULL,'connection_sent','Sent connection request','127.0.0.1','2026-05-17 14:03:33'),(12,1,201,'job_updated','Updated job details','127.0.0.1','2026-05-17 14:03:33'),(13,1,NULL,'application_updated','Application moved to review','127.0.0.1','2026-05-17 14:03:33'),(14,1,NULL,'user_blocked','Blocked another user','127.0.0.1','2026-05-17 14:03:33'),(15,1,NULL,'report_created','Reported spam content','127.0.0.1','2026-05-17 14:03:33'),(16,1,NULL,'user_deleted','User \'Priyanka Bisht\' (#204) deleted','127.0.0.1','2026-05-17 16:08:14'),(17,1,NULL,'user_deleted','User \'Anjali Joshi\' (#202) deleted','127.0.0.1','2026-05-17 16:08:17'),(18,1,NULL,'user_deleted','User \'Priya Kapoor\' (#103) deleted','127.0.0.1','2026-05-17 16:08:20'),(19,1,NULL,'user_deleted','User \'Rohan Mehta\' (#102) deleted','127.0.0.1','2026-05-17 16:08:23'),(20,1,NULL,'user_deleted','User \'Ananya Sharma\' (#101) deleted','127.0.0.1','2026-05-17 16:08:26'),(21,1,NULL,'post_deleted','Post #403 set to deleted','127.0.0.1','2026-05-17 16:11:41'),(22,1,234,'agent_approved','Agent #16 (Sumit Negi) approved','127.0.0.1','2026-05-17 18:30:30'),(23,1,235,'agent_approved','Agent #17 (Sumit Negi) approved','127.0.0.1','2026-05-17 18:55:59'),(24,1,NULL,'job_approved','Job #309 status set to approved','127.0.0.1','2026-05-17 23:04:02'),(25,1,NULL,'job_approved','Job #311 status set to approved','127.0.0.1','2026-05-18 09:16:45'),(26,1,NULL,'job_approved','Job #312 status set to approved','127.0.0.1','2026-05-18 09:20:16'),(27,1,NULL,'post_deleted','Post #423 set to deleted','127.0.0.1','2026-05-18 15:37:40'),(28,1,NULL,'job_approved','Job #313 status set to approved','127.0.0.1','2026-05-18 16:19:02'),(29,1,206,'user_updated','Admin updated profile of user #206','127.0.0.1','2026-05-19 14:39:38'),(30,1,232,'user_updated','Admin updated profile of user #232','127.0.0.1','2026-05-19 14:50:13'),(31,1,232,'user_updated','Admin updated profile of user #232','127.0.0.1','2026-05-19 14:50:17'),(32,1,NULL,'report_resolved','Report #3 marked resolved','127.0.0.1','2026-05-19 14:55:06'),(33,1,NULL,'job_rejected','Job #313 status set to rejected','127.0.0.1','2026-05-19 15:04:31'),(34,1,NULL,'job_approved','Job #310 status set to approved','127.0.0.1','2026-05-19 15:07:21'),(35,1,NULL,'job_approved','Job #313 status set to approved','127.0.0.1','2026-05-19 15:07:31'),(36,1,NULL,'job_rejected','Job #313 status set to rejected','127.0.0.1','2026-05-19 15:09:46'),(37,1,NULL,'job_rejected','Job #312 status set to rejected','127.0.0.1','2026-05-19 15:09:48'),(38,1,NULL,'job_rejected','Job #311 status set to rejected','127.0.0.1','2026-05-19 15:09:49'),(39,1,NULL,'job_approved','Job #313 status set to approved','127.0.0.1','2026-05-19 15:12:33'),(40,1,NULL,'job_approved','Job #312 status set to approved','127.0.0.1','2026-05-19 15:12:34'),(41,1,NULL,'job_approved','Job #311 status set to approved','127.0.0.1','2026-05-19 15:12:35'),(42,1,NULL,'report_resolved','Report #16 marked resolved','127.0.0.1','2026-05-19 15:19:23'),(43,1,232,'user_updated','User #232 profile updated by admin','127.0.0.1','2026-05-19 22:50:51'),(44,1,NULL,'job_rejected','Job #313 status set to rejected','127.0.0.1','2026-05-19 22:54:32'),(45,1,NULL,'job_approved','Job #313 status set to approved','127.0.0.1','2026-05-19 22:54:35'),(46,1,214,'user_updated','User #214 profile updated by admin','127.0.0.1','2026-05-19 23:12:01'),(47,1,215,'user_updated','User #215 profile updated by admin','127.0.0.1','2026-05-19 23:12:14'),(48,1,231,'user_updated','User #231 profile updated by admin','127.0.0.1','2026-05-21 09:26:20'),(49,1,230,'user_updated','User #230 profile updated by admin','127.0.0.1','2026-05-21 09:29:13'),(50,1,228,'user_updated','User #228 profile updated by admin','127.0.0.1','2026-05-21 09:29:26'),(51,1,227,'user_updated','User #227 profile updated by admin','127.0.0.1','2026-05-21 09:29:36'),(52,1,223,'user_updated','User #223 profile updated by admin','127.0.0.1','2026-05-21 09:29:50'),(53,1,223,'user_updated','User #223 profile updated by admin','127.0.0.1','2026-05-21 09:30:19'),(54,1,226,'user_updated','User #226 profile updated by admin','127.0.0.1','2026-05-21 09:30:30'),(55,1,224,'user_updated','User #224 profile updated by admin','127.0.0.1','2026-05-21 09:30:50'),(56,1,222,'user_updated','User #222 profile updated by admin','127.0.0.1','2026-05-21 09:31:03'),(57,1,220,'user_updated','User #220 profile updated by admin','127.0.0.1','2026-05-21 09:31:20'),(58,1,219,'user_updated','User #219 profile updated by admin','127.0.0.1','2026-05-21 09:31:33'),(59,1,218,'user_updated','User #218 profile updated by admin','127.0.0.1','2026-05-21 09:31:49'),(60,1,216,'user_updated','User #216 profile updated by admin','127.0.0.1','2026-05-21 09:32:01'),(61,1,NULL,'company_updated','Company #10 profile updated by admin','127.0.0.1','2026-05-21 09:32:13'),(62,1,NULL,'company_updated','Company #4 profile updated by admin','127.0.0.1','2026-05-21 09:32:23'),(63,1,NULL,'company_updated','Company #4 profile updated by admin','127.0.0.1','2026-05-21 09:32:36'),(64,1,NULL,'company_updated','Company #3 profile updated by admin','127.0.0.1','2026-05-21 09:32:47'),(65,1,NULL,'company_updated','Company #2 profile updated by admin','127.0.0.1','2026-05-21 09:32:59'),(66,1,NULL,'company_updated','Company #1 profile updated by admin','127.0.0.1','2026-05-21 09:33:10'),(67,1,NULL,'job_approved','Job #314 status set to approved','127.0.0.1','2026-05-21 15:49:07'),(68,1,NULL,'job_approved','Job #315 status set to approved','127.0.0.1','2026-05-23 11:18:10'),(69,1,NULL,'report_dismissed','Report #8 marked dismissed','127.0.0.1','2026-05-23 11:53:59'),(70,1,NULL,'report_dismissed','Report #12 marked dismissed','127.0.0.1','2026-05-23 11:54:04'),(71,1,NULL,'report_dismissed','Report #9 marked dismissed','127.0.0.1','2026-05-23 11:54:07');
/*!40000 ALTER TABLE `activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('super_admin','admin','moderator') COLLATE utf8mb4_unicode_ci DEFAULT 'admin',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_token_at` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'Anjali Joshi','anjalijoshi13354@gmail.com','$2y$10$biPvfJk3o3jqltew.tdtWu/dQTp4.DHWHRBgac/Ndy7zakiWti12y','super_admin',NULL,'ed4034c3d979033ca62efa359a72b49e4024b4197ed5f3d4fcf18f3ca986a660','2026-05-23 11:07:54','2026-05-23 15:33:25','2026-05-17 13:46:30','2026-05-23 15:33:25');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `agent_approvals`
--

DROP TABLE IF EXISTS `agent_approvals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agent_approvals` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `headline` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `location` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','approved','rejected','blocked') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `reviewed_by` int unsigned DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `notified_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `reviewed_by` (`reviewed_by`),
  KEY `idx_status` (`status`),
  CONSTRAINT `agent_approvals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `agent_approvals_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agent_approvals`
--

LOCK TABLES `agent_approvals` WRITE;
/*!40000 ALTER TABLE `agent_approvals` DISABLE KEYS */;
INSERT INTO `agent_approvals` VALUES (16,234,'Sumit Negi','anjali.2004.joshii@gmail.com','','Recruiter','','Mumbai',NULL,'approved',NULL,1,'2026-05-17 18:30:30','2026-05-17 18:30:30','2026-05-17 18:29:13','2026-05-18 07:09:12'),(17,235,'Sumit Negi','sumitnegi8445@gmail.com','9876567898','Technical Recruiter','product based company','Bangalore','https://www.google.com/','approved',NULL,1,'2026-05-17 18:55:59','2026-05-17 18:55:59','2026-05-17 18:55:01','2026-05-18 07:09:03');
/*!40000 ALTER TABLE `agent_approvals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `applications`
--

DROP TABLE IF EXISTS `applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `applications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `job_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `cover_letter` text COLLATE utf8mb4_unicode_ci,
  `resume` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('applied','reviewing','shortlisted','interview','hired','rejected','offered','withdrawn') COLLATE utf8mb4_unicode_ci DEFAULT 'applied',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_application` (`job_id`,`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_job` (`job_id`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `applications`
--

LOCK TABLES `applications` WRITE;
/*!40000 ALTER TABLE `applications` DISABLE KEYS */;
INSERT INTO `applications` VALUES (23,309,206,'Hello','uploads/resumes/b34849adc217d7e88d40f338.pdf','reviewing',NULL,'2026-05-18 06:13:49','2026-05-21 16:53:16'),(28,313,205,'','uploads/resumes/d809ee5b513390dbfdbc2f2e.pdf','applied',NULL,'2026-05-18 16:19:43','2026-05-18 16:19:43'),(30,309,205,'hihi','uploads/resumes/87d77cd25e93ed2e1a7e7d4f.pdf','reviewing',NULL,'2026-05-18 16:20:13','2026-05-19 09:44:49'),(33,311,205,'','uploads/resumes/ed87c480334183eeb66822cf.pdf','rejected',NULL,'2026-05-21 09:18:52','2026-05-21 16:53:05'),(34,304,205,'hhghghgh','uploads/resumes/9abca87b2766c2f4b41d5780.pdf','applied',NULL,'2026-05-21 10:25:50','2026-05-21 10:25:50');
/*!40000 ALTER TABLE `applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blocks`
--

DROP TABLE IF EXISTS `blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blocks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `blocker_id` int unsigned NOT NULL,
  `blocked_id` int unsigned NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_block` (`blocker_id`,`blocked_id`),
  KEY `blocked_id` (`blocked_id`),
  CONSTRAINT `blocks_ibfk_1` FOREIGN KEY (`blocker_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `blocks_ibfk_2` FOREIGN KEY (`blocked_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blocks`
--

LOCK TABLES `blocks` WRITE;
/*!40000 ALTER TABLE `blocks` DISABLE KEYS */;
/*!40000 ALTER TABLE `blocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `parent_id` int unsigned DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','hidden','deleted') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_post` (`post_id`),
  KEY `idx_parent` (`parent_id`),
  KEY `idx_comments_post_id` (`post_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (23,407,206,NULL,'good evening','active','2026-05-17 16:34:18'),(24,407,207,NULL,'good evening','deleted','2026-05-17 16:37:33'),(25,407,205,23,'@Priyanka Bisht goodevening','active','2026-05-17 16:46:29'),(26,418,205,NULL,'keep learing','deleted','2026-05-17 18:26:30'),(27,418,205,NULL,'keep learing','deleted','2026-05-17 18:26:31'),(28,418,205,NULL,'keep learing','deleted','2026-05-17 18:26:32'),(29,418,205,NULL,'keep learing','deleted','2026-05-17 18:26:32'),(30,418,205,NULL,'keep learing','deleted','2026-05-17 18:26:33'),(31,418,205,NULL,'keep learing','deleted','2026-05-17 18:26:33'),(32,418,205,NULL,'keep learing','deleted','2026-05-17 18:26:33'),(33,418,205,NULL,'keep learing','deleted','2026-05-17 18:26:33'),(34,418,205,NULL,'keep learing','deleted','2026-05-17 18:26:34'),(35,418,205,NULL,'keep learing','deleted','2026-05-17 18:26:36'),(36,418,205,NULL,'keep learing','deleted','2026-05-17 18:26:38'),(37,418,205,NULL,'keep learing','deleted','2026-05-17 18:26:39'),(38,418,205,NULL,'keep learing','deleted','2026-05-17 18:26:39'),(39,418,205,NULL,'keep learing','deleted','2026-05-17 18:26:39'),(40,418,205,NULL,'keep learning','deleted','2026-05-17 18:43:03'),(41,407,206,NULL,'hiii','active','2026-05-17 18:46:55'),(42,418,205,NULL,'Intersting','deleted','2026-05-17 20:47:54'),(43,418,205,NULL,'Intersting','deleted','2026-05-17 20:47:59'),(44,418,205,NULL,'Intersting','deleted','2026-05-17 20:48:03'),(45,418,205,NULL,'Keep Learning','deleted','2026-05-17 20:58:33'),(46,418,205,NULL,'Keep learning','deleted','2026-05-17 21:11:48'),(47,418,205,NULL,'Keep learning','deleted','2026-05-17 21:11:57'),(48,418,205,NULL,'keep learning','deleted','2026-05-17 21:17:31'),(49,418,205,NULL,'keep learning','deleted','2026-05-17 21:51:37'),(50,419,205,NULL,'good','active','2026-05-17 21:51:54'),(51,420,205,NULL,'nice','active','2026-05-17 21:52:57'),(52,418,205,NULL,'hii','active','2026-05-17 22:06:15'),(53,418,205,NULL,'goo','active','2026-05-17 22:53:30'),(54,418,205,NULL,'heelo','active','2026-05-17 22:55:40'),(55,418,205,NULL,'hello','active','2026-05-17 22:58:01'),(56,418,205,NULL,'hello','active','2026-05-17 22:59:41'),(57,418,205,NULL,'hello','active','2026-05-17 23:07:09'),(58,418,205,NULL,'hello','active','2026-05-17 23:09:44'),(59,418,205,NULL,'@Anjali Joshi hii','active','2026-05-17 23:10:06'),(60,419,205,NULL,'hii','active','2026-05-17 23:10:19'),(61,418,205,NULL,'hello','active','2026-05-17 23:12:31'),(62,418,235,NULL,'keep learning','active','2026-05-18 06:00:50'),(63,423,205,NULL,'hello','active','2026-05-18 07:14:22'),(64,423,205,NULL,'heelo','active','2026-05-18 08:55:11'),(65,407,206,NULL,'Good evening anjali','active','2026-05-18 09:23:23'),(67,423,205,NULL,'hii','active','2026-05-18 15:28:25'),(68,418,235,NULL,'hiii','active','2026-05-19 09:38:44'),(71,439,205,NULL,'interested','active','2026-05-21 09:17:15'),(72,440,242,NULL,'hhhhh','active','2026-05-21 16:44:03');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `companies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banner` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `industry` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_size` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `founded_year` year DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `location` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','verified','blocked','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `jobs_count` int unsigned DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `user_id` (`user_id`),
  KEY `idx_status` (`status`),
  FULLTEXT KEY `ft_companies_search` (`name`,`industry`),
  CONSTRAINT `companies_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `companies`
--

LOCK TABLES `companies` WRITE;
/*!40000 ALTER TABLE `companies` DISABLE KEYS */;
INSERT INTO `companies` VALUES (1,210,'Ebizon Netinfo Pvt Ltd','anjali.2004.joshi@gmail.com','','','uploads/companies/4d9cbab20ad0a882b561c200.jpg',NULL,'IT','',NULL,'','Dehradun, Uttarakhand','','verified',0,'2026-05-17 16:50:35','2026-05-21 09:33:10'),(2,211,'Amazon','diksha123@gmail.com','','','uploads/companies/ff395282a1d2803d66c30dd8.jpg',NULL,'IT','',NULL,'','Hyderabad','','verified',0,'2026-05-17 16:52:18','2026-05-21 09:32:59'),(3,212,'Facebook','akshita@gmail.com','','','uploads/companies/56a599be5e7d10e42f4b6e8f.png',NULL,'IT','',NULL,'','Bangalore','','verified',0,'2026-05-17 16:53:23','2026-05-21 09:32:47'),(4,213,'Netflix','shivangi@gmail.com','','','uploads/companies/e42b5b330743205183e94151.jpg',NULL,'IT','',NULL,'','Bangalore','','verified',0,'2026-05-17 16:54:42','2026-05-21 09:32:23'),(10,234,'Amazon','anjali.2004.joshii@gmail.com','','','uploads/companies/ba741c21e696229cf0588dbb.jpg',NULL,'IT','',NULL,'','Mumbai','','verified',0,'2026-05-17 18:29:08','2026-05-21 09:32:13'),(11,235,'Google','sumitnegi8445@gmail.com','9876567898','https://www.google.com/','uploads/companies/23c0da15cefa119154ddd562.jpg','uploads/companies/0077a095ed14522f887a8267.jpg','IT Services','5000+',NULL,'product based company','Bangalore','https://www.linkedin.com/company/google/posts/?feedView=all','verified',0,'2026-05-17 18:54:53','2026-05-18 15:01:50');
/*!40000 ALTER TABLE `companies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_applicants_last_seen`
--

DROP TABLE IF EXISTS `company_applicants_last_seen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `company_applicants_last_seen` (
  `company_id` int NOT NULL,
  `seen_at` datetime NOT NULL,
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_applicants_last_seen`
--

LOCK TABLES `company_applicants_last_seen` WRITE;
/*!40000 ALTER TABLE `company_applicants_last_seen` DISABLE KEYS */;
INSERT INTO `company_applicants_last_seen` VALUES (11,'2026-05-21 17:20:41');
/*!40000 ALTER TABLE `company_applicants_last_seen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_network_last_seen`
--

DROP TABLE IF EXISTS `company_network_last_seen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `company_network_last_seen` (
  `user_id` int unsigned NOT NULL,
  `seen_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `company_network_last_seen_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_network_last_seen`
--

LOCK TABLES `company_network_last_seen` WRITE;
/*!40000 ALTER TABLE `company_network_last_seen` DISABLE KEYS */;
INSERT INTO `company_network_last_seen` VALUES (235,'2026-05-19 11:04:10');
/*!40000 ALTER TABLE `company_network_last_seen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `connections`
--

DROP TABLE IF EXISTS `connections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `connections` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `requester_id` int unsigned NOT NULL,
  `receiver_id` int unsigned NOT NULL,
  `status` enum('pending','accepted','rejected','blocked') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_connection` (`requester_id`,`receiver_id`),
  KEY `receiver_id` (`receiver_id`),
  KEY `idx_connections_pair` (`requester_id`,`receiver_id`),
  CONSTRAINT `connections_ibfk_1` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `connections_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `connections`
--

LOCK TABLES `connections` WRITE;
/*!40000 ALTER TABLE `connections` DISABLE KEYS */;
INSERT INTO `connections` VALUES (8,207,206,'accepted','2026-05-17 16:35:52'),(11,208,206,'accepted','2026-05-17 16:38:34'),(12,208,205,'accepted','2026-05-17 16:38:35'),(13,209,205,'accepted','2026-05-17 16:41:28'),(28,205,207,'pending','2026-05-19 14:31:35'),(29,205,232,'pending','2026-05-19 16:15:18'),(30,205,231,'pending','2026-05-19 16:44:28'),(31,205,230,'pending','2026-05-19 17:18:14'),(33,206,205,'rejected','2026-05-21 09:45:31'),(34,236,205,'accepted','2026-05-21 09:47:09'),(35,237,205,'accepted','2026-05-21 09:48:30'),(36,205,206,'accepted','2026-05-21 10:07:58'),(37,239,205,'pending','2026-05-21 15:19:50'),(38,240,205,'pending','2026-05-21 15:38:21'),(39,241,205,'pending','2026-05-21 16:20:05'),(40,241,206,'pending','2026-05-21 16:22:25'),(41,241,207,'pending','2026-05-21 16:22:26'),(42,241,208,'pending','2026-05-21 16:22:30'),(43,240,206,'pending','2026-05-21 16:30:31'),(44,242,205,'pending','2026-05-21 16:44:08'),(45,242,241,'pending','2026-05-21 16:44:28'),(46,242,239,'pending','2026-05-21 16:44:39'),(48,243,205,'pending','2026-05-22 10:47:02');
/*!40000 ALTER TABLE `connections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `follows`
--

DROP TABLE IF EXISTS `follows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `follows` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `follower_id` int unsigned NOT NULL,
  `followed_id` int unsigned NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_follow` (`follower_id`,`followed_id`),
  KEY `followed_id` (`followed_id`),
  CONSTRAINT `follows_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `follows_ibfk_2` FOREIGN KEY (`followed_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `follows`
--

LOCK TABLES `follows` WRITE;
/*!40000 ALTER TABLE `follows` DISABLE KEYS */;
INSERT INTO `follows` VALUES (17,235,203,'2026-05-18 16:26:09'),(24,235,211,'2026-05-19 14:16:04'),(28,235,234,'2026-05-19 21:12:28'),(29,206,235,'2026-05-19 21:39:20'),(30,205,235,'2026-05-21 09:17:06'),(33,239,235,'2026-05-21 15:20:17'),(34,241,235,'2026-05-21 16:22:18'),(35,240,235,'2026-05-21 16:23:02');
/*!40000 ALTER TABLE `follows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `requirements` text COLLATE utf8mb4_unicode_ci,
  `benefits` text COLLATE utf8mb4_unicode_ci,
  `location` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `job_type` enum('full_time','part_time','remote','contract','internship') COLLATE utf8mb4_unicode_ci DEFAULT 'full_time',
  `experience_level` enum('entry','mid','senior','executive') COLLATE utf8mb4_unicode_ci DEFAULT 'mid',
  `salary_min` decimal(12,2) DEFAULT NULL,
  `salary_max` decimal(12,2) DEFAULT NULL,
  `salary_currency` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT 'USD',
  `is_featured` tinyint(1) DEFAULT '0',
  `status` enum('pending','approved','rejected','expired','closed','hidden') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `applications_count` int unsigned DEFAULT '0',
  `views_count` int unsigned DEFAULT '0',
  `expires_at` date DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_featured` (`is_featured`),
  KEY `idx_company` (`company_id`),
  KEY `idx_created_at` (`created_at`),
  FULLTEXT KEY `ft_jobs_search` (`title`),
  CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=316 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES (304,1,'PHP Developer','Job Desc','PHP, MySQL','WFH','Delhi','full_time','mid',400000.00,700000.00,'INR',1,'approved',1,0,NULL,'2026-05-17 17:00:29','2026-05-21 10:25:50'),(305,1,'Frontend Developer','Job Desc','HTML,CSS,JS','Insurance','Noida','full_time','entry',300000.00,500000.00,'INR',0,'approved',0,0,NULL,'2026-05-17 17:00:29','2026-05-17 22:46:12'),(306,2,'Laravel Developer','Job Desc','Laravel','Bonus','Mumbai','remote','mid',500000.00,800000.00,'INR',1,'approved',0,0,NULL,'2026-05-17 17:00:29','2026-05-18 16:09:05'),(307,3,'React Developer','Job Desc','ReactJS','Laptop','Pune','full_time','mid',600000.00,900000.00,'INR',0,'approved',0,0,NULL,'2026-05-17 17:00:29','2026-05-17 17:00:29'),(308,4,'NodeJS Developer','Job Desc','NodeJS','PF','Bangalore','remote','senior',800000.00,1200000.00,'INR',1,'approved',0,0,NULL,'2026-05-17 17:00:29','2026-05-18 08:55:30'),(309,11,'Frontend developer','hiring freshers','html,css,php,js','3lpa','Dehradun','full_time','entry',15000.00,20000.00,'USD',1,'approved',2,0,'2026-05-20','2026-05-17 22:10:47','2026-05-18 16:20:13'),(310,11,'Developer','Hirng fresher','Java','sfifh','Hybrid','full_time','entry',40000.00,50000.00,'USD',0,'approved',0,0,'2026-05-30','2026-05-18 06:03:11','2026-05-19 15:07:21'),(311,11,'gigi','hdieugier','Java','nfiwefh','Remote','full_time','entry',10000.00,200000.00,'USD',1,'approved',1,0,'2026-06-03','2026-05-18 08:52:15','2026-05-21 09:18:52'),(312,11,'hu','vurfvir','Java','b4hv','Hybrid','full_time','entry',5000.00,10000.00,'USD',0,'approved',0,0,'2026-05-20','2026-05-18 09:19:06','2026-05-19 15:12:34'),(313,11,'Java developer','nvbc','Java','vgfcgyfigukj','Dehradun, Uttarakhand','full_time','entry',20000.00,249999.00,'USD',0,'approved',1,0,'2026-05-20','2026-05-18 16:18:26','2026-05-21 14:47:12'),(314,11,'kjhg','Java','Java','kjh','jhgf','full_time','entry',10000.00,20000.00,'USD',0,'approved',0,0,'2026-05-23','2026-05-21 15:41:39','2026-05-21 15:49:07'),(315,11,'Content Writer','content','Content','kjahf','Dehradun, Uttarakhand','full_time','senior',20000.00,25000.00,'USD',0,'approved',0,0,'2026-05-30','2026-05-23 11:17:25','2026-05-23 11:18:10');
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` int unsigned NOT NULL,
  `receiver_id` int unsigned NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seen_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `receiver_id` (`receiver_id`),
  KEY `idx_thread` (`sender_id`,`receiver_id`,`created_at`),
  KEY `idx_messages_conversation` (`sender_id`,`receiver_id`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (22,205,209,'hello anjali',NULL,NULL,'2026-05-17 16:45:00'),(23,205,206,'hello priyanka',NULL,'2026-05-17 16:47:36','2026-05-17 16:45:07'),(24,206,205,'hello anjali',NULL,'2026-05-17 17:05:03','2026-05-17 16:47:40'),(25,205,206,'how are you',NULL,'2026-05-17 17:06:51','2026-05-17 17:05:12'),(26,206,205,'i am good',NULL,'2026-05-17 18:25:05','2026-05-17 17:06:54'),(27,206,205,'and you?',NULL,'2026-05-17 18:25:05','2026-05-17 17:06:59'),(28,206,205,'heyyyyy',NULL,'2026-05-18 09:14:50','2026-05-17 18:46:31'),(29,235,206,'hii',NULL,'2026-05-19 21:39:28','2026-05-17 22:12:11'),(30,235,205,'hello\\',NULL,'2026-05-18 08:49:48','2026-05-18 06:04:52'),(31,205,235,'hiii',NULL,'2026-05-19 10:36:32','2026-05-18 08:49:53'),(32,205,206,'hlo',NULL,'2026-05-18 09:22:33','2026-05-18 09:14:56'),(33,206,205,'hello anjali',NULL,'2026-05-18 10:22:35','2026-05-18 09:22:40'),(34,206,205,'hellooo',NULL,'2026-05-18 10:22:35','2026-05-18 09:22:42'),(35,206,205,'hiii',NULL,'2026-05-18 10:22:35','2026-05-18 09:22:44'),(36,206,205,'how are you',NULL,'2026-05-18 10:22:35','2026-05-18 09:22:50'),(37,205,206,'i am good',NULL,'2026-05-21 14:38:50','2026-05-18 10:22:50'),(42,235,205,'hii',NULL,'2026-05-19 10:37:03','2026-05-19 10:36:35'),(43,235,205,'how are you',NULL,'2026-05-19 10:37:03','2026-05-19 10:36:42'),(44,205,235,'i am good',NULL,'2026-05-20 22:41:55','2026-05-19 10:37:14'),(45,205,235,'hiii',NULL,'2026-05-20 22:41:55','2026-05-19 16:44:46'),(46,205,235,'hiii',NULL,'2026-05-20 22:41:55','2026-05-19 17:33:59'),(47,206,235,'hello',NULL,NULL,'2026-05-19 21:39:31'),(48,205,235,'hii',NULL,'2026-05-20 22:41:55','2026-05-20 22:23:36'),(49,205,235,'heelllooo',NULL,'2026-05-20 22:41:55','2026-05-20 22:23:46'),(50,205,235,'hii',NULL,'2026-05-20 22:41:55','2026-05-20 22:26:19'),(51,205,235,'hlo',NULL,'2026-05-20 22:41:55','2026-05-20 22:26:23'),(52,205,235,'hii',NULL,'2026-05-20 22:41:55','2026-05-20 22:31:31'),(53,205,235,'hii',NULL,'2026-05-20 22:41:55','2026-05-20 22:31:43'),(54,205,235,'hlo',NULL,'2026-05-20 22:41:55','2026-05-20 22:34:20'),(55,205,235,'hii',NULL,'2026-05-20 22:41:55','2026-05-20 22:40:51'),(56,205,235,'?',NULL,'2026-05-20 22:41:55','2026-05-20 22:41:05'),(57,235,205,'hello anjali',NULL,'2026-05-21 10:24:28','2026-05-20 22:45:22'),(58,235,205,'hi',NULL,'2026-05-21 10:24:28','2026-05-21 09:22:23'),(59,205,209,'hghg',NULL,NULL,'2026-05-21 10:24:21'),(60,206,205,'okay',NULL,NULL,'2026-05-21 14:38:59');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `recipient` enum('all_users','all_companies','active_users','specific') COLLATE utf8mb4_unicode_ci DEFAULT 'all_users',
  `user_id` int unsigned DEFAULT NULL,
  `type` enum('email','push','both') COLLATE utf8mb4_unicode_ci DEFAULT 'email',
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('draft','scheduled','sent','failed') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `scheduled_at` datetime DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `sent_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sent_by` (`sent_by`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`sent_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,'all_users',NULL,'email','Greetings','heloo guys','sent',NULL,'2026-05-17 23:08:27',1,'2026-05-17 23:08:27'),(2,'all_users',NULL,'email','Greetings','hhhhh','sent',NULL,'2026-05-18 07:10:33',1,'2026-05-18 07:10:33'),(3,'all_users',NULL,'email','dbsdu','hhiisdbvksug','sent',NULL,'2026-05-18 07:11:29',1,'2026-05-18 07:11:29'),(4,'all_users',NULL,'email','Greetings','hello from admin','sent',NULL,'2026-05-19 23:10:44',1,'2026-05-19 23:10:44'),(5,'all_users',NULL,'email','hello','hiu','sent',NULL,'2026-05-21 10:35:17',1,'2026-05-21 10:35:17'),(6,'all_users',NULL,'email','bv','jhgs','sent',NULL,'2026-05-21 12:42:35',1,'2026-05-21 12:42:35'),(7,'all_users',NULL,'email','kjhg','hgfghj','sent',NULL,'2026-05-21 13:04:03',1,'2026-05-21 13:04:03'),(8,'all_users',NULL,'email','cvb','fghjj','sent',NULL,'2026-05-21 13:09:50',1,'2026-05-21 13:09:50'),(9,'all_users',NULL,'email','nbv','kjh','sent',NULL,'2026-05-21 13:13:56',1,'2026-05-21 13:13:56'),(10,'all_users',NULL,'email','kjhv','kjh','sent',NULL,'2026-05-21 13:18:39',1,'2026-05-21 13:18:39'),(11,'all_users',NULL,'email','ncasjihcu','asb','sent',NULL,'2026-05-21 13:28:42',1,'2026-05-21 13:28:42'),(12,'all_users',NULL,'email','greetings','hello','sent',NULL,'2026-05-21 14:21:53',1,'2026-05-21 14:21:53'),(13,'all_users',NULL,'email','jhg','hi','sent',NULL,'2026-05-21 14:27:15',1,'2026-05-21 14:27:15');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_likes`
--

DROP TABLE IF EXISTS `post_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_likes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_post_like` (`post_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `post_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `post_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_likes`
--

LOCK TABLES `post_likes` WRITE;
/*!40000 ALTER TABLE `post_likes` DISABLE KEYS */;
INSERT INTO `post_likes` VALUES (14,403,201,'2026-05-17 14:02:15'),(33,406,207,'2026-05-17 16:37:20'),(34,407,207,'2026-05-17 16:37:21'),(35,405,207,'2026-05-17 16:37:23'),(37,419,205,'2026-05-17 17:05:39'),(38,420,205,'2026-05-17 17:05:41'),(39,421,205,'2026-05-17 17:05:43'),(40,422,205,'2026-05-17 17:05:45'),(41,407,205,'2026-05-17 17:05:46'),(50,418,205,'2026-05-17 18:43:15'),(51,407,206,'2026-05-17 18:46:45'),(52,423,205,'2026-05-18 07:14:24'),(53,406,205,'2026-05-18 10:23:14'),(54,405,205,'2026-05-18 10:23:17'),(56,418,235,'2026-05-19 09:38:40'),(59,437,235,'2026-05-19 21:06:54'),(60,439,205,'2026-05-19 23:16:03'),(61,437,205,'2026-05-21 14:13:12'),(63,440,242,'2026-05-21 16:43:49');
/*!40000 ALTER TABLE `post_likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_reposts`
--

DROP TABLE IF EXISTS `post_reposts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_reposts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_repost` (`post_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `post_reposts_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `post_reposts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_reposts`
--

LOCK TABLES `post_reposts` WRITE;
/*!40000 ALTER TABLE `post_reposts` DISABLE KEYS */;
/*!40000 ALTER TABLE `post_reposts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `media` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `media_type` enum('image','video','document','none') COLLATE utf8mb4_unicode_ci DEFAULT 'none',
  `visibility` enum('public','connections','private') COLLATE utf8mb4_unicode_ci DEFAULT 'public',
  `likes` int unsigned DEFAULT '0',
  `comments_count` int unsigned DEFAULT '0',
  `shares_count` int unsigned DEFAULT '0',
  `status` enum('active','hidden','reported','offensive','deleted') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `author` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_user` (`user_id`),
  FULLTEXT KEY `ft_posts_search` (`content`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=442 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (403,201,'We are hiring PHP and frontend engineers for remote-friendly roles. Easy Apply is open on our latest jobs.',NULL,'none','public',26,1,8,'deleted','TalentBridge Recruiter','2026-05-17 10:44:02','2026-05-17 16:11:41'),(405,205,'Hello everyone??',NULL,'none','public',2,0,0,'active','Anjali Joshi','2026-05-17 16:30:37','2026-05-18 10:23:17'),(406,205,'Hello guys',NULL,'none','public',2,0,0,'active','Anjali Joshi','2026-05-17 16:30:46','2026-05-18 10:23:14'),(407,205,'Good eveneing all',NULL,'none','public',3,4,0,'active','Anjali Joshi','2026-05-17 16:30:57','2026-05-18 09:23:23'),(418,203,'Learning Core PHP MVC architecture.',NULL,'none','public',12,13,1,'active','Anjali Joshi','2026-05-17 17:03:36','2026-05-19 09:38:44'),(419,206,'Bootstrap 5 is awesome for dashboards.',NULL,'none','public',13,5,2,'active','Priyanka Bisht','2026-05-17 17:03:36','2026-05-17 23:10:19'),(420,207,'Just completed a MySQL optimization task.',NULL,'none','public',8,2,0,'active','Gunjan Suyal','2026-05-17 17:03:36','2026-05-17 21:52:57'),(421,208,'Hiring PHP Developers now.',NULL,'none','public',21,4,5,'active','Ayushi','2026-05-17 17:03:36','2026-05-17 17:08:48'),(422,209,'Remote jobs are increasing rapidly.',NULL,'none','public',16,2,3,'active','Anjali Purohit','2026-05-17 17:03:36','2026-05-17 17:08:50'),(423,235,'hello everyone, we are hiring',NULL,'none','public',1,3,0,'deleted','Sumit Negi','2026-05-18 06:00:38','2026-05-18 15:37:40'),(437,205,'hiii everyone',NULL,'none','public',2,0,0,'active','Anjali Joshi','2026-05-19 17:24:13','2026-05-21 14:13:12'),(439,235,'hiring',NULL,'none','public',1,1,0,'active','Sumit Negi','2026-05-19 21:07:03','2026-05-21 09:17:15'),(440,205,'hello everyone how are you??',NULL,'none','public',1,1,0,'active','Anjali Joshi','2026-05-21 15:42:45','2026-05-21 16:44:03');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `reporter_id` int unsigned DEFAULT NULL,
  `reporter_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_type` enum('user','post','job','company','comment') COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_id` int unsigned NOT NULL,
  `type` enum('spam','offensive','fake','harassment','copyright','other') COLLATE utf8mb4_unicode_ci DEFAULT 'other',
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','reviewed','resolved','dismissed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `reviewed_by` int unsigned DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `reporter_id` (`reporter_id`),
  KEY `reviewed_by` (`reviewed_by`),
  KEY `idx_status` (`status`),
  KEY `idx_target_type` (`target_type`),
  KEY `idx_reports_status` (`status`),
  CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
INSERT INTO `reports` VALUES (1,NULL,'user@demo.com','post',401,'spam','Looks promotional','pending',NULL,NULL,NULL,'2026-05-17 14:03:12'),(2,NULL,'rohan@demo.com','user',103,'fake','Suspicious activity','reviewed',NULL,NULL,NULL,'2026-05-17 14:03:12'),(3,NULL,'priya@demo.com','comment',1,'offensive','Rude language','resolved',NULL,1,'2026-05-19 14:55:06','2026-05-17 14:03:12'),(4,NULL,'user@demo.com','job',301,'fake','Salary looks unrealistic','dismissed',NULL,NULL,NULL,'2026-05-17 14:03:12'),(5,NULL,'rohan@demo.com','company',201,'other','Needs verification','resolved',NULL,NULL,NULL,'2026-05-17 14:03:12'),(7,NULL,'user@demo.com','user',102,'harassment','Too many messages','reviewed',NULL,NULL,NULL,'2026-05-17 14:03:12'),(8,NULL,'rohan@demo.com','comment',2,'offensive','Abusive comment','dismissed',NULL,1,'2026-05-23 11:53:59','2026-05-17 14:03:12'),(9,NULL,'priya@demo.com','job',302,'fake','Duplicate posting','dismissed',NULL,1,'2026-05-23 11:54:07','2026-05-17 14:03:12'),(10,NULL,'user@demo.com','company',201,'copyright','Copied branding','dismissed',NULL,NULL,NULL,'2026-05-17 14:03:12'),(11,NULL,'rohan@demo.com','post',403,'spam','Too many hiring posts','resolved',NULL,NULL,NULL,'2026-05-17 14:03:12'),(12,NULL,'priya@demo.com','user',101,'other','Testing report system','dismissed',NULL,1,'2026-05-23 11:54:04','2026-05-17 14:03:12'),(13,NULL,'user@demo.com','comment',3,'offensive','Inappropriate content','reviewed',NULL,NULL,NULL,'2026-05-17 14:03:12'),(14,NULL,'rohan@demo.com','job',303,'fake','Invalid details','dismissed',NULL,NULL,NULL,'2026-05-17 14:03:12'),(15,NULL,'priya@demo.com','post',401,'spam','Duplicate content','resolved',NULL,NULL,NULL,'2026-05-17 14:03:12'),(16,205,'anjalijoshi.200409@gmail.com','post',423,'fake','asxdw','resolved',NULL,1,'2026-05-19 15:19:23','2026-05-18 15:35:11'),(17,205,'anjalijoshi.200409@gmail.com','post',418,'spam','kjh','pending',NULL,NULL,NULL,'2026-05-21 15:43:16');
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `saved_jobs`
--

DROP TABLE IF EXISTS `saved_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `saved_jobs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `job_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_saved_job` (`job_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `saved_jobs_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `saved_jobs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `saved_jobs`
--

LOCK TABLES `saved_jobs` WRITE;
/*!40000 ALTER TABLE `saved_jobs` DISABLE KEYS */;
INSERT INTO `saved_jobs` VALUES (23,313,205,'2026-05-18 16:19:49'),(24,304,205,'2026-05-18 16:20:19'),(25,306,205,'2026-05-18 16:20:21');
/*!40000 ALTER TABLE `saved_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `saved_posts`
--

DROP TABLE IF EXISTS `saved_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `saved_posts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_saved_post` (`post_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `saved_posts_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `saved_posts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `saved_posts`
--

LOCK TABLES `saved_posts` WRITE;
/*!40000 ALTER TABLE `saved_posts` DISABLE KEYS */;
INSERT INTO `saved_posts` VALUES (11,403,201,'2026-05-17 14:02:20');
/*!40000 ALTER TABLE `saved_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `search_history`
--

DROP TABLE IF EXISTS `search_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `search_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `query` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_query` (`query`),
  CONSTRAINT `search_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `search_history`
--

LOCK TABLES `search_history` WRITE;
/*!40000 ALTER TABLE `search_history` DISABLE KEYS */;
INSERT INTO `search_history` VALUES (1,NULL,'PHP developer jobs','2026-05-17 14:02:49'),(2,NULL,'Remote frontend jobs','2026-05-17 14:02:49'),(3,NULL,'Bootstrap admin dashboard','2026-05-17 14:02:49'),(4,NULL,'SQL data analyst jobs','2026-05-17 14:02:49'),(5,201,'Full stack developers','2026-05-17 14:02:49'),(6,NULL,'Core PHP MVC','2026-05-17 14:02:49'),(7,NULL,'JavaScript interview questions','2026-05-17 14:02:49'),(8,NULL,'Power BI tutorials','2026-05-17 14:02:49'),(9,201,'Hiring UI engineers','2026-05-17 14:02:49'),(10,NULL,'Remote companies India','2026-05-17 14:02:49'),(11,NULL,'LinkedIn clone UI','2026-05-17 14:02:49'),(12,NULL,'Analytics dashboards','2026-05-17 14:02:49'),(13,201,'Technical recruiter tools','2026-05-17 14:02:49'),(14,NULL,'MySQL optimization','2026-05-17 14:02:49'),(15,NULL,'Responsive navbar examples','2026-05-17 14:02:49');
/*!40000 ALTER TABLE `search_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_settings`
--

LOCK TABLES `site_settings` WRITE;
/*!40000 ALTER TABLE `site_settings` DISABLE KEYS */;
INSERT INTO `site_settings` VALUES (1,'site_name','LinkedIn','2026-05-17 13:40:23'),(2,'site_url','https://linkedin-admin.com','2026-05-17 13:40:23'),(3,'tagline','Connect top talent with the world\'s best companies','2026-05-17 13:40:23'),(4,'maintenance_mode','0','2026-05-17 13:40:23'),(5,'allow_user_reg','1','2026-05-17 13:40:23'),(6,'allow_company_reg','1','2026-05-17 13:40:23'),(7,'email_verification','1','2026-05-17 13:40:23'),(8,'job_auto_approve','0','2026-05-17 13:40:23'),(9,'smtp_host','','2026-05-17 13:40:23'),(10,'smtp_port','587','2026-05-17 13:40:23'),(11,'smtp_user','','2026-05-17 13:40:23'),(12,'smtp_pass','','2026-05-17 13:40:23'),(13,'smtp_from_email','noreply@linkedin-admin.com','2026-05-17 13:40:23'),(14,'smtp_from_name','LinkedIn','2026-05-17 13:40:23');
/*!40000 ALTER TABLE `site_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_replies`
--

DROP TABLE IF EXISTS `support_replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_replies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int unsigned NOT NULL,
  `sender` enum('user','admin') NOT NULL,
  `admin_id` int unsigned DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ticket` (`ticket_id`),
  CONSTRAINT `support_replies_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_replies`
--

LOCK TABLES `support_replies` WRITE;
/*!40000 ALTER TABLE `support_replies` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_replies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_tickets`
--

DROP TABLE IF EXISTS `support_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_tickets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `user_type` enum('user','company') NOT NULL DEFAULT 'user',
  `name` varchar(150) NOT NULL,
  `email` varchar(200) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `attachment` varchar(350) DEFAULT NULL,
  `status` enum('pending','in_progress','resolved','closed') NOT NULL DEFAULT 'pending',
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `is_seen_admin` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_seen` (`is_seen_admin`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_tickets`
--

LOCK TABLES `support_tickets` WRITE;
/*!40000 ALTER TABLE `support_tickets` DISABLE KEYS */;
INSERT INTO `support_tickets` VALUES (1,205,'user','Anjali Joshi','anjalijoshi.200409@gmail.com','Facing profile updation problem','I am not able to update my profile',NULL,'pending','normal',0,'2026-05-23 15:32:44','2026-05-23 15:32:44'),(2,205,'user','Anjali Joshi','anjalijoshi.200409@gmail.com','Facing profile updation problem','I am not able to update my profile',NULL,'pending','normal',1,'2026-05-23 15:32:52','2026-05-23 15:33:36');
/*!40000 ALTER TABLE `support_tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_job_last_seen`
--

DROP TABLE IF EXISTS `user_job_last_seen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_job_last_seen` (
  `user_id` int unsigned NOT NULL,
  `seen_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `user_job_last_seen_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_job_last_seen`
--

LOCK TABLES `user_job_last_seen` WRITE;
/*!40000 ALTER TABLE `user_job_last_seen` DISABLE KEYS */;
INSERT INTO `user_job_last_seen` VALUES (205,'2026-05-23 10:39:12'),(206,'2026-05-21 14:38:42'),(241,'2026-05-21 16:20:38'),(242,'2026-05-21 16:44:54');
/*!40000 ALTER TABLE `user_job_last_seen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_notifications`
--

DROP TABLE IF EXISTS `user_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_notifications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `type` enum('connection','like','comment','message','job','application','system','follow') COLLATE utf8mb4_unicode_ci DEFAULT 'system',
  `message` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_id` int unsigned DEFAULT NULL,
  `sender_id` int unsigned DEFAULT NULL,
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_read` (`user_id`,`read_at`),
  CONSTRAINT `user_notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=486 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_notifications`
--

LOCK TABLES `user_notifications` WRITE;
/*!40000 ALTER TABLE `user_notifications` DISABLE KEYS */;
INSERT INTO `user_notifications` VALUES (3,201,'application','Ananya Sharma applied to Core PHP Full Stack Developer.','job',301,NULL,NULL,'2026-05-17 13:44:23'),(8,201,'like','Someone liked your post.','post',403,NULL,NULL,'2026-05-17 13:56:34'),(9,201,'comment','Someone commented on your post.','post',403,NULL,NULL,'2026-05-17 13:56:48'),(14,201,'application','Rohan applied for Frontend Engineer role.','job',302,NULL,NULL,'2026-05-17 14:02:59'),(18,201,'system','Company profile verified.','company',201,NULL,NULL,'2026-05-17 14:02:59'),(22,201,'job','Your job received 10 applications.','job',301,NULL,NULL,'2026-05-17 14:02:59'),(27,201,'application','New application for Core PHP Full Stack Developer','job',301,NULL,NULL,'2026-05-17 14:16:35'),(29,201,'application','New application for Junior Data Analyst','job',303,NULL,NULL,'2026-05-17 15:03:29'),(43,201,'like','Someone liked your post.','post',403,NULL,NULL,'2026-05-17 15:32:37'),(46,201,'application','New application for Frontend Engineer - Bootstrap UI','job',302,NULL,NULL,'2026-05-17 15:59:32'),(49,205,'connection','Priyanka Bisht sent you a connection request.','user',206,NULL,'2026-05-17 18:25:09','2026-05-17 16:32:44'),(50,205,'like','Someone liked your post.','post',407,NULL,'2026-05-17 18:25:09','2026-05-17 16:34:11'),(51,205,'comment','Someone commented on your post.','post',407,NULL,'2026-05-17 18:25:09','2026-05-17 16:34:18'),(52,206,'connection','Gunjan Suyal sent you a connection request.','user',207,NULL,'2026-05-17 18:46:34','2026-05-17 16:35:52'),(53,205,'connection','Gunjan Suyal sent you a connection request.','user',207,NULL,'2026-05-17 18:25:09','2026-05-17 16:35:53'),(54,205,'like','Someone liked your post.','post',406,NULL,'2026-05-17 18:25:09','2026-05-17 16:37:20'),(55,205,'like','Someone liked your post.','post',407,NULL,'2026-05-17 18:25:09','2026-05-17 16:37:21'),(56,205,'like','Someone liked your post.','post',405,NULL,'2026-05-17 18:25:09','2026-05-17 16:37:23'),(57,205,'comment','Someone commented on your post.','post',407,NULL,'2026-05-17 18:25:09','2026-05-17 16:37:33'),(58,207,'connection','Ayushi sent you a connection request.','user',208,NULL,NULL,'2026-05-17 16:38:33'),(59,206,'connection','Ayushi sent you a connection request.','user',208,NULL,'2026-05-17 18:46:34','2026-05-17 16:38:34'),(60,205,'connection','Ayushi sent you a connection request.','user',208,NULL,'2026-05-17 18:25:09','2026-05-17 16:38:35'),(61,205,'connection','Anjali Purohit sent you a connection request.','user',209,NULL,'2026-05-17 18:25:09','2026-05-17 16:41:28'),(62,209,'connection','Anjali Joshi accepted your connection request.','user',205,NULL,NULL,'2026-05-17 16:44:37'),(63,206,'connection','Anjali Joshi accepted your connection request.','user',205,NULL,'2026-05-17 18:46:34','2026-05-17 16:44:42'),(64,209,'message','You have a new message.','message',205,NULL,NULL,'2026-05-17 16:45:00'),(65,206,'message','You have a new message.','message',205,NULL,'2026-05-17 18:46:34','2026-05-17 16:45:07'),(66,205,'message','You have a new message.','message',206,NULL,'2026-05-17 18:25:09','2026-05-17 16:47:40'),(67,210,'application','New application for PHP Developer','job',304,NULL,NULL,'2026-05-17 17:04:47'),(68,206,'message','You have a new message.','message',205,NULL,'2026-05-17 18:46:34','2026-05-17 17:05:12'),(69,203,'like','Someone liked your post.','post',418,NULL,NULL,'2026-05-17 17:05:37'),(70,206,'like','Someone liked your post.','post',419,NULL,'2026-05-17 18:46:34','2026-05-17 17:05:39'),(71,207,'like','Someone liked your post.','post',420,NULL,NULL,'2026-05-17 17:05:41'),(72,208,'like','Someone liked your post.','post',421,NULL,NULL,'2026-05-17 17:05:43'),(73,209,'like','Someone liked your post.','post',422,NULL,NULL,'2026-05-17 17:05:45'),(74,205,'message','You have a new message.','message',206,NULL,'2026-05-17 18:25:09','2026-05-17 17:06:54'),(75,205,'message','You have a new message.','message',206,NULL,'2026-05-17 18:25:09','2026-05-17 17:06:59'),(76,203,'like','Someone liked your post.','post',418,NULL,NULL,'2026-05-17 17:08:27'),(77,207,'like','Someone liked your post.','post',420,NULL,NULL,'2026-05-17 17:08:31'),(78,208,'like','Someone liked your post.','post',421,NULL,NULL,'2026-05-17 17:08:32'),(79,209,'like','Someone liked your post.','post',422,NULL,NULL,'2026-05-17 17:08:34'),(80,205,'like','Someone liked your post.','post',406,NULL,'2026-05-17 18:25:09','2026-05-17 17:08:37'),(81,205,'like','Someone liked your post.','post',405,NULL,'2026-05-17 18:25:09','2026-05-17 17:08:38'),(82,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 18:26:30'),(83,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 18:26:31'),(84,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 18:26:32'),(85,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 18:26:32'),(86,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 18:26:33'),(87,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 18:26:33'),(88,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 18:26:33'),(89,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 18:26:33'),(90,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 18:26:34'),(91,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 18:26:36'),(92,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 18:26:38'),(93,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 18:26:39'),(94,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 18:26:39'),(95,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 18:26:39'),(96,203,'like','Anjali Joshi liked your post.','post',418,205,NULL,'2026-05-17 18:42:49'),(97,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 18:43:03'),(98,203,'like','Anjali Joshi liked your post.','post',418,205,NULL,'2026-05-17 18:43:15'),(99,232,'connection','Anjali Joshi sent you a connection request.','user',205,NULL,NULL,'2026-05-17 18:45:30'),(100,205,'message','You have a new message.','message',206,NULL,'2026-05-17 18:48:11','2026-05-17 18:46:31'),(101,205,'like','Priyanka Bisht liked your post.','post',407,206,'2026-05-17 18:48:11','2026-05-17 18:46:45'),(102,205,'comment','Priyanka Bisht commented on your post.','post',407,206,'2026-05-17 18:48:11','2026-05-17 18:46:55'),(103,210,'application','New application for PHP Developer','job',304,NULL,NULL,'2026-05-17 19:01:18'),(104,211,'application','New application for Laravel Developer','job',306,NULL,NULL,'2026-05-17 20:40:36'),(105,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 20:47:54'),(106,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 20:47:59'),(107,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 20:48:03'),(108,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 20:58:33'),(109,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 21:11:48'),(110,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 21:11:57'),(111,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 21:17:31'),(112,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 21:51:37'),(113,206,'comment','Anjali Joshi commented on your post.','post',419,205,'2026-05-21 14:36:15','2026-05-17 21:51:54'),(114,207,'comment','Anjali Joshi commented on your post.','post',420,205,NULL,'2026-05-17 21:52:57'),(115,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 22:06:15'),(116,206,'message','You have a new message.','message',235,NULL,NULL,'2026-05-17 22:12:11'),(117,213,'application','New application for NodeJS Developer','job',308,NULL,NULL,'2026-05-17 22:43:05'),(118,210,'application','New application for PHP Developer','job',304,NULL,NULL,'2026-05-17 22:45:32'),(119,211,'application','New application for Laravel Developer','job',306,NULL,NULL,'2026-05-17 22:45:35'),(120,235,'application','New application for Frontend developer','job',309,NULL,'2026-05-18 05:38:29','2026-05-17 22:45:39'),(121,210,'application','New application for Frontend Developer','job',305,NULL,NULL,'2026-05-17 22:45:56'),(122,210,'application','New application for PHP Developer','job',304,NULL,NULL,'2026-05-17 22:48:55'),(123,211,'application','New application for Laravel Developer','job',306,NULL,NULL,'2026-05-17 22:48:59'),(124,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 22:53:30'),(125,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 22:55:40'),(126,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 22:58:01'),(127,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 22:59:41'),(128,210,'application','New application for PHP Developer','job',304,NULL,NULL,'2026-05-17 23:02:33'),(129,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 23:07:09'),(130,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 23:09:44'),(131,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 23:10:06'),(132,206,'comment','Anjali Joshi commented on your post.','post',419,205,NULL,'2026-05-17 23:10:19'),(133,203,'comment','Anjali Joshi commented on your post.','post',418,205,NULL,'2026-05-17 23:12:31'),(134,203,'comment','Sumit Negi commented on your post.','post',418,235,NULL,'2026-05-18 06:00:50'),(135,205,'message','You have a new message.','message',235,NULL,'2026-05-18 07:11:56','2026-05-18 06:04:52'),(136,235,'application','New application for Frontend developer','job',309,NULL,'2026-05-18 06:10:54','2026-05-18 06:10:27'),(137,205,'application','Your application for Frontend developer is now reviewing.','application',22,NULL,'2026-05-18 07:11:56','2026-05-18 06:11:28'),(138,235,'application','New application for Frontend developer','job',309,NULL,'2026-05-18 06:15:31','2026-05-18 06:13:49'),(139,206,'application','Your application for Frontend developer is now reviewing.','application',23,NULL,NULL,'2026-05-18 06:15:01'),(140,205,'system','dbsdu: hhiisdbvksug',NULL,NULL,NULL,'2026-05-18 07:11:56','2026-05-18 07:11:29'),(141,206,'system','dbsdu: hhiisdbvksug',NULL,NULL,NULL,NULL,'2026-05-18 07:11:29'),(142,207,'system','dbsdu: hhiisdbvksug',NULL,NULL,NULL,NULL,'2026-05-18 07:11:29'),(143,208,'system','dbsdu: hhiisdbvksug',NULL,NULL,NULL,NULL,'2026-05-18 07:11:29'),(144,209,'system','dbsdu: hhiisdbvksug',NULL,NULL,NULL,NULL,'2026-05-18 07:11:29'),(145,214,'system','dbsdu: hhiisdbvksug',NULL,NULL,NULL,NULL,'2026-05-18 07:11:29'),(146,215,'system','dbsdu: hhiisdbvksug',NULL,NULL,NULL,NULL,'2026-05-18 07:11:29'),(147,216,'system','dbsdu: hhiisdbvksug',NULL,NULL,NULL,NULL,'2026-05-18 07:11:29'),(148,218,'system','dbsdu: hhiisdbvksug',NULL,NULL,NULL,NULL,'2026-05-18 07:11:29'),(149,219,'system','dbsdu: hhiisdbvksug',NULL,NULL,NULL,NULL,'2026-05-18 07:11:29'),(150,220,'system','dbsdu: hhiisdbvksug',NULL,NULL,NULL,NULL,'2026-05-18 07:11:29'),(151,222,'system','dbsdu: hhiisdbvksug',NULL,NULL,NULL,NULL,'2026-05-18 07:11:29'),(152,223,'system','dbsdu: hhiisdbvksug',NULL,NULL,NULL,NULL,'2026-05-18 07:11:29'),(153,224,'system','dbsdu: hhiisdbvksug',NULL,NULL,NULL,NULL,'2026-05-18 07:11:29'),(154,226,'system','dbsdu: hhiisdbvksug',NULL,NULL,NULL,NULL,'2026-05-18 07:11:29'),(155,227,'system','dbsdu: hhiisdbvksug',NULL,NULL,NULL,NULL,'2026-05-18 07:11:29'),(156,228,'system','dbsdu: hhiisdbvksug',NULL,NULL,NULL,NULL,'2026-05-18 07:11:29'),(157,230,'system','dbsdu: hhiisdbvksug',NULL,NULL,NULL,NULL,'2026-05-18 07:11:29'),(158,231,'system','dbsdu: hhiisdbvksug',NULL,NULL,NULL,NULL,'2026-05-18 07:11:29'),(159,232,'system','dbsdu: hhiisdbvksug',NULL,NULL,NULL,NULL,'2026-05-18 07:11:29'),(160,235,'comment','Anjali Joshi commented on your post.','post',423,205,'2026-05-18 09:07:14','2026-05-18 07:14:22'),(161,235,'like','Anjali Joshi liked your post.','post',423,205,'2026-05-18 09:07:14','2026-05-18 07:14:24'),(162,231,'connection','Anjali Joshi sent you a connection request.','user',205,NULL,NULL,'2026-05-18 07:14:27'),(163,235,'message','You have a new message.','message',205,NULL,'2026-05-18 09:07:14','2026-05-18 08:49:53'),(164,235,'comment','Anjali Joshi commented on your post.','post',423,205,'2026-05-18 09:07:14','2026-05-18 08:55:11'),(165,213,'application','New application for NodeJS Developer','job',308,NULL,NULL,'2026-05-18 08:55:24'),(166,206,'application','Your application for Frontend developer is now shortlisted.','application',23,NULL,NULL,'2026-05-18 09:07:00'),(167,206,'message','You have a new message.','message',205,NULL,NULL,'2026-05-18 09:14:56'),(168,206,'application','Your application for Frontend developer is now interview.','application',23,NULL,NULL,'2026-05-18 09:19:21'),(169,206,'application','Your application for Frontend developer is now applied.','application',23,NULL,'2026-05-21 14:38:42','2026-05-18 09:19:29'),(170,235,'application','New application for hu','job',312,NULL,'2026-05-18 17:32:04','2026-05-18 09:21:00'),(171,205,'message','You have a new message.','message',206,NULL,'2026-05-18 10:22:54','2026-05-18 09:22:40'),(172,205,'message','You have a new message.','message',206,NULL,'2026-05-18 10:22:54','2026-05-18 09:22:42'),(173,205,'message','You have a new message.','message',206,NULL,'2026-05-18 10:22:54','2026-05-18 09:22:44'),(174,205,'message','You have a new message.','message',206,NULL,'2026-05-18 10:22:54','2026-05-18 09:22:50'),(175,205,'comment','Priyanka Bisht commented on your post.','post',407,206,'2026-05-18 10:22:54','2026-05-18 09:23:23'),(176,207,'connection','Anjali Joshi accepted your connection request.','user',205,NULL,NULL,'2026-05-18 10:15:12'),(177,209,'system','Anjali Joshi shared a new post.','post',424,NULL,NULL,'2026-05-18 10:18:29'),(178,207,'system','Anjali Joshi shared a new post.','post',424,NULL,NULL,'2026-05-18 10:18:29'),(179,206,'system','Anjali Joshi shared a new post.','post',424,NULL,NULL,'2026-05-18 10:18:29'),(180,208,'connection','Anjali Joshi accepted your connection request.','user',205,NULL,NULL,'2026-05-18 10:22:11'),(181,206,'message','You have a new message.','message',205,NULL,'2026-05-21 14:38:28','2026-05-18 10:22:50'),(182,235,'comment','Anjali Joshi commented on your post.','post',423,205,'2026-05-18 17:32:04','2026-05-18 15:28:25'),(183,206,'connection','Anjali Joshi sent you a connection request.','user',205,NULL,'2026-05-21 14:38:33','2026-05-18 15:48:37'),(184,207,'connection','Anjali Joshi sent you a connection request.','user',205,NULL,NULL,'2026-05-18 15:48:39'),(185,205,'connection','Priyanka Bisht accepted your connection request.','user',206,NULL,'2026-05-18 16:25:07','2026-05-18 15:57:20'),(186,208,'connection','Priyanka Bisht accepted your connection request.','user',206,NULL,NULL,'2026-05-18 15:57:24'),(187,207,'connection','Priyanka Bisht accepted your connection request.','user',206,NULL,NULL,'2026-05-18 15:57:29'),(188,205,'connection','Priyanka Bisht sent you a connection request.','user',206,NULL,'2026-05-18 16:25:07','2026-05-18 16:00:04'),(189,209,'connection','Priyanka Bisht sent you a connection request.','user',206,NULL,NULL,'2026-05-18 16:00:08'),(190,206,'connection','Anjali Joshi accepted your connection request.','user',205,NULL,NULL,'2026-05-18 16:00:42'),(191,235,'application','New application for gigi','job',311,NULL,'2026-05-18 17:32:04','2026-05-18 16:04:38'),(192,235,'application','New application for gigi','job',311,NULL,'2026-05-18 17:32:04','2026-05-18 16:05:00'),(193,235,'application','New application for Java developer','job',313,NULL,'2026-05-18 17:32:04','2026-05-18 16:19:43'),(194,235,'application','New application for gigi','job',311,NULL,'2026-05-18 17:32:04','2026-05-18 16:19:59'),(195,235,'application','New application for Frontend developer','job',309,NULL,'2026-05-18 17:32:04','2026-05-18 16:20:13'),(196,226,'connection','Anjali Joshi sent you a connection request.','user',205,NULL,NULL,'2026-05-18 16:41:22'),(197,205,'connection','Sumit Negi sent you a connection request.','user',235,NULL,'2026-05-19 09:21:37','2026-05-18 17:08:22'),(198,214,'connection','Anjali Joshi sent you a connection request.','user',205,NULL,NULL,'2026-05-19 09:21:10'),(199,203,'like','Sumit Negi liked your post.','post',418,235,NULL,'2026-05-19 09:38:40'),(200,203,'comment','Sumit Negi commented on your post.','post',418,235,NULL,'2026-05-19 09:38:44'),(201,205,'application','Your application for Frontend developer is now reviewing.','application',30,NULL,'2026-05-19 16:11:12','2026-05-19 09:44:49'),(202,206,'application','Your application for Frontend developer is now shortlisted.','application',23,NULL,'2026-05-21 14:38:23','2026-05-19 09:44:58'),(203,235,'follow','Anjali Joshi started following your company.','user',205,205,'2026-05-19 10:33:58','2026-05-19 10:31:52'),(204,235,'follow','Anjali Joshi started following your company.','user',205,205,'2026-05-19 10:33:58','2026-05-19 10:31:57'),(205,235,'follow','Anjali Joshi started following your company.','user',205,205,'2026-05-19 10:33:58','2026-05-19 10:32:01'),(206,235,'follow','Anjali Joshi started following your company.','user',205,205,'2026-05-19 10:33:58','2026-05-19 10:32:27'),(207,235,'follow','Anjali Joshi started following your company.','user',205,205,'2026-05-19 10:33:58','2026-05-19 10:33:30'),(208,205,'message','You have a new message.','message',235,NULL,'2026-05-19 16:11:12','2026-05-19 10:36:35'),(209,205,'message','You have a new message.','message',235,NULL,'2026-05-19 16:11:12','2026-05-19 10:36:42'),(210,235,'message','You have a new message.','message',205,NULL,'2026-05-19 12:36:51','2026-05-19 10:37:14'),(211,206,'application','Your application for Frontend developer is now hired.','application',23,NULL,NULL,'2026-05-19 11:02:21'),(212,235,'application','New application for gigi','job',311,NULL,'2026-05-19 12:36:51','2026-05-19 12:36:00'),(213,205,'application','Your application for gigi is now shortlisted.','application',31,NULL,'2026-05-19 16:11:12','2026-05-19 12:36:45'),(214,235,'follow','Anjali Joshi started following your company.','user',205,205,'2026-05-19 14:16:30','2026-05-19 14:04:50'),(215,211,'follow','Sumit Negi started following your company.','user',235,235,NULL,'2026-05-19 14:16:04'),(216,207,'connection','Anjali Joshi sent you a connection request.','user',205,NULL,NULL,'2026-05-19 14:31:35'),(217,232,'connection','Anjali Joshi sent you a connection request.','user',205,NULL,NULL,'2026-05-19 16:15:18'),(218,234,'follow','Anjali Joshi started following your company.','user',205,205,NULL,'2026-05-19 16:41:57'),(219,231,'connection','Anjali Joshi sent you a connection request.','user',205,NULL,NULL,'2026-05-19 16:44:28'),(220,235,'message','You have a new message.','message',205,NULL,'2026-05-19 21:07:45','2026-05-19 16:44:46'),(221,230,'connection','Anjali Joshi sent you a connection request.','user',205,NULL,NULL,'2026-05-19 17:18:14'),(222,235,'message','You have a new message.','message',205,NULL,'2026-05-19 21:07:45','2026-05-19 17:33:59'),(223,205,'like','Sumit Negi liked your post.','post',437,235,'2026-05-19 23:11:26','2026-05-19 21:06:54'),(224,235,'application','New application for gigi','job',311,NULL,'2026-05-19 21:38:06','2026-05-19 21:37:35'),(225,235,'message','You have a new message.','message',206,NULL,'2026-05-19 22:13:09','2026-05-19 21:39:31'),(226,205,'system','Greetings: hello from admin',NULL,NULL,NULL,'2026-05-19 23:11:26','2026-05-19 23:10:44'),(227,206,'system','Greetings: hello from admin',NULL,NULL,NULL,'2026-05-21 14:38:21','2026-05-19 23:10:44'),(228,207,'system','Greetings: hello from admin',NULL,NULL,NULL,NULL,'2026-05-19 23:10:44'),(229,208,'system','Greetings: hello from admin',NULL,NULL,NULL,NULL,'2026-05-19 23:10:44'),(230,209,'system','Greetings: hello from admin',NULL,NULL,NULL,NULL,'2026-05-19 23:10:44'),(231,214,'system','Greetings: hello from admin',NULL,NULL,NULL,NULL,'2026-05-19 23:10:44'),(232,215,'system','Greetings: hello from admin',NULL,NULL,NULL,NULL,'2026-05-19 23:10:44'),(233,216,'system','Greetings: hello from admin',NULL,NULL,NULL,NULL,'2026-05-19 23:10:44'),(234,218,'system','Greetings: hello from admin',NULL,NULL,NULL,NULL,'2026-05-19 23:10:44'),(235,219,'system','Greetings: hello from admin',NULL,NULL,NULL,NULL,'2026-05-19 23:10:44'),(236,220,'system','Greetings: hello from admin',NULL,NULL,NULL,NULL,'2026-05-19 23:10:44'),(237,222,'system','Greetings: hello from admin',NULL,NULL,NULL,NULL,'2026-05-19 23:10:44'),(238,223,'system','Greetings: hello from admin',NULL,NULL,NULL,NULL,'2026-05-19 23:10:44'),(239,224,'system','Greetings: hello from admin',NULL,NULL,NULL,NULL,'2026-05-19 23:10:44'),(240,226,'system','Greetings: hello from admin',NULL,NULL,NULL,NULL,'2026-05-19 23:10:44'),(241,227,'system','Greetings: hello from admin',NULL,NULL,NULL,NULL,'2026-05-19 23:10:44'),(242,228,'system','Greetings: hello from admin',NULL,NULL,NULL,NULL,'2026-05-19 23:10:44'),(243,230,'system','Greetings: hello from admin',NULL,NULL,NULL,NULL,'2026-05-19 23:10:44'),(244,231,'system','Greetings: hello from admin',NULL,NULL,NULL,NULL,'2026-05-19 23:10:44'),(245,232,'system','Greetings: hello from admin',NULL,NULL,NULL,NULL,'2026-05-19 23:10:45'),(246,235,'like','Anjali Joshi liked your post.','post',439,205,'2026-05-21 09:21:24','2026-05-19 23:16:03'),(247,235,'message','You have a new message.','message',205,NULL,'2026-05-21 09:21:24','2026-05-20 22:23:36'),(248,235,'message','You have a new message.','message',205,NULL,'2026-05-21 09:21:24','2026-05-20 22:23:46'),(249,235,'message','You have a new message.','message',205,NULL,'2026-05-21 09:21:24','2026-05-20 22:26:19'),(250,235,'message','You have a new message.','message',205,NULL,'2026-05-21 09:21:24','2026-05-20 22:26:23'),(251,235,'message','You have a new message.','message',205,NULL,'2026-05-21 09:21:24','2026-05-20 22:31:31'),(252,235,'message','You have a new message.','message',205,NULL,'2026-05-21 09:21:24','2026-05-20 22:31:43'),(253,235,'message','You have a new message.','message',205,NULL,'2026-05-21 09:21:24','2026-05-20 22:34:21'),(254,235,'message','You have a new message.','message',205,NULL,'2026-05-21 09:21:24','2026-05-20 22:40:51'),(255,235,'message','You have a new message.','message',205,NULL,'2026-05-21 09:21:24','2026-05-20 22:41:05'),(256,205,'message','You have a new message.','message',235,NULL,'2026-05-21 09:19:07','2026-05-20 22:45:22'),(257,235,'comment','Anjali Joshi commented on your post.','post',439,205,'2026-05-21 09:21:24','2026-05-21 09:17:15'),(258,206,'connection','Anjali Joshi sent you a connection request.','user',205,NULL,'2026-05-21 14:38:37','2026-05-21 09:17:56'),(259,235,'application','New application for gigi','job',311,NULL,'2026-05-21 09:21:24','2026-05-21 09:18:52'),(260,205,'message','You have a new message.','message',235,NULL,'2026-05-21 10:24:34','2026-05-21 09:22:23'),(261,205,'connection','Priyanka Bisht accepted your connection request.','user',206,NULL,'2026-05-21 10:24:34','2026-05-21 09:42:28'),(262,205,'connection','Priyanka Bisht sent you a connection request.','user',206,NULL,'2026-05-21 10:24:34','2026-05-21 09:45:31'),(263,205,'connection','Ayush sent you a connection request.','user',236,NULL,'2026-05-21 10:24:34','2026-05-21 09:47:09'),(264,205,'connection','Krishna sent you a connection request.','user',237,NULL,'2026-05-21 10:24:34','2026-05-21 09:48:30'),(265,206,'connection','Anjali Joshi sent you a connection request.','user',205,NULL,NULL,'2026-05-21 10:07:58'),(266,237,'connection','Anjali Joshi accepted your connection request.','user',205,NULL,NULL,'2026-05-21 10:23:55'),(267,236,'connection','Anjali Joshi accepted your connection request.','user',205,NULL,NULL,'2026-05-21 10:24:06'),(268,209,'message','You have a new message.','message',205,NULL,NULL,'2026-05-21 10:24:21'),(269,210,'application','New application for PHP Developer','job',304,NULL,NULL,'2026-05-21 10:25:50'),(270,205,'system','hello: hiu',NULL,NULL,NULL,'2026-05-21 10:36:02','2026-05-21 10:35:17'),(271,206,'system','hello: hiu',NULL,NULL,NULL,'2026-05-21 14:36:28','2026-05-21 10:35:17'),(272,207,'system','hello: hiu',NULL,NULL,NULL,NULL,'2026-05-21 10:35:17'),(273,208,'system','hello: hiu',NULL,NULL,NULL,NULL,'2026-05-21 10:35:17'),(274,209,'system','hello: hiu',NULL,NULL,NULL,NULL,'2026-05-21 10:35:17'),(275,214,'system','hello: hiu',NULL,NULL,NULL,NULL,'2026-05-21 10:35:17'),(276,215,'system','hello: hiu',NULL,NULL,NULL,NULL,'2026-05-21 10:35:17'),(277,216,'system','hello: hiu',NULL,NULL,NULL,NULL,'2026-05-21 10:35:17'),(278,218,'system','hello: hiu',NULL,NULL,NULL,NULL,'2026-05-21 10:35:17'),(279,219,'system','hello: hiu',NULL,NULL,NULL,NULL,'2026-05-21 10:35:17'),(280,220,'system','hello: hiu',NULL,NULL,NULL,NULL,'2026-05-21 10:35:17'),(281,222,'system','hello: hiu',NULL,NULL,NULL,NULL,'2026-05-21 10:35:17'),(282,223,'system','hello: hiu',NULL,NULL,NULL,NULL,'2026-05-21 10:35:17'),(283,224,'system','hello: hiu',NULL,NULL,NULL,NULL,'2026-05-21 10:35:17'),(284,226,'system','hello: hiu',NULL,NULL,NULL,NULL,'2026-05-21 10:35:17'),(285,227,'system','hello: hiu',NULL,NULL,NULL,NULL,'2026-05-21 10:35:17'),(286,228,'system','hello: hiu',NULL,NULL,NULL,NULL,'2026-05-21 10:35:17'),(287,230,'system','hello: hiu',NULL,NULL,NULL,NULL,'2026-05-21 10:35:17'),(288,231,'system','hello: hiu',NULL,NULL,NULL,NULL,'2026-05-21 10:35:17'),(289,232,'system','hello: hiu',NULL,NULL,NULL,NULL,'2026-05-21 10:35:17'),(290,236,'system','hello: hiu',NULL,NULL,NULL,NULL,'2026-05-21 10:35:17'),(291,237,'system','hello: hiu',NULL,NULL,NULL,NULL,'2026-05-21 10:35:17'),(292,205,'system','bv: jhgs',NULL,NULL,NULL,'2026-05-21 12:45:50','2026-05-21 12:42:35'),(293,206,'system','bv: jhgs',NULL,NULL,NULL,'2026-05-21 14:36:26','2026-05-21 12:42:35'),(294,207,'system','bv: jhgs',NULL,NULL,NULL,NULL,'2026-05-21 12:42:35'),(295,208,'system','bv: jhgs',NULL,NULL,NULL,NULL,'2026-05-21 12:42:35'),(296,209,'system','bv: jhgs',NULL,NULL,NULL,NULL,'2026-05-21 12:42:35'),(297,214,'system','bv: jhgs',NULL,NULL,NULL,NULL,'2026-05-21 12:42:35'),(298,215,'system','bv: jhgs',NULL,NULL,NULL,NULL,'2026-05-21 12:42:35'),(299,216,'system','bv: jhgs',NULL,NULL,NULL,NULL,'2026-05-21 12:42:35'),(300,218,'system','bv: jhgs',NULL,NULL,NULL,NULL,'2026-05-21 12:42:35'),(301,219,'system','bv: jhgs',NULL,NULL,NULL,NULL,'2026-05-21 12:42:35'),(302,220,'system','bv: jhgs',NULL,NULL,NULL,NULL,'2026-05-21 12:42:35'),(303,222,'system','bv: jhgs',NULL,NULL,NULL,NULL,'2026-05-21 12:42:35'),(304,223,'system','bv: jhgs',NULL,NULL,NULL,NULL,'2026-05-21 12:42:35'),(305,224,'system','bv: jhgs',NULL,NULL,NULL,NULL,'2026-05-21 12:42:35'),(306,226,'system','bv: jhgs',NULL,NULL,NULL,NULL,'2026-05-21 12:42:35'),(307,227,'system','bv: jhgs',NULL,NULL,NULL,NULL,'2026-05-21 12:42:35'),(308,228,'system','bv: jhgs',NULL,NULL,NULL,NULL,'2026-05-21 12:42:35'),(309,230,'system','bv: jhgs',NULL,NULL,NULL,NULL,'2026-05-21 12:42:35'),(310,231,'system','bv: jhgs',NULL,NULL,NULL,NULL,'2026-05-21 12:42:35'),(311,232,'system','bv: jhgs',NULL,NULL,NULL,NULL,'2026-05-21 12:42:35'),(312,236,'system','bv: jhgs',NULL,NULL,NULL,NULL,'2026-05-21 12:42:35'),(313,237,'system','bv: jhgs',NULL,NULL,NULL,NULL,'2026-05-21 12:42:35'),(314,205,'system','kjhg: hgfghj',NULL,NULL,NULL,'2026-05-21 13:04:10','2026-05-21 13:04:03'),(315,206,'system','kjhg: hgfghj',NULL,NULL,NULL,'2026-05-21 14:36:32','2026-05-21 13:04:03'),(316,207,'system','kjhg: hgfghj',NULL,NULL,NULL,NULL,'2026-05-21 13:04:03'),(317,208,'system','kjhg: hgfghj',NULL,NULL,NULL,NULL,'2026-05-21 13:04:03'),(318,209,'system','kjhg: hgfghj',NULL,NULL,NULL,NULL,'2026-05-21 13:04:03'),(319,214,'system','kjhg: hgfghj',NULL,NULL,NULL,NULL,'2026-05-21 13:04:03'),(320,215,'system','kjhg: hgfghj',NULL,NULL,NULL,NULL,'2026-05-21 13:04:03'),(321,216,'system','kjhg: hgfghj',NULL,NULL,NULL,NULL,'2026-05-21 13:04:03'),(322,218,'system','kjhg: hgfghj',NULL,NULL,NULL,NULL,'2026-05-21 13:04:03'),(323,219,'system','kjhg: hgfghj',NULL,NULL,NULL,NULL,'2026-05-21 13:04:03'),(324,220,'system','kjhg: hgfghj',NULL,NULL,NULL,NULL,'2026-05-21 13:04:03'),(325,222,'system','kjhg: hgfghj',NULL,NULL,NULL,NULL,'2026-05-21 13:04:03'),(326,223,'system','kjhg: hgfghj',NULL,NULL,NULL,NULL,'2026-05-21 13:04:03'),(327,224,'system','kjhg: hgfghj',NULL,NULL,NULL,NULL,'2026-05-21 13:04:03'),(328,226,'system','kjhg: hgfghj',NULL,NULL,NULL,NULL,'2026-05-21 13:04:03'),(329,227,'system','kjhg: hgfghj',NULL,NULL,NULL,NULL,'2026-05-21 13:04:03'),(330,228,'system','kjhg: hgfghj',NULL,NULL,NULL,NULL,'2026-05-21 13:04:03'),(331,230,'system','kjhg: hgfghj',NULL,NULL,NULL,NULL,'2026-05-21 13:04:03'),(332,231,'system','kjhg: hgfghj',NULL,NULL,NULL,NULL,'2026-05-21 13:04:03'),(333,232,'system','kjhg: hgfghj',NULL,NULL,NULL,NULL,'2026-05-21 13:04:03'),(334,236,'system','kjhg: hgfghj',NULL,NULL,NULL,NULL,'2026-05-21 13:04:03'),(335,237,'system','kjhg: hgfghj',NULL,NULL,NULL,NULL,'2026-05-21 13:04:03'),(336,205,'system','cvb: fghjj',NULL,NULL,NULL,'2026-05-21 13:09:56','2026-05-21 13:09:50'),(337,206,'system','cvb: fghjj',NULL,NULL,NULL,'2026-05-21 14:36:25','2026-05-21 13:09:50'),(338,207,'system','cvb: fghjj',NULL,NULL,NULL,NULL,'2026-05-21 13:09:50'),(339,208,'system','cvb: fghjj',NULL,NULL,NULL,NULL,'2026-05-21 13:09:50'),(340,209,'system','cvb: fghjj',NULL,NULL,NULL,NULL,'2026-05-21 13:09:50'),(341,214,'system','cvb: fghjj',NULL,NULL,NULL,NULL,'2026-05-21 13:09:50'),(342,215,'system','cvb: fghjj',NULL,NULL,NULL,NULL,'2026-05-21 13:09:50'),(343,216,'system','cvb: fghjj',NULL,NULL,NULL,NULL,'2026-05-21 13:09:50'),(344,218,'system','cvb: fghjj',NULL,NULL,NULL,NULL,'2026-05-21 13:09:50'),(345,219,'system','cvb: fghjj',NULL,NULL,NULL,NULL,'2026-05-21 13:09:50'),(346,220,'system','cvb: fghjj',NULL,NULL,NULL,NULL,'2026-05-21 13:09:50'),(347,222,'system','cvb: fghjj',NULL,NULL,NULL,NULL,'2026-05-21 13:09:50'),(348,223,'system','cvb: fghjj',NULL,NULL,NULL,NULL,'2026-05-21 13:09:50'),(349,224,'system','cvb: fghjj',NULL,NULL,NULL,NULL,'2026-05-21 13:09:50'),(350,226,'system','cvb: fghjj',NULL,NULL,NULL,NULL,'2026-05-21 13:09:50'),(351,227,'system','cvb: fghjj',NULL,NULL,NULL,NULL,'2026-05-21 13:09:50'),(352,228,'system','cvb: fghjj',NULL,NULL,NULL,NULL,'2026-05-21 13:09:50'),(353,230,'system','cvb: fghjj',NULL,NULL,NULL,NULL,'2026-05-21 13:09:50'),(354,231,'system','cvb: fghjj',NULL,NULL,NULL,NULL,'2026-05-21 13:09:50'),(355,232,'system','cvb: fghjj',NULL,NULL,NULL,NULL,'2026-05-21 13:09:50'),(356,236,'system','cvb: fghjj',NULL,NULL,NULL,NULL,'2026-05-21 13:09:50'),(357,237,'system','cvb: fghjj',NULL,NULL,NULL,NULL,'2026-05-21 13:09:50'),(358,205,'system','nbv: kjh',NULL,NULL,NULL,'2026-05-21 13:14:10','2026-05-21 13:13:56'),(359,206,'system','nbv: kjh',NULL,NULL,NULL,'2026-05-21 14:36:21','2026-05-21 13:13:56'),(360,207,'system','nbv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:13:56'),(361,208,'system','nbv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:13:56'),(362,209,'system','nbv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:13:56'),(363,214,'system','nbv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:13:56'),(364,215,'system','nbv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:13:56'),(365,216,'system','nbv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:13:56'),(366,218,'system','nbv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:13:56'),(367,219,'system','nbv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:13:56'),(368,220,'system','nbv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:13:56'),(369,222,'system','nbv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:13:56'),(370,223,'system','nbv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:13:56'),(371,224,'system','nbv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:13:56'),(372,226,'system','nbv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:13:56'),(373,227,'system','nbv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:13:56'),(374,228,'system','nbv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:13:56'),(375,230,'system','nbv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:13:56'),(376,231,'system','nbv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:13:56'),(377,232,'system','nbv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:13:56'),(378,236,'system','nbv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:13:56'),(379,237,'system','nbv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:13:56'),(380,205,'system','kjhv: kjh',NULL,NULL,NULL,'2026-05-21 13:19:28','2026-05-21 13:18:39'),(381,206,'system','kjhv: kjh',NULL,NULL,NULL,'2026-05-21 14:36:20','2026-05-21 13:18:39'),(382,207,'system','kjhv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:18:39'),(383,208,'system','kjhv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:18:39'),(384,209,'system','kjhv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:18:39'),(385,214,'system','kjhv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:18:39'),(386,215,'system','kjhv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:18:39'),(387,216,'system','kjhv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:18:39'),(388,218,'system','kjhv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:18:39'),(389,219,'system','kjhv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:18:39'),(390,220,'system','kjhv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:18:39'),(391,222,'system','kjhv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:18:39'),(392,223,'system','kjhv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:18:39'),(393,224,'system','kjhv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:18:39'),(394,226,'system','kjhv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:18:39'),(395,227,'system','kjhv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:18:39'),(396,228,'system','kjhv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:18:39'),(397,230,'system','kjhv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:18:39'),(398,231,'system','kjhv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:18:39'),(399,232,'system','kjhv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:18:39'),(400,236,'system','kjhv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:18:39'),(401,237,'system','kjhv: kjh',NULL,NULL,NULL,NULL,'2026-05-21 13:18:39'),(402,205,'system','ncasjihcu: asb',NULL,NULL,NULL,'2026-05-21 13:28:59','2026-05-21 13:28:42'),(403,206,'system','ncasjihcu: asb',NULL,NULL,NULL,'2026-05-21 14:36:19','2026-05-21 13:28:42'),(404,207,'system','ncasjihcu: asb',NULL,NULL,NULL,NULL,'2026-05-21 13:28:42'),(405,208,'system','ncasjihcu: asb',NULL,NULL,NULL,NULL,'2026-05-21 13:28:42'),(406,209,'system','ncasjihcu: asb',NULL,NULL,NULL,NULL,'2026-05-21 13:28:42'),(407,214,'system','ncasjihcu: asb',NULL,NULL,NULL,NULL,'2026-05-21 13:28:42'),(408,215,'system','ncasjihcu: asb',NULL,NULL,NULL,NULL,'2026-05-21 13:28:42'),(409,216,'system','ncasjihcu: asb',NULL,NULL,NULL,NULL,'2026-05-21 13:28:42'),(410,218,'system','ncasjihcu: asb',NULL,NULL,NULL,NULL,'2026-05-21 13:28:42'),(411,219,'system','ncasjihcu: asb',NULL,NULL,NULL,NULL,'2026-05-21 13:28:42'),(412,220,'system','ncasjihcu: asb',NULL,NULL,NULL,NULL,'2026-05-21 13:28:42'),(413,222,'system','ncasjihcu: asb',NULL,NULL,NULL,NULL,'2026-05-21 13:28:42'),(414,223,'system','ncasjihcu: asb',NULL,NULL,NULL,NULL,'2026-05-21 13:28:42'),(415,224,'system','ncasjihcu: asb',NULL,NULL,NULL,NULL,'2026-05-21 13:28:43'),(416,226,'system','ncasjihcu: asb',NULL,NULL,NULL,NULL,'2026-05-21 13:28:43'),(417,227,'system','ncasjihcu: asb',NULL,NULL,NULL,NULL,'2026-05-21 13:28:43'),(418,228,'system','ncasjihcu: asb',NULL,NULL,NULL,NULL,'2026-05-21 13:28:43'),(419,230,'system','ncasjihcu: asb',NULL,NULL,NULL,NULL,'2026-05-21 13:28:43'),(420,231,'system','ncasjihcu: asb',NULL,NULL,NULL,NULL,'2026-05-21 13:28:43'),(421,232,'system','ncasjihcu: asb',NULL,NULL,NULL,NULL,'2026-05-21 13:28:43'),(422,236,'system','ncasjihcu: asb',NULL,NULL,NULL,NULL,'2026-05-21 13:28:43'),(423,237,'system','ncasjihcu: asb',NULL,NULL,NULL,NULL,'2026-05-21 13:28:43'),(424,205,'system','greetings: hello',NULL,NULL,NULL,'2026-05-21 14:22:59','2026-05-21 14:21:53'),(425,206,'system','greetings: hello',NULL,NULL,NULL,'2026-05-21 14:36:22','2026-05-21 14:21:53'),(426,207,'system','greetings: hello',NULL,NULL,NULL,NULL,'2026-05-21 14:21:53'),(427,208,'system','greetings: hello',NULL,NULL,NULL,NULL,'2026-05-21 14:21:53'),(428,209,'system','greetings: hello',NULL,NULL,NULL,NULL,'2026-05-21 14:21:53'),(429,214,'system','greetings: hello',NULL,NULL,NULL,NULL,'2026-05-21 14:21:53'),(430,215,'system','greetings: hello',NULL,NULL,NULL,NULL,'2026-05-21 14:21:53'),(431,216,'system','greetings: hello',NULL,NULL,NULL,NULL,'2026-05-21 14:21:53'),(432,218,'system','greetings: hello',NULL,NULL,NULL,NULL,'2026-05-21 14:21:53'),(433,219,'system','greetings: hello',NULL,NULL,NULL,NULL,'2026-05-21 14:21:53'),(434,220,'system','greetings: hello',NULL,NULL,NULL,NULL,'2026-05-21 14:21:53'),(435,222,'system','greetings: hello',NULL,NULL,NULL,NULL,'2026-05-21 14:21:53'),(436,223,'system','greetings: hello',NULL,NULL,NULL,NULL,'2026-05-21 14:21:53'),(437,224,'system','greetings: hello',NULL,NULL,NULL,NULL,'2026-05-21 14:21:53'),(438,226,'system','greetings: hello',NULL,NULL,NULL,NULL,'2026-05-21 14:21:53'),(439,227,'system','greetings: hello',NULL,NULL,NULL,NULL,'2026-05-21 14:21:53'),(440,228,'system','greetings: hello',NULL,NULL,NULL,NULL,'2026-05-21 14:21:53'),(441,230,'system','greetings: hello',NULL,NULL,NULL,NULL,'2026-05-21 14:21:53'),(442,231,'system','greetings: hello',NULL,NULL,NULL,NULL,'2026-05-21 14:21:53'),(443,232,'system','greetings: hello',NULL,NULL,NULL,NULL,'2026-05-21 14:21:53'),(444,236,'system','greetings: hello',NULL,NULL,NULL,NULL,'2026-05-21 14:21:53'),(445,237,'system','greetings: hello',NULL,NULL,NULL,NULL,'2026-05-21 14:21:53'),(446,205,'system','jhg: hi',NULL,NULL,NULL,'2026-05-21 14:32:14','2026-05-21 14:27:15'),(447,206,'system','jhg: hi',NULL,NULL,NULL,'2026-05-21 14:36:23','2026-05-21 14:27:15'),(448,207,'system','jhg: hi',NULL,NULL,NULL,NULL,'2026-05-21 14:27:15'),(449,208,'system','jhg: hi',NULL,NULL,NULL,NULL,'2026-05-21 14:27:15'),(450,209,'system','jhg: hi',NULL,NULL,NULL,NULL,'2026-05-21 14:27:15'),(451,214,'system','jhg: hi',NULL,NULL,NULL,NULL,'2026-05-21 14:27:15'),(452,215,'system','jhg: hi',NULL,NULL,NULL,NULL,'2026-05-21 14:27:15'),(453,216,'system','jhg: hi',NULL,NULL,NULL,NULL,'2026-05-21 14:27:15'),(454,218,'system','jhg: hi',NULL,NULL,NULL,NULL,'2026-05-21 14:27:15'),(455,219,'system','jhg: hi',NULL,NULL,NULL,NULL,'2026-05-21 14:27:15'),(456,220,'system','jhg: hi',NULL,NULL,NULL,NULL,'2026-05-21 14:27:15'),(457,222,'system','jhg: hi',NULL,NULL,NULL,NULL,'2026-05-21 14:27:15'),(458,223,'system','jhg: hi',NULL,NULL,NULL,NULL,'2026-05-21 14:27:15'),(459,224,'system','jhg: hi',NULL,NULL,NULL,NULL,'2026-05-21 14:27:15'),(460,226,'system','jhg: hi',NULL,NULL,NULL,NULL,'2026-05-21 14:27:15'),(461,227,'system','jhg: hi',NULL,NULL,NULL,NULL,'2026-05-21 14:27:15'),(462,228,'system','jhg: hi',NULL,NULL,NULL,NULL,'2026-05-21 14:27:15'),(463,230,'system','jhg: hi',NULL,NULL,NULL,NULL,'2026-05-21 14:27:15'),(464,231,'system','jhg: hi',NULL,NULL,NULL,NULL,'2026-05-21 14:27:15'),(465,232,'system','jhg: hi',NULL,NULL,NULL,NULL,'2026-05-21 14:27:15'),(466,236,'system','jhg: hi',NULL,NULL,NULL,NULL,'2026-05-21 14:27:15'),(467,237,'system','jhg: hi',NULL,NULL,NULL,NULL,'2026-05-21 14:27:15'),(468,205,'message','You have a new message.','message',206,NULL,NULL,'2026-05-21 14:38:59'),(469,205,'connection','Priyanka Bisht accepted your connection request.','user',206,NULL,'2026-05-21 15:40:41','2026-05-21 14:39:07'),(470,205,'connection','Gunjan Suyal sent you a connection request.','user',239,NULL,'2026-05-21 15:40:38','2026-05-21 15:19:50'),(471,205,'connection','Shivangi sent you a connection request.','user',240,NULL,'2026-05-21 15:40:35','2026-05-21 15:38:21'),(472,205,'connection','Aditi sent you a connection request.','user',241,NULL,NULL,'2026-05-21 16:20:05'),(473,206,'connection','Aditi sent you a connection request.','user',241,NULL,NULL,'2026-05-21 16:22:25'),(474,207,'connection','Aditi sent you a connection request.','user',241,NULL,NULL,'2026-05-21 16:22:26'),(475,208,'connection','Aditi sent you a connection request.','user',241,NULL,NULL,'2026-05-21 16:22:30'),(476,206,'connection','Shivangi sent you a connection request.','user',240,NULL,NULL,'2026-05-21 16:30:31'),(477,205,'like','nikhil liked your post.','post',440,242,NULL,'2026-05-21 16:43:49'),(478,205,'comment','nikhil commented on your post.','post',440,242,NULL,'2026-05-21 16:44:03'),(479,205,'connection','nikhil sent you a connection request.','user',242,NULL,NULL,'2026-05-21 16:44:08'),(480,241,'connection','nikhil sent you a connection request.','user',242,NULL,NULL,'2026-05-21 16:44:28'),(481,239,'connection','nikhil sent you a connection request.','user',242,NULL,NULL,'2026-05-21 16:44:39'),(482,205,'connection','nikhil sent you a connection request.','user',242,NULL,NULL,'2026-05-21 16:44:49'),(483,205,'application','Your application for gigi is now rejected.','application',33,NULL,'2026-05-22 14:24:39','2026-05-21 16:53:05'),(484,206,'application','Your application for Frontend developer is now reviewing.','application',23,NULL,NULL,'2026-05-21 16:53:16'),(485,205,'connection','Pawan sent you a connection request.','user',243,NULL,'2026-05-22 13:01:21','2026-05-22 10:47:02');
/*!40000 ALTER TABLE `user_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('user','company') COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `headline` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `location` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resume` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skills` text COLLATE utf8mb4_unicode_ci,
  `experience` text COLLATE utf8mb4_unicode_ci,
  `education` text COLLATE utf8mb4_unicode_ci,
  `certifications` text COLLATE utf8mb4_unicode_ci,
  `languages` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_links` text COLLATE utf8mb4_unicode_ci,
  `profile_public` tinyint(1) DEFAULT '1',
  `status` enum('active','blocked','pending','suspended') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `email_verified` tinyint(1) DEFAULT '0',
  `email_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_status` (`status`),
  KEY `idx_role` (`role`),
  FULLTEXT KEY `ft_users_search` (`name`,`headline`)
) ENGINE=InnoDB AUTO_INCREMENT=247 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (201,'TalentBridge Recruiter','recruiter@demo.com','9999991001','$2y$10$3waF5x/nPt43oJoCidCc7e4cdkz9GxcMjvVyMJVlZckLBNjd14P96','company',NULL,NULL,'Senior Technical Recruiter','Hiring engineers for modern product teams.','Remote','https://talentbridge.example.com',NULL,'Hiring, Sourcing, Screening','Recruiter at TalentBridge','MBA HR',NULL,'English',NULL,1,'active',1,NULL,NULL,NULL,NULL,'2026-05-17 13:43:20','2026-05-17 13:43:20'),(203,'Anjali Joshi','anjalijoshi.092004@gmail.com','','$2y$10$9ofz7B.9LqUpLZ1dqkmideKxHvObqFgTQlv/NYF7c16Yi5fdonT1q','company',NULL,NULL,'Technical Recruiter',NULL,'Dehradun',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,'5432d6a7238c1774e73e20bacc6f9e91e740ef0361b1de18',NULL,NULL,NULL,'2026-05-17 13:58:24','2026-05-17 13:58:24'),(205,'Anjali Joshi','anjalijoshi.200409@gmail.com','6396444909','$2y$10$zSMoy9fSwVa3vP6gaF479.P5oiUVyurj52Ok073EO5cD8IuMkymMO','user','uploads/avatars/50515d2034c3789da2520355.png','uploads/covers/745c8cbeb03abdfb9994edc5.jpg','Software Engineer','Software Engineer Working at Ebizon\r\nHey, I am an MCA student at Graphic Era Hill University, Dehradun, with a strong foundation in C++, Java and web technologies. \r\n\r\nI am passionate about software development, problem-solving, and continuous learning. Actively improving my DSA skills and open to internships and entry-level opportunities.','Dehradun, Uttarakhand','google.com','uploads/resumes/b279380bc65f08cdc9049cd8.pdf','JAva,C++','Fresher','BCA\r\nMCA','Java','Hindi, English','instagram.com',0,'active',1,'a658575a218f7d9a25507195db41767a3198466db9e19297','68fa371cd8300b92c568904fefcec3e45328d0fcf7645e51a1ae10f6dfde169c','2026-05-23 13:11:23','2026-05-23 15:32:03','2026-05-17 16:28:10','2026-05-23 15:32:03'),(206,'Priyanka Bisht','priyanka@gmail.com','8765567898','$2y$10$h3mqSEf.gsE18wp8ldRPWu5FNp1F0Ef7b6SvjvXqTo2twpusPuwJe','user','uploads/avatars/f52f4207b0b4a5241bf6bc78.jpg','uploads/covers/6fcc497719eb39b30b59da5d.jpg','Frontend Developer','Frontend developer at amazon','Hyderabad','https://www.ebizondigital.com/','uploads/resumes/be735618b9474241f52ea240.pdf','Java','Fresher','Mca','Java','Hindi, English','instagram.com',1,'active',1,'59626042fcf257f7e824bec997441985c0e7aac359e63757',NULL,NULL,'2026-05-21 14:36:06','2026-05-17 16:32:31','2026-05-21 14:36:06'),(207,'Gunjan Suyal','gunjansuyal@gmail.com','98765345678','$2y$10$71QZGc8CPnXr3TObNzi4Cexi6N9L.i/BVlDtmQwejFOxPM6IWYJKe','user','uploads/avatars/a930ada88aeed2a0d8dfb7df.jpg','uploads/covers/a02d4eecfe02739b420ad807.jpg','Software Engineer','Working at amazon','Dehradun, Uttarakhand','https://www.ebizondigital.com/','uploads/resumes/747c625b07b00dd42b068d48.pdf','dufieur','9hgyijb','kgifhrbn','dfggr','ewfwgr','efrg',1,'active',1,'81808f400aebc1b81450884f07839b63f7f1cb03c3879070',NULL,NULL,NULL,'2026-05-17 16:35:38','2026-05-17 16:36:54'),(208,'Ayushi','ayushi@gmail.com','98765546789','$2y$10$7C0nhU8NlPZGXvQD3rRVfuCzU1kgAsVoyP4X7wqTvmR1mWbxFQGjS','user','uploads/avatars/c330b43dbb69738add6fceb2.jpg','uploads/covers/7e2bb5ac010f89979b15adeb.jpg','Software Engineer','werth','Hyderabad','sdgfhjh','uploads/resumes/65fbb08ce564b18da186a899.pdf','2345tgfd','ergh','3erf','dtrds','sdd','fgnkj',1,'active',1,'5a15d622a3de770b4de62ae88fe44f4ff243bec96664c507',NULL,NULL,NULL,'2026-05-17 16:38:29','2026-05-17 16:39:48'),(209,'Anjali Purohit','anjali@gmail.com','7655898999','$2y$10$AJHhzY/TLPViFi3NpQsjhOQbmf9mfbZkZYl8SN.TKs7Cs6O6HmKVq','user','uploads/avatars/acccc48d5d9331590432572d.jpg','uploads/covers/8b35a6fbbd39d199101a7e8b.jpg','Developer','hxusgcsdug','Dehradun','https://www.ebizondigital.com/','uploads/resumes/4eaf472b4c0263543c5c220c.pdf','cbjsdg','chsgc','cbhus','sbdhus','cbhsd','csdh',1,'active',1,'49dd88a6b60fec9c4b081e09835d8d61d2947e276a385bbc',NULL,NULL,NULL,'2026-05-17 16:41:17','2026-05-17 16:43:36'),(210,'Anjali Joshi','anjali.2004.joshi@gmail.com','','$2y$10$f21pZ5zzd1orLx.6pZ926OXPHuvqpRcibzwl/ZlhT2KXr1gpEg38K','company',NULL,NULL,'Recruiter',NULL,'Dehradun, Uttarakhand',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,'046a455473d1cae1c5ef24498ed95bb2b5795f6c67226627',NULL,NULL,NULL,'2026-05-17 16:50:35','2026-05-17 16:50:35'),(211,'Diksha Bhatia','diksha123@gmail.com','','$2y$10$cO/lAsVqDcshQS7G6/m0a.aSwjjbTh.novwhumO6XkpmNKL7kk4GK','company',NULL,NULL,'Recruiter',NULL,'Hyderabad',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,'2c83bedb8586db89c027353b949858273d4308397d2c17b7',NULL,NULL,NULL,'2026-05-17 16:52:18','2026-05-17 16:52:18'),(212,'Akshita Negi','akshita@gmail.com','','$2y$10$BXJ4mTlsvSmJb47wlZjmzuhJ3iLwlVkLeEWD8jgZG09IhgEwBB7Km','company',NULL,NULL,'Recruiter',NULL,'Bangalore',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,'eef2d8010a046abbff1ae9d7e6159805645d02008f79c253',NULL,NULL,NULL,'2026-05-17 16:53:23','2026-05-17 16:53:23'),(213,'Shivangi','shivangi@gmail.com','','$2y$10$.zOJSMHNoQsfoxu9D.Ob5efGrbLusrLYMqV5diBJp5Gvm9wxxW5TW','company',NULL,NULL,'Recruiter',NULL,'Bangalore',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,'a6ec0b451592e101a2396ba480c066fe3384a19522a3ba4d',NULL,NULL,NULL,'2026-05-17 16:54:42','2026-05-17 16:54:42'),(214,'User 1','user1@test.com','9000000001','$2y$10$demo','user','uploads/avatars/555d8dd94afc4b8a43ee8e7a.jpg',NULL,'PHP Developer','Bio 1','Delhi','https://site1.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,NULL,NULL,NULL,NULL,'2026-05-17 17:00:04','2026-05-19 23:12:01'),(215,'User 2','user2@test.com','9000000002','$2y$10$demo','user','uploads/avatars/6e6916ba6be3bbfb6971b085.jpg',NULL,'Frontend Developer','Bio 2','Mumbai','https://site2.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,NULL,NULL,NULL,NULL,'2026-05-17 17:00:04','2026-05-19 23:12:14'),(216,'User 3','user3@test.com','9000000003','$2y$10$demo','user','uploads/avatars/3d74d6aac47b2fb3bc8acef9.jpg',NULL,'Backend Engineer','Bio 3','Noida','https://site3.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,NULL,NULL,NULL,NULL,'2026-05-17 17:00:04','2026-05-21 09:32:01'),(217,'User 4','user4@test.com','9000000004','$2y$10$demo','company',NULL,NULL,'HR Manager','Bio 4','Pune','https://site4.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,NULL,NULL,NULL,NULL,'2026-05-17 17:00:04','2026-05-17 17:00:04'),(218,'User 5','user5@test.com','9000000005','$2y$10$demo','user','uploads/avatars/32c8858bec9299120885a373.jpg',NULL,'Designer','Bio 5','Bangalore','https://site5.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,NULL,NULL,NULL,NULL,'2026-05-17 17:00:04','2026-05-21 09:31:49'),(219,'User 6','user6@test.com','9000000006','$2y$10$demo','user','uploads/avatars/6af45fab4871d964dabd925d.jpg',NULL,'Laravel Dev','Bio 6','Delhi','https://site6.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,NULL,NULL,NULL,NULL,'2026-05-17 17:00:04','2026-05-21 09:31:33'),(220,'User 7','user7@test.com','9000000007','$2y$10$demo','user','uploads/avatars/fe16a4900f562c899a6e46f0.jpg',NULL,'React Dev','Bio 7','Jaipur','https://site7.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,NULL,NULL,NULL,NULL,'2026-05-17 17:00:04','2026-05-21 09:31:20'),(221,'User 8','user8@test.com','9000000008','$2y$10$demo','company',NULL,NULL,'Recruiter','Bio 8','Lucknow','https://site8.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,NULL,NULL,NULL,NULL,'2026-05-17 17:00:04','2026-05-17 17:00:04'),(222,'User 9','user9@test.com','9000000009','$2y$10$demo','user','uploads/avatars/78d8d0d31e7a5d31ba681b86.jpg',NULL,'NodeJS Dev','Bio 9','Chennai','https://site9.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,NULL,NULL,NULL,NULL,'2026-05-17 17:00:04','2026-05-21 09:31:03'),(223,'User 10','user10@test.com','9000000010','$2y$10$demo','user','uploads/avatars/66d380929defc1335700b82d.jpg',NULL,'Java Dev','Bio 10','Hyderabad','https://site10.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,NULL,NULL,NULL,NULL,'2026-05-17 17:00:04','2026-05-21 09:29:50'),(224,'User 11','user11@test.com','9000000011','$2y$10$demo','user','uploads/avatars/589580a3fca26adfba6b8a49.jpg',NULL,'Python Dev','Bio 11','Kolkata','https://site11.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,NULL,NULL,NULL,NULL,'2026-05-17 17:00:04','2026-05-21 09:30:50'),(225,'User 12','user12@test.com','9000000012','$2y$10$demo','company',NULL,NULL,'HR Executive','Bio 12','Delhi','https://site12.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,NULL,NULL,NULL,NULL,'2026-05-17 17:00:04','2026-05-17 17:00:04'),(226,'User 13','user13@test.com','9000000013','$2y$10$demo','user','uploads/avatars/23240477d7e0e7ab8c80ea07.jpg',NULL,'Angular Dev','Bio 13','Noida','https://site13.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,NULL,NULL,NULL,NULL,'2026-05-17 17:00:04','2026-05-21 09:30:30'),(227,'User 14','user14@test.com','9000000014','$2y$10$demo','user','uploads/avatars/4b392b303766c9a5566d9918.jpg',NULL,'VueJS Dev','Bio 14','Mumbai','https://site14.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,NULL,NULL,NULL,NULL,'2026-05-17 17:00:04','2026-05-21 09:29:36'),(228,'User 15','user15@test.com','9000000015','$2y$10$demo','user','uploads/avatars/f63015b03a8363ebdd1bca8e.png',NULL,'SEO Expert','Bio 15','Pune','https://site15.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,NULL,NULL,NULL,NULL,'2026-05-17 17:00:04','2026-05-21 09:29:26'),(229,'User 16','user16@test.com','9000000016','$2y$10$demo','company',NULL,NULL,'Talent Manager','Bio 16','Delhi','https://site16.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,NULL,NULL,NULL,NULL,'2026-05-17 17:00:04','2026-05-17 17:00:04'),(230,'User 17','user17@test.com','9000000017','$2y$10$demo','user','uploads/avatars/9094030cb427cbe49ddfd3e7.jpg',NULL,'QA Engineer','Bio 17','Gurgaon','https://site17.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,NULL,NULL,NULL,NULL,'2026-05-17 17:00:04','2026-05-21 09:29:13'),(231,'User 18','user18@test.com','9000000018','$2y$10$demo','user','uploads/avatars/bc67fc8913bca264c4d92c42.jpg',NULL,'DevOps Engineer','Bio 18','Noida','https://site18.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,NULL,NULL,NULL,NULL,'2026-05-17 17:00:04','2026-05-21 09:26:20'),(232,'User 19','user19@test.com','9000000019','$2y$10$demo','user','uploads/avatars/7845382fb4103efa5f4da72b.jpg',NULL,'AI Engineer','Bio 19','Bangalore','https://site19.com',NULL,'','','','','',NULL,1,'active',1,NULL,NULL,NULL,NULL,'2026-05-17 17:00:04','2026-05-19 22:50:51'),(233,'User 20','user20@test.com','9000000020','$2y$10$demo','company',NULL,NULL,'Tech Recruiter','Bio 20','Hyderabad','https://site20.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,NULL,NULL,NULL,NULL,'2026-05-17 17:00:04','2026-05-17 17:00:04'),(234,'Sumit Negi','anjali.2004.joshii@gmail.com','','$2y$10$.TKogCor.HyzVJ4f9vLdyOshIy1PXdLV8u0VU1PaNXlyfyMYPJKJS','company',NULL,NULL,'Recruiter',NULL,'Mumbai',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,'c01da03bc03056c9a505b1c7fe8d317a55fdd488e3cdfb9c',NULL,NULL,NULL,'2026-05-17 18:29:08','2026-05-18 07:09:12'),(235,'Sumit Negi','sumitnegi8445@gmail.com','9876567898','$2y$10$1ziEZ1SFz5Y0SDkzq3SZtOE3CFywcjutoz8VNqAWq7NLnb51Jox3K','company',NULL,NULL,'Technical Recruiter',NULL,'Bangalore',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,'5283dadb5044cd88603f7922f2e9cc80f46a30082730f39e',NULL,NULL,'2026-05-23 11:16:31','2026-05-17 18:54:53','2026-05-23 11:16:31'),(236,'Ayush','ayush@gamil.com','9876548898','$2y$10$2fcbBe44bLhMPSvIrI6PGu4jRtguv8oSO60IIOoGaQEl3G8OIE.dG','user','uploads/avatars/7ae6728649bc4e2ee2e11535.jpg',NULL,'AI Engineer','najcas','Mumbai','scjas',NULL,'ssssssssddddd','bjash','ahdashu','jbdj','ewfwgr','cjsdj',0,'active',1,'2690e121e13d5075afbdfbd6cd9c616a83c531b65adecfef',NULL,NULL,NULL,'2026-05-21 09:46:03','2026-05-21 09:47:05'),(237,'Krishna','krishnasb2005@gmail.com','','$2y$10$xWKG1WYttBTVvHA1RFSaAOjAk2pgxwa2hsy92QYMRZe4YR8ewhZku','user',NULL,NULL,'Software Engineer',NULL,'Mumbai',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,'bc90c0247cf7a8679d6944dc19be906b4cda3edaf14872ce',NULL,NULL,NULL,'2026-05-21 09:48:23','2026-05-21 09:48:23'),(238,'Priyanka Bisht','priyankabisht1977@gmail.com','','$2y$10$oBwVClb1uTHUy5Ch3qumk.HhLz8S7n0jmoflEClx381IkSACi5p1e','user',NULL,NULL,'Software Engineer',NULL,'Mumbai',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,'7fa58275242d41033066de632a1d7e11c9a986b3deba13eb',NULL,NULL,NULL,'2026-05-21 15:16:35','2026-05-21 15:16:35'),(239,'Gunjan Suyal','gunjansuyal496@gmail.com','','$2y$10$8Im4epSM3Fy02Lw47zMlCeykU/DcoIpnhbdhPmV3938KLRvqNAfaC','user','uploads/avatars/7832515530561a7595b0e543.jpg',NULL,'Software Engineer','','Bangalore','',NULL,'','','','','','',0,'active',1,'a37ac03c18a443994bf152fd72072701e5c234a30e014ae8',NULL,NULL,NULL,'2026-05-21 15:19:40','2026-05-21 15:20:09'),(240,'Shivangi','gunjansuyal29@gmail.com','','$2y$10$CgjISh.6ZEOs13z/XCq4c.tEfacTFV74fZqP4YpI182EeE0x1gIvq','user','uploads/avatars/80740a11ef11a63f01dbbd21.jpg',NULL,'Software Engineer','','Hyderabad','',NULL,'','','','','','',0,'active',1,'b2fa80b3a1f3829e51cab2d7c1fd574c7f19a008347f6d41',NULL,NULL,'2026-05-21 16:23:00','2026-05-21 15:38:09','2026-05-21 16:23:00'),(241,'Aditi','aditi@gmail.com','','$2y$10$wcWn3S5/olg4ntigNZK8zOMOW/thNsCYsUSpUPxWa9Cq2nerWW3VC','user','uploads/avatars/802e26f77d97b61354611070.jpg',NULL,'Software Engineer','','Mumbai','',NULL,'','','','','','',0,'active',1,'46d967a568d1364ec1127078da126e02b7fef393adb3807f',NULL,NULL,'2026-05-21 16:22:13','2026-05-21 16:19:54','2026-05-21 16:22:13'),(242,'nikhil','nagpaln201@gmail.com','','$2y$10$hR.35uQ85KhDSN1GCtPcd.laORDVBmhGPaYvsS5nHZjyJ9VE5KZ2i','user',NULL,NULL,'studentt',NULL,'harkipori',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,'58f218821919171dfaa308987ef5648191c1f8ba7acb1ea7',NULL,NULL,'2026-05-21 16:43:33','2026-05-21 16:41:24','2026-05-21 16:43:33'),(243,'Pawan','kumarpavan6654@gmail.com','','$2y$10$4pQSmKSzWm3Odeq3VskgmeXNM8IiucG/0VTQ9.qU1Tn1GPMNLFwfu','user',NULL,NULL,'Software Engineer',NULL,'Mumbai, Maharashtra, India',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,'dc73330bc2f1ed182317542e4c532a983bc665a56d28e42f',NULL,NULL,NULL,'2026-05-22 10:46:20','2026-05-22 10:46:20'),(244,'Gunjan','gunjan123@gmail.com','','$2y$10$dPqGX8wXgOUAshwYDJdPy.B.TfD8P4gqtuzrM0EJBehTZYPQO0tPa','user',NULL,NULL,'Java Dev',NULL,'Mumbai, Maharashtra, India',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,'7a5c9aed05a5e9c062be7d87b90242a04a03a71cf7a62559',NULL,NULL,NULL,'2026-05-22 14:27:29','2026-05-22 14:27:29'),(245,'Anjali Purohit','anjalipurohit2021@gmail.com','','$2y$10$I02KVfrarlwwtBvV4PhByOSV6cZq6BbOdLWcLS37QxJayIFRunJUW','user',NULL,NULL,'Software Engineer',NULL,'Bengaluru, Karnataka, India',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,'48baa64fdd1024a92ba0b034e1454584b915e1e94f3d42c0',NULL,NULL,NULL,'2026-05-22 16:29:47','2026-05-22 16:29:47'),(246,'Anjali','purohitanjali098@gmail.com','','$2y$10$chpIfetZQ0bYgwpqTauw9evbdM9rUrh51xoJW7.IUrf85tLrboTdG','user',NULL,NULL,'Software Engineer',NULL,'Dehradun, Uttarakhand, India',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'active',1,'97c92084725c702bc570b5fdbb14369608805036cf5cf09f',NULL,NULL,NULL,'2026-05-22 16:33:08','2026-05-22 16:33:08');
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

-- Dump completed on 2026-05-23 16:55:58
