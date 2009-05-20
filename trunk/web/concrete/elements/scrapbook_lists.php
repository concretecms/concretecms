<? 
Loader::model('pile');
$ci = Loader::helper('concrete/urls');

if(!$scrapbookName || $scrapbookName=='userScrapbook'){ ?>
	<div id="ccm-scrapbook-list">
	<?

	$sp = Pile::getDefault();
	$contents = $sp->getPileContentObjects('date_desc');
	if (count($contents) == 0) { 
		print t('You have no items in your scrapbook.');
	}
	foreach($contents as $obj) { 
		$item = $obj->getObject();
		if (is_object($item)) {
			$bt = $item->getBlockTypeObject();
			$btIcon = $ci->getBlockTypeIconURL($bt);
			?>			
			<div class="ccm-scrapbook-list-item" id="ccm-pc-<?=$obj->getPileContentID()?>">
				<div class="ccm-block-type">
					<a class="ccm-scrapbook-delete" title="Remove from Scrapbook" href="javascript:void(0)" id="sb<?=$obj->getPileContentID()?>"><img src="<?=ASSETS_URL_IMAGES?>/icons/delete_small.png" width="16" height="16" /></a>
					<a class="ccm-block-type-inner" style="background-image: url(<?=$btIcon?>)" href="<?=DIR_REL?>/index.php?pcID[]=<?=$obj->getPileContentID()?>&add=1&processBlock=1&cID=<?=$c->getCollectionID()?>&arHandle=<?=$a->getAreaHandle()?>&btask=alias_existing_block&<?=$token?>"><?=$bt->getBlockTypeName()?></a>
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
		} 
	}	?> 
	</div>
<? } ?>

<? 
if($scrapbookName && $scrapbookName!='userScrapbook'){ 
	$globalScrapbookArea = new Area($scrapbookName); 
	$scrapbookHelper=Loader::helper('concrete/scrapbook'); 
	$globalScrapbookC = $scrapbookHelper->getGlobalScrapbookPage(); 
	$globalScrapbookBlocks = $globalScrapbookArea->getAreaBlocksArray( $globalScrapbookC ); 
	if( count($globalScrapbookBlocks) ){ ?> 
		<div id="ccm-scrapbook-list">
		<? foreach($globalScrapbookBlocks as $b){ 
			$bt = BlockType::getByID( $b->getBlockTypeID() ); 
			$btIcon = $ci->getBlockTypeIconURL($bt);
			?>
			<div class="ccm-scrapbook-list-item" id=""> 
				<div class="ccm-block-type">
					<a class="ccm-block-type-inner" style="background-image: url(<?=$btIcon?>)" 
					   href="<?=DIR_REL?>/index.php?globalBlock=1&bID=<?=$b->bID ?>&add=1&processBlock=1&cID=<?=$c->getCollectionID()?>&arHandle=<?=$a->getAreaHandle()?>&btask=alias_existing_block&<?=$token?>">
							<?=$bt->getBlockTypeName()?>: "<?=$b->getBlockName() ?>"
					</a>
					<div class="ccm-scrapbook-list-item-detail">	
						<?	
						try {
							$bv = new BlockView();
							$bv->render( $b, 'scrapbook');
						} catch(Exception $e) {
							print BLOCK_NOT_AVAILABLE_TEXT;
						}	
						?>
					</div>
				</div> 
			</div> 
		<? } ?> 
		</div> 
<? 	} 
}
?>