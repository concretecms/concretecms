<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-pane-controls">

<script type="text/javascript">
	function makeAlias(value, formInputID) {
		alias = value.replace(/[&]/gi, "and");
		alias = alias.replace(/[\s|.]+/gi, "<?=PAGE_PATH_SEPARATOR?>");
		
		// thanks fernandos
        alias = alias.replace(/[\u00C4\u00E4]/gi, "ae");            // Ää    
        alias = alias.replace(/[\u00D6\u00F6]/gi, "oe");            // Öö    
        alias = alias.replace(/[\u00DF]/gi, "ss");                  // ß    
        alias = alias.replace(/[\u00DC\u00FC]/gi, "ue");            // Üü
        alias = alias.replace(/[\u00C6\u00E6]/gi, "ae");            // Ææ 
        alias = alias.replace(/[\u00D8\u00F8]/gi, "oe");            // ø 
        alias = alias.replace(/[\u00C5\u00E5]/gi, "aa");            // Åå    
        alias = alias.replace(/[\u00E8\u00C8\u00E9\u00C9]/gi, "e"); // éÉèÈ 
		
		alias = alias.replace(/[^0-9A-Za-z]/gi, "<?=PAGE_PATH_SEPARATOR?>");
		alias = alias.replace(/<?=PAGE_PATH_SEPARATOR?>+/gi, '<?=PAGE_PATH_SEPARATOR?>');
		if (alias.charAt(alias.length-1) == '<?=PAGE_PATH_SEPARATOR?>') {
			alias = alias.substring(0,alias.length-1);
		}
		if (alias.charAt(0) == '<?=PAGE_PATH_SEPARATOR?>') {
			alias = alias.substring(1,alias.length);
		}
		alias = alias.toLowerCase();
		
		formObj = document.getElementById(formInputID);
		formObj.value = alias;
	} 	
</script>

<? 

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
	
<div class="ccm-ui">
	<form method="post" action="<?=$c->getCollectionAction()?>" id="ccmAddPage">		
	<input type="hidden" name="rel" value="<?=$_REQUEST['rel']?>" />
	<? // sitemap mode ?>
	<input type="hidden" name="mode" value="<?=$_REQUEST['mode']?>" />
	<input type="hidden" name="ctID" value="0" />
	 
	<div class="ccm-form-area">
			
		<div id="ccm-choose-pg-type">
			<h4 id="ccm-choose-pg-type-title"><?=t('Choose a Page Type')?></h4>
			
			<div id="ccm-page-type-scroller" class="ccm-scroller" current-page="1" current-pos="0" num-pages="<?=ceil($cnt/4)?>">
				<a href="javascript:void(0)" class="ccm-scroller-l"><img src="<?=ASSETS_URL_IMAGES?>/button_scroller_l.png" width="28" height="79" alt="l" /></a>
				<a href="javascript:void(0)" class="ccm-scroller-r"><img src="<?=ASSETS_URL_IMAGES?>/button_scroller_r.png" width="28" height="79" alt="l" /></a>
				
				<div class="ccm-scroller-inner">
					<ul id="ccm-select-page-type" style="width: <?=$cnt * 132?>px">
						<? 
						foreach($ctArray as $ct) { 
							if ($cp->canAddSubCollection($ct)) { 
							$requiredKeys=array();
							$aks = $ct->getAvailableAttributeKeys();
							foreach($aks as $ak)
								$requiredKeys[] = intval($ak->getAttributeKeyID());
								
							$usedKeysCombined=array();
							$usedKeys=array();
							$setAttribs = $c->getSetCollectionAttributes();
							foreach($setAttribs as $ak) 
								$usedKeys[] = $ak->getAttributeKeyID(); 
							$usedKeysCombined = array_merge($requiredKeys, $usedKeys);
							?>
							
							<? $class = ($ct->getCollectionTypeID() == $ctID) ? 'ccm-item-selected' : ''; ?>
					
							<li class="<?=$class?>"><a href="javascript:void(0)" ccm-page-type-id="<?=$ct->getCollectionTypeID()?>"><?= $ct->getCollectionTypeIconImage(); ?></a>
							<span id="pgTypeName<?=$ct->getCollectionTypeID()?>"><?=$ct->getCollectionTypeName()?></span>
							<input id="shownAttributeKeys<?=$ct->getCollectionTypeID()?>" name="shownAttributeKeys<?=$ct->getCollectionTypeID()?>" type="hidden" value="<?=join(',',$usedKeysCombined)?>" />
							<input id="requiredAttributeKeys<?=$ct->getCollectionTypeID()?>" name="requiredAttributeKeys<?=$ct->getCollectionTypeID()?>" type="hidden" value="<?=join(',',$requiredKeys)?>" />
							</li> 
						
						<? } 
						
						}?>
					
					</ul>
				</div>
			
			</div>
		</div> 
		
		<div id="ccm-add-page-information" style="display: none">
		
		<h4><?=t('Page Information')?></h4>
		<? $form = Loader::helper('form'); ?>

		<div id="ccm-show-page-types" style="display: none">
		<div class="clearfix">
			<label><?=t('Page Type')?></label>
			<div class="input">
			<span id="ccm-selectedPgType" style="padding-right:4px" class="label"></span>
			<a href="javascript:void(0)" class="btn" onclick="ccmChangePgType(this)"><?=t('Change')?></a>
			</div>
		</div>	
	

		<div class="clearfix">
			<?=$form->label('cName', t('Name'))?>
			<div class="input"><input type="text" name="cName" value="" class="text span8" onKeyUp="makeAlias(this.value, 'cHandle')" ></div>
		</div>

		
		<div class="clearfix">
			<?=$form->label('cHandle', t('Alias'))?>
			<div class="input"><input type="text" name="cHandle" class="span8" value="" id="cHandle"></div>
		</div>
		
		<div class="clearfix">		
			<?=$form->label('cDatePublic', t('Public Date/Time'))?>
			<div class="input">
			<?
			$dt = Loader::helper('form/date_time');
			echo $dt->datetime('cDatePublic' );
			?> 
			</div>
		</div>		
		
		<div class="clearfix">
			<?=$form->label('cDescription', t('Description'))?>
			<div class="input">
			<textarea name="cDescription" rows="4" class="span8"></textarea>
			</div>
		</div>	
		
		</div>
	
	</div>
	
	

	<div class="dialog-buttons">
		<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop();" class="ccm-button-left btn"><?=t('Cancel')?></a>
		<input type="submit" onclick="$('#ccmAddPage').submit()" class="btn primary ccm-button-right" value="<?=t('Add Page')?>" />
	</div>	

	<input type="hidden" name="add" value="1" />
	<input type="hidden" name="processCollection" value="1">
	<div class="ccm-spacer">&nbsp;</div>
	 
	
</form>
</div>

<? $pageTypeMSG = t('You must choose a page type.'); ?>

<script type="text/javascript">
$(function() {
	$("a.ccm-scroller-l").hover(function() {
		$(this).children('img').attr('src', '<?=ASSETS_URL_IMAGES?>/button_scroller_l_active.png');
	}, function() {
		$(this).children('img').attr('src', '<?=ASSETS_URL_IMAGES?>/button_scroller_l.png');
	});

	$("a.ccm-scroller-r").hover(function() {
		$(this).children('img').attr('src', '<?=ASSETS_URL_IMAGES?>/button_scroller_r_active.png');
	}, function() {
		$(this).children('img').attr('src', '<?=ASSETS_URL_IMAGES?>/button_scroller_r.png');
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
			alert("<?=$pageTypeMSG?>");
			return false;
		}
		return true;
	}
	
	$("#ccmAddPage").submit(function() {
		if (ccm_testAddSubmit()) {
			jQuery.fn.dialog.showLoader();
			return true;
		} else {
			return false;
		}
	});
	
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
		$('#ccm-choose-pg-type-title').hide();
		$("#ccm-select-page-type li").each(function() {
			$(this).removeClass('ccm-item-selected');
		});
		$('#ccm-dialog-content1').dialog('option','height','460');
		$('#ccm-dialog-content1').dialog('option','position','center');

		$(this).parent().addClass('ccm-item-selected');
		var ptid=$(this).attr('ccm-page-type-id');
		$("input[name=ctID]").val(ptid);
		$('#ccm-add-page-information').show();
		
		$('#ccm-page-type-scroller').css('display','none');
		$('#ccm-show-page-types').css('display','block');
		$('#ccm-selectedPgType').html( $('#pgTypeName'+ptid).html() );
		
		$('#ccm-metadata-fields').css('display','block');		
		$('.ccm-field-meta').css('display','none');
		/*
		//set all attributes as not active
		$('.ccm-meta-field-selected').each(function(i,el){ el.value=0; })
		
		//all shown attributes
		
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
		*/
		
	});

});

function ccmChangePgType(a){
	$('#ccm-dialog-content1').dialog('option','height','310');
	$('#ccm-dialog-content1').dialog('option','position','center');
	$('#ccm-choose-pg-type-title').show();
	$('#ccm-add-page-information').hide();
	$('#ccm-page-type-scroller').css('display','block');
	$('#ccm-metadata-fields').css('display','none');
}
</script>