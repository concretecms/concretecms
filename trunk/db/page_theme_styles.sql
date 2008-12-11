DROP TABLE IF EXISTS `PageThemeStyles`;
CREATE TABLE `PageThemeStyles` (
  `ptID` int(10) unsigned NOT NULL default '0',
  `ptsHandle` varchar(128) NOT NULL default '',
  `ptsValue` varchar(255) default NULL,
  `ptsType` varchar(32) default NULL,
  PRIMARY KEY  (`ptID`,`ptsHandle`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

