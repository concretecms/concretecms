<?
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

#ccm-scrapbook-list .ccm-scrapbook-list-item .ccm-scrapbook-list-item-detail{ overflow:hidden } 
</style>

<script>
var GlobalScrapbook = {   
	addBlock:function(e){
		var ccm_areaScrapbookObj = new Object();
		ccm_areaScrapbookObj.type = "AREA";	
		ccm_areaScrapbookObj.aID = <?=intval($globalScrapbookArea->getAreaID()) ?>;
		ccm_areaScrapbookObj.arHandle = "<?=$globalScrapbookArea->getAreaHandle() ?>";	
		ccm_areaScrapbookObj.addOnly = 1;
		ccm_showAreaMenu(ccm_areaScrapbookObj,e); 
	},
	editBlock:function(bID,w,h){ 
		if(!w) w=550;
		if(!h) h=380; 
		var editBlockURL = '<?=REL_DIR_FILES_TOOLS_REQUIRED ?>/edit_block_popup.php';
		$.fn.dialog.open({
			title: ccmi18n.editBlock,
			href: editBlockURL+'?cID='+CCM_CID+'&bID='+bID+'&arHandle=Global+Scrapbook&btask=edit#_edit'+bID,
			width: w,
			modal: false,
			height: h
		});		
	},
	confirmDelete:function(){
		if(!confirm("<?=t('Are you sure you want to delete this block?') ?>")) return false;
		return true;
	},
	toggleRename:function(bID){
		$('#ccm-block-type-inner'+bID).toggleClass('editMode'); 
	}
}
</script>


<h1><span><?=t('Global Scrapbook')?></span></h1>

<div class="ccm-dashboard-inner"> 
	
	<?= $ih->button_js( t('Add Block to Scrapbook'), 'GlobalScrapbook.addBlock(event)','left'); ?>
	
	<div class="ccm-spacer"></div>
	
	<div id="ccm-scrapbook-list"> 
		<? 
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
					  <a onclick="GlobalScrapbook.toggleRename(<?=intval($b->bID) ?>)"><?=t('Rename')?></a>
					  &nbsp;|&nbsp; 
					  <a onclick="GlobalScrapbook.editBlock(<?=intval($b->bID) ?>,<?=$bt->getBlockTypeInterfaceWidth()?> , <?=$bt->getBlockTypeInterfaceHeight()?> )" ><?=t('Edit')?></a> 
					  &nbsp;|&nbsp; 					 
					  <a href="<?= $this->url($c->getCollectionPath(),'delete','?bID='.intval($b->bID))?>" onclick="return GlobalScrapbook.confirmDelete()">
					  	<? /* <img src="<?=ASSETS_URL_IMAGES?>/icons/delete_small.png" width="16" height="16" /> */ ?>
						<?=t('Delete')?>
					  </a>
					</div> 
					<div id="ccm-block-type-inner<?=intval($b->bID)?>" class="ccm-block-type-inner" style="background-image: url(<?=$btIcon?>)" >
					  	<div class="view">
							<a onclick="GlobalScrapbook.toggleRename(<?=intval($b->bID) ?>)" >
								<?=$bt->getBlockTypeName()?>: "<?=$b->getBlockName() ?>"
							</a>&nbsp;
						</div>
						<div class="edit">
							<form method="post" action="<?=$this->url($c->getCollectionPath(), 'rename_block' )?>">
								<input name="bID" type="hidden" value="<?=intval($b->bID) ?>" />
								<input name="bName" type="text" value="<?=$b->getBlockName() ?>" />
								<input name="Submit" type="submit" value="<?=t('Save')?>" />
								<input onclick="GlobalScrapbook.toggleRename(<?=intval($b->bID) ?>)" name="cancel" type="button" value="<?=t('Cancel')?>" />
								&nbsp;
							</form>
						</div>
					</div>
					<div class="ccm-scrapbook-list-item-detail">	
						<?= $bv->render($b, 'scrapbook'); ?>
					</div>
				</div>
			</div>
		<? } ?>	
	</div> 	

</div>