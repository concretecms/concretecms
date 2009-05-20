<?
$ih = Loader::helper('concrete/interface'); 
$ci = Loader::helper('concrete/urls');
$u = new User();
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

#availableScrapbooks { width:100%; margin-bottom:16px; }
#availableScrapbooks .options { text-align:left; white-space:nowrap; width:18% }

#addScrapbookForm #fieldsWrap{ display:none }
#addScrapbookForm #enableButton{ display:block }
#addScrapbookForm.editMode #fieldsWrap{ display:block }
#addScrapbookForm.editMode #enableButton{ display:none }
#addScrapbookForm.editMode #fieldsWrap input.faint{ color:#999 }

#ccm-scrapbook-list.user-scrapbook .ccm-scrapbook-list-item a.ccm-block-type-inner,
#ccm-scrapbook-list.user-scrapbook .ccm-scrapbook-list-item a.ccm-block-type-inner:hover{ border:1px solid #e1e1e1; background-color:#f6f6f6; margin-bottom:8px  }

.ccm-scrapbookNameWrap .view { display:block }
.ccm-scrapbookNameWrap .edit { display:none }
.ccm-scrapbookNameWrap.editMode .view { display:none }
.ccm-scrapbookNameWrap.editMode .edit { display:block }
</style> 

<script>
var GlobalScrapbook = {   
	addBlock:function(e){
		<? if(!$globalScrapbookArea){ ?>
		return false;
		<? }else{ ?>
		var ccm_areaScrapbookObj = new Object();
		ccm_areaScrapbookObj.type = "AREA";	
		ccm_areaScrapbookObj.aID = <?=intval($globalScrapbookArea->getAreaID()) ?>;
		ccm_areaScrapbookObj.arHandle = "<?=$globalScrapbookArea->getAreaHandle() ?>";	
		ccm_areaScrapbookObj.addOnly = 1;
		ccm_showAreaMenu(ccm_areaScrapbookObj,e); 
		<? } ?>
	},
	editBlock:function(bID,w,h){ 
		if(!w) w=550;
		if(!h) h=380; 
		var editBlockURL = '<?=REL_DIR_FILES_TOOLS_REQUIRED ?>/edit_block_popup';
		$.fn.dialog.open({
			title: ccmi18n.editBlock,
			href: editBlockURL+'?cID='+CCM_CID+'&bID='+bID+'&arHandle='+encodeURIComponent("<?=$scrapbookName?>")+'&btask=edit#_edit'+bID,
			width: w,
			modal: false,
			height: h
		});		
	},
	editBlockTemplate:function(bID){ 
		var editBlockURL = '<?=REL_DIR_FILES_TOOLS_REQUIRED ?>/edit_block_popup';
		$.fn.dialog.open({
			title: ccmi18n.changeBlockTemplate,
			href: editBlockURL+'?cID='+CCM_CID+'&bID='+bID+'&arHandle='+encodeURIComponent("<?=$scrapbookName?>")+'&btask=template#_edit'+bID,
			width: 300,
			modal: false,
			height: 100
		});		
	},
	confirmDelete:function(){
		if(!confirm("<?=t('Are you sure you want to delete this block?') ?>")) return false;
		return true;
	},
	toggleRename:function(bID){
		$('#ccm-block-type-inner'+bID).toggleClass('editMode'); 
	},
	toggleScrapbookRename:function(arID){
		$('#ccm-scrapbookNameWrap'+arID).toggleClass('editMode'); 
	},	
	clrInitTxt:function(field,initText,removeClass,blurred){
		if(blurred && field.value==''){
			field.value=initText;
			$(field).addClass(removeClass);
			return;	
		}
		if(field.value==initText) field.value='';
		if($(field).hasClass(removeClass)) $(field).removeClass(removeClass);
	},
	toggleAddScrapbook:function(){
		$('#addScrapbookForm').toggleClass('editMode');
	},
	submitAddScrapbookForm:function(){
		$('#addScrapbookForm').submit();
	}
}
</script>



<? if(!$scrapbookName){ ?>

	<h1><span><?=t('Choose a Scrapbook')?></span></h1>
	
	<div class="ccm-dashboard-inner"> 
		 
		<table id="availableScrapbooks" class="grid-list" >
			<tr>
				<td class="subheader">
					Scrapbook Name
				</td>
				<td class="subheader">
					Options
				</td>
			</tr>		
			<tr>
				<td>  
					<a href="<?=View::url($cPath,'view','?scrapbookName=userScrapbook' ) ?>">
					<?=ucfirst($u->getUserName()) ?><?=t("'s Scrapbook") ?>
					</a>
				</td>
				<td class="options">
					<a href="<?=View::url($cPath,'view','?scrapbookName=userScrapbook' ) ?>">View</a> &nbsp; 
				</td>
			</tr>			
			<? if(is_array($availableScrapbooks)) 
				foreach($availableScrapbooks as $availableScrapbook){ ?>
			<tr>
				<td>					
					<div id="ccm-scrapbookNameWrap<?=$availableScrapbook['arID'] ?>" class="ccm-scrapbookNameWrap">
						<div class="view">
							<a href="<?=View::url($cPath,'view','?scrapbookName='.urlencode($availableScrapbook['arHandle']) ) ?>" >
								<?=$availableScrapbook['arHandle'] ?>
							</a>&nbsp;
						</div>
						
						<? if($availableScrapbook['arHandle'] != t('Global Scrapbook')){ ?> 
						<div class="edit">
							<form method="post" action="<?=$this->url($cPath, 'rename_scrapbook' )?>">
								<input name="arID" type="hidden" value="<?=intval($availableScrapbook['arID']) ?>" /> 
								<input name="scrapbookName" type="text" value="<?=addslashes($availableScrapbook['arHandle']) ?>" />
								<input name="Submit" type="submit" value="<?=t('Save')?>" />
								<input onclick="GlobalScrapbook.toggleScrapbookRename(<?=intval($availableScrapbook['arID']) ?>)" name="cancel" type="button" value="<?=t('Cancel')?>" />
								&nbsp;
							</form>
						</div>
						<? } ?>	
					</div>					
				</td>
				<td class="options">
					<a href="<?=View::url($cPath,'view','?scrapbookName='.urlencode($availableScrapbook['arHandle']) ) ?>">View</a>
					<? if( $availableScrapbook['arHandle'] != t('Global Scrapbook') ){ ?>
						 &nbsp;|&nbsp; 
						<a onclick="GlobalScrapbook.toggleScrapbookRename(<?=intval($availableScrapbook['arID']) ?>); return false;" href="#">Rename</a> &nbsp;|&nbsp; 
						<a onclick="if(!confirm('<?=t('Are you sure you want to permantly delete this scrapbook?')?>')) return false;" 
						   href="<?=View::url($cPath,'delete_scrapbook','?arHandle='.urlencode($availableScrapbook['arHandle']) ) ?>">Delete</a>
					<? } ?>
				</td>
			</tr> 
			<? } ?>
		</table>
		
		<form id="addScrapbookForm" method="post" action="<?=View::url($cPath,'addScrapbook') ?>">
			<div id="fieldsWrap"> 
				<? $defaultNewScrapbookName = t("Type your scrapbook name"); ?>
				<div style="float:left; padding:8px 12px 0px 0px">
				<input name="scrapbookName" type="text" value="<?=$defaultNewScrapbookName?>" size="30" class="faint" 
				  onfocus="GlobalScrapbook.clrInitTxt(this,'<?=$defaultNewScrapbookName?>','faint',0)" 
				  onblur="GlobalScrapbook.clrInitTxt(this,'<?=$defaultNewScrapbookName?>','faint',1)" />
				</div>
				<?= $ih->button_js( t('Add'), 'GlobalScrapbook.submitAddScrapbookForm()','left'); ?>
				<?= $ih->button_js( t('Cancel'), 'GlobalScrapbook.toggleAddScrapbook()','left'); ?>
			</div>
			<div id="enableButton" class="sillyIE7">
				<?= $ih->button_js( t('Add New Scrapbook'), 'GlobalScrapbook.toggleAddScrapbook()','left'); ?>
			</div>
		</form> 
		
		<div class="ccm-spacer"></div>			
		
	</div>



<? }elseif($scrapbookName=='userScrapbook'){ ?>
	
	<h1><span><?=t('User Scrapbook')?></span></h1>
	
	<div class="ccm-dashboard-inner">	
	
		<div id="ccm-scrapbook-list" class="user-scrapbook">
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
				$pcID=$obj->getPileContentID();
				?>			
				<div class="ccm-scrapbook-list-item" id="ccm-pc-<?=$pcID ?>">
					<div class="ccm-block-type">
						<div class="options">  					 
						  <a title="Remove from Scrapbook" 
							href="<?=$this->url( $cPath, 'deleteBlock', '?scrapbookName='.urlencode($scrapbookName).'&pcID='.$pcID ) ?>" 
							id="sb<?=$pcID ?>">
							<?=t('Delete') ?>
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
		
		<a href="<?=View::url($cPath) ?>"><?= t("&laquo; Return to Scrapbook List") ?></a>		
	
	</div>

<? }else{ ?>

	<h1><span><?=htmlentities($scrapbookName) ?></span></h1>
	
	<div class="ccm-dashboard-inner"> 
		
		<div class="sillyIE7"><?= $ih->button_js( t('Add Block to Scrapbook'), 'GlobalScrapbook.addBlock(event)','left'); ?></div>
		
		<div class="ccm-spacer"></div>	
		
		<div id="ccm-scrapbook-list"> 
			
			<? 		 			
			if( !count($globalScrapbookBlocks) ){
				echo t('You have no items in this scrapbook.');
			}else foreach($globalScrapbookBlocks as $b) {
				 $b->setBlockAreaObject($globalScrapbookArea);
				 $bv = new BlockView();
				 $bt = BlockType::getByID( $b->getBlockTypeID() ); 
				 $btIcon = $ci->getBlockTypeIconURL($bt); 			 
				 
				 //give this block a name if it doesn't have one
				 if( !strlen($b->getBlockName()) ){ 
					$b->updateBlockName( $scrapbookName.' '.intval($b->bID) );
				 }
				 ?>
				 <div class="ccm-scrapbook-list-item"> 
					 <div class="ccm-block-type">  
						<div class="options"> 
							<a href="javascript:void(0)" onclick="GlobalScrapbook.toggleRename(<?=intval($b->bID) ?>)"><?=t('Rename')?></a>
							&nbsp;|&nbsp; 
							<a href="javascript:void(0)" onclick="GlobalScrapbook.editBlockTemplate(<?=intval($b->bID) ?>)" ><?=t('Set Custom Template')?></a> 
							&nbsp;|&nbsp; 
							<a href="javascript:void(0)" onclick="GlobalScrapbook.editBlock(<?=intval($b->bID) ?>,<?=$bt->getBlockTypeInterfaceWidth()?> , <?=$bt->getBlockTypeInterfaceHeight()?> )" ><?=t('Edit')?></a> 
							&nbsp;|&nbsp; 					 
							<a href="<?= $this->url($c->getCollectionPath(),'deleteBlock','?scrapbookName='.urlencode($scrapbookName).'&bID='.intval($b->bID))?>" onclick="return GlobalScrapbook.confirmDelete()">
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
									<input name="scrapbookName" type="hidden" value="<?=addslashes($scrapbookName) ?>" />
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
		
		<a href="<?=View::url($cPath) ?>"><?= t("&laquo; Return to Scrapbook List") ?></a>
	
	</div>

<? } ?>