<?php
defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getCurrentPage();
if (is_object($c)) {
	$cp = new Permissions($c);
}

/**
 * Handle page title
 */

if (is_object($c)) {
	// We can set a title 3 ways:
	// 1. It comes through programmatically as $pageTitle. If this is the case then we pass it through, no questions asked
	// 2. It comes from meta title
	// 3. It comes from getCollectionName()
	// In the case of 3, we also pass it through page title format.

	if (!isset($pageTitle) || !$pageTitle) {
		// we aren't getting it dynamically.
		$pageTitle = $c->getCollectionAttributeValue('meta_title');
		if (!$pageTitle) {
			$pageTitle = $c->getCollectionName();
			if($c->isSystemPage()) {
				$pageTitle = t($pageTitle);
			}
			$seo = Core::make('helper/seo');
			if (!$seo->hasCustomTitle()) {
				$seo->addTitleSegmentBefore($pageTitle);
			}
			$seo->setSiteName(Config::get('concrete.site'));
			$seo->setTitleFormat(Config::get('concrete.seo.title_format'));
			$seo->setTitleSegmentSeparator(Config::get('concrete.seo.title_segment_separator'));
			$pageTitle = $seo->getTitle();
		}
	}

	if (!isset($pageDescription) || !$pageDescription) {
		// we aren't getting it dynamically.
		$pageDescription = $c->getCollectionAttributeValue('meta_description');
		if (!$pageDescription) {
			$pageDescription = $c->getCollectionDescription();
		}
	}

	$cID = $c->getCollectionID();
	$isEditMode = ($c->isEditMode()) ? "true" : "false";
	$isArrangeMode = ($c->isArrangeMode()) ? "true" : "false";


    if ($c->hasPageThemeCustomizations()) {
        $styleObject = $c->getCustomStyleObject();
    } else {
        $pt = $c->getCollectionThemeObject();
        if (is_object($pt)) {
            $styleObject = $pt->getThemeCustomStyleObject();
        }
    }

    if (is_object($styleObject)) {
        $scc = $styleObject->getCustomCssRecord();
    }

} else {
	$cID = 1;
	if (!isset($pageTitle)) {
	    $pageTitle = null;
	}
}
?>

<meta http-equiv="content-type" content="text/html; charset=<?php echo APP_CHARSET?>" />
<?php
$akk = $c->getCollectionAttributeValue('meta_keywords');
?>
<title><?php echo htmlspecialchars($pageTitle, ENT_COMPAT, APP_CHARSET)?></title>
<meta name="description" content="<?=htmlspecialchars($pageDescription, ENT_COMPAT, APP_CHARSET)?>" />

<? if ($akk) { ?>
<meta name="keywords" content="<?=htmlspecialchars($akk, ENT_COMPAT, APP_CHARSET)?>" />
<?php }
if($c->getCollectionAttributeValue('exclude_search_index')) { ?>
    <meta name="robots" content="noindex" />
<?php } ?>
<?php
if (Config::get('concrete.misc.app_version_display_in_header')) {
    echo '<meta name="generator" content="concrete5 - ' . APP_VERSION . '" />';
}
else {
    echo '<meta name="generator" content="concrete5" />';
}
?>

<?php $u = new User(); ?>
<script type="text/javascript">
<?php
	echo("var CCM_DISPATCHER_FILENAME = '" . DIR_REL . '/' . DISPATCHER_FILENAME . "';\r");
	echo("var CCM_CID = ".($cID?$cID:0).";\r");
	if (isset($isEditMode)) {
		echo("var CCM_EDIT_MODE = {$isEditMode};\r");
	}
	if (isset($isEditMode)) {
		echo("var CCM_ARRANGE_MODE = {$isArrangeMode};\r");
	}
?>
var CCM_IMAGE_PATH = "<?php echo ASSETS_URL_IMAGES?>";
var CCM_TOOLS_PATH = "<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>";
var CCM_APPLICATION_URL = "<?php echo \Core::getApplicationURL()?>";
var CCM_REL = "<?php echo \Core::getApplicationRelativePath()?>";

</script>

<? if (isset($scc) && is_object($scc)) { ?>
    <style type="text/css">
        <? print $scc->getValue();?>
    </style>
<? } ?>

<?php

$v = View::getInstance();

if (Config::get('concrete.user.profiles_enabled') && $u->isRegistered()) {
	$v->requireAsset('core/account');
	$v->addFooterItem('<script type="text/javascript">$(function() { ccm_enableUserProfileMenu(); });</script>');
}

$favIconFID=intval(Config::get('concrete.misc.favicon_fid'));
$appleIconFID =intval(Config::get('concrete.misc.iphone_home_screen_thumbnail_fid'));
$modernIconFID = intval(Config::get('concrete.misc.modern_tile_thumbnail_fid'));
$modernIconBGColor = strval(Config::get('concrete.misc.modern_tile_thumbnail_bgcolor'));

if($favIconFID) {
    $f = File::getByID($favIconFID);
    if (is_object($f)) {
        ?>
        <link rel="shortcut icon" href="<?php echo $f->getURL() ?>" type="image/x-icon"/>
        <link rel="icon" href="<?php echo $f->getURL() ?>" type="image/x-icon"/>
    <?php
    }
}

if($appleIconFID) {
    $f = File::getByID($appleIconFID);
    if (is_object($f)) {
        ?>
        <link rel="apple-touch-icon" href="<?php echo $f->getURL() ?>"/>
    <?php
    }
}

if($modernIconFID) {
	$f = File::getByID($modernIconFID);
    if(is_object($f)) {
        ?>
        <meta name="msapplication-TileImage" content="<?php echo $f->getURL(); ?>" /><?php
        echo "\n";
        if (strlen($modernIconBGColor)) {
            ?>
            <meta name="msapplication-TileColor" content="<?php echo $modernIconBGColor; ?>" /><?php
            echo "\n";
        }
    }
}

if (is_object($cp)) {

	Loader::element('page_controls_header', array('cp' => $cp, 'c' => $c));

	$cih = Loader::helper('concrete/ui');
	if ($cih->showNewsflowOverlay()) {
		$v->addFooterItem('<script type="text/javascript">$(function() { new ConcreteNewsflowDialog().open(); });</script>');
	}

	if (array_get($_COOKIE, 'ccmLoadAddBlockWindow') && $c->isEditMode()) {
		$v->addFooterItem('<script type="text/javascript">$(function() { setTimeout(function() { $("a[data-launch-panel=add-block]").click()}, 100); });</script>', 'CORE');
		setcookie("ccmLoadAddBlockWindow", false, -1, DIR_REL . '/');
	}
}

$v = View::getInstance();
$v->markHeaderAssetPosition();
$_trackingCodePosition = Config::get('concrete.seo.tracking.code_position');
if (empty($disableTrackingCode) && $_trackingCodePosition === 'top') {
	echo Config::get('concrete.seo.tracking.code');
}
echo $c->getCollectionAttributeValue('header_extra_content');
