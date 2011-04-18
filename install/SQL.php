<?

// this file contains all the SQL needed to create the PeoplePods database.


$tables = array();

$tables['activity'] = 'CREATE TABLE `activity` (
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
) ENGINE=MyISAM AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;';

$tables['alerts'] = "CREATE TABLE `alerts` (
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
) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=latin1;";

$tables['comments'] = 'CREATE TABLE `comments` (
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
) ENGINE=MyISAM AUTO_INCREMENT=82 DEFAULT CHARSET=utf8;';

$tables['content'] = "CREATE TABLE `content` (
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
) ENGINE=MyISAM AUTO_INCREMENT=123 DEFAULT CHARSET=utf8;";

$tables['files'] = 'CREATE TABLE `files` (
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
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;';

$tables['flags'] = "CREATE TABLE `flags` (
  `type` enum('content','user','group','comment','file') DEFAULT NULL,
  `itemId` bigint(12) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `value` text,
  `userId` bigint(12) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `id` bigint(12) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `lookup` (`type`,`itemId`,`name`,`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=77 DEFAULT CHARSET=utf8;";

$tables['groupMember'] = "CREATE TABLE `groupMember` (
  `userId` bigint(12) DEFAULT NULL,
  `groupId` bigint(12) DEFAULT NULL,
  `type` enum('invitee','owner','manager','member') DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  UNIQUE KEY `nodupes` (`userId`,`groupId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$tables['groups'] = "CREATE TABLE `groups` (
  `groupname` varchar(255) DEFAULT NULL,
  `description` text,
  `stub` varchar(40) DEFAULT NULL,
  `type` enum('public','private') DEFAULT NULL,
  `userId` bigint(12) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `changeDate` datetime DEFAULT NULL,
  `id` bigint(12) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;";

$tables['invites'] = "CREATE TABLE `invites` (
  `userId` bigint(12) DEFAULT NULL,
  `groupId` bigint(12) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `code` varchar(200) DEFAULT NULL,
  `id` bigint(12) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$tables['messages'] = "CREATE TABLE `messages` (
  `id` bigint(12) NOT NULL AUTO_INCREMENT,
  `userId` bigint(12) NOT NULL,
  `targetUserId` bigint(12) DEFAULT NULL,
  `fromId` bigint(12) NOT NULL,
  `message` text,
  `date` datetime DEFAULT NULL,
  `status` enum('new','read','flagged','spam') DEFAULT 'new',
  PRIMARY KEY (`id`),
  KEY `user_and_friend` (`userId`,`targetUserId`)
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;";

$tables['meta'] = "CREATE TABLE `meta` (
  `type` enum('group','content','user','comment','file') NOT NULL,
  `itemId` bigint(12) NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` text NOT NULL,
  `id` bigint(12) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `u` (`type`,`itemId`,`name`),
  KEY `lookup` (`type`,`itemId`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;";

$tables['tagRef'] = "CREATE TABLE `tagRef` (
  `tagId` bigint(12) DEFAULT NULL,
  `itemId` bigint(12) DEFAULT NULL,
  `type` enum('content','user','group','comment','file') DEFAULT NULL,
  `id` bigint(12) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `lookup` (`type`,`itemId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$tables['tags'] = "CREATE TABLE `tags` (
  `value` varchar(100) DEFAULT NULL,
  `weight` bigint(12) DEFAULT 0,
  `date` datetime DEFAULT NULL,
  `id` bigint(12) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$tables['users'] = "CREATE TABLE `users` (
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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;";