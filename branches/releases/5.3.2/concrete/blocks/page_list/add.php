<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model("collection_types");

global $c;
global $b;

$uh = Loader::helper('concrete/urls');
$rssUrl = $uh->getBlockTypeToolsURL($bt);
	
$c = $cp->getOriginalObject();
//	echo $rssUrl;

include(DIR_FILES_BLOCK_TYPES_CORE.'/page_list/page_list_form.php');

?>