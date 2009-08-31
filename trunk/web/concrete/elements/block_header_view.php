<?
defined('C5_EXECUTE') or die(_("Access Denied."));

$b = $obj;
if( method_exists($b,'getBlockCollectionObject') ){
	$bc = $b->getBlockCollectionObject(); 
	$blockStyles = BlockStyles::retrieve($b->bID,$bc->cID);
}

if($blockStyles){ 
	//var_dump($blockStyles->getStylesArray());
	echo 'TO DO: block deletion, caching, add ID field.';
	?>
<div class="<?=$blockStyles->getClassName() ?>ccm-block-styles" <?=$blockStyles->getStylesTag()?> >
<? } ?>