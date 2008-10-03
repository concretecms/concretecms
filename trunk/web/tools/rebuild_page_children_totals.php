<?

$db = Loader::db();
Loader::model('page_statistics');

// set all cTotalChildren records to zero
$db->Execute("update PageStatisticsSummary set cTotalChildren = 0");


// retrieve all the pages that DON'T have any children
$r = $db->Execute("select cID from Pages where cIsTemplate = 0");
while ($row = $r->fetchRow()) {

	// foreach one of these, we grab all the pages, and increment the parent totals by one
	PageStatistics::incrementViewsForParents($row['cID']);
	
}