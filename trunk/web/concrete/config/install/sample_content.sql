DROP TABLE IF EXISTS AreaGroupBlockTypes;

CREATE TABLE IF NOT EXISTS `AreaGroupBlockTypes` (
  `cID` int(10) unsigned NOT NULL default '0',
  `arHandle` varchar(255) NOT NULL,
  `gID` int(10) unsigned NOT NULL default '0',
  `uID` int(10) unsigned NOT NULL default '0',
  `btID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cID`,`arHandle`,`gID`,`uID`,`btID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS AreaGroups;

CREATE TABLE IF NOT EXISTS `AreaGroups` (
  `cID` int(10) unsigned NOT NULL default '0',
  `arHandle` varchar(255) NOT NULL,
  `gID` int(10) unsigned NOT NULL default '0',
  `uID` int(10) unsigned NOT NULL default '0',
  `agPermissions` varchar(64) NOT NULL,
  PRIMARY KEY  (`cID`,`arHandle`,`gID`,`uID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS Areas;

CREATE TABLE IF NOT EXISTS `Areas` (
  `arID` int(10) unsigned NOT NULL auto_increment,
  `cID` int(10) unsigned NOT NULL default '0',
  `arHandle` varchar(255) NOT NULL,
  `arOverrideCollectionPermissions` tinyint(1) NOT NULL default '0',
  `arInheritPermissionsFromAreaOnCID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`arID`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

INSERT INTO Areas VALUES(1,3,'Header Nav',0,0)
 ,(2,2,'Header Nav',0,0)
 ,(3,4,'Header Nav',0,0)
 ,(4,3,'Sidebar',0,0)
 ,(5,2,'Sidebar',0,0)
 ,(6,1,'Header',0,0)
 ,(7,3,'Header',0,0)
 ,(8,2,'Header',0,0)
 ,(9,4,'Header',0,0)
 ,(10,36,'Global Scrapbook',0,0)
 ,(11,1,'Main',0,0)
 ,(12,1,'Sidebar',0,0)
 ,(13,56,'Main',0,0)
 ,(14,56,'Header',0,0)
 ,(15,57,'Main',0,0)
 ,(16,58,'Header Nav',0,0)
 ,(17,58,'Sidebar',0,0)
 ,(18,58,'Header',0,0)
 ,(19,59,'Header Nav',0,0)
 ,(20,59,'Sidebar',0,0)
 ,(21,59,'Header',0,0)
 ,(22,59,'Main',0,0)
 ,(23,60,'Main',0,0)
 ,(24,60,'Sidebar',0,0)
 ,(25,61,'Main',0,0)
 ,(26,61,'Sidebar',0,0)
 ,(27,62,'Header Nav',0,0)
 ,(28,62,'Sidebar',0,0)
 ,(29,62,'Header',0,0)
 ,(30,62,'Main',0,0)
 ,(31,56,'Header Nav',0,0)
 ,(32,56,'Sidebar',0,0)
 ,(33,57,'Header Nav',0,0)
 ,(34,57,'Sidebar',0,0)
 ,(35,57,'Header',0,0)
 ,(36,60,'Header Nav',0,0)
 ,(37,60,'Header',0,0)
 ,(38,61,'Header Nav',0,0)
 ,(39,61,'Header',0,0)
 ,(40,1,'Header Nav',0,0);

DROP TABLE IF EXISTS AttributeKeyCategories;

CREATE TABLE IF NOT EXISTS `AttributeKeyCategories` (
  `akCategoryID` int(10) unsigned NOT NULL auto_increment,
  `akCategoryHandle` varchar(255) NOT NULL,
  `akCategoryAllowSets` smallint(4) NOT NULL default '0',
  `pkgID` int(10) unsigned default NULL,
  PRIMARY KEY  (`akCategoryID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO AttributeKeyCategories VALUES(1,'collection',0,NULL)
 ,(2,'user',0,NULL)
 ,(3,'file',0,NULL);

DROP TABLE IF EXISTS AttributeKeys;

CREATE TABLE IF NOT EXISTS `AttributeKeys` (
  `akID` int(10) unsigned NOT NULL auto_increment,
  `akHandle` varchar(255) NOT NULL,
  `akName` varchar(255) NOT NULL,
  `akIsSearchable` tinyint(1) NOT NULL default '0',
  `akIsSearchableIndexed` tinyint(1) NOT NULL default '0',
  `akIsAutoCreated` tinyint(1) NOT NULL default '0',
  `akIsColumnHeader` tinyint(1) NOT NULL default '0',
  `akIsEditable` tinyint(1) NOT NULL default '0',
  `atID` int(10) unsigned default NULL,
  `akCategoryID` int(10) unsigned default NULL,
  `pkgID` int(10) unsigned default NULL,
  PRIMARY KEY  (`akID`),
  UNIQUE KEY `akHandle` (`akHandle`,`akCategoryID`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

INSERT INTO AttributeKeys VALUES(1,'meta_title','Meta Title',1,0,0,0,1,1,1,0)
 ,(2,'meta_description','Meta Description',1,0,0,0,1,2,1,0)
 ,(3,'meta_keywords','Meta Keywords',1,0,0,0,1,2,1,0)
 ,(4,'exclude_nav','Exclude From Nav',1,0,0,0,1,3,1,0)
 ,(5,'exclude_page_list','Exclude From Page List',1,0,0,0,1,3,1,0)
 ,(6,'header_extra_content','Header Extra Content',1,0,0,0,1,2,1,0)
 ,(7,'exclude_search_index','Exclude From Search Index',1,0,0,0,1,3,1,0)
 ,(8,'exclude_sitemapxml','Exclude From sitemap.xml',1,0,0,0,1,3,1,0)
 ,(9,'date_of_birth','Date of Birth',1,0,0,0,1,4,2,0)
 ,(10,'profile_private_messages_enabled','I would like to receive private messages.',1,0,0,0,1,3,2,0)
 ,(11,'profile_private_messages_notification_enabled','Send me email notifications when I receive a private message.',1,0,0,0,1,3,2,0)
 ,(12,'width','Width',1,0,1,0,0,6,3,0)
 ,(13,'height','Height',1,0,1,0,0,6,3,0)
 ,(14,'Release_Date','Release Date',1,0,0,0,1,4,1,0)
 ,(15,'Press_Release_Type','Press Release Type',1,0,0,0,1,8,1,0);

DROP TABLE IF EXISTS AttributeSetKeys;

CREATE TABLE IF NOT EXISTS `AttributeSetKeys` (
  `akID` int(10) unsigned NOT NULL default '0',
  `asID` int(10) unsigned NOT NULL default '0',
  `displayOrder` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`akID`,`asID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS AttributeSets;

CREATE TABLE IF NOT EXISTS `AttributeSets` (
  `asID` int(10) unsigned NOT NULL auto_increment,
  `asName` varchar(255) default NULL,
  `asHandle` varchar(255) NOT NULL,
  `akCategoryID` int(10) unsigned NOT NULL default '0',
  `pkgID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`asID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS AttributeTypeCategories;

CREATE TABLE IF NOT EXISTS `AttributeTypeCategories` (
  `atID` int(10) unsigned NOT NULL default '0',
  `akCategoryID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`atID`,`akCategoryID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO AttributeTypeCategories VALUES(1,1)
 ,(1,2)
 ,(1,3)
 ,(2,1)
 ,(2,2)
 ,(2,3)
 ,(3,1)
 ,(3,2)
 ,(3,3)
 ,(4,1)
 ,(4,2)
 ,(4,3)
 ,(5,1)
 ,(6,1)
 ,(6,2)
 ,(6,3)
 ,(7,1)
 ,(7,3)
 ,(8,1)
 ,(8,2)
 ,(8,3)
 ,(9,2);

DROP TABLE IF EXISTS AttributeTypes;

CREATE TABLE IF NOT EXISTS `AttributeTypes` (
  `atID` int(10) unsigned NOT NULL auto_increment,
  `atHandle` varchar(255) NOT NULL,
  `atName` varchar(255) NOT NULL,
  `pkgID` int(10) unsigned default NULL,
  PRIMARY KEY  (`atID`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

INSERT INTO AttributeTypes VALUES(1,'text','Text',0)
 ,(2,'textarea','Text Area',0)
 ,(3,'boolean','Checkbox',0)
 ,(4,'date_time','Date/Time',0)
 ,(5,'image_file','Image/File',0)
 ,(6,'number','Number',0)
 ,(7,'rating','Rating',0)
 ,(8,'select','Select',0)
 ,(9,'address','Address',0);

DROP TABLE IF EXISTS AttributeValues;

CREATE TABLE IF NOT EXISTS `AttributeValues` (
  `avID` int(10) unsigned NOT NULL auto_increment,
  `akID` int(10) unsigned default NULL,
  `avDateAdded` datetime NOT NULL default '0000-00-00 00:00:00',
  `uID` int(10) unsigned default NULL,
  `atID` int(10) unsigned default NULL,
  PRIMARY KEY  (`avID`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

INSERT INTO AttributeValues VALUES(1,12,NOW(),0,6)
 ,(2,13,NOW(),0,6)
 ,(3,12,NOW(),0,6)
 ,(4,13,NOW(),0,6)
 ,(5,12,NOW(),0,6)
 ,(6,13,NOW(),0,6)
 ,(7,12,NOW(),0,6)
 ,(8,13,NOW(),0,6)
 ,(9,12,NOW(),0,6)
 ,(10,13,NOW(),0,6)
 ,(11,14,NOW(),0,4)
 ,(12,15,NOW(),0,8)
 ,(13,4,NOW(),0,3);

DROP TABLE IF EXISTS BlockRelations;

CREATE TABLE IF NOT EXISTS `BlockRelations` (
  `brID` int(10) unsigned NOT NULL auto_increment,
  `bID` int(10) unsigned NOT NULL default '0',
  `originalBID` int(10) unsigned NOT NULL default '0',
  `relationType` varchar(50) NOT NULL,
  PRIMARY KEY  (`brID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS BlockTypes;

CREATE TABLE IF NOT EXISTS `BlockTypes` (
  `btID` int(10) unsigned NOT NULL auto_increment,
  `btHandle` varchar(32) NOT NULL,
  `btName` varchar(128) NOT NULL,
  `btDescription` text,
  `btActiveWhenAdded` tinyint(1) NOT NULL default '1',
  `btCopyWhenPropagate` tinyint(1) NOT NULL default '0',
  `btIncludeAll` tinyint(1) NOT NULL default '0',
  `btIsInternal` tinyint(1) NOT NULL default '0',
  `btInterfaceWidth` int(10) unsigned NOT NULL default '400',
  `btInterfaceHeight` int(10) unsigned NOT NULL default '400',
  `pkgID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`btID`),
  UNIQUE KEY `btHandle` (`btHandle`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

INSERT INTO BlockTypes VALUES(1,'content','Content','HTML/WYSIWYG Editor Content.',1,0,0,0,600,465,0)
 ,(2,'html','HTML','For adding HTML by hand.',1,0,0,0,600,465,0)
 ,(3,'autonav','Auto-Nav','Creates navigation trees and sitemaps.',1,0,0,0,500,350,0)
 ,(4,'external_form','External Form','Include external forms in the filesystem and place them on pages.',1,0,0,0,300,200,0)
 ,(5,'form','Form','Build simple forms and surveys.',1,0,0,0,420,430,0)
 ,(6,'page_list','Page List','List pages based on type, area.',1,0,0,0,500,350,0)
 ,(7,'file','File','Link to files stored in the asset library.',1,0,0,0,300,250,0)
 ,(8,'image','Image','Adds images and onstates from the library to pages.',1,0,0,0,300,440,0)
 ,(9,'flash_content','Flash Content','Embeds SWF files, including flash detection.',1,0,0,0,300,240,0)
 ,(10,'guestbook','Guestbook','Adds blog-style comments (a guestbook) to your page.',1,0,1,0,300,260,0)
 ,(11,'slideshow','Slideshow','Display a running loop of images.',1,0,0,0,550,400,0)
 ,(12,'search','Search','Add a search box to your site.',1,0,0,0,400,170,0)
 ,(13,'google_map','Google Map','Enter an address and a Google Map of that location will be placed in your page.',1,0,0,0,400,220,0)
 ,(14,'video','Video Player','Embeds uploaded video into a web page. Supports AVI, WMV, Quicktime/MPEG4 and FLV formats.',1,0,0,0,300,200,0)
 ,(15,'rss_displayer','RSS Displayer','Fetch, parse and display the contents of an RSS or Atom feed.',1,0,0,0,400,170,0)
 ,(16,'youtube','Youtube Video','Embeds a Youtube Video in your web page.',1,0,0,0,400,170,0)
 ,(17,'survey','Survey','Provide a simple survey, along with results in a pie chart format.',1,0,1,0,420,300,0);

DROP TABLE IF EXISTS Blocks;

CREATE TABLE IF NOT EXISTS `Blocks` (
  `bID` int(10) unsigned NOT NULL auto_increment,
  `bName` varchar(60) default NULL,
  `bDateAdded` datetime NOT NULL default '0000-00-00 00:00:00',
  `bDateModified` datetime NOT NULL default '0000-00-00 00:00:00',
  `bFilename` varchar(32) default NULL,
  `bIsActive` varchar(1) NOT NULL default '1',
  `btID` int(10) unsigned NOT NULL default '0',
  `uID` int(10) unsigned default NULL,
  PRIMARY KEY  (`bID`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

INSERT INTO Blocks VALUES(1,NULL,NOW(),NOW(),'header_menu.php','1',3,1)
 ,(2,NULL,NOW(),NOW(),'header_menu.php','1',3,1)
 ,(3,NULL,NOW(),NOW(),'header_menu.php','1',3,1)
 ,(4,NULL,NOW(),NOW(),NULL,'1',3,1)
 ,(5,NULL,NOW(),NOW(),NULL,'1',3,1)
 ,(15,NULL,NOW(),NOW(),NULL,'1',11,NULL)
 ,(7,NULL,NOW(),NOW(),NULL,'1',8,1)
 ,(8,NULL,NOW(),NOW(),NULL,'1',8,1)
 ,(9,NULL,NOW(),NOW(),NULL,'1',8,1)
 ,(10,'My_Site_Name',NOW(),NOW(),NULL,'1',1,1)
 ,(11,NULL,NOW(),NOW(),NULL,'1',1,1)
 ,(12,NULL,NOW(),NOW(),NULL,'1',16,NULL)
 ,(13,NULL,NOW(),NOW(),NULL,'1',1,1)
 ,(14,NULL,NOW(),NOW(),NULL,'1',1,1)
 ,(16,NULL,NOW(),NOW(),NULL,'1',1,1)
 ,(17,NULL,NOW(),NOW(),NULL,'1',1,1)
 ,(18,NULL,NOW(),NOW(),NULL,'1',5,1)
 ,(19,NULL,NOW(),NOW(),NULL,'1',11,NULL)
 ,(20,NULL,NOW(),NOW(),NULL,'1',1,1)
 ,(21,NULL,NOW(),NOW(),'custom.php','1',6,NULL)
 ,(22,NULL,NOW(),NOW(),'header_menu.php','1',3,1)
 ,(23,NULL,NOW(),NOW(),NULL,'1',3,1)
 ,(24,NULL,NOW(),NOW(),NULL,'1',8,1)
 ,(25,NULL,NOW(),NOW(),NULL,'1',1,1)
 ,(26,NULL,NOW(),NOW(),NULL,'1',1,1)
 ,(27,NULL,NOW(),NOW(),NULL,'1',1,1)
 ,(28,NULL,NOW(),NOW(),NULL,'1',10,NULL)
 ,(29,NULL,NOW(),NOW(),NULL,'1',17,NULL)
 ,(30,NULL,NOW(),NOW(),NULL,'1',1,1)
 ,(31,NULL,NOW(),NOW(),NULL,'1',3,1)
 ,(32,NULL,NOW(),NOW(),NULL,'1',12,NULL)
 ,(33,NULL,NOW(),NOW(),NULL,'1',12,NULL);

DROP TABLE IF EXISTS CollectionAttributeValues;

CREATE TABLE IF NOT EXISTS `CollectionAttributeValues` (
  `cID` int(10) unsigned NOT NULL default '0',
  `cvID` int(10) unsigned NOT NULL default '0',
  `akID` int(10) unsigned NOT NULL default '0',
  `avID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cID`,`cvID`,`akID`,`avID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO CollectionAttributeValues VALUES(59,1,14,11)
 ,(59,1,15,12)
 ,(62,1,4,13);

DROP TABLE IF EXISTS CollectionSearchIndexAttributes;

CREATE TABLE IF NOT EXISTS `CollectionSearchIndexAttributes` (
  `cID` int(11) unsigned NOT NULL default '0',
  `ak_meta_title` text,
  `ak_meta_description` text,
  `ak_meta_keywords` text,
  `ak_exclude_nav` tinyint(4) default '0',
  `ak_exclude_page_list` tinyint(4) default '0',
  `ak_header_extra_content` text,
  `ak_exclude_search_index` tinyint(4) default '0',
  `ak_exclude_sitemapxml` tinyint(4) default '0',
  `ak_Release_Date` datetime default NULL,
  `ak_Press_Release_Type` text,
  PRIMARY KEY  (`cID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO CollectionSearchIndexAttributes VALUES(1,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(55,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(16,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(17,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(18,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(19,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(21,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(22,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(23,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(24,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(25,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(26,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(27,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(28,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(29,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(30,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(31,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(32,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(33,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(34,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(35,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(36,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(37,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(38,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(43,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(45,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(46,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(47,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(49,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(52,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(53,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(59,NULL,NULL,NULL,0,0,NULL,0,0,'2010-09-13 11:37:00','\nPress Release\n')
 ,(62,NULL,NULL,NULL,1,0,NULL,0,0,NULL,NULL)
 ,(9,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(42,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(56,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(57,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(60,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL)
 ,(61,NULL,NULL,NULL,0,0,NULL,0,0,NULL,NULL);

DROP TABLE IF EXISTS CollectionVersionAreaLayouts;

CREATE TABLE IF NOT EXISTS `CollectionVersionAreaLayouts` (
  `cvalID` int(10) unsigned NOT NULL auto_increment,
  `cID` int(10) unsigned default '0',
  `cvID` int(10) unsigned default '0',
  `arHandle` varchar(255) default NULL,
  `layoutID` int(10) unsigned NOT NULL default '0',
  `position` int(10) default '1000',
  `areaNameNumber` int(10) unsigned default '0',
  PRIMARY KEY  (`cvalID`),
  KEY `areaLayoutsIndex` (`cID`,`cvID`,`arHandle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS CollectionVersionAreaStyles;

CREATE TABLE IF NOT EXISTS `CollectionVersionAreaStyles` (
  `cID` int(10) unsigned NOT NULL default '0',
  `cvID` int(10) unsigned NOT NULL default '0',
  `arHandle` varchar(255) NOT NULL,
  `csrID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cID`,`cvID`,`arHandle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS CollectionVersionBlockPermissions;

CREATE TABLE IF NOT EXISTS `CollectionVersionBlockPermissions` (
  `cID` int(10) unsigned NOT NULL default '0',
  `cvID` int(10) unsigned NOT NULL default '1',
  `bID` int(10) unsigned NOT NULL default '0',
  `gID` int(10) unsigned NOT NULL default '0',
  `uID` int(10) unsigned NOT NULL default '0',
  `cbgPermissions` varchar(32) default NULL,
  PRIMARY KEY  (`cID`,`cvID`,`bID`,`gID`,`uID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS CollectionVersionBlockStyles;

CREATE TABLE IF NOT EXISTS `CollectionVersionBlockStyles` (
  `cID` int(10) unsigned NOT NULL default '0',
  `cvID` int(10) unsigned NOT NULL default '0',
  `bID` int(10) unsigned NOT NULL default '0',
  `arHandle` varchar(255) NOT NULL,
  `csrID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cID`,`cvID`,`bID`,`arHandle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO CollectionVersionBlockStyles VALUES(1,1,1,'Header Nav',0)
 ,(56,1,1,'Header Nav',0)
 ,(56,1,4,'Sidebar',0)
 ,(57,1,1,'Header Nav',0)
 ,(57,1,4,'Sidebar',0)
 ,(57,1,7,'Header',0)
 ,(59,1,22,'Header Nav',0)
 ,(59,1,23,'Sidebar',0)
 ,(59,1,24,'Header',0)
 ,(60,1,1,'Header Nav',0)
 ,(60,1,4,'Sidebar',0)
 ,(60,1,7,'Header',0)
 ,(61,1,1,'Header Nav',0)
 ,(62,1,1,'Header Nav',0)
 ,(61,1,7,'Header',0)
 ,(62,1,7,'Header',0);

DROP TABLE IF EXISTS CollectionVersionBlocks;

CREATE TABLE IF NOT EXISTS `CollectionVersionBlocks` (
  `cID` int(10) unsigned NOT NULL default '0',
  `cvID` int(10) unsigned NOT NULL default '1',
  `bID` int(10) unsigned NOT NULL default '0',
  `arHandle` varchar(255) NOT NULL,
  `cbDisplayOrder` int(10) unsigned NOT NULL default '0',
  `isOriginal` varchar(1) NOT NULL default '0',
  `cbOverrideAreaPermissions` tinyint(1) NOT NULL default '0',
  `cbIncludeAll` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`cID`,`cvID`,`bID`,`arHandle`),
  KEY `cbIncludeAll` (`cbIncludeAll`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO CollectionVersionBlocks VALUES(3,1,1,'Header Nav',0,'1',0,0)
 ,(2,1,2,'Header Nav',0,'1',0,0)
 ,(4,1,3,'Header Nav',0,'1',0,0)
 ,(3,1,4,'Sidebar',0,'1',0,0)
 ,(2,1,5,'Sidebar',0,'1',0,0)
 ,(1,1,1,'Header Nav',0,'0',0,0)
 ,(1,1,15,'Header',0,'1',0,0)
 ,(3,1,7,'Header',0,'1',0,0)
 ,(2,1,8,'Header',0,'1',0,0)
 ,(4,1,9,'Header',0,'1',0,0)
 ,(36,1,10,'Global Scrapbook',0,'1',0,0)
 ,(1,1,11,'Main',0,'1',0,0)
 ,(1,1,12,'Main',1,'1',0,0)
 ,(1,1,13,'Main',2,'1',0,0)
 ,(1,1,14,'Sidebar',0,'1',0,0)
 ,(56,1,1,'Header Nav',0,'0',0,0)
 ,(56,1,4,'Sidebar',0,'0',0,0)
 ,(56,1,19,'Header',0,'1',0,0)
 ,(56,1,16,'Main',0,'1',0,0)
 ,(56,1,17,'Main',1,'1',0,0)
 ,(56,1,18,'Main',2,'1',0,0)
 ,(57,1,1,'Header Nav',0,'0',0,0)
 ,(57,1,4,'Sidebar',0,'0',0,0)
 ,(57,1,7,'Header',0,'0',0,0)
 ,(57,1,20,'Main',0,'1',0,0)
 ,(57,1,21,'Main',1,'1',0,0)
 ,(58,1,22,'Header Nav',0,'1',0,0)
 ,(58,1,23,'Sidebar',0,'1',0,0)
 ,(58,1,24,'Header',0,'1',0,0)
 ,(59,1,22,'Header Nav',0,'0',0,0)
 ,(59,1,23,'Sidebar',0,'0',0,0)
 ,(59,1,24,'Header',0,'0',0,0)
 ,(59,1,25,'Main',0,'1',0,0)
 ,(59,1,26,'Main',1,'1',0,0)
 ,(60,1,1,'Header Nav',0,'0',0,0)
 ,(60,1,4,'Sidebar',0,'0',0,0)
 ,(60,1,7,'Header',0,'0',0,0)
 ,(60,1,27,'Main',0,'1',0,0)
 ,(60,1,28,'Main',1,'1',0,1)
 ,(60,1,29,'Sidebar',1,'1',0,1)
 ,(61,1,1,'Header Nav',0,'0',0,0)
 ,(62,1,1,'Header Nav',0,'0',0,0)
 ,(61,1,7,'Header',0,'0',0,0)
 ,(61,1,30,'Main',0,'1',0,0)
 ,(61,1,31,'Main',1,'1',0,0)
 ,(62,1,32,'Main',0,'1',0,0)
 ,(62,1,7,'Header',0,'0',0,0)
 ,(61,1,33,'Sidebar',0,'1',0,0);

DROP TABLE IF EXISTS CollectionVersions;

CREATE TABLE IF NOT EXISTS `CollectionVersions` (
  `cID` int(10) unsigned NOT NULL default '0',
  `cvID` int(10) unsigned NOT NULL default '1',
  `cvName` text,
  `cvHandle` varchar(64) default NULL,
  `cvDescription` text,
  `cvDatePublic` datetime default NULL,
  `cvDateCreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `cvComments` varchar(255) default NULL,
  `cvIsApproved` tinyint(1) NOT NULL default '0',
  `cvIsNew` tinyint(1) NOT NULL default '0',
  `cvAuthorUID` int(10) unsigned default NULL,
  `cvApproverUID` int(10) unsigned default NULL,
  `cvActivateDatetime` datetime default NULL,
  PRIMARY KEY  (`cID`,`cvID`),
  KEY `cvIsApproved` (`cvIsApproved`),
  KEY `cvName` (`cvName`(128))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO CollectionVersions VALUES(1,1,'Home','home',NULL,'2010-09-13 11:37:30','2010-09-13 11:37:30','Initial Version',1,0,1,NULL,NULL)
 ,(2,1,NULL,NULL,NULL,'2010-09-13 11:37:30','2010-09-13 11:37:30','Initial Version',1,0,1,NULL,NULL)
 ,(3,1,NULL,NULL,NULL,'2010-09-13 11:37:30','2010-09-13 11:37:30','Initial Version',1,0,1,NULL,NULL)
 ,(4,1,NULL,NULL,NULL,'2010-09-13 11:37:30','2010-09-13 11:37:30','Initial Version',1,0,1,NULL,NULL)
 ,(5,1,'Login','login',NULL,'2010-09-13 11:37:31','2010-09-13 11:37:31','Initial Version',1,0,1,NULL,NULL)
 ,(6,1,'Register','register',NULL,'2010-09-13 11:37:31','2010-09-13 11:37:31','Initial Version',1,0,1,NULL,NULL)
 ,(7,1,'Profile','profile',NULL,'2010-09-13 11:37:31','2010-09-13 11:37:31','Initial Version',1,0,1,NULL,NULL)
 ,(8,1,'Edit','edit',NULL,'2010-09-13 11:37:31','2010-09-13 11:37:31','Initial Version',1,0,1,NULL,NULL)
 ,(9,1,'Members','members',NULL,'2010-09-13 11:37:31','2010-09-13 11:37:31','Initial Version',1,0,1,NULL,NULL)
 ,(10,1,'Avatar','avatar',NULL,'2010-09-13 11:37:31','2010-09-13 11:37:31','Initial Version',1,0,1,NULL,NULL)
 ,(11,1,'Messages','messages',NULL,'2010-09-13 11:37:31','2010-09-13 11:37:31','Initial Version',1,0,1,NULL,NULL)
 ,(12,1,'Friends','friends',NULL,'2010-09-13 11:37:31','2010-09-13 11:37:31','Initial Version',1,0,1,NULL,NULL)
 ,(13,1,'Page Not Found','page_not_found',NULL,'2010-09-13 11:37:31','2010-09-13 11:37:31','Initial Version',1,0,1,NULL,NULL)
 ,(14,1,'Page Forbidden','page_forbidden',NULL,'2010-09-13 11:37:31','2010-09-13 11:37:31','Initial Version',1,0,1,NULL,NULL)
 ,(15,1,'Dashboard','dashboard',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(16,1,'Sitemap','sitemap','Whole world at a glance.',NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(17,1,'Full Sitemap','full',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(18,1,'Flat View','explore',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(19,1,'Page Search','search',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(20,1,'Access','access',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(21,1,'File Manager','files','All documents and images.',NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(22,1,'Search','search',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(23,1,'Attributes','attributes',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(24,1,'Sets','sets',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(25,1,'Access','access',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(26,1,'Reports','reports','Get data from forms and logs.',NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(27,1,'Form Results','forms','Get submission data.',NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(28,1,'Surveys','surveys',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(29,1,'Logs','logs',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(30,1,'Users and Groups','users','Add and manage people.',NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(31,1,'Find Users','search',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(32,1,'Add User','add',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(33,1,'Groups','groups',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(34,1,'User Attributes','attributes',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(35,1,'Login & Registration','registration',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(36,1,'Scrapbook','scrapbook','Share content across your site.',NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(37,1,'Pages and Themes','pages','Reskin your site.',NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(38,1,'Themes','themes','Reskin your site.',NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(39,1,'Add','add',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(40,1,'Inspect','inspect',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(41,1,'Customize','customize',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(42,1,'Marketplace','marketplace',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(43,1,'Page Types','types','What goes in your site.',NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(44,1,'Attributes','attributes',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(45,1,'Single Pages','single',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(46,1,'Add Functionality','install','Install addons & themes.',NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(47,1,'System & Maintenance','system','Backup, cleanup and update.',NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(48,1,'Jobs','jobs',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(49,1,'Backup & Restore','backup',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(50,1,'Update','update',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(51,1,'Notifications','notifications',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(52,1,'Sitewide Settings','settings','Secure and setup your site.',NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(53,1,'Email','mail','Enable post via email and other settings.',NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(54,1,'Marketplace','marketplace',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(55,1,'Download File','download_file',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(56,1,'About','about',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(57,1,'Press Room','press-room',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(58,1,NULL,NULL,NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(59,1,'Launch our new site!','launch-our-new-site','Neeto speedo! We just rebuilt our site in record time and now we can easily change EVERYTHING.',NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(60,1,'Guestbook','guestbook','Neeto speedo! We just rebuilt our site in record time and now we can easily change EVERYTHING.',NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(61,1,'Search','search',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(62,1,'Search Results','search-results',NULL,NOW(),NOW(),'Initial Version',1,0,1,NULL,NULL)
 ,(63,1,NULL,'uID=1',NULL,'2010-09-13 11:37:46','2010-09-13 11:37:46','Initial Version',1,0,NULL,NULL,NULL);

DROP TABLE IF EXISTS Collections;

CREATE TABLE IF NOT EXISTS `Collections` (
  `cID` int(10) unsigned NOT NULL auto_increment,
  `cDateAdded` datetime NOT NULL default '0000-00-00 00:00:00',
  `cDateModified` datetime NOT NULL default '0000-00-00 00:00:00',
  `cHandle` varchar(255) default NULL,
  PRIMARY KEY  (`cID`),
  KEY `cDateModified` (`cDateModified`),
  KEY `cDateAdded` (`cDateAdded`)
) ENGINE=MyISAM AUTO_INCREMENT=64 DEFAULT CHARSET=utf8;

INSERT INTO Collections VALUES(1,'2010-09-13 11:37:30','2010-09-13 11:37:30','home')
 ,(2,'2010-09-13 11:37:30','2010-09-13 11:37:30',NULL)
 ,(3,'2010-09-13 11:37:30','2010-09-13 11:37:30',NULL)
 ,(4,'2010-09-13 11:37:30','2010-09-13 11:37:30',NULL)
 ,(5,'2010-09-13 11:37:31','2010-09-13 11:37:31','login')
 ,(6,'2010-09-13 11:37:31','2010-09-13 11:37:31','register')
 ,(7,'2010-09-13 11:37:31','2010-09-13 11:37:31','profile')
 ,(8,'2010-09-13 11:37:31','2010-09-13 11:37:31','edit')
 ,(9,'2010-09-13 11:37:31','2010-09-13 11:37:31','members')
 ,(10,'2010-09-13 11:37:31','2010-09-13 11:37:31','avatar')
 ,(11,'2010-09-13 11:37:31','2010-09-13 11:37:31','messages')
 ,(12,'2010-09-13 11:37:31','2010-09-13 11:37:31','friends')
 ,(13,'2010-09-13 11:37:31','2010-09-13 11:37:31','page_not_found')
 ,(14,'2010-09-13 11:37:31','2010-09-13 11:37:31','page_forbidden')
 ,(15,NOW(),NOW(),'dashboard')
 ,(16,NOW(),NOW(),'sitemap')
 ,(17,NOW(),NOW(),'full')
 ,(18,NOW(),NOW(),'explore')
 ,(19,NOW(),NOW(),'search')
 ,(20,NOW(),NOW(),'access')
 ,(21,NOW(),NOW(),'files')
 ,(22,NOW(),NOW(),'search')
 ,(23,NOW(),NOW(),'attributes')
 ,(24,NOW(),NOW(),'sets')
 ,(25,NOW(),NOW(),'access')
 ,(26,NOW(),NOW(),'reports')
 ,(27,NOW(),NOW(),'forms')
 ,(28,NOW(),NOW(),'surveys')
 ,(29,NOW(),NOW(),'logs')
 ,(30,NOW(),NOW(),'users')
 ,(31,NOW(),NOW(),'search')
 ,(32,NOW(),NOW(),'add')
 ,(33,NOW(),NOW(),'groups')
 ,(34,NOW(),NOW(),'attributes')
 ,(35,NOW(),NOW(),'registration')
 ,(36,NOW(),NOW(),'scrapbook')
 ,(37,NOW(),NOW(),'pages')
 ,(38,NOW(),NOW(),'themes')
 ,(39,NOW(),NOW(),'add')
 ,(40,NOW(),NOW(),'inspect')
 ,(41,NOW(),NOW(),'customize')
 ,(42,NOW(),NOW(),'marketplace')
 ,(43,NOW(),NOW(),'types')
 ,(44,NOW(),NOW(),'attributes')
 ,(45,NOW(),NOW(),'single')
 ,(46,NOW(),NOW(),'install')
 ,(47,NOW(),NOW(),'system')
 ,(48,NOW(),NOW(),'jobs')
 ,(49,NOW(),NOW(),'backup')
 ,(50,NOW(),NOW(),'update')
 ,(51,NOW(),NOW(),'notifications')
 ,(52,NOW(),NOW(),'settings')
 ,(53,NOW(),NOW(),'mail')
 ,(54,NOW(),NOW(),'marketplace')
 ,(55,NOW(),NOW(),'download_file')
 ,(56,NOW(),NOW(),'about')
 ,(57,NOW(),NOW(),'press-room')
 ,(58,NOW(),NOW(),NULL)
 ,(59,NOW(),NOW(),'launch-our-new-site')
 ,(60,NOW(),NOW(),'guestbook')
 ,(61,NOW(),NOW(),'search')
 ,(62,NOW(),NOW(),'search-results')
 ,(63,'2010-09-13 11:37:46','2010-09-13 11:37:46','uID=1');

DROP TABLE IF EXISTS Config;

CREATE TABLE IF NOT EXISTS `Config` (
  `cfKey` varchar(64) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `cfValue` longtext,
  `uID` int(10) unsigned NOT NULL default '0',
  `pkgID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cfKey`,`uID`),
  KEY `uID` (`uID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO Config VALUES('SITE',NOW(),'{[CCM:SITE]}',0,0);

DROP TABLE IF EXISTS CustomStylePresets;

CREATE TABLE IF NOT EXISTS `CustomStylePresets` (
  `cspID` int(10) unsigned NOT NULL auto_increment,
  `cspName` varchar(255) NOT NULL,
  `csrID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cspID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS CustomStyleRules;

CREATE TABLE IF NOT EXISTS `CustomStyleRules` (
  `csrID` int(10) unsigned NOT NULL auto_increment,
  `css_id` varchar(128) default NULL,
  `css_class` varchar(128) default NULL,
  `css_serialized` text,
  `css_custom` text,
  PRIMARY KEY  (`csrID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS DashboardHomepage;

CREATE TABLE IF NOT EXISTS `DashboardHomepage` (
  `dbhID` int(10) unsigned NOT NULL auto_increment,
  `dbhModule` varchar(255) NOT NULL,
  `dbhDisplayName` varchar(255) default NULL,
  `dbhDisplayOrder` int(10) unsigned NOT NULL default '0',
  `pkgID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`dbhID`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT INTO DashboardHomepage VALUES(1,'activity','Site Activity',0,0)
 ,(2,'reports','Statistics',0,0)
 ,(3,'help','Help',0,0)
 ,(4,'news','Latest News',0,0)
 ,(5,'notes','Notes',0,0);

DROP TABLE IF EXISTS DownloadStatistics;

CREATE TABLE IF NOT EXISTS `DownloadStatistics` (
  `dsID` int(10) unsigned NOT NULL auto_increment,
  `fID` int(10) unsigned NOT NULL,
  `fvID` int(10) unsigned NOT NULL,
  `uID` int(10) unsigned NOT NULL,
  `rcID` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`dsID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS FileAttributeValues;

CREATE TABLE IF NOT EXISTS `FileAttributeValues` (
  `fID` int(10) unsigned NOT NULL default '0',
  `fvID` int(10) unsigned NOT NULL default '0',
  `akID` int(10) unsigned NOT NULL default '0',
  `avID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fID`,`fvID`,`akID`,`avID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO FileAttributeValues VALUES(1,1,12,1)
 ,(1,1,13,2)
 ,(2,1,12,3)
 ,(2,1,13,4)
 ,(3,1,12,5)
 ,(3,1,13,6)
 ,(4,1,12,7)
 ,(4,1,13,8)
 ,(5,1,12,9)
 ,(5,1,13,10);

DROP TABLE IF EXISTS FilePermissionFileTypes;

CREATE TABLE IF NOT EXISTS `FilePermissionFileTypes` (
  `fsID` int(10) unsigned NOT NULL default '0',
  `gID` int(10) unsigned NOT NULL default '0',
  `uID` int(10) unsigned NOT NULL default '0',
  `extension` varchar(32) NOT NULL,
  PRIMARY KEY  (`fsID`,`gID`,`uID`,`extension`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS FilePermissions;

CREATE TABLE IF NOT EXISTS `FilePermissions` (
  `fID` int(10) unsigned NOT NULL default '0',
  `gID` int(10) unsigned NOT NULL default '0',
  `uID` int(10) unsigned NOT NULL default '0',
  `canRead` int(4) NOT NULL default '0',
  `canWrite` int(4) NOT NULL default '0',
  `canAdmin` int(4) NOT NULL default '0',
  `canSearch` int(4) NOT NULL default '0',
  PRIMARY KEY  (`fID`,`gID`,`uID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS FileSearchIndexAttributes;

CREATE TABLE IF NOT EXISTS `FileSearchIndexAttributes` (
  `fID` int(11) unsigned NOT NULL default '0',
  `ak_width` decimal(14,4) default '0.0000',
  `ak_height` decimal(14,4) default '0.0000',
  PRIMARY KEY  (`fID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO FileSearchIndexAttributes VALUES(1,800.0000,192.0000)
 ,(2,800.0000,192.0000)
 ,(3,800.0000,192.0000)
 ,(4,800.0000,192.0000)
 ,(5,800.0000,215.0000);

DROP TABLE IF EXISTS FileSetFiles;

CREATE TABLE IF NOT EXISTS `FileSetFiles` (
  `fsfID` int(10) unsigned NOT NULL auto_increment,
  `fID` int(10) unsigned NOT NULL,
  `fsID` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `fsDisplayOrder` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`fsfID`),
  KEY `fID` (`fID`),
  KEY `fsID` (`fsID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS FileSetPermissions;

CREATE TABLE IF NOT EXISTS `FileSetPermissions` (
  `fsID` int(10) unsigned NOT NULL default '0',
  `gID` int(10) unsigned NOT NULL default '0',
  `uID` int(10) unsigned NOT NULL default '0',
  `canRead` int(4) default NULL,
  `canWrite` int(4) default NULL,
  `canAdmin` int(4) default NULL,
  `canAdd` int(4) default NULL,
  `canSearch` int(3) unsigned default NULL,
  PRIMARY KEY  (`fsID`,`gID`,`uID`),
  KEY `canRead` (`canRead`),
  KEY `canWrite` (`canWrite`),
  KEY `canAdmin` (`canAdmin`),
  KEY `canSearch` (`canSearch`),
  KEY `canAdd` (`canAdd`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO FileSetPermissions VALUES(0,1,0,10,0,0,0,0)
 ,(0,2,0,10,0,0,0,0)
 ,(0,3,0,10,10,10,10,10);

DROP TABLE IF EXISTS FileSetSavedSearches;

CREATE TABLE IF NOT EXISTS `FileSetSavedSearches` (
  `fsID` int(10) unsigned NOT NULL default '0',
  `fsSearchRequest` text,
  `fsResultColumns` text,
  PRIMARY KEY  (`fsID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS FileSets;

CREATE TABLE IF NOT EXISTS `FileSets` (
  `fsID` int(10) unsigned NOT NULL auto_increment,
  `fsName` varchar(64) NOT NULL,
  `uID` int(10) unsigned NOT NULL default '0',
  `fsType` int(4) NOT NULL,
  `fsOverrideGlobalPermissions` int(4) default NULL,
  PRIMARY KEY  (`fsID`),
  KEY `fsOverrideGlobalPermissions` (`fsOverrideGlobalPermissions`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS FileStorageLocations;

CREATE TABLE IF NOT EXISTS `FileStorageLocations` (
  `fslID` int(10) unsigned NOT NULL default '0',
  `fslName` varchar(255) NOT NULL,
  `fslDirectory` varchar(255) NOT NULL,
  PRIMARY KEY  (`fslID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS FileVersionLog;

CREATE TABLE IF NOT EXISTS `FileVersionLog` (
  `fvlID` int(10) unsigned NOT NULL auto_increment,
  `fID` int(10) unsigned NOT NULL default '0',
  `fvID` int(10) unsigned NOT NULL default '0',
  `fvUpdateTypeID` int(3) unsigned NOT NULL default '0',
  `fvUpdateTypeAttributeID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fvlID`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

INSERT INTO FileVersionLog VALUES(1,1,1,5,12)
 ,(2,1,1,5,13)
 ,(3,2,1,5,12)
 ,(4,2,1,5,13)
 ,(5,3,1,5,12)
 ,(6,3,1,5,13)
 ,(7,4,1,5,12)
 ,(8,4,1,5,13)
 ,(9,5,1,5,12)
 ,(10,5,1,5,13);

DROP TABLE IF EXISTS FileVersions;

CREATE TABLE IF NOT EXISTS `FileVersions` (
  `fID` int(10) unsigned NOT NULL default '0',
  `fvID` int(10) unsigned NOT NULL default '0',
  `fvFilename` varchar(255) NOT NULL,
  `fvPrefix` varchar(12) default NULL,
  `fvGenericType` int(3) unsigned NOT NULL default '0',
  `fvSize` int(20) unsigned NOT NULL default '0',
  `fvTitle` varchar(255) default NULL,
  `fvDescription` text,
  `fvTags` varchar(255) default NULL,
  `fvIsApproved` int(10) unsigned NOT NULL default '1',
  `fvDateAdded` datetime default NULL,
  `fvApproverUID` int(10) unsigned NOT NULL default '0',
  `fvAuthorUID` int(10) unsigned NOT NULL default '0',
  `fvActivateDatetime` datetime default NULL,
  `fvHasThumbnail1` int(1) NOT NULL default '0',
  `fvHasThumbnail2` int(1) NOT NULL default '0',
  `fvHasThumbnail3` int(1) NOT NULL default '0',
  `fvExtension` varchar(32) default NULL,
  `fvType` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fID`,`fvID`),
  KEY `fvExtension` (`fvType`),
  KEY `fvTitle` (`fvTitle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO FileVersions VALUES(1,1,'inneroptics_dot_net_aspens.jpg','000000000000',1,108199,'inneroptics_dot_net_aspens.jpg','','',1,NOW(),0,0,NOW(),1,1,0,'jpg',1)
 ,(2,1,'inneroptics_dot_net_canyonlands.jpg','000000000000',1,54531,'inneroptics_dot_net_canyonlands.jpg','','',1,NOW(),0,0,NOW(),1,1,0,'jpg',1)
 ,(3,1,'inneroptics_dot_net_new_zealand_sheep.jpg','000000000000',1,80735,'inneroptics_dot_net_new_zealand_sheep.jpg','','',1,NOW(),0,0,NOW(),1,1,0,'jpg',1)
 ,(4,1,'inneroptics_dot_net_starfish.jpg','000000000000',1,88621,'inneroptics_dot_net_starfish.jpg','','',1,NOW(),0,0,NOW(),1,1,0,'jpg',1)
 ,(5,1,'inneroptics_dot_net_portland.jpg','000000000000',1,55737,'inneroptics_dot_net_portland.jpg','','',1,NOW(),0,0,NOW(),1,1,0,'jpg',1);

DROP TABLE IF EXISTS Files;

CREATE TABLE IF NOT EXISTS `Files` (
  `fID` int(10) unsigned NOT NULL auto_increment,
  `fDateAdded` datetime default NULL,
  `uID` int(10) unsigned NOT NULL default '0',
  `fslID` int(10) unsigned NOT NULL default '0',
  `ocID` int(10) unsigned NOT NULL,
  `fOverrideSetPermissions` int(1) NOT NULL default '0',
  `fPassword` varchar(255) default NULL,
  PRIMARY KEY  (`fID`,`uID`,`fslID`),
  KEY `fOverrideSetPermissions` (`fOverrideSetPermissions`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT INTO Files VALUES(1,NOW(),1,0,0,0,NULL)
 ,(2,NOW(),1,0,0,0,NULL)
 ,(3,NOW(),1,0,0,0,NULL)
 ,(4,NOW(),1,0,0,0,NULL)
 ,(5,NOW(),1,0,0,0,NULL);

DROP TABLE IF EXISTS Groups;

CREATE TABLE IF NOT EXISTS `Groups` (
  `gID` int(10) unsigned NOT NULL auto_increment,
  `gName` varchar(128) NOT NULL,
  `gDescription` varchar(255) NOT NULL,
  `gUserExpirationIsEnabled` int(1) NOT NULL default '0',
  `gUserExpirationMethod` varchar(12) default NULL,
  `gUserExpirationSetDateTime` datetime default NULL,
  `gUserExpirationInterval` int(10) unsigned NOT NULL default '0',
  `gUserExpirationAction` varchar(20) default NULL,
  PRIMARY KEY  (`gID`),
  UNIQUE KEY `gName` (`gName`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO Groups VALUES(1,'Guest','The guest group represents unregistered visitors to your site.',0,NULL,NULL,0,NULL)
 ,(2,'Registered Users','The registered users group represents all user accounts.',0,NULL,NULL,0,NULL)
 ,(3,'Administrators','',0,NULL,NULL,0,NULL);

DROP TABLE IF EXISTS Jobs;

CREATE TABLE IF NOT EXISTS `Jobs` (
  `jID` int(10) unsigned NOT NULL auto_increment,
  `jName` varchar(100) NOT NULL,
  `jDescription` varchar(255) NOT NULL,
  `jDateInstalled` datetime default NULL,
  `jDateLastRun` datetime default NULL,
  `pkgID` int(10) unsigned NOT NULL default '0',
  `jLastStatusText` varchar(255) default NULL,
  `jLastStatusCode` smallint(4) NOT NULL default '0',
  `jStatus` varchar(14) NOT NULL default 'ENABLED',
  `jHandle` varchar(255) NOT NULL,
  `jNotUninstallable` smallint(4) NOT NULL default '0',
  PRIMARY KEY  (`jID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO Jobs VALUES(1,'Index Search Engine','Index the site to allow searching to work quickly and accurately.',NOW(),NOW(),0,'Index updated. 6 pages required reindexing.',0,'ENABLED','index_search',1)
 ,(2,'Generate Sitemap File','Generate the sitemap.xml file that search engines use to crawl your site.',NOW(),NOW(),0,'Sitemap XML File Saved.',0,'ENABLED','generate_sitemap',0)
 ,(3,'Process Email Posts','Polls an email account and grabs private messages/postings that are sent there..',NOW(),NOW(),0,'The Job was run successfully.',0,'ENABLED','process_email',0);

DROP TABLE IF EXISTS JobsLog;

CREATE TABLE IF NOT EXISTS `JobsLog` (
  `jlID` int(10) unsigned NOT NULL auto_increment,
  `jID` int(10) unsigned NOT NULL,
  `jlMessage` varchar(255) NOT NULL,
  `jlTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `jlError` int(10) NOT NULL default '0',
  PRIMARY KEY  (`jlID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS LayoutPresets;

CREATE TABLE IF NOT EXISTS `LayoutPresets` (
  `lpID` int(10) unsigned NOT NULL auto_increment,
  `lpName` varchar(128) NOT NULL,
  `layoutID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`lpID`),
  UNIQUE KEY `layoutID` (`layoutID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS Layouts;

CREATE TABLE IF NOT EXISTS `Layouts` (
  `layoutID` int(10) unsigned NOT NULL auto_increment,
  `layout_rows` int(5) NOT NULL default '3',
  `layout_columns` int(3) NOT NULL default '3',
  `spacing` int(3) NOT NULL default '3',
  `breakpoints` varchar(255) NOT NULL default '',
  `locked` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`layoutID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS Logs;

CREATE TABLE IF NOT EXISTS `Logs` (
  `logID` int(10) unsigned NOT NULL auto_increment,
  `logType` varchar(64) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `logText` longtext,
  `logIsInternal` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`logID`),
  KEY `logType` (`logType`),
  KEY `logIsInternal` (`logIsInternal`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS MailImporters;

CREATE TABLE IF NOT EXISTS `MailImporters` (
  `miID` int(10) unsigned NOT NULL auto_increment,
  `miHandle` varchar(64) NOT NULL,
  `miServer` varchar(255) default NULL,
  `miUsername` varchar(255) default NULL,
  `miPassword` varchar(255) default NULL,
  `miEncryption` varchar(32) default NULL,
  `miIsEnabled` int(1) NOT NULL default '0',
  `miEmail` varchar(255) default NULL,
  `miPort` int(10) unsigned NOT NULL default '0',
  `pkgID` int(10) unsigned default NULL,
  PRIMARY KEY  (`miID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO MailImporters VALUES(1,'private_message',NULL,NULL,NULL,NULL,0,NULL,0,0);

DROP TABLE IF EXISTS MailValidationHashes;

CREATE TABLE IF NOT EXISTS `MailValidationHashes` (
  `mvhID` int(10) unsigned NOT NULL auto_increment,
  `miID` int(10) unsigned NOT NULL default '0',
  `email` varchar(255) NOT NULL,
  `mHash` varchar(128) NOT NULL,
  `mDateGenerated` int(10) unsigned NOT NULL default '0',
  `mDateRedeemed` int(10) unsigned NOT NULL default '0',
  `data` text,
  PRIMARY KEY  (`mvhID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS Packages;

CREATE TABLE IF NOT EXISTS `Packages` (
  `pkgID` int(10) unsigned NOT NULL auto_increment,
  `pkgName` varchar(255) NOT NULL,
  `pkgHandle` varchar(64) NOT NULL,
  `pkgDescription` text,
  `pkgDateInstalled` datetime NOT NULL,
  `pkgIsInstalled` tinyint(1) NOT NULL default '1',
  `pkgVersion` varchar(32) default NULL,
  `pkgAvailableVersion` varchar(32) default NULL,
  PRIMARY KEY  (`pkgID`),
  UNIQUE KEY `pkgHandle` (`pkgHandle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS PagePaths;

CREATE TABLE IF NOT EXISTS `PagePaths` (
  `ppID` int(10) unsigned NOT NULL auto_increment,
  `cID` int(10) unsigned default '0',
  `cPath` text,
  `ppIsCanonical` varchar(1) NOT NULL default '1',
  PRIMARY KEY  (`ppID`),
  KEY `cID` (`cID`),
  KEY `ppIsCanonical` (`ppIsCanonical`),
  KEY `cPath` (`cPath`(128))
) ENGINE=MyISAM AUTO_INCREMENT=58 DEFAULT CHARSET=utf8;

INSERT INTO PagePaths VALUES(1,5,'/login','1')
 ,(2,6,'/register','1')
 ,(3,7,'/profile','1')
 ,(4,8,'/profile/edit','1')
 ,(5,9,'/members','1')
 ,(6,10,'/profile/avatar','1')
 ,(7,11,'/profile/messages','1')
 ,(8,12,'/profile/friends','1')
 ,(9,13,'/page_not_found','1')
 ,(10,14,'/page_forbidden','1')
 ,(11,15,'/dashboard','1')
 ,(12,16,'/dashboard/sitemap','1')
 ,(13,17,'/dashboard/sitemap/full','1')
 ,(14,18,'/dashboard/sitemap/explore','1')
 ,(15,19,'/dashboard/sitemap/search','1')
 ,(16,20,'/dashboard/sitemap/access','1')
 ,(17,21,'/dashboard/files','1')
 ,(18,22,'/dashboard/files/search','1')
 ,(19,23,'/dashboard/files/attributes','1')
 ,(20,24,'/dashboard/files/sets','1')
 ,(21,25,'/dashboard/files/access','1')
 ,(22,26,'/dashboard/reports','1')
 ,(23,27,'/dashboard/reports/forms','1')
 ,(24,28,'/dashboard/reports/surveys','1')
 ,(25,29,'/dashboard/reports/logs','1')
 ,(26,30,'/dashboard/users','1')
 ,(27,31,'/dashboard/users/search','1')
 ,(28,32,'/dashboard/users/add','1')
 ,(29,33,'/dashboard/users/groups','1')
 ,(30,34,'/dashboard/users/attributes','1')
 ,(31,35,'/dashboard/users/registration','1')
 ,(32,36,'/dashboard/scrapbook','1')
 ,(33,37,'/dashboard/pages','1')
 ,(34,38,'/dashboard/pages/themes','1')
 ,(35,39,'/dashboard/pages/themes/add','1')
 ,(36,40,'/dashboard/pages/themes/inspect','1')
 ,(37,41,'/dashboard/pages/themes/customize','1')
 ,(38,42,'/dashboard/pages/themes/marketplace','1')
 ,(39,43,'/dashboard/pages/types','1')
 ,(40,44,'/dashboard/pages/attributes','1')
 ,(41,45,'/dashboard/pages/single','1')
 ,(42,46,'/dashboard/install','1')
 ,(43,47,'/dashboard/system','1')
 ,(44,48,'/dashboard/system/jobs','1')
 ,(45,49,'/dashboard/system/backup','1')
 ,(46,50,'/dashboard/system/update','1')
 ,(47,51,'/dashboard/system/notifications','1')
 ,(48,52,'/dashboard/settings','1')
 ,(49,53,'/dashboard/settings/mail','1')
 ,(50,54,'/dashboard/settings/marketplace','1')
 ,(51,55,'/download_file','1')
 ,(52,56,'/about','1')
 ,(53,57,'/about/press-room','1')
 ,(54,59,'/about/press-room/launch-our-new-site','1')
 ,(55,60,'/about/guestbook','1')
 ,(56,61,'/search','1')
 ,(57,62,'/search/search-results','1');

DROP TABLE IF EXISTS PagePermissionPageTypes;

CREATE TABLE IF NOT EXISTS `PagePermissionPageTypes` (
  `cID` int(10) unsigned NOT NULL default '0',
  `gID` int(10) unsigned NOT NULL default '0',
  `uID` int(10) unsigned NOT NULL default '0',
  `ctID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cID`,`gID`,`uID`,`ctID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS PagePermissions;

CREATE TABLE IF NOT EXISTS `PagePermissions` (
  `cID` int(10) unsigned NOT NULL default '0',
  `gID` int(10) unsigned NOT NULL default '0',
  `uID` int(10) unsigned NOT NULL default '0',
  `cgPermissions` varchar(32) default NULL,
  `cgStartDate` datetime default NULL,
  `cgEndDate` datetime default NULL,
  PRIMARY KEY  (`cID`,`gID`,`uID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO PagePermissions VALUES(5,1,0,'r',NULL,NULL)
 ,(5,2,0,'r',NULL,NULL)
 ,(6,1,0,'r',NULL,NULL)
 ,(15,3,0,'r:wa:adm',NULL,NULL)
 ,(1,1,0,'r',NULL,NULL)
 ,(1,3,0,'r:rv:wa:db:av:dc:adm',NULL,NULL);

DROP TABLE IF EXISTS PageSearchIndex;

CREATE TABLE IF NOT EXISTS `PageSearchIndex` (
  `cID` int(10) unsigned NOT NULL default '0',
  `content` text,
  `cName` varchar(255) default NULL,
  `cDescription` text,
  `cPath` text,
  `cDatePublic` datetime default NULL,
  `cDateLastIndexed` datetime default NULL,
  `cDateLastSitemapped` datetime default NULL,
  PRIMARY KEY  (`cID`),
  KEY `cDateLastIndexed` (`cDateLastIndexed`),
  KEY `cDateLastSitemapped` (`cDateLastSitemapped`),
  FULLTEXT KEY `cName` (`cName`),
  FULLTEXT KEY `cDescription` (`cDescription`),
  FULLTEXT KEY `content` (`content`),
  FULLTEXT KEY `content2` (`cName`,`cDescription`,`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO PageSearchIndex VALUES(1,'','Home',NULL,NULL,'2010-09-13 11:37:30','2010-09-13 11:37:30',NULL)
 ,(55,'','Download File',NULL,'/download_file',NOW(),NOW(),NULL)
 ,(16,'','Sitemap',NULL,'/dashboard/sitemap',NOW(),NOW(),NULL)
 ,(17,'','Full',NULL,'/dashboard/sitemap/full',NOW(),NOW(),NULL)
 ,(18,'','Explore',NULL,'/dashboard/sitemap/explore',NOW(),NOW(),NULL)
 ,(19,'','Search',NULL,'/dashboard/sitemap/search',NOW(),NOW(),NULL)
 ,(21,'','Files',NULL,'/dashboard/files',NOW(),NOW(),NULL)
 ,(22,'','Search',NULL,'/dashboard/files/search',NOW(),NOW(),NULL)
 ,(23,'','Attributes',NULL,'/dashboard/files/attributes',NOW(),NOW(),NULL)
 ,(24,'','Sets',NULL,'/dashboard/files/sets',NOW(),NOW(),NULL)
 ,(25,'','Access',NULL,'/dashboard/files/access',NOW(),NOW(),NULL)
 ,(26,'','Reports',NULL,'/dashboard/reports',NOW(),NOW(),NULL)
 ,(27,'','Forms',NULL,'/dashboard/reports/forms',NOW(),NOW(),NULL)
 ,(28,'','Surveys',NULL,'/dashboard/reports/surveys',NOW(),NOW(),NULL)
 ,(29,'','Logs',NULL,'/dashboard/reports/logs',NOW(),NOW(),NULL)
 ,(30,'','Users',NULL,'/dashboard/users',NOW(),NOW(),NULL)
 ,(31,'','Search',NULL,'/dashboard/users/search',NOW(),NOW(),NULL)
 ,(32,'','Add',NULL,'/dashboard/users/add',NOW(),NOW(),NULL)
 ,(33,'','Groups',NULL,'/dashboard/users/groups',NOW(),NOW(),NULL)
 ,(34,'','Attributes',NULL,'/dashboard/users/attributes',NOW(),NOW(),NULL)
 ,(35,'','Registration',NULL,'/dashboard/users/registration',NOW(),NOW(),NULL)
 ,(36,'','Scrapbook',NULL,'/dashboard/scrapbook',NOW(),NOW(),NULL)
 ,(37,'','Pages',NULL,'/dashboard/pages',NOW(),NOW(),NULL)
 ,(38,'','Themes',NULL,'/dashboard/pages/themes',NOW(),NOW(),NULL)
 ,(43,'','Types',NULL,'/dashboard/pages/types',NOW(),NOW(),NULL)
 ,(45,'','Single',NULL,'/dashboard/pages/single',NOW(),NOW(),NULL)
 ,(46,'','Install',NULL,'/dashboard/install',NOW(),NOW(),NULL)
 ,(47,'','System',NULL,'/dashboard/system',NOW(),NOW(),NULL)
 ,(49,'','Backup',NULL,'/dashboard/system/backup',NOW(),NOW(),NULL)
 ,(52,'','Settings',NULL,'/dashboard/settings',NOW(),NOW(),NULL)
 ,(53,'','Mail',NULL,'/dashboard/settings/mail',NOW(),NOW(),NULL)
 ,(59,'','Launch our new site!','Neeto speedo! We just rebuilt our site in record time and now we can easily change EVERYTHING.','/about/press-room/launch-our-new-site',NOW(),NOW(),NULL)
 ,(62,'','Search Results',NULL,'/search/search-results',NOW(),NOW(),NULL)
 ,(9,'','Members',NULL,'/members','2010-09-13 11:37:31',NOW(),NULL)
 ,(42,'','Marketplace',NULL,'/dashboard/pages/themes/marketplace',NOW(),NOW(),NULL)
 ,(56,'Sed ut perspiciatis unde omnis iste natus error (H1) Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?  Contact Us: ','About',NULL,'/about',NOW(),NOW(),NULL)
 ,(57,'Welcome This a great example of how flexible page types can be. We added a page type of \"press release\" so we could assign some custom attributes to it and make the very PR-ish formatted custom template for the page list block below. Press Releases: ','Press Room',NULL,'/about/press-room',NOW(),NOW(),NULL)
 ,(60,'We\'re happy to see you here.  Let us know you\'ve been here by signing our guestbook.  ','Guestbook','Neeto speedo! We just rebuilt our site in record time and now we can easily change EVERYTHING.','/about/guestbook',NOW(),NOW(),NULL)
 ,(61,'Sitemap ','Search',NULL,'/search',NOW(),NOW(),NULL);

DROP TABLE IF EXISTS PageStatistics;

CREATE TABLE IF NOT EXISTS `PageStatistics` (
  `pstID` bigint(20) unsigned NOT NULL auto_increment,
  `cID` int(10) unsigned NOT NULL default '0',
  `date` date default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `uID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pstID`),
  KEY `cID` (`cID`),
  KEY `date` (`date`),
  KEY `uID` (`uID`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS PageThemeStyles;

CREATE TABLE IF NOT EXISTS `PageThemeStyles` (
  `ptID` int(10) unsigned NOT NULL default '0',
  `ptsHandle` varchar(128) NOT NULL,
  `ptsValue` longtext,
  `ptsType` varchar(32) NOT NULL,
  PRIMARY KEY  (`ptID`,`ptsHandle`,`ptsType`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS PageThemes;

CREATE TABLE IF NOT EXISTS `PageThemes` (
  `ptID` int(10) unsigned NOT NULL auto_increment,
  `ptHandle` varchar(64) NOT NULL,
  `ptName` varchar(255) default NULL,
  `ptDescription` text,
  `pkgID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ptID`),
  UNIQUE KEY `ptHandle` (`ptHandle`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO PageThemes VALUES(1,'default','Plain Yogurt\n','Plain Yogurt is Concrete\'s default theme.',0)
 ,(2,'greensalad','Green Salad Theme\n','This is Concrete\'s Green Salad site theme.',0)
 ,(3,'dark_chocolate','Dark Chocolate\n','Dark Chocolate is Concrete\'s default theme in black.',0);

DROP TABLE IF EXISTS PageTypeAttributes;

CREATE TABLE IF NOT EXISTS `PageTypeAttributes` (
  `ctID` int(10) unsigned NOT NULL default '0',
  `akID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ctID`,`akID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO PageTypeAttributes VALUES(1,1)
 ,(1,2)
 ,(1,3)
 ,(1,4)
 ,(2,1)
 ,(2,2)
 ,(2,3)
 ,(2,4)
 ,(3,1)
 ,(3,2)
 ,(3,3)
 ,(3,4)
 ,(4,1)
 ,(4,2)
 ,(4,3)
 ,(4,4)
 ,(4,14)
 ,(4,15);

DROP TABLE IF EXISTS PageTypes;

CREATE TABLE IF NOT EXISTS `PageTypes` (
  `ctID` int(10) unsigned NOT NULL auto_increment,
  `ctHandle` varchar(32) NOT NULL,
  `ctIcon` varchar(128) default NULL,
  `ctName` varchar(90) NOT NULL,
  `pkgID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ctID`),
  UNIQUE KEY `ctHandle` (`ctHandle`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO PageTypes VALUES(1,'right_sidebar','template3.png','Right Sidebar',0)
 ,(2,'left_sidebar','template1.png','Left Sidebar',0)
 ,(3,'full','main.png','Full Width',0)
 ,(4,'Press Release','template3.png','Press Release',0);

DROP TABLE IF EXISTS Pages;

CREATE TABLE IF NOT EXISTS `Pages` (
  `cID` int(10) unsigned NOT NULL default '0',
  `ctID` int(10) unsigned NOT NULL default '0',
  `cIsTemplate` varchar(1) NOT NULL default '0',
  `uID` int(10) unsigned default NULL,
  `cIsCheckedOut` tinyint(1) NOT NULL default '0',
  `cCheckedOutUID` int(10) unsigned default NULL,
  `cCheckedOutDatetime` datetime default NULL,
  `cCheckedOutDatetimeLastEdit` datetime default NULL,
  `cPendingAction` varchar(6) default NULL,
  `cPendingActionDatetime` datetime NOT NULL default '0000-00-00 00:00:00',
  `cPendingActionUID` int(10) unsigned default NULL,
  `cPendingActionTargetCID` int(10) unsigned default NULL,
  `cOverrideTemplatePermissions` tinyint(1) NOT NULL default '1',
  `cInheritPermissionsFromCID` int(10) unsigned NOT NULL default '0',
  `cInheritPermissionsFrom` varchar(8) NOT NULL default 'PARENT',
  `cFilename` varchar(255) default NULL,
  `cPointerID` int(10) unsigned NOT NULL default '0',
  `cPointerExternalLink` varchar(255) default NULL,
  `cPointerExternalLinkNewWindow` tinyint(1) NOT NULL default '0',
  `cChildren` int(10) unsigned NOT NULL default '0',
  `cDisplayOrder` int(10) unsigned NOT NULL default '0',
  `cParentID` int(10) unsigned NOT NULL default '0',
  `pkgID` int(10) unsigned NOT NULL default '0',
  `ptID` int(10) unsigned NOT NULL default '0',
  `cCacheFullPageContent` int(4) NOT NULL default '-1',
  `cCacheFullPageContentOverrideLifetime` varchar(32) NOT NULL default '0',
  `cCacheFullPageContentLifetimeCustom` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cID`),
  KEY `cParentID` (`cParentID`),
  KEY `cCheckedOutUID` (`cCheckedOutUID`),
  KEY `cPointerID` (`cPointerID`),
  KEY `uID` (`uID`),
  KEY `ctID` (`ctID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO Pages VALUES(1,1,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,1,'OVERRIDE',NULL,0,NULL,0,10,0,0,0,1,-1,'0',0)
 ,(2,1,'1',NULL,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,0,'PARENT',NULL,0,NULL,0,0,0,0,0,0,-1,'0',0)
 ,(3,2,'1',NULL,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,0,'PARENT',NULL,0,NULL,0,0,0,0,0,0,-1,'0',0)
 ,(4,3,'1',NULL,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,0,'PARENT',NULL,0,NULL,0,0,0,0,0,0,-1,'0',0)
 ,(5,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,5,'OVERRIDE','/login.php',0,NULL,0,0,0,1,0,1,-1,'0',0)
 ,(6,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,6,'OVERRIDE','/register.php',0,NULL,0,0,1,1,0,1,-1,'0',0)
 ,(7,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,1,'PARENT','/profile/view.php',0,NULL,0,4,2,1,0,1,-1,'0',0)
 ,(8,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,1,'PARENT','/profile/edit.php',0,NULL,0,0,0,7,0,1,-1,'0',0)
 ,(9,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,1,'PARENT','/members.php',0,NULL,0,0,3,1,0,1,-1,'0',0)
 ,(10,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,1,'PARENT','/profile/avatar.php',0,NULL,0,0,1,7,0,1,-1,'0',0)
 ,(11,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,1,'PARENT','/profile/messages.php',0,NULL,0,0,2,7,0,1,-1,'0',0)
 ,(12,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,1,'PARENT','/profile/friends.php',0,NULL,0,0,3,7,0,1,-1,'0',0)
 ,(13,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,1,'PARENT','/page_not_found.php',0,NULL,0,0,4,1,0,1,-1,'0',0)
 ,(14,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,1,'PARENT','/page_forbidden.php',0,NULL,0,0,5,1,0,1,-1,'0',0)
 ,(15,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'OVERRIDE','/dashboard/view.php',0,NULL,0,9,6,1,0,0,-1,'0',0)
 ,(16,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/sitemap/view.php',0,NULL,0,4,0,15,0,0,-1,'0',0)
 ,(17,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/sitemap/full.php',0,NULL,0,0,0,16,0,0,-1,'0',0)
 ,(18,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/sitemap/explore.php',0,NULL,0,0,1,16,0,0,-1,'0',0)
 ,(19,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/sitemap/search.php',0,NULL,0,0,2,16,0,0,-1,'0',0)
 ,(20,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/sitemap/access.php',0,NULL,0,0,3,16,0,0,-1,'0',0)
 ,(21,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/files/view.php',0,NULL,0,4,1,15,0,0,-1,'0',0)
 ,(22,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/files/search.php',0,NULL,0,0,0,21,0,0,-1,'0',0)
 ,(23,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/files/attributes.php',0,NULL,0,0,1,21,0,0,-1,'0',0)
 ,(24,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/files/sets.php',0,NULL,0,0,2,21,0,0,-1,'0',0)
 ,(25,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/files/access.php',0,NULL,0,0,3,21,0,0,-1,'0',0)
 ,(26,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/reports.php',0,NULL,0,3,2,15,0,0,-1,'0',0)
 ,(27,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/reports/forms.php',0,NULL,0,0,0,26,0,0,-1,'0',0)
 ,(28,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/reports/surveys.php',0,NULL,0,0,1,26,0,0,-1,'0',0)
 ,(29,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/reports/logs.php',0,NULL,0,0,2,26,0,0,-1,'0',0)
 ,(30,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/users/view.php',0,NULL,0,5,3,15,0,0,-1,'0',0)
 ,(31,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/users/search.php',0,NULL,0,0,0,30,0,0,-1,'0',0)
 ,(32,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/users/add.php',0,NULL,0,0,1,30,0,0,-1,'0',0)
 ,(33,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/users/groups.php',0,NULL,0,0,2,30,0,0,-1,'0',0)
 ,(34,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/users/attributes.php',0,NULL,0,0,3,30,0,0,-1,'0',0)
 ,(35,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/users/registration.php',0,NULL,0,0,4,30,0,0,-1,'0',0)
 ,(36,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/scrapbook/view.php',0,NULL,0,0,4,15,0,0,-1,'0',0)
 ,(37,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/pages/view.php',0,NULL,0,4,5,15,0,0,-1,'0',0)
 ,(38,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/pages/themes/view.php',0,NULL,0,4,0,37,0,0,-1,'0',0)
 ,(39,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/pages/themes/add.php',0,NULL,0,0,0,38,0,0,-1,'0',0)
 ,(40,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/pages/themes/inspect.php',0,NULL,0,0,1,38,0,0,-1,'0',0)
 ,(41,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/pages/themes/customize.php',0,NULL,0,0,2,38,0,0,-1,'0',0)
 ,(42,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','0',0,NULL,0,0,3,38,0,0,-1,'0',0)
 ,(43,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/pages/types/view.php',0,NULL,0,0,1,37,0,0,-1,'0',0)
 ,(44,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/pages/attributes.php',0,NULL,0,0,2,37,0,0,-1,'0',0)
 ,(45,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/pages/single.php',0,NULL,0,0,3,37,0,0,-1,'0',0)
 ,(46,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/install.php',0,NULL,0,0,6,15,0,0,-1,'0',0)
 ,(47,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/system/view.php',0,NULL,0,4,7,15,0,0,-1,'0',0)
 ,(48,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/system/jobs.php',0,NULL,0,0,0,47,0,0,-1,'0',0)
 ,(49,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/system/backup.php',0,NULL,0,0,1,47,0,0,-1,'0',0)
 ,(50,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/system/update.php',0,NULL,0,0,2,47,0,0,-1,'0',0)
 ,(51,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/system/notifications.php',0,NULL,0,0,3,47,0,0,-1,'0',0)
 ,(52,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/settings/view.php',0,NULL,0,2,8,15,0,0,-1,'0',0)
 ,(53,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/settings/mail/view.php',0,NULL,0,0,0,52,0,0,-1,'0',0)
 ,(54,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,15,'PARENT','/dashboard/settings/marketplace.php',0,NULL,0,0,1,52,0,0,-1,'0',0)
 ,(55,0,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,1,'PARENT','/download_file.php',0,NULL,0,0,7,1,0,0,-1,'0',0)
 ,(56,2,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,1,'PARENT',NULL,0,NULL,0,2,8,1,0,0,-1,'0',0)
 ,(57,2,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,1,'PARENT',NULL,0,NULL,0,1,0,56,0,1,-1,'0',0)
 ,(58,4,'1',NULL,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,0,'PARENT',NULL,0,NULL,0,0,0,0,0,0,-1,'0',0)
 ,(59,4,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,1,'PARENT',NULL,0,NULL,0,0,0,57,0,1,-1,'0',0)
 ,(60,2,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,1,'PARENT',NULL,0,NULL,0,0,1,56,0,1,-1,'0',0)
 ,(61,2,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,1,'PARENT',NULL,0,NULL,0,1,9,1,0,0,-1,'0',0)
 ,(62,2,'0',1,0,NULL,NULL,NULL,NULL,NOW(),NULL,NULL,1,1,'PARENT',NULL,0,NULL,0,0,0,61,0,1,-1,'0',0);

DROP TABLE IF EXISTS PileContents;

CREATE TABLE IF NOT EXISTS `PileContents` (
  `pcID` int(10) unsigned NOT NULL auto_increment,
  `pID` int(10) unsigned NOT NULL default '0',
  `itemID` int(10) unsigned NOT NULL default '0',
  `itemType` varchar(64) NOT NULL,
  `quantity` int(10) unsigned NOT NULL default '1',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `displayOrder` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pcID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS Piles;

CREATE TABLE IF NOT EXISTS `Piles` (
  `pID` int(10) unsigned NOT NULL auto_increment,
  `uID` int(10) unsigned default NULL,
  `isDefault` tinyint(1) NOT NULL default '0',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `name` varchar(255) default NULL,
  `state` varchar(64) NOT NULL,
  PRIMARY KEY  (`pID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS SignupRequests;

CREATE TABLE IF NOT EXISTS `SignupRequests` (
  `id` int(11) NOT NULL auto_increment,
  `ipFrom` int(10) unsigned NOT NULL default '0',
  `date_access` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `index_ipFrom` (`ipFrom`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS SystemNotifications;

CREATE TABLE IF NOT EXISTS `SystemNotifications` (
  `snID` int(10) unsigned NOT NULL auto_increment,
  `snTypeID` int(3) unsigned NOT NULL default '0',
  `snURL` text,
  `snURL2` text,
  `snDateTime` datetime NOT NULL,
  `snIsArchived` int(1) NOT NULL default '0',
  `snIsNew` int(1) NOT NULL default '0',
  `snTitle` varchar(255) default NULL,
  `snDescription` text,
  `snBody` text,
  PRIMARY KEY  (`snID`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS TaskPermissionUserGroups;

CREATE TABLE IF NOT EXISTS `TaskPermissionUserGroups` (
  `tpID` int(10) unsigned NOT NULL default '0',
  `gID` int(10) unsigned NOT NULL default '0',
  `uID` int(10) unsigned NOT NULL default '0',
  `canRead` int(1) NOT NULL default '0',
  PRIMARY KEY  (`tpID`,`gID`,`uID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO TaskPermissionUserGroups VALUES(2,3,0,1)
 ,(3,3,0,1)
 ,(4,3,0,1)
 ,(6,3,0,1);

DROP TABLE IF EXISTS TaskPermissions;

CREATE TABLE IF NOT EXISTS `TaskPermissions` (
  `tpID` int(10) unsigned NOT NULL auto_increment,
  `tpHandle` varchar(255) default NULL,
  `tpName` varchar(255) default NULL,
  `tpDescription` text,
  `pkgID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`tpID`),
  UNIQUE KEY `tpHandle` (`tpHandle`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

INSERT INTO TaskPermissions VALUES(1,'access_task_permissions','Change Task Permissions','',0)
 ,(2,'access_sitemap','Access Sitemap and Page Search','',0)
 ,(3,'access_user_search','Access User Search','',0)
 ,(4,'access_group_search','Access Group Search','',0)
 ,(5,'access_page_defaults','Change Content on Page Type Default Pages','',0)
 ,(6,'backup','Perform Full Database Backups','',0)
 ,(7,'sudo','Sign in as User','',0)
 ,(8,'uninstall_packages','Uninstall Packages','',0);

DROP TABLE IF EXISTS UserAttributeKeys;

CREATE TABLE IF NOT EXISTS `UserAttributeKeys` (
  `akID` int(10) unsigned NOT NULL,
  `uakProfileDisplay` tinyint(1) NOT NULL default '0',
  `uakMemberListDisplay` tinyint(1) NOT NULL default '0',
  `uakProfileEdit` tinyint(1) NOT NULL default '1',
  `uakProfileEditRequired` tinyint(1) NOT NULL default '0',
  `uakRegisterEdit` tinyint(1) NOT NULL default '0',
  `uakRegisterEditRequired` tinyint(1) NOT NULL default '0',
  `displayOrder` int(10) unsigned default '0',
  `uakIsActive` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`akID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO UserAttributeKeys VALUES(9,0,0,1,0,0,0,1,1)
 ,(10,0,0,1,0,1,0,2,1)
 ,(11,0,0,1,0,1,0,3,1);

DROP TABLE IF EXISTS UserAttributeValues;

CREATE TABLE IF NOT EXISTS `UserAttributeValues` (
  `uID` int(10) unsigned NOT NULL default '0',
  `akID` int(10) unsigned NOT NULL default '0',
  `avID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`uID`,`akID`,`avID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS UserBannedIPs;

CREATE TABLE IF NOT EXISTS `UserBannedIPs` (
  `ipFrom` int(10) unsigned NOT NULL default '0',
  `ipTo` int(10) unsigned NOT NULL default '0',
  `banCode` int(1) unsigned NOT NULL default '1',
  `expires` int(10) unsigned NOT NULL default '0',
  `isManual` int(1) NOT NULL default '0',
  PRIMARY KEY  (`ipFrom`,`ipTo`),
  KEY `ipFrom` (`ipFrom`),
  KEY `ipTo` (`ipTo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS UserGroups;

CREATE TABLE IF NOT EXISTS `UserGroups` (
  `uID` int(10) unsigned NOT NULL default '0',
  `gID` int(10) unsigned NOT NULL default '0',
  `ugEntered` datetime NOT NULL default '0000-00-00 00:00:00',
  `type` varchar(64) default NULL,
  PRIMARY KEY  (`uID`,`gID`),
  KEY `uID` (`uID`),
  KEY `gID` (`gID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS UserOpenIDs;

CREATE TABLE IF NOT EXISTS `UserOpenIDs` (
  `uID` int(10) unsigned NOT NULL,
  `uOpenID` varchar(255) NOT NULL,
  PRIMARY KEY  (`uOpenID`),
  KEY `uID` (`uID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS UserPrivateMessages;

CREATE TABLE IF NOT EXISTS `UserPrivateMessages` (
  `msgID` int(10) unsigned NOT NULL auto_increment,
  `uAuthorID` int(10) unsigned NOT NULL default '0',
  `msgDateCreated` datetime NOT NULL,
  `msgSubject` varchar(255) NOT NULL,
  `msgBody` text,
  `uToID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`msgID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS UserPrivateMessagesTo;

CREATE TABLE IF NOT EXISTS `UserPrivateMessagesTo` (
  `msgID` int(10) unsigned NOT NULL default '0',
  `uID` int(10) unsigned NOT NULL default '0',
  `uAuthorID` int(10) unsigned NOT NULL default '0',
  `msgMailboxID` int(11) NOT NULL,
  `msgIsNew` int(1) NOT NULL default '0',
  `msgIsUnread` int(1) NOT NULL default '0',
  `msgIsReplied` int(1) NOT NULL default '0',
  PRIMARY KEY  (`msgID`,`uID`,`uAuthorID`),
  KEY `uID` (`uID`),
  KEY `uAuthorID` (`uAuthorID`),
  KEY `msgFolderID` (`msgMailboxID`),
  KEY `msgIsNew` (`msgIsNew`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS UserSearchIndexAttributes;

CREATE TABLE IF NOT EXISTS `UserSearchIndexAttributes` (
  `uID` int(11) unsigned NOT NULL default '0',
  `ak_date_of_birth` datetime default NULL,
  `ak_profile_private_messages_enabled` tinyint(4) default '0',
  `ak_profile_private_messages_notification_enabled` tinyint(4) default '0',
  PRIMARY KEY  (`uID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS UserValidationHashes;

CREATE TABLE IF NOT EXISTS `UserValidationHashes` (
  `uvhID` int(10) unsigned NOT NULL auto_increment,
  `uID` int(10) unsigned default NULL,
  `uHash` varchar(64) NOT NULL,
  `type` int(4) unsigned NOT NULL default '0',
  `uDateGenerated` int(10) unsigned NOT NULL default '0',
  `uDateRedeemed` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`uvhID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS Users;

CREATE TABLE IF NOT EXISTS `Users` (
  `uID` int(10) unsigned NOT NULL auto_increment,
  `uName` varchar(64) NOT NULL,
  `uEmail` varchar(64) NOT NULL,
  `uPassword` varchar(255) NOT NULL,
  `uIsActive` varchar(1) NOT NULL default '0',
  `uIsValidated` tinyint(4) NOT NULL default '-1',
  `uIsFullRecord` tinyint(1) NOT NULL default '1',
  `uDateAdded` datetime NOT NULL default '0000-00-00 00:00:00',
  `uHasAvatar` tinyint(1) NOT NULL default '0',
  `uLastOnline` int(10) unsigned NOT NULL default '0',
  `uLastLogin` int(10) unsigned NOT NULL default '0',
  `uPreviousLogin` int(10) unsigned NOT NULL default '0',
  `uNumLogins` int(10) unsigned NOT NULL default '0',
  `uTimezone` varchar(255) default NULL,
  PRIMARY KEY  (`uID`),
  UNIQUE KEY `uName` (`uName`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS UsersFriends;

CREATE TABLE IF NOT EXISTS `UsersFriends` (
  `ufID` int(10) unsigned NOT NULL auto_increment,
  `uID` int(10) unsigned default NULL,
  `status` varchar(64) NOT NULL,
  `friendUID` int(10) unsigned default NULL,
  `uDateAdded` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`ufID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS atAddress;

CREATE TABLE IF NOT EXISTS `atAddress` (
  `avID` int(10) unsigned NOT NULL default '0',
  `address1` varchar(255) default NULL,
  `address2` varchar(255) default NULL,
  `city` varchar(255) default NULL,
  `state_province` varchar(255) default NULL,
  `country` varchar(4) default NULL,
  `postal_code` varchar(32) default NULL,
  PRIMARY KEY  (`avID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS atAddressCustomCountries;

CREATE TABLE IF NOT EXISTS `atAddressCustomCountries` (
  `atAddressCustomCountryID` int(10) unsigned NOT NULL auto_increment,
  `akID` int(10) unsigned NOT NULL default '0',
  `country` varchar(5) NOT NULL,
  PRIMARY KEY  (`atAddressCustomCountryID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS atAddressSettings;

CREATE TABLE IF NOT EXISTS `atAddressSettings` (
  `akID` int(10) unsigned NOT NULL default '0',
  `akHasCustomCountries` int(1) NOT NULL default '0',
  `akDefaultCountry` varchar(12) default NULL,
  PRIMARY KEY  (`akID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS atBoolean;

CREATE TABLE IF NOT EXISTS `atBoolean` (
  `avID` int(10) unsigned NOT NULL,
  `value` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`avID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO atBoolean VALUES(13,1);

DROP TABLE IF EXISTS atBooleanSettings;

CREATE TABLE IF NOT EXISTS `atBooleanSettings` (
  `akID` int(10) unsigned NOT NULL,
  `akCheckedByDefault` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`akID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO atBooleanSettings VALUES(4,0)
 ,(5,0)
 ,(7,0)
 ,(8,0)
 ,(10,1)
 ,(11,1);

DROP TABLE IF EXISTS atDateTime;

CREATE TABLE IF NOT EXISTS `atDateTime` (
  `avID` int(10) unsigned NOT NULL,
  `value` datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (`avID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO atDateTime VALUES(11,'2010-09-13 11:37:00');

DROP TABLE IF EXISTS atDateTimeSettings;

CREATE TABLE IF NOT EXISTS `atDateTimeSettings` (
  `akID` int(10) unsigned NOT NULL,
  `akDateDisplayMode` varchar(255) default NULL,
  PRIMARY KEY  (`akID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO atDateTimeSettings VALUES(9,'text')
 ,(14,'date_time');

DROP TABLE IF EXISTS atDefault;

CREATE TABLE IF NOT EXISTS `atDefault` (
  `avID` int(10) unsigned NOT NULL,
  `value` longtext,
  PRIMARY KEY  (`avID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS atFile;

CREATE TABLE IF NOT EXISTS `atFile` (
  `avID` int(10) unsigned NOT NULL,
  `fID` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`avID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS atNumber;

CREATE TABLE IF NOT EXISTS `atNumber` (
  `avID` int(10) unsigned NOT NULL,
  `value` decimal(14,4) default '0.0000',
  PRIMARY KEY  (`avID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO atNumber VALUES(1,800.0000)
 ,(2,192.0000)
 ,(3,800.0000)
 ,(4,192.0000)
 ,(5,800.0000)
 ,(6,192.0000)
 ,(7,800.0000)
 ,(8,192.0000)
 ,(9,800.0000)
 ,(10,215.0000);

DROP TABLE IF EXISTS atSelectOptions;

CREATE TABLE IF NOT EXISTS `atSelectOptions` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `akID` int(10) unsigned default NULL,
  `value` varchar(255) default NULL,
  `displayOrder` int(10) unsigned default NULL,
  `isEndUserAdded` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO atSelectOptions VALUES(1,15,'Press Release',0,0)
 ,(2,15,'News Item',1,0)
 ,(3,15,'Speaking/Event',2,0);

DROP TABLE IF EXISTS atSelectOptionsSelected;

CREATE TABLE IF NOT EXISTS `atSelectOptionsSelected` (
  `avID` int(10) unsigned NOT NULL,
  `atSelectOptionID` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`avID`,`atSelectOptionID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO atSelectOptionsSelected VALUES(12,1);

DROP TABLE IF EXISTS atSelectSettings;

CREATE TABLE IF NOT EXISTS `atSelectSettings` (
  `akID` int(10) unsigned NOT NULL,
  `akSelectAllowMultipleValues` tinyint(1) NOT NULL default '0',
  `akSelectOptionDisplayOrder` varchar(255) NOT NULL default 'display_asc',
  `akSelectAllowOtherValues` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`akID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO atSelectSettings VALUES(15,0,'display_asc',0);

DROP TABLE IF EXISTS atTextareaSettings;

CREATE TABLE IF NOT EXISTS `atTextareaSettings` (
  `akID` int(10) unsigned NOT NULL default '0',
  `akTextareaDisplayMode` varchar(255) default NULL,
  PRIMARY KEY  (`akID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO atTextareaSettings VALUES(2,'text')
 ,(3,'text')
 ,(6,'text');

DROP TABLE IF EXISTS btContentFile;

CREATE TABLE IF NOT EXISTS `btContentFile` (
  `bID` int(10) unsigned NOT NULL,
  `fID` int(10) unsigned default NULL,
  `fileLinkText` varchar(255) default NULL,
  `filePassword` varchar(255) default NULL,
  PRIMARY KEY  (`bID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS btContentImage;

CREATE TABLE IF NOT EXISTS `btContentImage` (
  `bID` int(10) unsigned NOT NULL,
  `fID` int(10) unsigned default '0',
  `fOnstateID` int(10) unsigned default '0',
  `maxWidth` int(10) unsigned default '0',
  `maxHeight` int(10) unsigned default '0',
  `externalLink` varchar(255) default NULL,
  `altText` varchar(255) default NULL,
  PRIMARY KEY  (`bID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO btContentImage VALUES(24,5,0,0,0,NULL,'Header Image')
 ,(7,2,0,0,0,NULL,'Left Sidebar Page Type Image')
 ,(8,3,0,0,0,NULL,'Right Sidebar Page Type Image')
 ,(9,3,0,0,0,NULL,'Full Width Page Type Image');

DROP TABLE IF EXISTS btContentLocal;

CREATE TABLE IF NOT EXISTS `btContentLocal` (
  `bID` int(10) unsigned NOT NULL,
  `content` longtext,
  PRIMARY KEY  (`bID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO btContentLocal VALUES(10,'{[CCM:SITE]}')
 ,(11,'<h1>Welcome to Concrete.</h1><p>Learn how to:</p><ul><li><a title=\"web editing with concrete5\" href=\"http://www.concrete5.org/help/editing/login-incontext-editing/\" target=\"_blank\">Edit</a> this page.</li><li>Add a <a title=\"add pages in concrete5\" href=\"http://www.concrete5.org/help/editing/add-a-page/\" target=\"_blank\">new page</a>.</li><li>Add some basic functionality, like <a title=\"os cms concrete5\" href=\"http://www.concrete5.org/help/editing/add_a_form/\" target=\"_blank\">a Form</a>.</li><li><a title=\"add-on marketplace for concrete5\" href=\"http://www.concrete5.org/help/editing/installing_a_package/\" target=\"_blank\">Finding &amp; adding</a> more functionality and themes. </li></ul><p>We\'ve taken the liberty to build out the rest of this like a typical small organization. Wander around and put these pages in edit mode to see how we did it.</p>')
 ,(13,'<h3>Learn More</h3><p>Visit concrete5.org to learn more from the <a title=\"open source content management system\" href=\"{CCM:BASE_URL}.org/community\" target=\"_blank\">community</a> and the <a title=\"CMS concrete5\" href=\"{CCM:BASE_URL}.org/help\" target=\"_blank\">help</a> section.</p>')
 ,(14,'<h2>Sidebar</h2><p>Everything about Concrete is completely customizable through the CMS. This is a separate area from the main content on the homepage. You can <a title=\"blocks on concrete5\" href=\"http://www.concrete5.org/help/editing/arrange_blocks_on_a_page/\" target=\"_blank\">drag and drop blocks</a> like this around your layout.</p>')
 ,(16,'<h1>Sed ut perspiciatis unde omnis iste natus error (H1)</h1><p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>')
 ,(17,'<h2>Contact Us:</h2>')
 ,(20,'<h1>Welcome</h1><p>This a great example of how flexible page types can be. We added a page type of \"press release\" so we could assign some custom attributes to it and make the very PR-ish formatted custom template for the page list block below.</p><h2>Press Releases:</h2>')
 ,(25,'<p>Vestibulum a tristique tellus. Morbi nunc orci, ornare sit amet accumsan nec, sagittis eu nisi. Sed elementum fringilla ipsum a interdum. Nulla egestas turpis at dui interdum faucibus. Proin consectetur nibh eros, eget sodales felis. Nam volutpat bibendum augue ut lacinia. Nam volutpat fringilla odio, vitae feugiat dui sagittis sed. Aenean interdum accumsan luctus. Suspendisse congue sagittis tortor ut porta. Etiam justo augue, auctor posuere iaculis ac, aliquet vitae purus. In malesuada, ipsum non vulputate semper, felis purus condimentum augue, a mollis metus nisi a diam. Nulla eu scelerisque lacus. Morbi congue massa vitae nulla auctor suscipit. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vivamus ut mauris quis nunc rhoncus lobortis. Aliquam sit amet dui et felis sodales volutpat nec eget nisl.</p><p>Nulla elit dui, pharetra eget elementum eu, varius sed nisi. Morbi semper interdum nisl, eget rutrum ante venenatis nec. Vivamus dignissim, justo quis semper ultricies, quam elit elementum eros, rutrum volutpat urna nulla quis dui. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed et orci ligula. In est dui, porta at posuere a, ullamcorper ut nisi. Maecenas nibh eros, suscipit et pulvinar non, tempus varius dui. Quisque nec tortor sed erat rhoncus laoreet. Sed pulvinar est eu quam adipiscing tincidunt. Donec auctor arcu et ante venenatis sagittis.</p><p>&nbsp;</p>')
 ,(26,'<h2>About Us:<br /></h2><p>This is a content block that is part of the page\'s defaults so it will show up every time you make a new press release. If <a title=\"building with concrete5\" href=\"http://www.concrete5.org/help/editing/scrapbook_defaults/\" target=\"_blank\">change it through defaults</a>, you update it everywhere!</p>')
 ,(27,'<h1>We\'re happy to see you here.<br /></h1><p>Let us know you\'ve been here by signing our guestbook.</p>')
 ,(30,'<h1>Sitemap</h1>');

DROP TABLE IF EXISTS btExternalForm;

CREATE TABLE IF NOT EXISTS `btExternalForm` (
  `bID` int(10) unsigned NOT NULL,
  `filename` varchar(128) default NULL,
  PRIMARY KEY  (`bID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS btFlashContent;

CREATE TABLE IF NOT EXISTS `btFlashContent` (
  `bID` int(10) unsigned NOT NULL,
  `fID` int(10) unsigned default NULL,
  `quality` varchar(255) default NULL,
  `minVersion` varchar(255) default NULL,
  PRIMARY KEY  (`bID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS btForm;

CREATE TABLE IF NOT EXISTS `btForm` (
  `bID` int(10) unsigned NOT NULL,
  `questionSetId` int(10) unsigned default '0',
  `surveyName` varchar(255) default NULL,
  `thankyouMsg` text,
  `notifyMeOnSubmission` tinyint(3) unsigned NOT NULL default '0',
  `recipientEmail` varchar(255) default NULL,
  `displayCaptcha` int(11) default '1',
  `redirectCID` int(11) default '0',
  PRIMARY KEY  (`bID`),
  KEY `questionSetIdForeign` (`questionSetId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO btForm VALUES(18,1,'About','Thanks!',0,NULL,0,0);

DROP TABLE IF EXISTS btFormAnswerSet;

CREATE TABLE IF NOT EXISTS `btFormAnswerSet` (
  `asID` int(10) unsigned NOT NULL auto_increment,
  `questionSetId` int(10) unsigned default '0',
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `uID` int(10) unsigned default '0',
  PRIMARY KEY  (`asID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS btFormAnswers;

CREATE TABLE IF NOT EXISTS `btFormAnswers` (
  `aID` int(10) unsigned NOT NULL auto_increment,
  `asID` int(10) unsigned default '0',
  `msqID` int(10) unsigned default '0',
  `answer` varchar(255) default NULL,
  `answerLong` text,
  PRIMARY KEY  (`aID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS btFormQuestions;

CREATE TABLE IF NOT EXISTS `btFormQuestions` (
  `qID` int(10) unsigned NOT NULL auto_increment,
  `msqID` int(10) unsigned default '0',
  `bID` int(10) unsigned default '0',
  `questionSetId` int(10) unsigned default '0',
  `question` varchar(255) default NULL,
  `inputType` varchar(255) default NULL,
  `options` text,
  `position` int(10) unsigned default '1000',
  `width` int(10) unsigned default '50',
  `height` int(10) unsigned default '3',
  `required` int(11) default '0',
  PRIMARY KEY  (`qID`),
  KEY `questionSetId` (`questionSetId`),
  KEY `msqID` (`msqID`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO btFormQuestions VALUES(1,1,18,1,'Name:','field','',1,0,0,1)
 ,(2,2,18,1,'Phone:','field','',2,0,0,0)
 ,(3,3,18,1,'eMail:','field','',3,0,0,0)
 ,(4,4,18,1,'Comments:','text','',4,20,10,0);

DROP TABLE IF EXISTS btGoogleMap;

CREATE TABLE IF NOT EXISTS `btGoogleMap` (
  `bID` int(10) unsigned NOT NULL,
  `title` varchar(255) default NULL,
  `api_key` varchar(255) default NULL,
  `location` varchar(255) default NULL,
  `latitude` double default NULL,
  `longitude` double default NULL,
  `zoom` int(8) default NULL,
  PRIMARY KEY  (`bID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS btGuestBook;

CREATE TABLE IF NOT EXISTS `btGuestBook` (
  `bID` int(10) unsigned NOT NULL,
  `requireApproval` int(11) default '0',
  `title` varchar(100) default 'Comments',
  `dateFormat` varchar(100) default NULL,
  `displayGuestBookForm` int(11) default '1',
  `displayCaptcha` int(11) default '1',
  `authenticationRequired` int(11) default '0',
  `notifyEmail` varchar(100) default NULL,
  PRIMARY KEY  (`bID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO btGuestBook VALUES(28,0,'Comments',NULL,1,1,0,NULL);

DROP TABLE IF EXISTS btGuestBookEntries;

CREATE TABLE IF NOT EXISTS `btGuestBookEntries` (
  `bID` int(11) default NULL,
  `cID` int(11) default '1',
  `entryID` int(11) NOT NULL auto_increment,
  `uID` int(11) default '0',
  `commentText` longtext,
  `user_name` varchar(100) default NULL,
  `user_email` varchar(100) default NULL,
  `entryDate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `approved` int(11) default '1',
  PRIMARY KEY  (`entryID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS btNavigation;

CREATE TABLE IF NOT EXISTS `btNavigation` (
  `bID` int(10) unsigned NOT NULL,
  `orderBy` varchar(255) default 'alpha_asc',
  `displayPages` varchar(255) default 'top',
  `displayPagesCID` int(10) unsigned NOT NULL default '1',
  `displayPagesIncludeSelf` tinyint(3) unsigned NOT NULL default '0',
  `displaySubPages` varchar(255) default 'none',
  `displaySubPageLevels` varchar(255) default 'none',
  `displaySubPageLevelsNum` smallint(5) unsigned NOT NULL default '0',
  `displayUnavailablePages` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`bID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO btNavigation VALUES(1,'display_asc','top',0,0,'none',NULL,0,0)
 ,(2,'display_asc','top',0,0,'none',NULL,0,0)
 ,(3,'display_asc','top',0,0,'none',NULL,0,0)
 ,(4,'display_asc','second_level',0,0,'relevant','none',0,0)
 ,(5,'display_asc','second_level',0,0,'relevant','none',0,0)
 ,(22,'display_asc','top',0,0,'none',NULL,0,0)
 ,(23,'display_asc','second_level',0,0,'relevant','enough_plus1',0,0)
 ,(31,'display_asc','top',0,0,'all','all',0,0);

DROP TABLE IF EXISTS btPageList;

CREATE TABLE IF NOT EXISTS `btPageList` (
  `bID` int(10) unsigned NOT NULL,
  `num` smallint(5) unsigned NOT NULL,
  `orderBy` varchar(32) default NULL,
  `cParentID` int(10) unsigned NOT NULL default '1',
  `cThis` tinyint(3) unsigned NOT NULL default '0',
  `paginate` tinyint(3) unsigned NOT NULL default '0',
  `displayAliases` tinyint(3) unsigned NOT NULL default '1',
  `ctID` smallint(5) unsigned default NULL,
  `rss` int(11) default '0',
  `rssTitle` varchar(255) default NULL,
  `rssDescription` longtext,
  `truncateSummaries` int(11) default '0',
  `displayFeaturedOnly` int(11) default '0',
  `truncateChars` int(11) default '128',
  PRIMARY KEY  (`bID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO btPageList VALUES(21,99,'display_asc',57,1,0,0,NULL,NULL,NULL,NULL,0,0,0);

DROP TABLE IF EXISTS btRssDisplay;

CREATE TABLE IF NOT EXISTS `btRssDisplay` (
  `bID` int(10) unsigned NOT NULL,
  `title` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `dateFormat` varchar(100) default NULL,
  `itemsToDisplay` int(10) unsigned default '5',
  `showSummary` tinyint(3) unsigned NOT NULL default '1',
  `launchInNewWindow` tinyint(3) unsigned NOT NULL default '1',
  PRIMARY KEY  (`bID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS btSearch;

CREATE TABLE IF NOT EXISTS `btSearch` (
  `bID` int(10) unsigned NOT NULL,
  `title` varchar(255) default NULL,
  `buttonText` varchar(128) default NULL,
  `baseSearchPath` varchar(255) default NULL,
  `resultsURL` varchar(255) default NULL,
  PRIMARY KEY  (`bID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO btSearch VALUES(32,'Search','Go','','')
 ,(33,'Search Your Site','Search','','/search/search-results');

DROP TABLE IF EXISTS btSlideshow;

CREATE TABLE IF NOT EXISTS `btSlideshow` (
  `bID` int(10) unsigned NOT NULL,
  `fsID` int(10) unsigned default NULL,
  `playback` varchar(50) default NULL,
  `duration` int(10) unsigned default NULL,
  `fadeDuration` int(10) unsigned default NULL,
  PRIMARY KEY  (`bID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO btSlideshow VALUES(15,0,'ORDER',NULL,NULL)
 ,(19,0,'ORDER',NULL,NULL);

DROP TABLE IF EXISTS btSlideshowImg;

CREATE TABLE IF NOT EXISTS `btSlideshowImg` (
  `slideshowImgId` int(10) unsigned NOT NULL auto_increment,
  `bID` int(10) unsigned default NULL,
  `fID` int(10) unsigned default NULL,
  `url` varchar(255) default NULL,
  `duration` int(10) unsigned default NULL,
  `fadeDuration` int(10) unsigned default NULL,
  `groupSet` int(10) unsigned default NULL,
  `position` int(10) unsigned default NULL,
  `imgHeight` int(10) unsigned default NULL,
  PRIMARY KEY  (`slideshowImgId`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

INSERT INTO btSlideshowImg VALUES(1,15,1,'',5,2,0,0,192)
 ,(2,15,4,'',5,2,0,1,192)
 ,(3,15,3,'',5,2,0,2,192)
 ,(4,19,1,'',5,2,0,0,192)
 ,(5,19,4,'',5,2,0,1,192)
 ,(6,19,3,'',5,2,0,2,192);

DROP TABLE IF EXISTS btSurvey;

CREATE TABLE IF NOT EXISTS `btSurvey` (
  `bID` int(10) unsigned NOT NULL,
  `question` varchar(255) default '',
  `requiresRegistration` int(11) default '0',
  PRIMARY KEY  (`bID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO btSurvey VALUES(29,'Do you like what you see?',NULL);

DROP TABLE IF EXISTS btSurveyOptions;

CREATE TABLE IF NOT EXISTS `btSurveyOptions` (
  `optionID` int(10) unsigned NOT NULL auto_increment,
  `bID` int(11) default NULL,
  `optionName` varchar(255) default NULL,
  `displayOrder` int(11) default '0',
  PRIMARY KEY  (`optionID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO btSurveyOptions VALUES(1,29,'Yes',0)
 ,(2,29,'Kinda',1)
 ,(3,29,'Not Really',2);

DROP TABLE IF EXISTS btSurveyResults;

CREATE TABLE IF NOT EXISTS `btSurveyResults` (
  `resultID` int(10) unsigned NOT NULL auto_increment,
  `optionID` int(10) unsigned default '0',
  `uID` int(10) unsigned default '0',
  `bID` int(11) default NULL,
  `cID` int(11) default NULL,
  `ipAddress` varchar(128) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`resultID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS btVideo;

CREATE TABLE IF NOT EXISTS `btVideo` (
  `bID` int(10) unsigned NOT NULL,
  `fID` int(10) unsigned default NULL,
  `width` int(10) unsigned default NULL,
  `height` int(10) unsigned default NULL,
  PRIMARY KEY  (`bID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS btYouTube;

CREATE TABLE IF NOT EXISTS `btYouTube` (
  `bID` int(10) unsigned NOT NULL,
  `title` varchar(255) default NULL,
  `videoURL` varchar(255) default NULL,
  PRIMARY KEY  (`bID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO btYouTube VALUES(12,'Basic Editing','http://www.youtube.com/watch?v=oYSOFTNLbKY');

