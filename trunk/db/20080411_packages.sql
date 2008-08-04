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
