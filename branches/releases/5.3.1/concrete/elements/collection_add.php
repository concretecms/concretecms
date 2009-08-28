<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div class="ccm-pane-controls">

<script type="text/javascript">
	function makeAlias(value, formInputID) {
		alias = value.replace(/[&]/gi, "and");
		alias = alias.replace(/[\s|.]+/gi, "_");
		alias = alias.replace(/[^0-9A-Za-z-]/gi, "_");
		//alias = alias.replace(/--/gi, '_');
		alias = alias.replace(/-+/gi, '_');
		alias = alias.toLowerCase();
		
		formObj = document.getElementById(formInputID);
		formObj.value = alias;
	} 	
</script>

<?php  

Loader::model('collection_attributes');
Loader::model('collection_types');
$dh = Loader::helper('date');

$ctArray = CollectionType::getList($c->getAllowedSubCollections());
$cp = new Permissions($c);

$cnt = 0;
for ($i = 0; $i < count($ctArray); $i++) {
	$ct = $ctArray[$i];
	if ($cp->canAddSubCollection($ct)) { 
		$cnt++;
	}
}

	?>
	
	<h1><?php echo t('Add Page')?></h1>

	<form method="post" action="<?php echo $c->getCollectionAction()?>" id="ccmAddPage">		
	<input type="hidden" name="rel" value="<?php echo $_REQUEST['rel']?>" />
	<input type="hidden" name="ctID" value="0" />
	 
	<div class="ccm-form-area">
			
		<div id="ccm-choose-pg-type">
			<div id="ccm-show-page-types" style="float:right; display:none">
				<span id="ccm-selectedPgType" style="padding-right:4px"></span>
				<a onclick="ccmChangePgType(this)">(<?php echo t('Change')?>)</a>
			</div>	
		
			<h2><?php echo t('Choose a Page Type')?></h2>
			
			<div id="ccm-page-type-scroller" class="ccm-scroller" current-page="1" current-pos="0" num-pages="<?php echo ceil($cnt/4)?>">
				<a href="javascript:void(0)" class="ccm-scroller-l"><img src="<?php echo ASSETS_URL_IMAGES?>/button_scroller_l.png" width="28" height="79" alt="l" /></a>
				<a href="javascript:void(0)" class="ccm-scroller-r"><img src="<?php echo ASSETS_URL_IMAGES?>/button_scroller_r.png" width="28" height="79" alt="l" /></a>
				
				<div class="ccm-scroller-inner">
					<ul id="ccm-select-page-type" style="width: <?php echo $cnt * 132?>px">
						<?php  
						foreach($ctArray as $ct) { 
							if ($cp->canAddSubCollection($ct)) { 
							$requiredKeys=array();
							$aks = $ct->getAvailableAttributeKeys();
							foreach($aks as $ak)
								$requiredKeys[] = intval($ak->getCollectionAttributeKeyID());
								
							$usedKeysCombined=array();
							$usedKeys=array();
							$setAttribs = $c->getSetCollectionAttributes();
							foreach($setAttribs as $ak) 
								$usedKeys[] = $ak->getCollectionAttributeKeyID(); 
							$usedKeysCombined = array_merge($requiredKeys, $usedKeys);
							?>
							
							<?php  $class = ($ct->getCollectionTypeID() == $ctID) ? 'ccm-item-selected' : ''; ?>
					
							<li class="<?php echo $class?>"><a href="javascript:void(0)" ccm-page-type-id="<?php echo $ct->getCollectionTypeID()?>"><img src="<?php echo REL_DIR_FILES_COLLECTION_TYPE_ICONS?>/<?php echo $ct->getCollectionTypeIcon()?>" /></a>
							<span id="pgTypeName<?php echo $ct->getCollectionTypeID()?>"><?php echo $ct->getCollectionTypeName()?></span>
							<input id="shownAttributeKeys<?php echo $ct->getCollectionTypeID()?>" name="shownAttributeKeys<?php echo $ct->getCollectionTypeID()?>" type="hidden" value="<?php echo join(',',$usedKeysCombined)?>" />
							<input id="requiredAttributeKeys<?php echo $ct->getCollectionTypeID()?>" name="requiredAttributeKeys<?php echo $ct->getCollectionTypeID()?>" type="hidden" value="<?php echo join(',',$requiredKeys)?>" />
							</li> 
						
						<?php  } 
						
						}?>
					
					</ul>
				</div>
			
			</div>
		</div> 

		<h2><?php echo t('Page Information')?></h2>

		<div class="ccm-field">	
			<div class="ccm-field-one" style="width: 400px">
				<label><?php echo t('Name')?></label> <input type="text" name="cName" value="" class="text" style="width: 100%" onBlur="makeAlias(this.value, 'cHandle')" onkeypress="makeAlias(this.value, 'cHandle')" >
			</div>
			
			<div class="ccm-field-two" style="width: 200px"	>
				<label><?php echo t('Alias')?></label> <input type="text" name="cHandle" style="width: 100%" value="" id="cHandle">
			</div>
		
			<div class="ccm-spacer">&nbsp;</div>
		</div>
		
		<div class="ccm-field">		
			<label><?php echo t('Public Date/Time')?></label> 
			<?php 
			$dt = Loader::helper('form/date_time');
			echo $dt->datetime('cDatePublic' );
			?> 
		</div>		

	
		<div class="ccm-field">
			<label><?php echo t('Description')?></label> <textarea name="cDescription" style="width: 100%; height: 80px"></textarea>
		</div>
		
		<style>
		#ccm-metadata-fields{display:none; }
		</style>
		<?php 
		$nc=new Page();
		Loader::element('collection_metadata_fields', array('c'=>$nc) ); ?>
	
	</div>
	
	

	<div class="ccm-buttons">
	<!--	<a href="javascript:void(0)" onclick="ccm_hidePane()" class="ccm-button-left cancel"><span><em class="ccm-button-close">Cancel</em></span></a>//-->
	<a href="javascript:void(0)" onclick="ccm_doAddSubmit()" class="ccm-button-right accept"><span><?php echo t('Add')?></span></a>
	</div>	
	<input type="hidden" name="add" value="1" />
	<input type="hidden" name="processCollection" value="1">
	<div class="ccm-spacer">&nbsp;</div>
	 
	
</form>
</div>

<?php  $pageTypeMSG = t('You must choose a page type.'); ?>

<script type="text/javascript">
$(function() {
	$("a.ccm-scroller-l").hover(function() {
		$(this).children('img').attr('src', '<?php echo ASSETS_URL_IMAGES?>/button_scroller_l_active.png');
	}, function() {
		$(this).children('img').attr('src', '<?php echo ASSETS_URL_IMAGES?>/button_scroller_l.png');
	});

	$("a.ccm-scroller-r").hover(function() {
		$(this).children('img').attr('src', '<?php echo ASSETS_URL_IMAGES?>/button_scroller_r_active.png');
	}, function() {
		$(this).children('img').attr('src', '<?php echo ASSETS_URL_IMAGES?>/button_scroller_r.png');
	});
	
	var numThumbs = 4;	
	var thumbWidth = 132;
	
	$('a.ccm-scroller-r').click(function() {
		var item = $(this).parent().children('div.ccm-scroller-inner').children('ul');

		var currentPage = $(this).parent().attr('current-page');
		var currentPos = $(this).parent().attr('current-pos');
		var numPages = $(this).parent().attr('num-pages');
		
		var migratePos = numThumbs * thumbWidth;
		currentPos = parseInt(currentPos) - migratePos;
		currentPage++;
		
		$(this).parent().attr('current-page', currentPage);
		$(this).parent().attr('current-pos', currentPos);
		
		if (currentPage == numPages) {
			$(this).hide();
		}
		if (currentPage > 1) {
			$(this).siblings('a.ccm-scroller-l').show();
		}
		/*
		$(item).animate({
			left: currentPos + 'px'
		}, 300);*/
		
		$(item).css('left', currentPos + "px");
		
	});
	
	ccm_testAddSubmit = function() {
		if ($("input[name=ctID]").val() < 1) {
			alert("<?php echo $pageTypeMSG?>");
			return false;
		}
		return true;
	}
	
	$("#ccmAddPage").submit(function() {
		return ccm_testAddSubmit();
	});
	
	ccm_doAddSubmit = function() {
		if (ccm_testAddSubmit()) {
			$("#ccmAddPage").get(0).submit();
		}
	}
	
	$('a.ccm-scroller-l').click(function() {
		var item = $(this).parent().children('div.ccm-scroller-inner').children('ul');
		var currentPage = $(this).parent().attr('current-page');
		var currentPos = $(this).parent().attr('current-pos');
		var numPages = $(this).parent().attr('num-pages');
		
		var migratePos = numThumbs * thumbWidth;
		currentPos = parseInt(currentPos) + migratePos;
		currentPage--;

		$(this).parent().attr('current-page', currentPage);
		$(this).parent().attr('current-pos', currentPos);
		
		if (currentPage == 1) {
			$(this).hide();
		}
		
		if (currentPage < numPages) {
			$(this).siblings('a.ccm-scroller-r').show();
		}
		/*
		$(item).animate({
			left: currentPos + 'px'
		}, 300);*/
		$(item).css('left', currentPos + "px");
		
	});
	$('a.ccm-scroller-l').hide();
	$('a.ccm-scroller-r').each(function() {
		if (parseInt($(this).parent().attr('num-pages')) == 1) {
			$(this).hide();
		}
	});
	
	$("#ccm-select-page-type a").click(function() {
		$("#ccm-select-page-type li").each(function() {
			$(this).removeClass('ccm-item-selected');
		});
		$(this).parent().addClass('ccm-item-selected');
		var ptid=$(this).attr('ccm-page-type-id');
		$("input[name=ctID]").val(ptid);
		
		$('#ccm-page-type-scroller').css('display','none');
		$('#ccm-show-page-types').css('display','block');
		$('#ccm-selectedPgType').html( $('#pgTypeName'+ptid).html() );
		
		$('#ccm-metadata-fields').css('display','block');		
		$('.ccm-field-meta').css('display','none');
		
		//set all attributes as not active
		$('.ccm-meta-field-selected').each(function(i,el){ el.value=0; })
		
		//all shown attributes
		/*
		var shownAttrKeys=$('#shownAttributeKeys'+ptid).val().split(',');		
		for(var i=0;i<shownAttrKeys.length;i++){
			$('#ccm-field-ak'+shownAttrKeys[i]).css('display','block');
			$('#ccm-meta-field-selected'+shownAttrKeys[i]).val(shownAttrKeys[i]);
		}
		*/
		
		//show required attributes
		$('.ccm-meta-close').css('display','block');
		var requiredAttrKeys=$('#requiredAttributeKeys'+ptid).val().split(',');		
		for(var i=0;i<requiredAttrKeys.length;i++){
			$('#ccm-field-ak'+requiredAttrKeys[i]).css('display','block');
			$('#ccm-meta-field-selected'+requiredAttrKeys[i]).val(requiredAttrKeys[i]);		
			$('#ccm-remove-field-ak'+requiredAttrKeys[i]).css('display','none');
		}
		
		//remove all options from the custom attributes select menu
		$("#ccm-meta-custom-fields option").each(function() {
			if(this.value.length>0) $(this).remove();
		});
		
		// add the hidden attribute back to the custom attributes select menu	
		$('.ccm-meta-close').each(function(){ 
			var metaCstmSelect=$("#ccm-meta-custom-fields").get(0); 
			var thisField = $(this).attr('id').substring(19);
			var thisName = $(this).attr('ccm-meta-name'); 
			if($('#ccm-field-ak'+thisField).css('display')=='block') return;
			metaCstmSelect.options[metaCstmSelect.options.length] = new Option(thisName, thisField);
		}); 
		
	});

});

function ccmChangePgType(a){
	$(a.parentNode).css('display','none');
	$('#ccm-page-type-scroller').css('display','block');
	$('#ccm-metadata-fields').css('display','none');
}
</script>