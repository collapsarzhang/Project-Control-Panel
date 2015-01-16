-- MySQL dump 10.11
--
-- Host: localhost    Database: IVR_data
-- ------------------------------------------------------
-- Server version	5.0.95

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
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config` (
  `key` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `Comments` text,
  `id` tinyint(4) default NULL,
  PRIMARY KEY  (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config`
--

LOCK TABLES `config` WRITE;
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
INSERT INTO `config` VALUES ('watchdog_file','watchdog','Daemon will write to this file every cycle, consists of the timestamp and the iteration count\n******look for the file under value NOT the key name*********',1),('logVerbosity','4','Log verbosity, 7=debug, 6=info*, 5=notice, 4=warning, 3=err, 2=crit, 1=alert, 0=emerg',2),('sleepinterval','2','Daemon will \"wake\" every N seconds and poll for additional numbers. Do not set too low or too high. (2)',4),('configrefresh','30','Re-read the configuration from the database every N cycles (30)',5),('table_projects','dean_poll_projects','Name of the MySQL table containing the projects (projects)',6),('table_numbers','dialout_numbers','Name of the MySQL table containing the phone numbers (numbers)',7),('default-context','app-polling-outbound-answering-detect-project-test','Bridge connected calls to this context if there is no project-specified override (default)',8),('default-callerid','16042485257','Make calls using this callerid if there is no project-specified override ()',9),('default-timeout','40000','Timeout calls with no answer after X milliseconds if there is no project-specified override (30000)',10),('default-extension','s','Bridge connected calls to this extension if there is no project-specified override (s)',11),('default-channel','SIP/thinktel/NUMBER','Use this channel to place outbound calls, replacing NUMBER with the phone number to dial (Local/NUMBER@default)',12),('killswitch---disabled','0xAA55AA55','Set this to \"0xAA55AA55\" to force the daemon to shutdown when it refreshes its configuration next. Key must = killswitch to work',13),('callspersecond','3','Number of call originations in a single second. (1)\nCan run 15 when doing straight bvm\nuse 2 for agi poll',3),('secondary-channel','SIP/siproutes/85611NUMBER','the secondary channel for load balancing',14),('channel-percentage','50','defines the percentage of calls should go to primary(default) channel, the others will go to secondary channel',15);
/*!40000 ALTER TABLE `config` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-01-15 15:13:36
