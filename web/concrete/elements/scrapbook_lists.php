<? 
Loader::model('pile');
$ci = Loader::helper('concrete/urls');
$ap = new Permissions($a);
?>
	<div id="ccm-scrapbook-list">
	<?

	$sp = Pile::getDefault();
	$contents = $sp->getPileContentObjects('date_desc');
	if (count($contents) == 0) { 
		print t('You have no items in your Clipboard.');
	}
	foreach($contents as $obj) { 
		$item = $obj->getObject();
		if (is_object($item)) {
			$bt = $item->getBlockTypeObject();
			$btIcon = $ci->getBlockTypeIconURL($bt);
			if (!$ap->canAddBlockToArea($bt)) {
				continue;
			}
			?>			
			<div class="ccm-scrapbook-list-item" id="ccm-pc-<?=$obj->getPileContentID()?>">
				<div class="ccm-block-type">
					<a class="ccm-scrapbook-delete" title="Remove from Clipboard" href="javascript:void(0)" id="sb<?=$obj->getPileContentID()?>"><img src="<?=ASSETS_URL_IMAGES?>/icons/delete_small.png" width="16" height="16" /></a>
					<a class="ccm-block-type-inner" style="background-image: url(<?=$btIcon?>)" href="javascript:void(0)" onclick="var me=this; if(me.disabled)return; me.disabled=true; jQuery.fn.dialog.showLoader();$.get('<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?pcID[]=<?=$obj->getPileContentID()?>&add=1&processBlock=1&cID=<?=$c->getCollectionID()?>&arHandle=<?=$a->getAreaHandle()?>&btask=alias_existing_block&<?=$token?>', function(r) { me.disabled=false; ccm_parseBlockResponse(r, false, 'add'); })"><?=$bt->getBlockTypeName()?></a>
					<div class="ccm-scrapbook-list-item-detail">	
						<?	
						try {
							$bv = new BlockView();
							$bv->render($item, 'scrapbook');
						} catch(Exception $e) {
							print BLOCK_NOT_AVAILABLE_TEXT;
						}	
						?>
					</div>
				</div>
			</div>	
			<?
			$i++;
		} else { ?>
		
		
			<div class="ccm-scrapbook-list-item" id="ccm-pc-<?=$obj->getPileContentID()?>">
				<div class="ccm-block-type">
					<a class="ccm-scrapbook-delete" title="<?php echo t('Remove from Clipboard')?>" href="javascript:void(0)" id="sb<?=$obj->getPileContentID()?>"><img src="<?=ASSETS_URL_IMAGES?>/icons/delete_small.png" width="16" height="16" /></a>
					<div class="ccm-scrapbook-list-item-detail">	
						<?	
						print BLOCK_NOT_AVAILABLE_TEXT;
						?>
					</div>
				</div>
			</div>	

		
		<? } 
	}	?> 
	</div>
