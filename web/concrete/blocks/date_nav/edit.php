<?php 
defined('C5_EXECUTE') or die("Access Denied.");

if ($c->getCollectionID() != $cParentID && (!$cThis) && ($cParentID != 0)) { 
	$isOtherPage = true;
}

global $c;
global $b;

$uh = Loader::helper('concrete/urls');

if($b){
	$bCID = $b->getBlockCollectionID();
	$bID=$b->getBlockID();
}else{
	$bCID=0;
	$bID=0;
}

//include(DIR_FILES_BLOCK_TYPES_CORE.'/page_list/page_list_form.php');

$data=array( 'c'=>$c, 'b'=>$b, 'bID'=>$bID, 'bCID'=>$bCID, 'uh'=>$uh, 'isOtherPage'=>$isOtherPage);
$data['controller']=$controller;
$data = array_merge($controller->getSets(), $data);
$bt->inc('form_setup_html.php', $data ); 
