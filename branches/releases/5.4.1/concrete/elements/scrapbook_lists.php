<?php  
Loader::model('pile');
$ci = Loader::helper('concrete/urls');

if(!$scrapbookName || $scrapbookName=='userScrapbook'){ ?>
	<div id="ccm-scrapbook-list">
	<?php 

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
			<div class="ccm-scrapbook-list-item" id="ccm-pc-<?php echo $obj->getPileContentID()?>">
				<div class="ccm-block-type">
					<a class="ccm-scrapbook-delete" title="Remove from Scrapbook" href="javascript:void(0)" id="sb<?php echo $obj->getPileContentID()?>"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/delete_small.png" width="16" height="16" /></a>
					<a class="ccm-block-type-inner" style="background-image: url(<?php echo $btIcon?>)" href="javascript:void(0)" onclick="jQuery.fn.dialog.showLoader();$.get('<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?pcID[]=<?php echo $obj->getPileContentID()?>&add=1&processBlock=1&cID=<?php echo $c->getCollectionID()?>&arHandle=<?php echo $a->getAreaHandle()?>&btask=alias_existing_block&<?php echo $token?>', function(r) { ccm_parseBlockResponse(r, false, 'add'); })"><?php echo $bt->getBlockTypeName()?></a>
					<div class="ccm-scrapbook-list-item-detail">	
						<?php 	
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
			<?php 
			$i++;
		} 
	}	?> 
	</div>
<?php  } ?>

<?php  
if($scrapbookName && $scrapbookName!='userScrapbook'){ 
	$globalScrapbookArea = new Area($scrapbookName); 
	$scrapbookHelper=Loader::helper('concrete/scrapbook'); 
	$globalScrapbookC = $scrapbookHelper->getGlobalScrapbookPage(); 
	$styleHeader = $globalScrapbookC->outputCustomStyleHeaderItems(true); 
	if ($styleHeader) {
		print '<style type="text/css">' . $styleHeader . '</style>';
	}
	$globalScrapbookBlocks = $globalScrapbookArea->getAreaBlocksArray( $globalScrapbookC ); 
	if( !count($globalScrapbookBlocks) ){ ?> 
		<div style="padding:16px 0px;"><?php echo t('No blocks have been added to this scrapbook.')?></div>	
	<?php  }else{ ?>
		<div id="ccm-scrapbook-list">
		<?php  foreach($globalScrapbookBlocks as $b){ 
			$bt = BlockType::getByID( $b->getBlockTypeID() ); 
			$btIcon = $ci->getBlockTypeIconURL($bt);
			?>
			<div class="ccm-scrapbook-list-item" id="ccm-scrapbook-list-item-<?php echo intval($b->bID) ?>"> 
				<div class="ccm-block-type">
					<a class="ccm-scrapbook-delete" title="Remove from Scrapbook" href="javascript:void(0)" arHandle="<?php echo addslashes($scrapbookName)?>" id="scrapbook-bID<?php echo intval($b->bID) ?>"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/delete_small.png" width="16" height="16" /></a>				
					<a class="ccm-block-type-inner" style="background-image: url(<?php echo $btIcon?>)" 
					   href="javascript:void(0)" onclick="jQuery.fn.dialog.showLoader();$.get('<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?globalBlock=1&bID=<?php echo $b->bID ?>&globalScrapbook=<?php echo urlencode($scrapbookName)?>&add=1&processBlock=1&cID=<?php echo $c->getCollectionID()?>&arHandle=<?php echo $a->getAreaHandle()?>&btask=alias_existing_block&<?php echo $token?>', function(r) { ccm_parseBlockResponse(r, false, 'add'); })">
							<?php echo $bt->getBlockTypeName()?>: "<?php echo $b->getBlockName() ?>"
					</a>
					<div class="ccm-scrapbook-list-item-detail">	
						<?php 	
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
		<?php  } ?> 
		</div> 
<?php  	} 
}
?>