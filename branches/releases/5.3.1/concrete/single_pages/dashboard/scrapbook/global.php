<?php 
$globalScrapbookArea = new Area('Global Scrapbook');
$ih = Loader::helper('concrete/interface'); 
$ci = Loader::helper('concrete/urls');
?> 
<style>
#ccm-scrapbook-list { margin-top:32px; margin-bottom:32px; } 
#ccm-scrapbook-list .ccm-block-type{border:none 0px}
#ccm-scrapbook-list .ccm-block-type .options { float:right; padding:8px }
#ccm-scrapbook-list .ccm-block-type-inner{ border:1px solid #e1e1e1; background-color:#f6f6f6 }
#ccm-scrapbook-list .ccm-scrapbook-list-item-detail{margin:8px 0px}
#ccm-scrapbook-list .ccm-scrapbook-list-item{margin-bottom:16px; border:none;}

#ccm-scrapbook-list .ccm-block-type-inner .edit{ display:none }
#ccm-scrapbook-list .ccm-block-type-inner.editMode .view{ display:none }
#ccm-scrapbook-list .ccm-block-type-inner.editMode .edit{ display:block }
#ccm-scrapbook-list .ccm-block-type-inner a{ cursor:pointer }
#ccm-scrapbook-list .ccm-block-type-inner.editMode .view a{cursor:text}

#ccm-scrapbook-list .ccm-scrapbook-list-item .ccm-scrapbook-list-item-detail{ overflow:hidden } 
</style>

<script>
var GlobalScrapbook = {   
	addBlock:function(e){
		var ccm_areaScrapbookObj = new Object();
		ccm_areaScrapbookObj.type = "AREA";	
		ccm_areaScrapbookObj.aID = <?php echo intval($globalScrapbookArea->getAreaID()) ?>;
		ccm_areaScrapbookObj.arHandle = "<?php echo $globalScrapbookArea->getAreaHandle() ?>";	
		ccm_areaScrapbookObj.addOnly = 1;
		ccm_showAreaMenu(ccm_areaScrapbookObj,e); 
	},
	editBlock:function(bID,w,h){ 
		if(!w) w=550;
		if(!h) h=380; 
		var editBlockURL = '<?php echo REL_DIR_FILES_TOOLS_REQUIRED ?>/edit_block_popup';
		$.fn.dialog.open({
			title: ccmi18n.editBlock,
			href: editBlockURL+'?cID='+CCM_CID+'&bID='+bID+'&arHandle=Global+Scrapbook&btask=edit#_edit'+bID,
			width: w,
			modal: false,
			height: h
		});		
	},
	editBlockTemplate:function(bID){ 
		var editBlockURL = '<?php echo REL_DIR_FILES_TOOLS_REQUIRED ?>/edit_block_popup';
		$.fn.dialog.open({
			title: ccmi18n.changeBlockTemplate,
			href: editBlockURL+'?cID='+CCM_CID+'&bID='+bID+'&arHandle=Global+Scrapbook&btask=template#_edit'+bID,
			width: 300,
			modal: false,
			height: 100
		});		
	},
	confirmDelete:function(){
		if(!confirm("<?php echo t('Are you sure you want to delete this block?') ?>")) return false;
		return true;
	},
	toggleRename:function(bID){
		$('#ccm-block-type-inner'+bID).toggleClass('editMode'); 
	}
}
</script>


<h1><span><?php echo t('Global Scrapbook')?></span></h1>

<div class="ccm-dashboard-inner"> 
	
	<div class="sillyIE7"><?php echo  $ih->button_js( t('Add Block to Scrapbook'), 'GlobalScrapbook.addBlock(event)','left'); ?></div>
	
	<div class="ccm-spacer"></div>	
	
	<div id="ccm-scrapbook-list"> 
		<?php  
		
		//$globalScrapbookArea->display($c);
		$globalScrapbookBlocks = $globalScrapbookArea->getAreaBlocksArray($c); 
		
		if( !count($globalScrapbookBlocks) ){
			echo t('You have no items in your global scrapbook.');
		}else foreach($globalScrapbookBlocks as $b) {
			 $b->setBlockAreaObject($globalScrapbookArea);
			 $bv = new BlockView();
			 $bt = BlockType::getByID( $b->getBlockTypeID() ); 
			 $btIcon = $ci->getBlockTypeIconURL($bt); 			 
			 
			 //give this block a name if it doesn't have one
			 if( !strlen($b->getBlockName()) ){ 
				$b->updateBlockName( 'Global Block '.intval($b->bID) );
			 }
			 ?>
			 <div class="ccm-scrapbook-list-item"> 
				 <div class="ccm-block-type">  
				 	<div class="options"> 
					  <a href="javascript:void(0)" onclick="GlobalScrapbook.toggleRename(<?php echo intval($b->bID) ?>)"><?php echo t('Rename')?></a>
					  &nbsp;|&nbsp; 
					  <a href="javascript:void(0)" onclick="GlobalScrapbook.editBlockTemplate(<?php echo intval($b->bID) ?>)" ><?php echo t('Set Custom Template')?></a> 
					  &nbsp;|&nbsp; 
					  <a href="javascript:void(0)" onclick="GlobalScrapbook.editBlock(<?php echo intval($b->bID) ?>,<?php echo $bt->getBlockTypeInterfaceWidth()?> , <?php echo $bt->getBlockTypeInterfaceHeight()?> )" ><?php echo t('Edit')?></a> 
					  &nbsp;|&nbsp; 					 
					  <a href="<?php echo  $this->url($c->getCollectionPath(),'delete','?bID='.intval($b->bID))?>" onclick="return GlobalScrapbook.confirmDelete()">
					  	<?php echo t('Delete')?>
					  </a>
					</div> 
					<div id="ccm-block-type-inner<?php echo intval($b->bID)?>" class="ccm-block-type-inner" style="background-image: url(<?php echo $btIcon?>)" >
					  	<div class="view">
							<a onclick="GlobalScrapbook.toggleRename(<?php echo intval($b->bID) ?>)" >
								<?php echo $bt->getBlockTypeName()?>: "<?php echo $b->getBlockName() ?>"
							</a>&nbsp;
						</div>
						<div class="edit">
							<form method="post" action="<?php echo $this->url($c->getCollectionPath(), 'rename_block' )?>">
								<input name="bID" type="hidden" value="<?php echo intval($b->bID) ?>" />
								<input name="bName" type="text" value="<?php echo $b->getBlockName() ?>" />
								<input name="Submit" type="submit" value="<?php echo t('Save')?>" />
								<input onclick="GlobalScrapbook.toggleRename(<?php echo intval($b->bID) ?>)" name="cancel" type="button" value="<?php echo t('Cancel')?>" />
								&nbsp;
							</form>
						</div>
					</div>
					<div class="ccm-scrapbook-list-item-detail">	
						<?php echo  $bv->render($b, 'scrapbook'); ?>
					</div>
				</div>
			</div>
		<?php  } ?>	
		
	</div> 	

</div>