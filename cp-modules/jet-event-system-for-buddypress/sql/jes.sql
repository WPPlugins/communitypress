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
-- Table structure for table `wp_jet_events`
--

DROP TABLE IF EXISTS `wp_jet_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_jet_events` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `creator_id` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `etype` varchar(20) NOT NULL,
  `eventapproved` varchar(1) DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `description` longtext NOT NULL,
  `eventterms` longtext,
  `placedcountry` varchar(25) DEFAULT NULL,
  `placedstate` varchar(25) DEFAULT NULL,
  `placedcity` varchar(25) NOT NULL,
  `placedaddress` varchar(70) NOT NULL,
  `placednote` varchar(70) DEFAULT NULL,
  `placedgooglemap` varchar(250) DEFAULT NULL,
  `flyer` varchar(250) DEFAULT NULL,
  `newspublic` longtext,
  `newsprivate` longtext,
  `edtsd` varchar(16) NOT NULL,
  `edted` varchar(16) NOT NULL,
  `edtsth` varchar(2) NOT NULL DEFAULT '0',
  `edteth` varchar(2) NOT NULL DEFAULT '23',
  `edtstm` varchar(2) NOT NULL DEFAULT '0',
  `edtetm` varchar(2) NOT NULL DEFAULT '59',
  `edtsdunix` varchar(16) NOT NULL,
  `edtedunix` varchar(16) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'public',
  `grouplink` varchar(5) NOT NULL DEFAULT '0',
  `forumlink` varchar(5) NOT NULL DEFAULT '0',
  `enablesocial` tinyint(1) NOT NULL DEFAULT '0',
  `enable_forum` tinyint(1) NOT NULL DEFAULT '1',
  `date_created` datetime NOT NULL,
  `notify_timed_enable` varchar(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `creator_id` (`creator_id`),
  KEY `status` (`status`),
  KEY `etype` (`etype`),
  KEY `name` (`name`),
  KEY `eventapproved` (`eventapproved`),
  KEY `placedcity` (`placedcity`),
  KEY `placedcountry` (`placedcountry`),
  KEY `grouplink` (`grouplink`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_jet_events`
--

LOCK TABLES `wp_jet_events` WRITE;
/*!40000 ALTER TABLE `wp_jet_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_jet_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_jet_events_activity`
--

DROP TABLE IF EXISTS `wp_jet_events_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_jet_events_activity` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `creator_id` bigint(20) NOT NULL,
  `description` longtext NOT NULL,
  `a_datetime` varchar(18) NOT NULL,
  `a_datetime_unix` varchar(18) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `creator_id` (`creator_id`),
  KEY `a_datetime_unix` (`a_datetime_unix`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_jet_events_activity`
--

LOCK TABLES `wp_jet_events_activity` WRITE;
/*!40000 ALTER TABLE `wp_jet_events_activity` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_jet_events_activity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_jet_events_eventmeta`
--

DROP TABLE IF EXISTS `wp_jet_events_eventmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_jet_events_eventmeta` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) NOT NULL,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_jet_events_eventmeta`
--

LOCK TABLES `wp_jet_events_eventmeta` WRITE;
/*!40000 ALTER TABLE `wp_jet_events_eventmeta` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_jet_events_eventmeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_jet_events_members`
--

DROP TABLE IF EXISTS `wp_jet_events_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_jet_events_members` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `inviter_id` bigint(20) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `is_mod` tinyint(1) NOT NULL DEFAULT '0',
  `user_title` varchar(100) NOT NULL,
  `date_modified` datetime NOT NULL,
  `comments` longtext NOT NULL,
  `is_confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `is_banned` tinyint(1) NOT NULL DEFAULT '0',
  `invite_sent` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  KEY `is_admin` (`is_admin`),
  KEY `is_mod` (`is_mod`),
  KEY `user_id` (`user_id`),
  KEY `inviter_id` (`inviter_id`),
  KEY `is_confirmed` (`is_confirmed`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_jet_events_members`
--

LOCK TABLES `wp_jet_events_members` WRITE;
/*!40000 ALTER TABLE `wp_jet_events_members` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_jet_events_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'jesdb'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-09-22 23:25:56
