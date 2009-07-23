<?php 
$ih = Loader::helper('concrete/interface'); 
$ci = Loader::helper('concrete/urls');
$u = new User();
?> 
<style>
#ccm-scrapbook-list { margin-top:32px; margin-bottom:32px; } 
#ccm-scrapbook-list .ccm-block-type{border:none 0px}
#ccm-scrapbook-list .ccm-block-type .options { float:right; padding:8px }
#ccm-scrapbook-list .ccm-block-type-inner{ border:1px solid #e1e1e1; background-color:#f6f6f6; padding-left:8px; }
#ccm-scrapbook-list .ccm-block-type-inner .ccm-block-type-inner-icon {width:16px; height:16px; margin-right:8px; float:left; cursor:move}
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
	init:function(){
		this.enableSorting();
	},  
	enableSorting:function(){ 
		$("div#ccm-scrapbook-list").sortable({
			handle: '.handle',
			cursor: 'move',
			opacity: 0.5,
			stop: function() {
				var idslist = $('#ccm-scrapbook-list').sortable('serialize'); 
				idslist=idslist+'&arHandle=<?php echo ($globalScrapbookArea) ? urlencode($globalScrapbookArea->getAreaHandle()) : '' ?>';
				$.post('<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/dashboard/scrapbook_services.php?mode=reorder', idslist, function(r) {
					
				});
			}
		});
	},
	addBlock:function(e){
		<?php  if(!$globalScrapbookArea){ ?>
		return false;
		<?php  }else{ ?>
		var ccm_areaScrapbookObj = new Object();
		ccm_areaScrapbookObj.type = "AREA";	
		ccm_areaScrapbookObj.aID = <?php echo intval($globalScrapbookArea->getAreaID()) ?>;
		ccm_areaScrapbookObj.arHandle = "<?php echo $globalScrapbookArea->getAreaHandle() ?>";	
		ccm_areaScrapbookObj.addOnly = 1;
		ccm_showAreaMenu(ccm_areaScrapbookObj,e); 
		<?php  } ?>
	},
	editBlock:function(bID,w,h){ 
		if(!w) w=550;
		if(!h) h=380; 
		var editBlockURL = '<?php echo REL_DIR_FILES_TOOLS_REQUIRED ?>/edit_block_popup';
		$.fn.dialog.open({
			title: ccmi18n.editBlock,
			href: editBlockURL+'?cID='+CCM_CID+'&bID='+bID+'&arHandle=<?php echo urlencode($scrapbookName)?>&btask=edit',
			width: w,
			modal: false,
			height: h
		});		
	},
	editBlockTemplate:function(bID){ 
		var editBlockURL = '<?php echo REL_DIR_FILES_TOOLS_REQUIRED ?>/edit_block_popup';
		$.fn.dialog.open({
			title: ccmi18n.changeBlockTemplate,
			href: editBlockURL+'?cID='+CCM_CID+'&bID='+bID+'&arHandle=<?php echo urlencode($scrapbookName)?>&btask=template',
			width: 300,
			modal: false,
			height: 100
		});		
	},
	confirmDelete:function(){
		if(!confirm("<?php echo t('Are you sure you want to delete this block?').'\n'.t('(All page instances will also be removed)') ?>")) return false;
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
$(function(){ GlobalScrapbook.init(); }); 
</script>



<?php  if(!$scrapbookName){ ?>

	<h1><span><?php echo t('Choose a Scrapbook')?></span></h1>
	
	<div class="ccm-dashboard-inner"> 
		 
		<table id="availableScrapbooks" border="0" cellspacing="1" class="grid-list" >
			<tr>
				<td class="header">
					<?php echo t('Scrapbook Name')?>
				</td>
				<td class="header">
					<?php echo t('Options')?>
				</td>
			</tr>		
			<tr>
				<td>  
					<a href="<?php echo View::url($cPath,'view','?scrapbookName=userScrapbook' ) ?>">
					<?php echo t("%s's Personal Scrapbook", $u->getUserName()) ?>
					</a>
				</td>
				<td class="options">
					<a href="<?php echo View::url($cPath,'view','?scrapbookName=userScrapbook' ) ?>">View</a> &nbsp; 
				</td>
			</tr>			
			<?php  if(is_array($availableScrapbooks)) 
				foreach($availableScrapbooks as $availableScrapbook){ ?>
			<tr>
				<td>		
					<div id="ccm-scrapbookNameWrap<?php echo $availableScrapbook['arID'] ?>" class="ccm-scrapbookNameWrap">
						<div class="view">
							<a href="<?php echo View::url($cPath,'view','?scrapbookName='.urlencode($availableScrapbook['arHandle']) ) ?>" >
								<?php echo $availableScrapbook['arHandle'] ?>
							</a>&nbsp;
						</div>
						<div class="edit">
							<form method="post" action="<?php echo $this->url($cPath, 'rename_scrapbook' )?>">
								<input name="arID" type="hidden" value="<?php echo intval($availableScrapbook['arID']) ?>" /> 
								<input name="scrapbookName" type="text" value="<?php echo addslashes($availableScrapbook['arHandle']) ?>" />
								<input name="Submit" type="submit" value="<?php echo t('Save')?>" />
								<input onclick="GlobalScrapbook.toggleScrapbookRename(<?php echo intval($availableScrapbook['arID']) ?>)" name="cancel" type="button" value="<?php echo t('Cancel')?>" />
								&nbsp;
							</form>
						</div>
					</div>					
				</td>
				<td class="options">
					<a href="<?php echo View::url($cPath,'view','?scrapbookName='.urlencode($availableScrapbook['arHandle']) ) ?>"><?php echo t('View')?></a> &nbsp;|&nbsp; 
						<a onclick="GlobalScrapbook.toggleScrapbookRename(<?php echo intval($availableScrapbook['arID']) ?>); return false;" href="#"><?php echo t('Rename')?></a> &nbsp;|&nbsp; 
						<a onclick="if(!confirm('<?php echo t('Are you sure you want to permantly delete this scrapbook?')?>')) return false;" 
						   href="<?php echo View::url($cPath,'delete_scrapbook','?arHandle='.urlencode($availableScrapbook['arHandle']) ) ?>"><?php echo t('Delete')?></a>
				</td>
			</tr> 
			<?php  } 
			
			$form = Loader::helper('form'); ?>
			
			<tr>
				<td colspan="2" class="subheader"><?php echo t('Add a Shared Scrapbook')?></td>
			</tr>
			<tr>
			<td colspan="2">
			<form id="addScrapbookForm" method="post" action="<?php echo View::url($cPath,'addScrapbook') ?>">
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td><?php echo $form->label('scrapbookName', t('Scrapbook Name'))?><br/>
			<input name="scrapbookName" id="scrapbookName" class="ccm-input-text" type="text" value="" size="30"  />
			</td>
			<td valign="bottom">
			<?php echo  $ih->button_js( t('Add'), 'GlobalScrapbook.submitAddScrapbookForm()','left'); ?>
			</td>
			</tr>
			</table>
			
			</form>
			</td>
		</tr>
		</table>
		
		
		<div class="ccm-spacer"></div>			
		
	</div>



<?php  }elseif($scrapbookName=='userScrapbook'){ ?>
	
	<h1><span><?php echo t('User Scrapbook')?></span></h1>
	
	<div class="ccm-dashboard-inner">	
	
		<a style="float: right" href="<?php echo View::url($cPath) ?>"><?php echo  t("&laquo; Return to Scrapbook List") ?></a>		

		<div id="ccm-scrapbook-list" class="user-scrapbook ui-sortable">
		<?php   
		$sp = Pile::getDefault();
		$contents = $sp->getPileContentObjects('display_order_date');
		$realPilesCounter=0;
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
							href="<?php echo $this->url( $cPath, 'deleteBlock', '?scrapbookName='.urlencode($scrapbookName).'&pcID='.$pcID ) ?>" 
							id="sb<?php echo $pcID ?>">
							<?php echo t('Delete') ?>
						  </a>
						</div> 
						
						<div class="ccm-block-type-inner">
							<div class="ccm-block-type-inner-icon handle" style="background: url(<?php echo $btIcon?>) no-repeat center left;">
							
							</div>
							<div class="view">
								<a><?php echo $bt->getBlockTypeName()?></a>													
							</div>							
						</div>
						
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
				$realPilesCounter++;
			} 
		}	
		
		if(!$realPilesCounter){
			print t('You have no items in your scrapbook.');
		} 		
		?>
		</div>
		
	
	</div>

<?php  }else{ ?>

	<h1><span><?php echo htmlentities($scrapbookName) ?></span></h1>
	
	<div class="ccm-dashboard-inner"> 

		<a style="float: right" href="<?php echo View::url($cPath) ?>"><?php echo  t("&laquo; Return to Scrapbook List") ?></a>		
		
		<div class="sillyIE7"><?php echo  $ih->button_js( t('Add Block to Scrapbook'), 'GlobalScrapbook.addBlock(event)','left'); ?></div>
		
		<div class="ccm-spacer"></div>	
		
		<div id="ccm-scrapbook-list" class="ui-sortable">			
			<?php  		 			
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
				 <div class="ccm-scrapbook-list-item" id="ccm-scrapbook-list-item-<?php echo intval($b->bID)?>"> 
					 <div class="ccm-block-type">  
						<div class="options"> 
							<a href="javascript:void(0)" onclick="GlobalScrapbook.toggleRename(<?php echo intval($b->bID) ?>)"><?php echo t('Rename')?></a>
							&nbsp;|&nbsp; 
							<a href="javascript:void(0)" onclick="GlobalScrapbook.editBlockTemplate(<?php echo intval($b->bID) ?>)" ><?php echo t('Set Custom Template')?></a> 
							&nbsp;|&nbsp; 
							<a href="javascript:void(0)" onclick="GlobalScrapbook.editBlock(<?php echo intval($b->bID) ?>,<?php echo $bt->getBlockTypeInterfaceWidth()?> , <?php echo $bt->getBlockTypeInterfaceHeight()?> )" ><?php echo t('Edit')?></a> 
							&nbsp;|&nbsp; 					 
							<a href="<?php echo  $this->url($c->getCollectionPath(),'deleteBlock','?scrapbookName='.urlencode($scrapbookName).'&bID='.intval($b->bID))?>" onclick="return GlobalScrapbook.confirmDelete()">
								<?php echo t('Delete')?>
							</a> 
						</div>  
						<div id="ccm-block-type-inner<?php echo intval($b->bID)?>" class="ccm-block-type-inner">
							<div class="ccm-block-type-inner-icon handle" style="background: url(<?php echo $btIcon?>) no-repeat center left;">
							
							</div>
							<div class="view">
								<a onclick="GlobalScrapbook.toggleRename(<?php echo intval($b->bID) ?>)" >
									<?php echo $bt->getBlockTypeName()?>: "<?php echo $b->getBlockName() ?>"
								</a>&nbsp;
							</div>
							<div class="edit">
								<form method="post" action="<?php echo $this->url($c->getCollectionPath(), 'rename_block' )?>">
									<input name="bID" type="hidden" value="<?php echo intval($b->bID) ?>" />
									<input name="scrapbookName" type="hidden" value="<?php echo addslashes($scrapbookName) ?>" />
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

<?php  } ?>