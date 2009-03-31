<?
defined('C5_EXECUTE') or die(_("Access Denied."));

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

<style>
ul.ccm-area-theme-tabs.ccm-dialog-tabs{height:23px; margin-bottom:16px; padding-right:8px}
ul.ccm-area-theme-tabs.ccm-dialog-tabs li{ float:right; border-right:1px solid #ddd; }

li.themeWrap{text-align:center;white-space:normal}
li.themeWrap img.ccm-preview {float:right; padding-top:2px;}
div.ccm-scroller-inner ul li.themeWrap .preview-wrap img{border:0px none}
li.themeWrap .ccm-theme-name { width:auto; margin:0px 20px;}
li.themeWrap .ccm-theme-name a{text-decoration:none}
li.themeWrap .ccm-theme-name a:hover{ text-decoration:underline} 
ul#ccm-select-marketplace-theme li .desc{ font-size:10px; }
</style>

<script type="text/javascript">
var installURL = null;
var isRemotelyLoggedIn = '<?=UserInfo::isRemotelyLoggedIn()?>';
var remoteUID = <?=UserInfo::getRemoteAuthUserId() ?>;
var remoteUName = '<?=UserInfo::getRemoteAuthUserName()?>';

function installPackage() {
   $.ajax({
        url: installURL,
        type: 'POST',
        success: function(html){
            ccmAlert.notice('Marketplace Install', html,
				ccm_hidePane(ccm_showPane(this, CCM_TOOLS_PATH + "/edit_collection_popup.php?ctask=set_theme&cID=" + CCM_CID)));
        },
        error: function (XMLHttpRequest, textStatus, errorThrown){
            ccmAlert.notice('Marketplace Install', ccmi18n.marketplaceErrorMsg);
        }
    });
}
function loginStartInstall(jsObj) {
    remoteUID = jsObj.uID;
    remoteUName = jsObj.uName;
    jQuery.fn.dialog.closeTop();
    ccmAlert.notice('Marketplace Login', ccmi18n.marketplaceLoginSuccessMsg+ccmi18n.marketplaceInstallMsg, installPackage);
}
function loginSuccess() {
    jQuery.fn.dialog.closeTop();
    ccmAlert.notice('Marketplace Login', '<p>You have successfully logged into the concrete5 marketplace.</p>');
}
function updateMoreThemesTab() {
    $("#ccm-more-themes-interface-tab").html(ccmi18n.marketplaceLoadingMsg);
    $.ajax({
        url: '/tools/required/marketplace/refresh_theme',
        type: 'POST',
		data: 'cID=<?=$c->getCollectionID()?>',
        success: function(html){
            $("#ccm-more-themes-interface-tab").html(html);

			ccm_enable_scrollers();
            $(".ccm-button-marketplace-install a").click(function(e){
                installURL = $(this).attr('href');
                e.preventDefault();
                if (!isRemotelyLoggedIn) {
                    ccmPopupLogin.show('', loginStartInstall, '', 1, function() {
                        var plm=$('#ccm-popupLoginIntroMsg');
                        plm.css('display','block');
                        plm.css('margin-top','8px');
                        plm.css('margin-bottom','16px');
                        plm.html(ccmi18n.marketplaceLoginMsg);
                    });
                } else {
                    installPackage();
                }
            });
        },
    });
}

$(document).ready(function(){
    updateMoreThemesTab();
});
</script>

<div class="ccm-pane-controls">
 
 	<h1><?=t('Design')?></h1>

		<form method="post" name="ccmPermissionsForm" action="<?=$c->getCollectionAction()?>">
			<input type="hidden" name="plID" value="<?=$c->getCollectionThemeID()?>" />
			<input type="hidden" name="ctID" value="<?=$c->getCollectionTypeID()?>" />
			<input type="hidden" name="rel" value="<?=$_REQUEST['rel']?>" />
	
			<div class="ccm-form-area">
	
				<? if ($c->isMasterCollection()) { ?>
					<h2><?=t('Choose a Page Type')?></h2>
				
					<?=t("This is the defaults page for the %s page type. You cannot change it.", $c->getCollectionTypeName()); ?>
					<br/><br/>
				
				<? } else if ($c->isGeneratedCollection()) { ?>
				<h2><?=t('Choose a Page Type')?></h2>

				<?=t("This page is a single page, which means it doesn't have a page type associated with it."); ?>
	
				<? } else if ($cnt > 0) { ?>

				<h2><?=t('Choose a Page Type')?></h2>
	
				<div class="ccm-scroller" current-page="1" current-pos="0" num-pages="<?=ceil($cnt/4)?>">
					<a href="javascript:void(0)" class="ccm-scroller-l"><img src="<?=ASSETS_URL_IMAGES?>/button_scroller_l.png" width="28" height="79" alt="l" /></a>
					<a href="javascript:void(0)" class="ccm-scroller-r"><img src="<?=ASSETS_URL_IMAGES?>/button_scroller_r.png" width="28" height="79" alt="l" /></a>
	
					<div class="ccm-scroller-inner">
						<ul id="ccm-select-page-type" style="width: <?=$cnt * 132?>px">
							<? 
							foreach($ctArray as $ct) { 
								if ($c->getCollectionID() == 1 || $parentCP->canAddSubCollection($ct)) { 
								?>		
								<? $class = ($ct->getCollectionTypeID() == $ctID) ? 'ccm-item-selected' : ''; ?>
						
								<li class="<?=$class?>"><a href="javascript:void(0)" ccm-page-type-id="<?=$ct->getCollectionTypeID()?>"><img src="<?=REL_DIR_FILES_COLLECTION_TYPE_ICONS?>/<?=$ct->getCollectionTypeIcon()?>" /></a>
								<span><?=$ct->getCollectionTypeName()?></span>
								</li>
							<? } 
							
							}?>
						</ul>
					</div>
	
				</div>
	
				<? } ?>
				
				
			<? if(ENABLE_MARKETPLACE_SUPPORT){ ?>
			<div style="height:1px; overflow: visible; width:100%;">
				<ul style="position:relative; right:0px; top:4px; width:auto" class="ccm-dialog-tabs ccm-area-theme-tabs">
					<li><a href="javascript:void(0)" class="ccm-more-themes-interface" id="ccm-more-themes-interface"><?=t('Get More Themes')?></a></li>				
					<li class="ccm-nav-active"><a href="javascript:void(0)" class="ccm-current-themes-interface" id="ccm-current-themes-interface"><?=t('Current Themes')?></a></li>
				</ul>	
			</div>
			<? } ?>
				
			<div id="ccm-current-themes-interface-tab">
				
				<h2 ><?=t('Themes')?></h2>
	
				<div class="ccm-scroller" current-page="1" current-pos="0" num-pages="<?=ceil(count($tArray)/4)?>">
					<a href="javascript:void(0)" class="ccm-scroller-l"><img src="<?=ASSETS_URL_IMAGES?>/button_scroller_l.png" width="28" height="79" alt="l" /></a>
					<a href="javascript:void(0)" class="ccm-scroller-r"><img src="<?=ASSETS_URL_IMAGES?>/button_scroller_r.png" width="28" height="79" alt="l" /></a>
					
					<div class="ccm-scroller-inner">
						<ul id="ccm-select-theme" style="width: <?=count($tArray) * 132?>px">
						<? foreach($tArray as $t) { ?>
						
							<? $class = ($t->getThemeID() == $plID) ? 'ccm-item-selected' : ''; ?>
							<li class="<?=$class?> themeWrap">
							
								<a href="javascript:void(0)" ccm-theme-id="<?=$t->getThemeID()?>"><?=$t->getThemeThumbnail()?></a>
									<? if ($t->getThemeID() != $plID) { ?><a title="<?=t('Preview')?>" onclick="ccm_previewInternalTheme(<?=$c->getCollectionID()?>, <?=intval($t->getThemeID())?>,'<?=addslashes(str_replace(array("\r","\n",'\n'),'',$t->getThemeName())) ?>')" href="javascript:void(0)" class="preview">
									<img src="<?=ASSETS_URL_IMAGES?>/icons/magnifying.png" alt="<?=t('Preview')?>" class="ccm-preview" /></a><? } ?>
								<div class="ccm-theme-name" ><?=$t->getThemeName()?></div>
						
							</li>
						<? } ?>
						</ul>
					</div>
				</div>
			</div>
				
				
			<div id="ccm-more-themes-interface-tab" style="display:none">	 
			 
				<h2><?=t('Themes')?></h2> 
				<? /* 			
				<ul class="ccm-dialog-tabs ccm-area-theme-tabs">
					<li><a href="javascript:void(0)" class="ccm-more-themes-interface" id="ccm-more-themes-interface"><?=t('Get More Themes')?></a></li>				
					<li class="ccm-nav-active"><a href="javascript:void(0)" class="ccm-current-themes-interface" id="ccm-current-themes-interface"><?=t('Current Themes')?></a></li>
				</ul>
				*/ ?>	
			
				<p><?=t('Unable to connect to the Concrete5 Marketplace.')?></p>

			</div> 				
				
	
			</div>
	
			<div class="ccm-buttons">
			<!--	<a href="javascript:void(0)" onclick="ccm_hidePane()" class="ccm-button-left cancel"><span><em class="ccm-button-close">Cancel</em></span></a>//-->
				<a href="javascript:void(0)" onclick="ccm_submit()" class="ccm-button-right accept"><span><?=t('Save')?></span></a>
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
});

ccm_submit = function() {
	//ccm_showTopbarLoader();
	$('form[name=ccmPermissionsForm]').get(0).submit();
} 

ccm_enable_scrollers = function() {
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
}

$(function() {
	ccm_enable_scrollers();

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
