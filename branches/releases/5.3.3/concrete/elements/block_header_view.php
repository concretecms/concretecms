<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

$b = $obj; 
if( method_exists($b,'getBlockCollectionObject')  ){
	$bc = $b->getBlockCollectionObject();  
	if(!$b->isGlobal()) $blockStyles = BlockStyles::retrieve($b->bID, $bc );
} 

if($blockStyles){ ?>
	<div id="<?php echo $blockStyles->getCssID(1) ?>" class="<?php echo $blockStyles->getClassName() ?>ccm-block-styles" >
<?php  } ?>