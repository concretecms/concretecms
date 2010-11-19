<?php 
defined('C5_EXECUTE') or die("Access Denied.");

Loader::model('collection_types');
$stringHelper=Loader::helper('text');
$tArray = PageTheme::getGlobalList();
$tArray2 = PageTheme::getLocalList();
$tArray = array_merge($tArray, $tArray2);
$ctArray = CollectionType::getList($c->getAllowedSubCollections());

$cp = new Permissions($c);
if ($c->getCollectionID() > 1) {
	$parent = Page::getByID($c->getCollectionParentID());
	$parentCP = new Permissions($parent);
}
if (!$cp->canAdminPage()) {
	die(t('Access Denied'));
}

$cnt = 0;
for ($i = 0; $i < count($ctArray); $i++) {
	$ct = $ctArray[$i];
	if ($c->getCollectionID() == 1 || $parentCP->canAddSubCollection($ct)) { 
		$cnt++;
	}
}

$plID = $c->getCollectionThemeID();
$ctID = $c->getCollectionTypeID();
if ($plID == 0) {
	$pl = PageTheme::getSiteTheme();
	$plID = $pl->getThemeID();
}
?>

<style type="text/css">
ul.ccm-area-theme-tabs.ccm-dialog-tabs{height:23px; margin-bottom:16px; padding-right:8px}
ul.ccm-area-theme-tabs.ccm-dialog-tabs li{ float:right; border-right:1px solid #ddd; }

li.themeWrap{text-align:center;white-space:normal}
li.themeWrap img.ccm-preview {float:right; padding-top:2px;}
div.ccm-scroller-inner ul li.themeWrap .preview-wrap img{border:0px none}
li.themeWrap .ccm-theme-name { width:auto; margin:2px 20px; line-height: 14px; font-size: 12px}
li.themeWrap .ccm-theme-name a{text-decoration:none}
li.themeWrap .ccm-theme-name a:hover{ text-decoration:underline} 
ul#ccm-select-marketplace-theme li .desc{ font-size:10px; }
</style>

<script type="text/javascript">
var ccm_themesLoaded = false;

function ccm_updateMoreThemesTab() {
	if (!ccm_themesLoaded) {
        $("#ccm-more-themes-interface-tab").html('<div style="height: 204px">&nbsp;<\/div>');
		jQuery.fn.dialog.showLoader();
		$.ajax({
			url: CCM_TOOLS_PATH + '/marketplace/refresh_theme',
			type: 'POST',
			data: 'cID=<?php echo $c->getCollectionID()?>',
			success: function(html){
				jQuery.fn.dialog.hideLoader();
		        $("#ccm-more-themes-interface-tab").html(html);
				ccm_enable_scrollers();
			},
		});
		ccm_themesLoaded = true;
	}
}

</script>

<div class="ccm-pane-controls">
 
 	<h1><?php echo t('Design')?></h1>

		<form method="post" name="ccmThemeForm" action="<?php echo $c->getCollectionAction()?>">
			<input type="hidden" name="plID" value="<?php echo $c->getCollectionThemeID()?>" />
			<input type="hidden" name="ctID" value="<?php echo $c->getCollectionTypeID()?>" />
			<input type="hidden" name="rel" value="<?php echo $_REQUEST['rel']?>" />
	
			<div class="ccm-form-area">
	
				<?php  if ($c->isMasterCollection()) { ?>
					<h2><?php echo t('Choose a Page Type')?></h2>
				
					<?php echo t("This is the defaults page for the %s page type. You cannot change it.", $c->getCollectionTypeName()); ?>
					<br/><br/>
				
				<?php  } else if ($c->isGeneratedCollection()) { ?>
				<h2><?php echo t('Choose a Page Type')?></h2>

				<?php echo t("This page is a single page, which means it doesn't have a page type associated with it."); ?>
	
				<?php  } else if ($cnt > 0) { ?>

				<h2><?php echo t('Choose a Page Type')?></h2>
	
				<div class="ccm-scroller" current-page="1" current-pos="0" num-pages="<?php echo ceil($cnt/4)?>">
					<a href="javascript:void(0)" class="ccm-scroller-l"><img src="<?php echo ASSETS_URL_IMAGES?>/button_scroller_l.png" width="28" height="79" alt="l" /></a>
					<a href="javascript:void(0)" class="ccm-scroller-r"><img src="<?php echo ASSETS_URL_IMAGES?>/button_scroller_r.png" width="28" height="79" alt="l" /></a>
	
					<div class="ccm-scroller-inner">
						<ul id="ccm-select-page-type" style="width: <?php echo $cnt * 132?>px">
							<?php  
							foreach($ctArray as $ct) { 
								if ($c->getCollectionID() == 1 || $parentCP->canAddSubCollection($ct)) { 
								?>		
								<?php  $class = ($ct->getCollectionTypeID() == $ctID) ? 'ccm-item-selected' : ''; ?>
						
								<li class="<?php echo $class?>"><a href="javascript:void(0)" ccm-page-type-id="<?php echo $ct->getCollectionTypeID()?>"><?php echo $ct->getCollectionTypeIconImage();?></a><span><?php echo $ct->getCollectionTypeName()?></span>
								</li>
							<?php  } 
							
							}?>
						</ul>
					</div>
	
				</div>
	
				<?php  } ?>
				
				
			<?php  if(ENABLE_MARKETPLACE_SUPPORT){ ?>
			<div style="height:1px; overflow: visible; width:100%;">
				<ul style="position:relative; right:0px; top:4px; width:auto" class="ccm-dialog-tabs ccm-area-theme-tabs">
					<li><a href="javascript:void(0)" class="ccm-more-themes-interface" id="ccm-more-themes-interface"><?php echo t('Get More Themes')?></a></li>				
					<li class="ccm-nav-active"><a href="javascript:void(0)" class="ccm-current-themes-interface" id="ccm-current-themes-interface"><?php echo t('Current Themes')?></a></li>
				</ul>	
			</div>
			<?php  } ?>
				
			<div id="ccm-current-themes-interface-tab">
				
				<h2 ><?php echo t('Themes')?></h2>
	
				<div class="ccm-scroller" current-page="1" current-pos="0" num-pages="<?php echo ceil(count($tArray)/4)?>">
					<a href="javascript:void(0)" class="ccm-scroller-l"><img src="<?php echo ASSETS_URL_IMAGES?>/button_scroller_l.png" width="28" height="79" alt="l" /></a>
					<a href="javascript:void(0)" class="ccm-scroller-r"><img src="<?php echo ASSETS_URL_IMAGES?>/button_scroller_r.png" width="28" height="79" alt="l" /></a>
					
					<div class="ccm-scroller-inner">
						<ul id="ccm-select-theme" style="width: <?php echo count($tArray) * 132?>px">
						<?php  foreach($tArray as $t) { ?>
						
							<?php  $class = ($t->getThemeID() == $plID) ? 'ccm-item-selected' : ''; ?>
							<li class="<?php echo $class?> themeWrap">
							
								<a href="javascript:void(0)" ccm-theme-id="<?php echo $t->getThemeID()?>"><?php echo $t->getThemeThumbnail()?></a>
									<?php  if ($t->getThemeID() != $plID) { ?><a title="<?php echo t('Preview')?>" onclick="ccm_previewInternalTheme(<?php echo $c->getCollectionID()?>, <?php echo intval($t->getThemeID())?>,'<?php echo addslashes(str_replace(array("\r","\n",'\n'),'',$t->getThemeName())) ?>')" href="javascript:void(0)" class="preview">
									<img src="<?php echo ASSETS_URL_IMAGES?>/icons/magnifying.png" alt="<?php echo t('Preview')?>" class="ccm-preview" /></a><?php  } ?>
								<div class="ccm-theme-name" ><?php echo $t->getThemeName()?></div>
						
							</li>
						<?php  } ?>
						</ul>
					</div>
				</div>
			</div>
				
				
			<div id="ccm-more-themes-interface-tab" style="display:none">	 
			 
				<h2><?php echo t('Themes')?></h2> 

			</div> 				
				
	
			</div>
	
			<div class="ccm-buttons">
			<!--	<a href="javascript:void(0)" onclick="ccm_hidePane()" class="ccm-button-left cancel"><span><em class="ccm-button-close">Cancel</em></span></a>//-->
				<a href="javascript:void(0)" onclick="$('form[name=ccmThemeForm]').submit()" class="ccm-button-right accept"><span><?php echo t('Save')?></span></a>
			</div>	
			<input type="hidden" name="update_theme" value="1" class="accept">
			<input type="hidden" name="processCollection" value="1">
	
			<div class="ccm-spacer">&nbsp;</div>
		</form>
	
</div>

<script type="text/javascript">

var ccm_areaActiveThemeTab = "ccm-current-themes-interface";
$(".ccm-area-theme-tabs a").click(function() {
	$(".ccm-area-theme-tabs li.ccm-nav-active").removeClass('ccm-nav-active');
	$("#" + ccm_areaActiveThemeTab + "-tab").hide();
	ccm_areaActiveThemeTab = $(this).attr('id'); 
	$('.ccm-area-theme-tabs .'+this.id).parent().addClass("ccm-nav-active");
	$("#" + ccm_areaActiveThemeTab + "-tab").show();
	if (ccm_areaActiveThemeTab == 'ccm-more-themes-interface') {
		ccm_updateMoreThemesTab();	
	}
});

ccm_enable_scrollers = function() {
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


	
	$('a.ccm-scroller-r').unbind();
	$('a.ccm-scroller-l').unbind();
	
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
}

$(function() {
	ccm_enable_scrollers();
	<?php  if ($_REQUEST['rel'] == 'SITEMAP') { ?>
		$("form[name=ccmThemeForm]").ajaxForm({
		type: 'POST',
		iframe: true,
		beforeSubmit: function() {
			jQuery.fn.dialog.showLoader();
		},
		success: function(r) {
			var r = eval('(' + r + ')');
			if (r != null && r.rel == 'SITEMAP') {
				jQuery.fn.dialog.hideLoader();
				jQuery.fn.dialog.closeTop();
				ccmSitemapHighlightPageLabel(r.cID);
			} else {
				ccm_hidePane(function() {
					jQuery.fn.dialog.hideLoader();						
				});
			}
			ccmAlert.hud(ccmi18n_sitemap.pageDesignMsg, 2000, 'success', ccmi18n_sitemap.pageDesign);
		}
	});

	<?php  } else { ?>
		$('form[name=ccmThemeForm]').submit(function() {
			jQuery.fn.dialog.showLoader();
		});
	<?php  } ?>
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
