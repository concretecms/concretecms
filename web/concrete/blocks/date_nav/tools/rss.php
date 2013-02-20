<?php 
defined('C5_EXECUTE') or die("Access Denied.");

//Permissions Check
if($_GET['bID']) {
	$c = Page::getByID($_GET['cID']);
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
			<title><?php echo $controller->rssTitle?></title>
			<link><?php echo BASE_URL.DIR_REL.htmlspecialchars($rssUrl)?></link>
			<description><?php echo $controller->rssDescription?></description> 
<?php 
		for ($i = 0; $i < count($cArray); $i++ ) {
			$cobj = $cArray[$i]; 
			$title = $cobj->getCollectionName(); ?>
			<item>
			  <title><?php echo htmlspecialchars($title);?></title>
			  <link>
				<?php echo  BASE_URL.DIR_REL.$nh->getLinkToCollection($cobj) ?>		  
			  </link>
			  <description><?php echo htmlspecialchars(strip_tags($cobj->getCollectionDescription()))."....";?></description>
			  <?php  /* <pubDate><?php echo $cobj->getCollectionDatePublic()?></pubDate>
			  Wed, 23 Feb 2005 16:12:56 GMT  */ ?>
			  <pubDate><?php echo date( 'D, d M Y H:i:s T',strtotime($cobj->getCollectionDatePublic())) ?></pubDate>
			</item>
		<?php  } ?>
     		 </channel>
		</rss>
		
<?php 	} else {  	
		$v = View::getInstance();
		$v->renderError(t('Permission Denied'),t('You don\'t have permission to access this RSS feed'));
		exit;
	}
			
} else {
	echo t('You don\'t have permission to access this RSS feed');
}
exit;






