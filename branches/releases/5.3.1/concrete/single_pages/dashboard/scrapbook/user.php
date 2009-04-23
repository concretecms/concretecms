<style>

#ccm-scrapbook-list .ccm-block-type .options { float:right; padding:8px; }

#ccm-scrapbook-list .ccm-scrapbook-list-item a.ccm-block-type-inner,
#ccm-scrapbook-list .ccm-scrapbook-list-item a.ccm-block-type-inner:hover{ border:1px solid #e1e1e1; background-color:#f6f6f6; margin-bottom:8px  }

#ccm-scrapbook-list div.ccm-block-type{ border:0px none; }

#ccm-scrapbook-list .ccm-scrapbook-list-item .ccm-scrapbook-list-item-detail{ overflow:hidden } 

</style>

<h1><span><?php echo t('User Scrapbook')?></span></h1>

<div class="ccm-dashboard-inner">


	<div id="ccm-scrapbook-list">
	<?php  
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
			<div class="ccm-scrapbook-list-item" id="ccm-pc-<?php echo $pcID ?>">
				<div class="ccm-block-type">
				 	<div class="options">  					 
					  <a title="Remove from Scrapbook" 
						href="<?php echo $this->url('/dashboard/scrapbook/user/','delete','?pcID='.$pcID ) ?>" 
						id="sb<?php echo $pcID ?>">
					  	<?php echo t('Delete') ?>
					  </a>
					</div> 
					<a class="ccm-block-type-inner" style="background-image: url(<?php echo $btIcon?>)"><?php echo $bt->getBlockTypeName()?></a>
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

</div>