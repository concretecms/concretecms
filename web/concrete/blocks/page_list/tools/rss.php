<?php
defined('C5_EXECUTE') or die("Access Denied.");

//Permissions Check
$nh = Loader::helper('validation/numbers');
if($_GET['bID'] && $_GET['cID'] && $nh->integer($_GET['bID']) && $nh->integer($_GET['cID'])) {
	$c = Page::getByID($_GET['cID']);
	if (is_object($c) && !$c->isError()) { 

		$a = Area::get($c, $_GET['arHandle']);
			
		//edit survey mode
		$b = Block::getByID($_GET['bID'],$c, $a);
		
		$controller = new PageListBlockController($b);
		$rssUrl = $controller->getRssUrl($b);
		
		$bp = new Permissions($b);
		if( $bp->canViewBlock() && $controller->rss) {
	
			$cArray = $controller->getPages();
			$nh = Loader::helper('navigation');
	
			header('Content-type: text/xml');
			echo "<" . "?" . "xml version=\"1.0\"?>\n";
	
	?>
			<rss version="2.0">
			  <channel>
				<title><?=$controller->rssTitle?></title>
				<link><?=BASE_URL.$rssUrl?></link>
				<description><?=$controller->rssDescription?></description> 
	<?
			for ($i = 0; $i < count($cArray); $i++ ) {
				$cobj = $cArray[$i]; 
				$title = $cobj->getCollectionName(); ?>
				<item>
				  <title><?=htmlspecialchars($title);?></title>
				  <link>
					<?= BASE_URL.$nh->getLinkToCollection($cobj) ?>		  
				  </link>
				  <?php
				  if(strlen(htmlspecialchars(strip_tags($cobj->getCollectionDescription()))) != 0) {
					$desc = htmlspecialchars(strip_tags($cobj->getCollectionDescription()))."...";
				  } else {
					$desc = "";
				  }
				  ?>
				  <description><?php echo $desc;?></description>
				  <? /* <pubDate><?=$cobj->getCollectionDatePublic()?></pubDate>
				  Wed, 23 Feb 2005 16:12:56 GMT  */ ?>
				  <pubDate><?=date( 'D, d M Y H:i:s T',strtotime($cobj->getCollectionDatePublic())) ?></pubDate>
				</item>
			<? } ?>
				 </channel>
			</rss>
			
	<?	} else {  	
			$v = View::getInstance();
			$v->renderError(t('Permission Denied'), t("You don't have permission to access this RSS feed"));
			exit;
		}
	}
	
} else {
	echo t("You don't have permission to access this RSS feed");
}
exit;






