<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
require(dirname(__FILE__) . '/../controller.php');

//Permissions Check
if($_GET['bID']) {
	$c = Page::getByID($_GET['cID']);
	$a = Area::get($c, $_GET['arHandle']);
		
	//edit survey mode
	$b = Block::getByID($_GET['bID'],$c, $a);
	
	$controller = new PageListBlockController($b);
	$rssUrl = $controller->getRssUrl($b);
	
	$bp = new Permissions($b);
	if( $bp->canRead() && $controller->rss) {

		$cArray = $controller->getPages();
		$nh = Loader::helper('navigation');

		header('Content-type: text/xml');
		echo "<?xml version=\"1.0\"?>\n";

?>
		<rss version="2.0">
		  <channel>
			<title><?=$controller->rssTitle?></title>
			<link><?=htmlspecialchars($rssUrl)?></link>
			<description><?=$controller->rssDescription?></description> 
<?
		for ($i = 0; $i < count($cArray); $i++ ) {
			$cobj = $cArray[$i]; 
			$title = $cobj->getCollectionName(); ?>
			<item>
			  <title><?=htmlspecialchars($title);?></title>
			  <link>
				<?= BASE_URL.DIR_REL.$nh->getLinkToCollection($cobj) ?>		  
			  </link>
			  <description><?=htmlspecialchars(strip_tags($cobj->getCollectionDescription()))."....";?></description>
			  <pubDate><?=$cobj->getCollectionDateAdded()?></pubDate>
			</item>
		<? } ?>
     		 </channel>
		</rss>
		
<?	} else { 	
		$v = View::getInstance();
		$v->renderError('Permission Denied',"You don't have permission to access this RSS feed");
		exit;
	}
			
} else {
	echo "You don't have permission to access this RSS feed";
}
exit;






