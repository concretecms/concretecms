<?php  
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model("collection_types");

$uh = Loader::helper('concrete/urls'); 
	
$c = Page::getCurrentPage();
//	echo $rssUrl;

$bt->inc('form_setup_html.php', array( 'c'=>$c, 'b'=>$b, 'uh'=>$uh ) ); 
?>