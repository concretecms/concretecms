<?php  
defined('C5_EXECUTE') or die("Access Denied.");

$uh = Loader::helper('concrete/urls'); 
$c = $cp->getOriginalObject();

$view->inc('form_setup_html.php', array( 'c'=>$c, 'b'=>$b, 'uh'=>$uh, 'controller'=>$controller ) );
