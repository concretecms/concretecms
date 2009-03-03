<style>

#ccm-scrapbook-list .ccm-block-type .options { float:right; padding:8px; }
</style>

<h1><span><?=t('User Scrapbook')?></span></h1>

<div class="ccm-dashboard-inner">


	<div id="ccm-scrapbook-list">
	<? 
	$ci = Loader::helper('concrete/urls');
	
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
			$pcID=$obj->getPileContentID();
			?>			
			<div class="ccm-scrapbook-list-item" id="ccm-pc-<?=$pcID ?>">
				<div class="ccm-block-type">
				 	<div class="options">  					 
					  <a title="Remove from Scrapbook" 
						href="<?=$this->url('/dashboard/scrapbook/user/','delete','?pcID='.$pcID ) ?>" 
						id="sb<?=$pcID ?>">
					  	<img src="<?=ASSETS_URL_IMAGES?>/icons/delete_small.png" width="16" height="16" />
					  </a>
					</div> 
					<a class="ccm-block-type-inner" style="background-image: url(<?=$btIcon?>)"><?=$bt->getBlockTypeName()?></a>
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

</div>