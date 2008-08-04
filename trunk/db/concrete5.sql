-- MySQL dump 10.10
--
-- Host: 127.0.0.1    Database: concrete_testbed07
-- ------------------------------------------------------
-- Server version	5.0.41

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
-- Table structure for table `AreaGroupBlockTypes`
--

DROP TABLE IF EXISTS `AreaGroupBlockTypes`;
CREATE TABLE `AreaGroupBlockTypes` (
  `cID` int(10) unsigned NOT NULL default '0',
  `arHandle` varchar(255) NOT NULL default '',
  `gID` int(10) unsigned NOT NULL default '0',
  `uID` int(10) unsigned NOT NULL default '0',
  `btID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cID`,`arHandle`,`gID`,`uID`,`btID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `AreaGroupBlockTypes`
--


/*!40000 ALTER TABLE `AreaGroupBlockTypes` DISABLE KEYS */;
LOCK TABLES `AreaGroupBlockTypes` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `AreaGroupBlockTypes` ENABLE KEYS */;

--
-- Table structure for table `AreaGroups`
--

DROP TABLE IF EXISTS `AreaGroups`;
CREATE TABLE `AreaGroups` (
  `cID` int(10) unsigned NOT NULL default '0',
  `arHandle` varchar(255) NOT NULL default '',
  `gID` int(10) unsigned NOT NULL default '0',
  `uID` int(10) unsigned NOT NULL default '0',
  `agPermissions` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`cID`,`arHandle`,`gID`,`uID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `AreaGroups`
--


/*!40000 ALTER TABLE `AreaGroups` DISABLE KEYS */;
LOCK TABLES `AreaGroups` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `AreaGroups` ENABLE KEYS */;

--
-- Table structure for table `Areas`
--


DROP TABLE IF EXISTS `Areas`;
CREATE TABLE `Areas` (
  `arID` int(10) unsigned NOT NULL auto_increment,
  `cID` int(10) unsigned NOT NULL default '0',
  `arHandle` varchar(255) NOT NULL default '',
  `arOverrideCollectionPermissions` tinyint(1) NOT NULL default '0',
  `arInheritPermissionsFromAreaOnCID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`arID`)
) ENGINE=MyISAM AUTO_INCREMENT=135 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Areas`
--


/*!40000 ALTER TABLE `Areas` DISABLE KEYS */;
LOCK TABLES `Areas` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `Areas` ENABLE KEYS */;

DROP TABLE IF EXISTS `BlockRelations`;
CREATE TABLE `BlockRelations` (
  `brID` int(10) unsigned NOT NULL auto_increment,
  `bID` int(10) unsigned NOT NULL default '0',
  `originalBID` int(10) unsigned NOT NULL default '0',
  `relationType` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`brID`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `BlockRelations`
--


/*!40000 ALTER TABLE `BlockRelations` DISABLE KEYS */;
LOCK TABLES `BlockRelations` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `BlockRelations` ENABLE KEYS */;


--
-- Table structure for table `BlockTypes`
--

DROP TABLE IF EXISTS `BlockTypes`;
CREATE TABLE `BlockTypes` (
  `btID` int(10) unsigned NOT NULL auto_increment,
  `btHandle` varchar(32) NOT NULL default '',
  `btName` varchar(128) NOT NULL default '',
  `btDescription` text null,
  `btActiveWhenAdded` tinyint(1) NOT NULL default '1',
  `btIsEditedInline` tinyint(1) NOT NULL default '0',
  `btCopyWhenPropagate` tinyint(1) NOT NULL default '0',
  `btIncludeAll` tinyint(1) NOT NULL default '0',
  `btIsInternal` tinyint(1) NOT NULL default '0',
  `btInterfaceWidth` int(10) unsigned NOT NULL default '400',
  `btInterfaceHeight` int(10) unsigned NOT NULL default '400',
  `pkgID` int unsigned not null default 0,
  PRIMARY KEY  (`btID`),
  UNIQUE KEY `btHandle` (`btHandle`)
);


DROP TABLE IF EXISTS `Blocks`;
CREATE TABLE `Blocks` (
  `bID` int(10) unsigned NOT NULL auto_increment,
  `bName` varchar(60) default NULL,
  `bDateAdded` datetime NOT NULL default '0000-00-00 00:00:00',
  `bDateModified` datetime NOT NULL default '0000-00-00 00:00:00',
  `bFilename` varchar(32) default NULL,
  `bIsActive` char(1) NOT NULL default '1',
  `btID` int unsigned NOT NULL default '0',
  `uID` int(10) unsigned default NULL,
  `vID` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`bID`)
) ENGINE=MyISAM AUTO_INCREMENT=220 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Blocks`
--


/*!40000 ALTER TABLE `Blocks` DISABLE KEYS */;
LOCK TABLES `Blocks` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `Blocks` ENABLE KEYS */;

--
-- Table structure for table `CollectionAttributeKeys`
--

DROP TABLE IF EXISTS `CollectionAttributeKeys`;
CREATE TABLE `CollectionAttributeKeys` (
  `akID` bigint(20) NOT NULL auto_increment,
  `akHandle` varchar(255) NOT NULL default '',
  `akName` varchar(255) NOT NULL default '',
  `akSearchable` tinyint(1) NOT NULL default '0',
  `akValues` text,
  `akType` varchar(255) NOT NULL default 'TEXT',
  PRIMARY KEY  (`akID`),
  UNIQUE KEY `akHandle` (`akHandle`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `CollectionAttributeKeys`
--

DROP TABLE IF EXISTS `Packages`;
CREATE TABLE `Packages` (
  `pkgID` int(10) unsigned NOT NULL auto_increment,
  `pkgName` varchar(255) NOT NULL default '',
  `pkgHandle` varchar(64) NOT NULL default '',
  `pkgDescription` text,
  `pkgDateInstalled` datetime NOT NULL,
  `pkgIsInstalled` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`pkgID`),
  UNIQUE KEY `pkgHandle` (`pkgHandle`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;




/*!40000 ALTER TABLE `CollectionAttributeKeys` DISABLE KEYS */;
LOCK TABLES `CollectionAttributeKeys` WRITE;
INSERT INTO `CollectionAttributeKeys` VALUES (1,'keywords','Meta Keywords',1,'','TEXT'),(2,'post-featured','Feature this Post?',0,'','BOOLEAN'),(3,'post-mood','Mood',0,'positive,neutral,negative,question','SELECT'),(4,'senderUID','Sender User ID',0,'','TEXT'),(5,'receiverUID','Receiver User ID',0,'','TEXT'),(6,'disable-comments','Disable Comments on this Page?',0,'','BOOLEAN'),(7,'administrative-message','Administrative Message?',0,'','BOOLEAN'),(8,'default-mood','Default Mood for Replies',0,'positive,neutral,negative,question','SELECT'),(9,'post-attachments','Allow Attachments on Post?',0,'','BOOLEAN'),(10,'post-polls','Allow Poll Posts',0,'','BOOLEAN'),(11,'post-locations','Allow Location Posts',0,'','BOOLEAN'),(12,'post-button-text','Text of the Post Button',0,'','TEXT');
UNLOCK TABLES;
/*!40000 ALTER TABLE `CollectionAttributeKeys` ENABLE KEYS */;

--
-- Table structure for table `CollectionAttributeValues`
--

DROP TABLE IF EXISTS `CollectionAttributeValues`;
CREATE TABLE `CollectionAttributeValues` (
  `cID` bigint(20) NOT NULL default '0',
  `cvID` int(11) NOT NULL default '0',
  `akID` bigint(20) NOT NULL default '0',
  `value` text,
  PRIMARY KEY  (`cID`,`cvID`,`akID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `CollectionAttributeValues`
--


/*!40000 ALTER TABLE `CollectionAttributeValues` DISABLE KEYS */;
LOCK TABLES `CollectionAttributeValues` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `CollectionAttributeValues` ENABLE KEYS */;

--
-- Table structure for table `PagePermissionPageTypes`
--

DROP TABLE IF EXISTS `PagePermissionPageTypes`;
CREATE TABLE `PagePermissionPageTypes` (
  `cID` int(10) unsigned NOT NULL default '0',
  `gID` mediumint(9) NOT NULL default '0',
  `uID` int(10) unsigned NOT NULL default '0',
  `ctID` mediumint(9) NOT NULL default '0',
  PRIMARY KEY  (`cID`,`gID`,`uID`,`ctID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `PagePermissionPageTypes`
--


/*!40000 ALTER TABLE `PagePermissionPageTypes` DISABLE KEYS */;
LOCK TABLES `PagePermissionPageTypes` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `PagePermissionPageTypes` ENABLE KEYS */;

--
-- Table structure for table `CollectionGroups`
--

DROP TABLE IF EXISTS `PagePermissions`;
CREATE TABLE `PagePermissions` (
  `cID` int unsigned NOT NULL default '0',
  `gID` int unsigned NOT NULL default '0',
  `uID` int(11) NOT NULL default '0',
  `cgPermissions` varchar(32) default NULL,
  `cgStartDate` datetime default NULL,
  `cgEndDate` datetime default NULL,
  PRIMARY KEY  (`cID`,`gID`,`uID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `CollectionGroups`
--

DROP TABLE IF EXISTS `PagePaths`;
CREATE TABLE `PagePaths` (
  `cID` int unsigned NOT NULL default '0',
  `cPath` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`cID`),
  UNIQUE KEY `cPath` (`cPath`)
 ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `CollectionPaths`
--


--
-- Table structure for table `PageTypeAttributes`
--

DROP TABLE IF EXISTS `PageTypeAttributes`;
CREATE TABLE `PageTypeAttributes` (
  `ctID` int(11) NOT NULL default '0',
  `akID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ctID`,`akID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `PageTypeAttributes`
--


/*!40000 ALTER TABLE `PageTypeAttributes` DISABLE KEYS */;
LOCK TABLES `PageTypeAttributes` WRITE;
INSERT INTO `PageTypeAttributes` VALUES (1,1),(1,3),(1,5),(1,9),(1,11);
UNLOCK TABLES;
/*!40000 ALTER TABLE `PageTypeAttributes` ENABLE KEYS */;

--
-- Table structure for table `PageTypes`
--

DROP TABLE IF EXISTS `PageTypes`;
CREATE TABLE `PageTypes` (
  `ctID` int unsigned NOT NULL auto_increment,
  `ctHandle` varchar(32) NOT NULL default '',
  `ctIcon` varchar(128) null,
  `ctName` varchar(90) NOT NULL default '',
  `pkgID` int unsigned not null default 0,
  PRIMARY KEY  (`ctID`),
  UNIQUE KEY `ctHandle` (`ctHandle`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `PageTypes`
--


--
-- Table structure for table `CollectionVersionBlockPermissions`
--

DROP TABLE IF EXISTS `CollectionVersionBlockPermissions`;
CREATE TABLE `CollectionVersionBlockPermissions` (
  `cID` int(10) unsigned NOT NULL default '0',
  `cvID` int(10) unsigned NOT NULL default '1',
  `bID` int(10) unsigned NOT NULL default '0',
  `gID` int unsigned NOT NULL default '0',
  `uID` int(10) unsigned NOT NULL default '0',
  `cbgPermissions` varchar(32) default NULL,
  PRIMARY KEY  (`cID`,`bID`,`gID`,`cvID`,`uID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `CollectionVersionBlockPermissions`
--

--
-- Table structure for table `CollectionVersionBlocks`
--

DROP TABLE IF EXISTS `CollectionVersionBlocks`;
CREATE TABLE `CollectionVersionBlocks` (
  `cID` int unsigned NOT NULL default '0',
  `cvID` int(10) unsigned NOT NULL default '1',
  `bID` int(10) unsigned NOT NULL default '0',
  `arHandle` varchar(255) NOT NULL default '',
  `cbDisplayOrder` int(11) NOT NULL default '0',
  `isOriginal` char(1) NOT NULL default '0',
  `cbOverrideAreaPermissions` tinyint(1) NOT NULL default '0',
  `cbIncludeAll` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`cID`,`bID`,`arHandle`,`cvID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `CollectionVersionBlocks`
--


/*!40000 ALTER TABLE `CollectionVersionBlocks` DISABLE KEYS */;
LOCK TABLES `CollectionVersionBlocks` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `CollectionVersionBlocks` ENABLE KEYS */;

--
-- Table structure for table `CollectionVersions`
--

DROP TABLE IF EXISTS `CollectionVersions`;
CREATE TABLE `CollectionVersions` (
  `cID` int(10) unsigned NOT NULL default '0',
  `cvID` int(10) unsigned NOT NULL default '1',
  `cvName` varchar(128) NULL default '',
  `cvHandle` varchar(64) NULL default '',
  `cvDescription` text,
  `cvDatePublic` datetime default NULL,
  `cvDateCreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `cvComments` varchar(255) default NULL,
  `cvIsApproved` tinyint(1) NOT NULL default '0',
  `cvAuthorUID` int(10) unsigned default NULL,
  `cvApproverUID` int(10) unsigned default NULL,
  `cvActivateDatetime` datetime default NULL,
  `cvLookupIndex` mediumtext,
  PRIMARY KEY  (`cID`,`cvID`),
  index (cvIsApproved)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `CollectionVersions`
--

--
-- Table structure for table `Collections`
--

CREATE TABLE DashboardMenus (
  mID int(10) unsigned NOT NULL auto_increment,
  mDisplayName varchar(255) NOT NULL default '',
  mParentID int(10) unsigned NOT NULL default '0',
  cID int unsigned not null default 0,
  mDisplayOrder int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (mID)
) TYPE=MyISAM;


drop table if exists PageLayouts;
create table PageLayouts (
	plID int unsigned not null auto_increment,
	plHandle varchar(64) not null default '',
	plName varchar(255) null,
	plDescription text null,
	pkgID int unsigned not null default 0,
	primary key (plID),
	unique (plHandle));


DROP TABLE IF EXISTS `Pages`;

create table `Pages` (
  `cID` int unsigned not null default 0,
  `ctID` int unsigned NOT NULL default '0',
  `cIsTemplate` char(1) NOT NULL default '0',
  `uID` int(10) unsigned default NULL,
  `cIsCheckedOut` tinyint(1) NOT NULL default '0',
  `cCheckedOutUID` int(10) unsigned default NULL,
  `cCheckedOutDatetime` datetime default NULL,
  `cCheckedOutDatetimeLastEdit` datetime default NULL,
  `cPendingAction` enum('DELETE','COPY','MOVE') default NULL,
  `cPendingActionDatetime` datetime NOT NULL default '0000-00-00 00:00:00',
  `cPendingActionUID` int(10) unsigned default NULL,
  `cPendingActionTargetCID` int(10) unsigned default NULL,
  `cOverrideTemplatePermissions` tinyint(1) NOT NULL default '1',
  `cInheritPermissionsFromCID` int(11) NOT NULL default '0',
  `cInheritPermissionsFrom` enum('PARENT','TEMPLATE','OVERRIDE') NOT NULL default 'PARENT',
  `cFilename` varchar(255) default NULL,
  `cPointerID` int(10) unsigned NOT NULL default '0',
  `cChildren` int(10) unsigned NOT NULL default '0',
  `cDisplayOrder` int(11) NOT NULL default '0',
  `cParentID` int unsigned NOT NULL default '0',
  `pkgID` int unsigned not null default 0,
  `plID` int unsigned not null default 0,
  PRIMARY KEY  (`cID`),
  index (cParentID)
);


DROP TABLE IF EXISTS `Collections`;
CREATE TABLE `Collections` (
  `cID` int unsigned not null auto_increment,
  `cDateAdded` datetime NOT NULL default '0000-00-00 00:00:00',
  `cDateModified` datetime NOT NULL default '0000-00-00 00:00:00',
  `cHandle` varchar(255) null,
  primary key (`cID`)
);

--
-- Dumping data for table `Collections`
--



--
-- Table structure for table `Groups`
--

DROP TABLE IF EXISTS `Groups`;
CREATE TABLE `Groups` (
  `gID` int unsigned NOT NULL auto_increment,
  `gName` varchar(128) NOT NULL default '',
  `gDescription` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`gID`),
  UNIQUE KEY `gName` (`gName`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Groups`
--


/*!40000 ALTER TABLE `Groups` DISABLE KEYS */;
LOCK TABLES `Groups` WRITE;
INSERT INTO `Groups` VALUES (1,'Guest','Guest Group'),(2,'Registered Users','Registered Users'),(3,'Admin',''),(25,'Concrete',''),(26,'Test Accounts','');
UNLOCK TABLES;
/*!40000 ALTER TABLE `Groups` ENABLE KEYS */;

--
-- Table structure for table `PileContents`
--

DROP TABLE IF EXISTS `PileContents`;
CREATE TABLE `PileContents` (
  `pcID` int(10) unsigned NOT NULL auto_increment,
  `pID` int(10) unsigned NOT NULL default '0',
  `itemID` int(10) unsigned NOT NULL default '0',
  `itemType` varchar(64) NOT NULL default '',
  `quantity` mediumint(9) NOT NULL default '1',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `displayOrder` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pcID`)
) ENGINE=MyISAM AUTO_INCREMENT=118 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `PileContents`
--



--
-- Table structure for table `Piles`
--

DROP TABLE IF EXISTS `Piles`;
CREATE TABLE `Piles` (
  `pID` int(10) unsigned NOT NULL auto_increment,
  `uID` int(10) unsigned default NULL,
  `isDefault` tinyint(1) NOT NULL default '0',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `name` varchar(255) default NULL,
  `state` varchar(64) NOT NULL default '',
  `vID` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Piles`
--



--
-- Table structure for table `SitePermissions`
--

DROP TABLE IF EXISTS `Statistics`;
CREATE TABLE `Statistics` (
  `stID` bigint(20) unsigned NOT NULL auto_increment,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `cID` int(10) unsigned default NULL,
  `bID` int(10) unsigned default NULL,
  `uID` int(10) unsigned default NULL,
  `task` varchar(20) default NULL,
  PRIMARY KEY  (`stID`),
  KEY `uID` (`uID`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Statistics`
--

--
-- Table structure for table `UserAttributeKeys`
--

DROP TABLE IF EXISTS `UserAttributeKeys`;
CREATE TABLE `UserAttributeKeys` (
  `ukID` bigint(20) NOT NULL auto_increment,
  `ukHandle` varchar(255) NOT NULL default '',
  `ukName` varchar(255) NOT NULL default '',
  `ukHidden` tinyint(1) NOT NULL default '0',
  `ukValues` text,
  `ukType` varchar(255) NOT NULL default '',
  `displayOrder` int(10) unsigned NOT NULL default '0',
  `ukRequired` tinyint(1) NOT NULL default '0',
  `ukPrivate` tinyint(1) NOT NULL default '0',
  `ukDisplayedOnRegister` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`ukID`),
  UNIQUE KEY `ukHandle` (`ukHandle`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `UserAttributeKeys`
--


/*!40000 ALTER TABLE `UserAttributeKeys` DISABLE KEYS */;
LOCK TABLES `UserAttributeKeys` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `UserAttributeKeys` ENABLE KEYS */;

--
-- Table structure for table `UserAttributeValues`
--

DROP TABLE IF EXISTS `UserAttributeValues`;
CREATE TABLE `UserAttributeValues` (
  `uID` bigint(20) NOT NULL default '0',
  `ukID` bigint(20) NOT NULL default '0',
  `value` text,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`uID`,`ukID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `UserAttributeValues`
--


/*!40000 ALTER TABLE `UserAttributeValues` DISABLE KEYS */;
LOCK TABLES `UserAttributeValues` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `UserAttributeValues` ENABLE KEYS */;


-- Table structure for table `UserGroups`
--

DROP TABLE IF EXISTS `UserGroups`;
CREATE TABLE `UserGroups` (
  `uID` int unsigned NOT NULL default '0',
  `gID` int unsigned NOT NULL default '0',
  `ugEntered` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`uID`,`gID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `UserGroups`
--

DROP TABLE IF EXISTS `Users`;
CREATE TABLE `Users` (
  `uID` int unsigned NOT NULL auto_increment,
  `uName` varchar(12) NOT NULL default '',
  `uEmail` varchar(40) NOT NULL default '',
  `uPassword` varchar(255) NOT NULL default '',
  `uIsActive` char(1) NOT NULL default '0',
  `uDateAdded` datetime NOT NULL default '0000-00-00 00:00:00',
  `uHasAvatar` tinyint(1) NOT NULL default '0',
  `uLastOnline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`uID`),
  UNIQUE `uName` (`uName`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Users`
--


/*!40000 ALTER TABLE `Users` DISABLE KEYS */;
LOCK TABLES `Users` WRITE;
INSERT INTO `Users` VALUES (1,'admin','admin@concretecms.com','c4ca4238a0b923820dcc509a6f75849b','1','0000-00-00 00:00:00',0,1191797527);
UNLOCK TABLES;
/*!40000 ALTER TABLE `Users` ENABLE KEYS */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

