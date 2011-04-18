-- MySQL dump 10.13  Distrib 5.1.32, for apple-darwin9.5.0 (powerpc)
--
-- Host: localhost    Database: peoplepods_dev
-- ------------------------------------------------------
-- Server version	5.1.32

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
-- Table structure for table `activity`
--

DROP TABLE IF EXISTS `activity`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `activity` (
  `userId` bigint(12) DEFAULT NULL,
  `targetUserId` bigint(12) DEFAULT NULL,
  `targetContentId` bigint(12) DEFAULT NULL,
  `targetContentType` varchar(25) DEFAULT NULL,
  `resultContentId` bigint(12) DEFAULT NULL,
  `resultContentType` varchar(25) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `targetMessage` varchar(255) DEFAULT NULL,
  `userMessage` varchar(255) DEFAULT NULL,
  `gid` varchar(25) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `id` bigint(12) NOT NULL AUTO_INCREMENT,
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `gidx` (`gid`),
  KEY `uid` (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `alerts`
--

DROP TABLE IF EXISTS `alerts`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `alerts` (
  `id` bigint(12) NOT NULL AUTO_INCREMENT,
  `userId` bigint(12) DEFAULT NULL,
  `targetUserId` bigint(12) DEFAULT NULL,
  `targetContentId` bigint(12) DEFAULT NULL,
  `targetContentType` varchar(25) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `status` enum('new','read') DEFAULT 'new',
  PRIMARY KEY (`id`),
  KEY `uid` (`targetUserId`),
  KEY `targetidx` (`targetContentId`,`targetContentType`)
) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `comments` (
  `contentId` bigint(12) DEFAULT NULL,
  `profileId` bigint(12) DEFAULT NULL,
  `userId` bigint(12) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `comment` text,
  `type` varchar(12) DEFAULT NULL,
  `id` bigint(12) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `doc_idx` (`contentId`),
  KEY `pid` (`profileId`)
) ENGINE=MyISAM AUTO_INCREMENT=82 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `content`
--

DROP TABLE IF EXISTS `content`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `content` (
  `userId` bigint(12) DEFAULT NULL,
  `createdBy` bigint(12) DEFAULT NULL,
  `parentId` bigint(12) DEFAULT NULL,
  `groupId` bigint(12) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `changeDate` datetime DEFAULT NULL,
  `body` text,
  `headline` text,
  `link` varchar(1024) DEFAULT NULL,
  `type` varchar(12) DEFAULT NULL,
  `privacy` enum('public','friends_only','group_only','owner_only') DEFAULT 'public',
  `status` enum('new','approved','featured','hidden') DEFAULT 'new',
  `flagDate` datetime DEFAULT NULL,
  `id` bigint(12) NOT NULL AUTO_INCREMENT,
  `commentCount` bigint(12) DEFAULT '0',
  `yes_votes` int(11) DEFAULT '0',
  `no_votes` int(11) DEFAULT '0',
  `stub` text,
  `editDate` datetime DEFAULT NULL,
  `commentDate` datetime DEFAULT NULL,
  `hidden` int(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `datesort` (`date`),
  KEY `uidx` (`userId`),
  KEY `datex` (`date`),
  KEY `gidx` (`groupId`),
  FULLTEXT KEY `idx_full` (`headline`,`link`,`body`)
) ENGINE=MyISAM AUTO_INCREMENT=123 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `files` (
  `file_name` varchar(60) DEFAULT NULL,
  `original_name` varchar(60) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `extension` varchar(6) DEFAULT NULL,
  `mime_type` varchar(32) DEFAULT NULL,
  `userId` bigint(12) DEFAULT NULL,
  `contentId` bigint(12) DEFAULT NULL,
  `groupId` bigint(12) DEFAULT NULL,
  `changeDate` datetime DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `id` bigint(12) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `didx` (`contentId`),
  KEY `uidx` (`userId`),
  KEY `gid` (`groupId`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `flags`
--

DROP TABLE IF EXISTS `flags`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `flags` (
  `type` enum('content','user','group','comment','file') DEFAULT NULL,
  `itemId` bigint(12) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `value` text,
  `userId` bigint(12) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `id` bigint(12) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `lookup` (`type`,`itemId`,`name`,`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=77 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `groupMember`
--

DROP TABLE IF EXISTS `groupMember`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `groupMember` (
  `userId` bigint(12) DEFAULT NULL,
  `groupId` bigint(12) DEFAULT NULL,
  `type` enum('invitee','owner','manager','member') DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  UNIQUE KEY `nodupes` (`userId`,`groupId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `groups` (
  `groupname` varchar(255) DEFAULT NULL,
  `description` text,
  `stub` varchar(40) DEFAULT NULL,
  `type` enum('public','private') DEFAULT NULL,
  `userId` bigint(12) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `changeDate` datetime DEFAULT NULL,
  `id` bigint(12) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `invites`
--

DROP TABLE IF EXISTS `invites`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `invites` (
  `userId` bigint(12) DEFAULT NULL,
  `groupId` bigint(12) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `code` varchar(200) DEFAULT NULL,
  `id` bigint(12) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `messages` (
  `id` bigint(12) NOT NULL AUTO_INCREMENT,
  `userId` bigint(12) NOT NULL,
  `targetUserId` bigint(12) DEFAULT NULL,
  `fromId` bigint(12) NOT NULL,
  `message` text,
  `date` datetime DEFAULT NULL,
  `status` enum('new','read','flagged','spam') DEFAULT 'new',
  PRIMARY KEY (`id`),
  KEY `user_and_friend` (`userId`,`targetUserId`)
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `meta`
--

DROP TABLE IF EXISTS `meta`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `meta` (
  `type` enum('group','content','user','comment','file') NOT NULL,
  `itemId` bigint(12) NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` text NOT NULL,
  `id` bigint(12) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `u` (`type`,`itemId`,`name`),
  KEY `lookup` (`type`,`itemId`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `tagRef`
--

DROP TABLE IF EXISTS `tagRef`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tagRef` (
  `tagId` bigint(12) DEFAULT NULL,
  `contentId` bigint(12) DEFAULT NULL,
  `type` enum('pub','priv') DEFAULT NULL,
  `id` bigint(12) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tags` (
  `value` varchar(100) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `id` bigint(12) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `users` (
  `nick` varchar(255) DEFAULT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `stub` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `id` bigint(12) NOT NULL AUTO_INCREMENT,
  `password` varchar(100) DEFAULT NULL,
  `memberSince` datetime DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `homepage` varchar(255) DEFAULT NULL,
  `lastVisit` datetime DEFAULT NULL,
  `verificationKey` varchar(200) DEFAULT NULL,
  `authSecret` varchar(32) DEFAULT NULL,
  `passwordResetCode` varchar(32) DEFAULT NULL,
  `invitedBy` bigint(12) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-10-22 21:07:58
