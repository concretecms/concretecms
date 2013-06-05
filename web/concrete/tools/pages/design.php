<?
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');
$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
	die(t('Access Denied'));
}

if ($_POST['task'] == 'design_pages') {
	$json['error'] = false;

	if ($_POST['plID'] > 0) {
		$pl = PageTheme::getByID($_POST['plID']);
	}
	if ($_POST['ctID'] > 0) {
		$ct = CollectionType::getByID($_POST['ctID']);
	}
	if (is_array($_POST['cID'])) {
		foreach($_POST['cID'] as $cID) {
			$c = Page::getByID($cID);
			$cp = new Permissions($c);
			if ($cp->canEditPageTheme($pl)) {
				if ($_POST['plID'] > 0) {
					$c->setTheme($pl);
				}
				if ($_POST['ctID'] > 0 && (!$c->isMasterCollection() && !$c->isGeneratedCollection())) {
					$parentC = Page::getByID($c->getCollectionParentID());
					$parentCP = new Permissions($parentC);
					if ($c->getCollectionID() == HOME_CID || $parentCP->canAddSubCollection($ct)) { 
						$data = array('ctID' => $_POST['ctID']);
						$c->update($data);
					}
				}				
			} else {
				$json['error'] = t('Unable to delete one or more pages.');
			}
		}
	}
	
	$js = Loader::helper('json');
	print $js->encode($json);
	exit;
}

$form = Loader::helper('form');

$pages = array();
if (is_array($_REQUEST['cID'])) {
	foreach($_REQUEST['cID'] as $cID) {
		$pages[] = Page::getByID($cID);
	}
} else {
	$pages[] = Page::getByID($_REQUEST['cID']);
}

$pcnt = 0;
$isMasterCollection = false;
$isSinglePage = false;
$tArray = PageTheme::getGlobalList();
$tArray2 = PageTheme::getLocalList();
$tArray = array_merge($tArray, $tArray2);

foreach($pages as $c) { 
	if ($c->isGeneratedCollection()) {
		$isSinglePage = true;
	}
	if ($c->isMasterCollection()) {
		$isMasterCollection = true;
	}
	$cp = new Permissions($c);
	if ($cp->canEditPageTheme() && $cp->canEditPageType()) {
		$pcnt++;
	}
}

if ($pcnt > 0) { 
	// i realize there are a lot of loops through this, but the logic here is a bit tough to follow if you don't do it this way.
	// first we determine which page types to show, if any
	$notAllowedPageTypes = array();
	$allowedPageTypes = array();
	$ctArray = CollectionType::getList();
	foreach($ctArray as $ct) {
		foreach($pages as $c) {
			if ($c->getCollectionID() != HOME_CID) {
				$parentC = Page::getByID($c->getCollectionParentID());
				$parentCP = new Permissions($parentC);
				if (!$parentCP->canAddSubCollection($ct)) {
					$notAllowedPageTypes[] = $ct;
				}
			}
		}
	}
	foreach($ctArray as $ct) {
		if (!in_array($ct, $notAllowedPageTypes)) {
			$allowedPageTypes[] = $ct;
		}
	}
	$cnt = count($allowedPageTypes);	
	// next we determine which page type to select, if any
	$ctID = -1;
	foreach($pages as $c) {
		if ($c->getCollectionTypeID() != $ctID && $ctID != -1) {
			$ctID = 0;
		} else {
			$ctID = $c->getCollectionTypeID();
		}
	}
	// now we determine which theme to select, if any
	$plID = -1;
	foreach($pages as $c) {
		if ($c->getCollectionThemeID() != $plID && $plID != -1) {
			$plID = 0;
		} else {
			$plID = $c->getCollectionThemeID();
		}
	}
}

$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);

?>
<div class="ccm-ui">

<? if ($pcnt == 0) { ?>
	<?=t("You do not have permission to modify the page type or theme on any of the selected pages."); ?>
<? } else { ?>
	<form id="ccm-<?=$searchInstance?>-design-form" method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/design">
	<input type="hidden" name="plID" value="<?=$plID?>" />
	<input type="hidden" name="ctID" value="<?=$ctID?>" />
	<? foreach($pages as $c) { ?>
		<input type="hidden" name="cID[]" value="<?=$c->getCollectionID()?>" />
	<? } ?>
	
	<?=$form->hidden('task', 'design_pages')?>

	<? 
	if ($isMasterCollection) { ?>
		<h3><?=t('Choose a Page Type')?></h3>
	
		<p>
		<?=t("This is the defaults page for the %s page type. You cannot change it.", $c->getCollectionTypeName()); ?>
		</p>
		
	<? } else if ($isSinglePage) { ?>
	<h3><?=t('Choose a Page Type')?></h3>

	<p>
	<?=t("This page is a single page, which means it doesn't have a page type associated with it."); ?>
	</p>

	<? } else if ($cnt > 0) { ?>
	
	<h3><?=t('Choose a Page Type')?></h3>

	<div class="ccm-scroller" current-page="1" current-pos="0" num-pages="<?=ceil($cnt/4)?>">
		<a href="javascript:void(0)" class="ccm-scroller-l"><img src="<?=ASSETS_URL_IMAGES?>/button_scroller_l.png" width="28" height="79" alt="l" /></a>
		<a href="javascript:void(0)" class="ccm-scroller-r"><img src="<?=ASSETS_URL_IMAGES?>/button_scroller_r.png" width="28" height="79" alt="l" /></a>

		<div class="ccm-scroller-inner">
			<ul id="ccm-select-page-type" style="width: <?=$cnt * 132?>px">
				<? 
				foreach($allowedPageTypes as $ct) { ?>		
					<? $class = ($ct->getCollectionTypeID() == $ctID) ? 'ccm-item-selected' : ''; ?>
			
					<li class="<?=$class?>"><a href="javascript:void(0)" ccm-page-type-id="<?=$ct->getCollectionTypeID()?>"><?=$ct->getCollectionTypeIconImage();?></a><span><?=$ct->getCollectionTypeName()?></span>
					</li>
				<?
				}?>
			</ul>
		</div>
	</div>
	<? } ?>
	
	
	<? if(ENABLE_MARKETPLACE_SUPPORT){ ?>
		<a href="javascript:void(0)" class="btn ccm-button-right"><?=t("Get more themes.")?></a>
	<? } ?>

	<h3 ><?=t('Themes')?></h3>

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
	
	
	</form>
	<div class="dialog-buttons">
	<? $ih = Loader::helper('concrete/interface')?>
	<?=$ih->button_js(t('Cancel'), 'jQuery.fn.dialog.closeTop()', 'left', 'btn')?>	
	<?=$ih->button_js(t('Update'), 'ccm_sitemapUpdateDesign(\'' . $searchInstance . '\')', 'right', 'btn primary')?>
	</div>		
		
	<?
	
}
?>
</div>

<script type="text/javascript">
$(function() {
	ccm_enableDesignScrollers();
});
</script>