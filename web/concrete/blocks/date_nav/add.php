<?php  
defined('C5_EXECUTE') or die("Access Denied.");

global $c;
global $b;

$uh = Loader::helper('concrete/urls'); 
	
$c = $cp->getOriginalObject();

$bt->inc('form_setup_html.php', array( 'c'=>$c, 'b'=>$b, 'uh'=>$uh, 'controller'=>$controller ) );
