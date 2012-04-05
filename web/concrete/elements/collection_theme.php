<?
defined('C5_EXECUTE') or die("Access Denied.");

Loader::model('collection_types');
$stringHelper=Loader::helper('text');
$tArray = PageTheme::getGlobalList();
$tArray2 = PageTheme::getLocalList();
$tArrayTmp = array_merge($tArray, $tArray2);
$tArray = array();
foreach($tArrayTmp as $pt) {
	if ($cp->canEditPageTheme($pt)) {
		$tArray[] = $pt;
	}
}
$ctArray = CollectionType::getList();

$cp = new Permissions($c);
if ($c->getCollectionID() > 1) {
	$parent = Page::getByID($c->getCollectionParentID());
	$parentCP = new Permissions($parent);
}
if (!$cp->canEditPageType() && !$cp->canEditPageTheme()) {
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

<div class="ccm-ui">
<form method="post" name="ccmThemeForm" action="<?=$c->getCollectionAction()?>">
	<input type="hidden" name="plID" value="<?=$c->getCollectionThemeID()?>" />
	<input type="hidden" name="ctID" value="<?=$c->getCollectionTypeID()?>" />
	<input type="hidden" name="rel" value="<?=$_REQUEST['rel']?>" />


	<? 
	if (!$cp->canEditPageType()) { ?>

		<h3><?=t('Choose a Page Type')?></h3>
		<p>
		<?=t("You do not have access to change this page's type.")?>
		</p>
		<br/><br/>

	<?	
	
	} else if ($c->isMasterCollection()) { ?>
		<h3><?=t('Choose a Page Type')?></h3>
		<p>
		<?=t("This is the defaults page for the %s page type. You cannot change it.", $c->getCollectionTypeName()); ?>
		</p>
		<br/><br/>
	
	<? } else if ($c->isGeneratedCollection()) { ?>
	<h3><?=t('Choose a Page Type')?></h3>
	<p><?=t("This page is a single page, which means it doesn't have a page type associated with it."); ?></p>

	<? } else if ($cnt > 0) { ?>

	<h3><?=t('Choose a Page Type')?></h3>

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
			
					<li class="<?=$class?>"><a href="javascript:void(0)" ccm-page-type-id="<?=$ct->getCollectionTypeID()?>"><?=$ct->getCollectionTypeIconImage();?></a><span><?=$ct->getCollectionTypeName()?></span>
					</li>
				<? } 
				
				}?>
			</ul>
		</div>
	</div>

	<? } ?>
	
	<? if(ENABLE_MARKETPLACE_SUPPORT){ ?>
		<a href="javascript:void(0)" onclick="ccm_openThemeLauncher()" class="btn ccm-button-right success"><?=t("Get More Themes")?></a>
	<? } ?>

	<h3 ><?=t('Themes')?></h3>
	
	<? if ($cp->canEditPageTheme()) { ?>

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
	<? } else { ?>
	
	<p><?=t("You do not have access to change this page's theme."); ?></p>

	<? } ?>
	
	<div class="dialog-buttons">
		<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop()" class="ccm-button-left btn"><?=t('Cancel')?></a>
		<a href="javascript:void(0)" onclick="$('form[name=ccmThemeForm]').submit()" class="ccm-button-right primary btn"><?=t('Save')?></a>
	</div>	
	<input type="hidden" name="update_theme" value="1" class="accept">
	<input type="hidden" name="processCollection" value="1">

	<div class="ccm-spacer">&nbsp;</div>
</form>
</div>
	

<script type="text/javascript">

$(function() {
	ccm_enableDesignScrollers();
	<? if ($_REQUEST['rel'] == 'SITEMAP') { ?>
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
				jQuery.fn.dialog.closeTop();
			}
			ccmAlert.hud(ccmi18n_sitemap.pageDesignMsg, 2000, 'success', ccmi18n_sitemap.pageDesign);
		}
	});

	<? } else { ?>
		$('form[name=ccmThemeForm]').submit(function() {
			jQuery.fn.dialog.showLoader();
		});
	<? } ?>


});
</script>
