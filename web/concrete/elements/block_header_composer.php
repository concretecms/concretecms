<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? $bt = BlockType::getByID($b->getBlockTypeID());
$ci = Loader::helper("concrete/urls");
$btIcon = $ci->getBlockTypeIconURL($bt); 			 

?>

<div class="clearfix">
<?
if ($b->getBlockName() != '') { 
	$btName = $b->getBlockName();
} else {
	$btName = $bt->getBlockTypeName();
	
}
?>
<? if ($displayEditLink) { ?>
	<label><a href="javascript:void(0)" onclick="ccm_composerEditBlock(<?=$b->getBlockCollectionID()?>, <?=$b->getBlockID()?>, '<?=$b->getAreaHandle()?>', <?=$bt->getBlockTypeInterfaceWidth()?> , <?=$bt->getBlockTypeInterfaceHeight()?> )" ><?=$btName?></a></label>
<? } else { ?>
	<label><?=$btName?></label>
<? } ?>
<div class="input">
<? Loader::element('block_header', array('b' => $b))?>