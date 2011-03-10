<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? $bt = BlockType::getByID($b->getBlockTypeID());
$ci = Loader::helper("concrete/urls");
$btIcon = $ci->getBlockTypeIconURL($bt); 			 

?>

 <div class="ccm-composer-list-item" id="ccm-composer-list-item-<?=intval($b->bID)?>"> 
	 <div class="ccm-block-type">  
	 	<? if ($displayEditLink) { ?>
		<div class="options"> 
			<a href="javascript:void(0)" onclick="ccm_composerEditBlock(<?=$b->getBlockCollectionID()?>, <?=$b->getBlockID()?>, '<?=$b->getAreaHandle()?>', <?=$bt->getBlockTypeInterfaceWidth()?> , <?=$bt->getBlockTypeInterfaceHeight()?> )" ><?=t('Edit')?></a> 
		</div>  
		<? } ?>
		<div class="ccm-block-type-inner">
			<div class="ccm-block-type-inner-icon ccm-scrapbook-item-handle" style="background: url(<?=$btIcon?>) no-repeat center left;">
			<img src="<?=ASSETS_URL_IMAGES?>/spacer.gif" width="16" height="16" />
			</div>
			<?
			if ($b->getBlockName() != '') { 
				$btName = $b->getBlockName();
			} else {
				$btName = $bt->getBlockTypeName();
				
			}
			?>
			<div class="view"><?=$btName?></div>
		</div>
		<div class="ccm-composer-block-detail">
		<? Loader::element('block_header', array('b' => $b))?>

