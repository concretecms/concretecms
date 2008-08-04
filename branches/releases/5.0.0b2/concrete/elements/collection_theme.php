<?
Loader::model('collection_types');
$tArray = PageTheme::getGlobalList();
$tArray2 = PageTheme::getLocalList();
$tArray = array_merge($tArray, $tArray2);
$ctArray = CollectionType::getList($c->getAllowedSubCollections());

$plID = $c->getCollectionThemeID();
$ctID = $c->getCollectionTypeID();
if ($plID == 0) {
	$pl = PageTheme::getSiteTheme();
	$plID = $pl->getThemeID();
}
?>

<div class="ccm-pane-controls">
<form method="post" name="ccmPermissionsForm" action="<?=$c->getCollectionAction()?>">
<input type="hidden" name="plID" value="<?=$c->getCollectionThemeID()?>" />
<input type="hidden" name="ctID" value="<?=$c->getCollectionTypeID()?>" />

<input type="hidden" name="rel" value="<?=$_REQUEST['rel']?>" />

<h1>Design</h1>

<div class="ccm-form-area">

<h2>Choose a Page Type</h2>

<? if ($c->isGeneratedCollection()) { ?>

This page is a "single page," which means it doesn't have a page type associated with it.

<? } else { ?>


<div class="ccm-scroller" current-page="1" current-pos="0" num-pages="<?=ceil(count($ctArray)/4)?>">
<a href="javascript:void(0)" class="ccm-scroller-l"><img src="<?=ASSETS_URL_IMAGES?>/button_scroller_l.png" width="28" height="79" alt="l" /></a>
<a href="javascript:void(0)" class="ccm-scroller-r"><img src="<?=ASSETS_URL_IMAGES?>/button_scroller_r.png" width="28" height="79" alt="l" /></a>

<div class="ccm-scroller-inner">
<ul id="ccm-select-page-type" style="width: <?=count($ctArray) * 132?>px">
	<? 
	foreach($ctArray as $ct) { ?>
		
		<? $class = ($ct->getCollectionTypeID() == $ctID) ? 'ccm-item-selected' : ''; ?>

		<li class="<?=$class?>"><a href="javascript:void(0)" ccm-page-type-id="<?=$ct->getCollectionTypeID()?>"><img src="<?=REL_DIR_FILES_COLLECTION_TYPE_ICONS?>/<?=$ct->getCollectionTypeIcon()?>" /></a>
		<span><?=$ct->getCollectionTypeName()?></span>
		</li>

	
	<? 
	
	} ?>

</ul>
</div>

</div>

<? } ?>

<h2>Choose a Theme</h2>

<div class="ccm-scroller" current-page="1" current-pos="0" num-pages="<?=ceil(count($tArray)/4)?>">
<a href="javascript:void(0)" class="ccm-scroller-l"><img src="<?=ASSETS_URL_IMAGES?>/button_scroller_l.png" width="28" height="79" alt="l" /></a>
<a href="javascript:void(0)" class="ccm-scroller-r"><img src="<?=ASSETS_URL_IMAGES?>/button_scroller_r.png" width="28" height="79" alt="l" /></a>

<div class="ccm-scroller-inner">
<ul id="ccm-select-theme" style="width: <?=count($tArray) * 132?>px">
<? foreach($tArray as $t) { ?>

	<? $class = ($t->getThemeID() == $plID) ? 'ccm-item-selected' : ''; ?>
	<li class="<?=$class?>"><a href="javascript:void(0)" ccm-theme-id="<?=$t->getThemeID()?>"><?=$t->getThemeThumbnail()?></a>
	<span><?=$t->getThemeName()?></span>

	</li>
<? } ?>
</ul>
</div>
</div>

</div>

<div class="ccm-buttons">
<!--	<a href="javascript:void(0)" onclick="ccm_hidePane()" class="ccm-button-left cancel"><span><em class="ccm-button-close">Cancel</em></span></a>//-->
	<a href="javascript:void(0)" onclick="ccm_submit()" class="ccm-button-right accept"><span>Save</span></a>
</div>	
<input type="hidden" name="update_theme" value="1" class="accept">
<input type="hidden" name="processCollection" value="1">

<script type="text/javascript">
ccm_submit = function() {
	//ccm_showTopbarLoader();
	$('form[name=ccmPermissionsForm]').get(0).submit();
}

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
		
		$(item).css('left', currentPos + 'px');
		
		
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

		$(item).css('left', currentPos + 'px');
		
		
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
		$("input[name=ctID]").val($(this).attr('ccm-page-type-id'));
	});

	$("#ccm-select-theme a").click(function() {
		$("#ccm-select-theme li").each(function() {
			$(this).removeClass('ccm-item-selected');
		});
		$(this).parent().addClass('ccm-item-selected');
		$("input[name=plID]").val($(this).attr('ccm-theme-id'));
	});


});
</script>

<div class="ccm-spacer">&nbsp;</div>
</form>
</div>