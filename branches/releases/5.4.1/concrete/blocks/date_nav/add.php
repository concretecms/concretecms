<?php   
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model("collection_types");

global $c;
global $b;

$uh = Loader::helper('concrete/urls'); 
	
$c = $cp->getOriginalObject();
//	echo $rssUrl;

$bt->inc('form_setup_html.php', array( 'c'=>$c, 'b'=>$b, 'uh'=>$uh ) ); 
?>