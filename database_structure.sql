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
-- Table structure for table `IVR_Authenticate`
--

DROP TABLE IF EXISTS `IVR_Authenticate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IVR_Authenticate` (
  `Row` int(6) NOT NULL auto_increment,
  `Project_ID` int(11) default NULL,
  `User_ID` int(11) default NULL,
  `Password` int(11) default NULL,
  PRIMARY KEY  (`Row`),
  KEY `UserProject` (`Project_ID`,`User_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=10038563 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `KILL_TEST`
--

DROP TABLE IF EXISTS `KILL_TEST`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `KILL_TEST` (
  `id` int(11) NOT NULL auto_increment,
  `projectid` mediumint(24) default NULL,
  `phonenumber` varchar(10) default NULL,
  `lastattempt` datetime default '1970-01-01 00:00:00',
  `timezone` varchar(3) default NULL,
  `result` char(16) default NULL,
  `dialstatus` char(16) default NULL,
  `active` tinyint(1) default '0',
  `attempts` int(11) default '0',
  `ivrgroup` varchar(10) default NULL,
  `prov` varchar(10) default NULL,
  `temp` varchar(50) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `comp` (`projectid`,`phonenumber`)
) ENGINE=MyISAM AUTO_INCREMENT=3795277 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Results`
--

DROP TABLE IF EXISTS `Results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Results` (
  `Row` smallint(6) NOT NULL auto_increment,
  `Project_ID` int(11) default NULL,
  `User_ID` int(11) default NULL,
  `Question_ID` int(11) default NULL,
  `Answer` int(50) default NULL,
  PRIMARY KEY  (`Row`)
) ENGINE=MyISAM AUTO_INCREMENT=1333 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Temp`
--

DROP TABLE IF EXISTS `Temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Temp` (
  `id` int(11) NOT NULL auto_increment,
  `phonenumber` varchar(10) default NULL,
  `temp` varchar(20) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=97 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TimeZones`
--

DROP TABLE IF EXISTS `TimeZones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TimeZones` (
  `AreaCode` varchar(6) NOT NULL,
  `AreaCodeName` varchar(30) default NULL,
  `State` varchar(2) default NULL,
  `TimeZoneName` varchar(30) default NULL,
  `UTCWinter` int(11) default NULL,
  `UTCSummer` int(11) default NULL,
  PRIMARY KEY  (`AreaCode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `dean_poll_projects`
--

DROP TABLE IF EXISTS `dean_poll_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dean_poll_projects` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `auth` enum('none','userid','full') NOT NULL,
  `valid_users` text NOT NULL,
  `creation` datetime NOT NULL,
  `last_update` datetime NOT NULL,
  `last_response` datetime default NULL,
  `result_email` char(100) default NULL,
  `active` tinyint(1) NOT NULL,
  `callerid` varchar(255) default NULL,
  `timeout` int(11) default NULL,
  `dialplan_context` varchar(255) default NULL,
  `dialplan_extension` varchar(255) default NULL,
  `dialout_channel` varchar(255) default NULL,
  `time_start` varchar(5) default NULL,
  `time_end` varchar(5) default NULL,
  `pif_number` varchar(20) default NULL,
  `Notes` varchar(500) default NULL,
  `project_type` varchar(255) NOT NULL default 'regular',
  `project_state` varchar(255) NOT NULL default 'initial',
  `redial_interval` varchar(255) NOT NULL,
  `project_date` datetime NOT NULL,
  `redial_rounds` tinyint(4) NOT NULL default '1',
  `extra_field_titles` longtext,
  `lang` varchar(5) NOT NULL default 'eng',
  `list_last_upload` datetime default NULL,
  `fndp_active` tinyint(1) NOT NULL default '0',
  `redial_instruction` varchar(255) default 'N/A',
  `fndp_finished_round` tinyint(4) default '0',
  PRIMARY KEY  (`id`),
  KEY `active` (`active`),
  KEY `time_start` (`time_start`),
  KEY `time_end` (`time_end`)
) ENGINE=InnoDB AUTO_INCREMENT=964 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dean_poll_questions`
--

DROP TABLE IF EXISTS `dean_poll_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dean_poll_questions` (
  `question_id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL,
  `type` enum('single-digit','dtmf','recording','postal','phone','announce') default NULL,
  `data` varchar(255) default NULL,
  `next` varchar(255) NOT NULL,
  `first` tinyint(1) NOT NULL,
  UNIQUE KEY `UNIQUE` (`question_id`,`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=304101 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dean_poll_recordings`
--

DROP TABLE IF EXISTS `dean_poll_recordings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dean_poll_recordings` (
  `id` bigint(20) NOT NULL auto_increment,
  `recording` longblob NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dean_poll_responses`
--

DROP TABLE IF EXISTS `dean_poll_responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dean_poll_responses` (
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `callerid` varchar(64) default NULL,
  `project_id` bigint(20) NOT NULL,
  `user_id` varchar(20) default NULL,
  `question_id` bigint(20) NOT NULL,
  `response` varchar(255) NOT NULL default '',
  KEY `project_id` (`project_id`,`user_id`,`question_id`),
  KEY `IDX_PROJECT` (`project_id`),
  KEY `ndx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dean_poll_users`
--

DROP TABLE IF EXISTS `dean_poll_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dean_poll_users` (
  `userid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `passcode` int(11) NOT NULL,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `last_update` timestamp NOT NULL default '0000-00-00 00:00:00',
  `last_callerid` varchar(255) NOT NULL,
  `last_response` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`userid`,`passcode`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dialout_numbers`
--

DROP TABLE IF EXISTS `dialout_numbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dialout_numbers` (
  `id` int(11) NOT NULL auto_increment,
  `projectid` mediumint(24) default NULL,
  `phonenumber` varchar(10) default NULL,
  `lastattempt` datetime default '1970-01-01 00:00:00',
  `timezone` varchar(3) default NULL,
  `result` char(16) default NULL,
  `dialstatus` char(16) default NULL,
  `active` tinyint(1) default '0',
  `attempts` int(11) default '0',
  `ivrgroup` varchar(10) default NULL,
  `prov` varchar(10) default NULL,
  `temp` varchar(1000) default NULL,
  `extra_fields` longtext,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `comp` (`projectid`,`phonenumber`)
) ENGINE=MyISAM AUTO_INCREMENT=10129892 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dnc_list`
--

DROP TABLE IF EXISTS `dnc_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dnc_list` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `projectid` mediumint(9) NOT NULL,
  `phonenumber` varchar(10) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `userid` varchar(30) default NULL,
  `action` varchar(500) default NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7547 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_billing_info`
--

DROP TABLE IF EXISTS `project_billing_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_billing_info` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `projectid` bigint(20) unsigned NOT NULL,
  `billname` varchar(255) default NULL,
  `billaddress` varchar(255) default NULL,
  `billphone` varchar(255) default NULL,
  `billemail` varchar(255) default NULL,
  `billtype` varchar(255) default NULL,
  `billsetuptypes` varchar(255) default NULL,
  `billdatareturntypes` varchar(255) default NULL,
  `billfirstnameverifytypes` varchar(255) default NULL,
  `billinboundnumbertypes` varchar(255) default NULL,
  `billlanguagetypes` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=95 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `temp2`
--

DROP TABLE IF EXISTS `temp2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `temp2` (
  `number` char(10) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-01-15 10:52:05
