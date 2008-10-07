drop table if exists PageStatisticsSummary;
drop table if exists DiscussionSummary;

CREATE TABLE `DiscussionSummary` (
  `cID` int(10) unsigned NOT NULL default '0',
  `totalViews` int(10) unsigned NOT NULL default '0',
  `totalTopics` int(10) unsigned NOT NULL default '0',
  `totalPosts` int(10) unsigned NOT NULL default '0',
  `lastPostCID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
