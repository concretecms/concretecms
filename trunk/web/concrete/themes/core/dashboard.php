<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/2000/REC-xhtml1-20000126/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?
$v = View::getInstance();
$v->disableEditing();
require(DIR_FILES_ELEMENTS_CORE . '/header_required.php'); ?>
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm_dashboard.css";</style>
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm_colorpicker.css";</style>
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm_menus.css";</style>
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm_forms.css";</style>
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm_calendar.css";</style>
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm_dialog.css";</style>
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm_asset_library.css";</style>

<script type="text/javascript">
<?
$valt = Loader::helper('validation/token');
print "var CCM_SECURITY_TOKEN = '" . $valt->generate() . "';";
?>

</script>

<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/jquery.form.2.0.2.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/jquery.ui.1.5.2.no_datepicker.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/jquery.ui.1.6b.datepicker.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/jquery.colorpicker.js"></script>

<? if (LANGUAGE != 'en') { ?>
	<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/i18n/ui.datepicker-<?=LANGUAGE?>.js"></script>
<? } ?>

<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/ccm.dialog.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/ccm.base.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/ccm.themes.js"></script>

<script type="text/javascript">
$(function() {
	$("div.message").show('highlight', {
		color: '#ffffff'
	});
});
</script>
</head>
<body>

<div id="ccm-dashboard-page">

<div id="ccm-dashboard-header">
<a href="<?=$this->url('/dashboard/')?>"><img src="<?=ASSETS_URL_IMAGES?>/dashboard/logo.png" height="45" width="48" alt="Concrete5" /></a>
</div>


<div id="ccm-system-nav-wrapper1">
<div id="ccm-system-nav-wrapper2">
<ul id="ccm-system-nav">
<li><a id="ccm-nav-return" href="<?=$this->url('/')?>"><?=t('Return to Website')?></a></li>
<li><a id="ccm-nav-dashboard-help" href="<?=MENU_HELP_URL?>"><?=t('Help')?></a></li>
<li class="ccm-last"><a id="ccm-nav-logout" href="<?=$this->url('/login/', 'logout')?>"><?=t('Sign Out')?></a></li>
</ul>
</div>
</div>

<? 
Loader::block('autonav');
$nh = Loader::helper('navigation');
$dashboard = Page::getByPath("/dashboard");
$nav = AutonavBlockController::getChildPages($dashboard);
?>

<div id="ccm-dashboard-nav">
<ul>
<?
foreach($nav as $n2) { 
	$cp = new Permissions($n2);
	if ($cp->canRead()) { 
		if ($c->getCollectionPath() == $n2->getCollectionPath() || (strpos($c->getCollectionPath(), $n2->getCollectionPath()) == 0) && strpos($c->getCollectionPath(), $n2->getCollectionPath()) !== false) {
			$isActive = true;
		} else {
			$isActive = false;
		}
?>
	<li <? if ($isActive) { ?> class="ccm-nav-active" <? } ?>><a href="<?=$nh->getLinkToCollection($n2, false, true)?>"><?=$n2->getCollectionName()?> <span><?=$n2->getCollectionDescription()?></span></a></li>
<? }

}?>
</ul>
</div>

<? if (isset($subnav)) { ?>

<div id="ccm-dashboard-subnav">
<ul><? foreach($subnav as $item) { ?><li <? if (isset($item[2]) && $item[2] == true) { ?> class="nav-selected" <? } ?>><a href="<?=$item[0]?>"><?=$item[1]?></a></li><? } ?></ul>
<br/><div class="ccm-spacer">&nbsp;</div>
</div>
<? } else if ($c->getCollectionID() != $dashboard->getCollectionID()) {
	// we auto-gen the subnav 
	// if we're right under the dashboard, we get items beneath us. If not we get items at our same level
	$pcs = $nh->getTrailToCollection($c);
	$pcs = array_reverse($pcs);
	if (count($pcs) == 2) {
		$parent = $c;
	} else {
		$parent = $pcs[2];
	}

	$subpages = AutonavBlockController::getChildPages($parent);
	if (count($subpages) > 0) { 
	?>	
		<div id="ccm-dashboard-subnav">
		<ul><? foreach($subpages as $sc) { ?><li <? if ($sc->getCollectionID() == $c->getCollectionID()) { ?> class="nav-selected" <? } ?>><a href="<?=$nh->getLinkToCollection($sc, false, true)?>"><?=$sc->getCollectionName()?></a></li><? } ?></ul>
		<br/><div class="ccm-spacer">&nbsp;</div>
		</div>
	
	
	<?
		}
} ?>

<?
	if (isset($latest_version)){ 
		print Loader::element('dashboard/notification_update', array('latest_version' => $latest_version));
	}
?>

<div id="ccm-dashboard-content">

	<div style="margin:0px; padding:0px; width:100%; ">
	<? if (isset($error)) { ?>
		<? 
		if ($error instanceof Exception) {
			$_error[] = $error->getMessage();
		} else if ($error instanceof ValidationErrorHelper) { 
			$_error = $error->getList();
		} else {
			$_error = $error;
		}
			?>
			<div class="message error">
			<strong><?=t('The following errors occurred when attempting to process your request:')?></strong>
			<ul>
			<? foreach($_error as $e) { ?><li><?=$e?></li><? } ?>
			</ul>
			</div>
		<? 
	}
	
	if (isset($message)) { ?>
		<div class="message success"><?=$message?></div>
	<? } ?>
	
	<?php print $innerContent; ?>
	</div>
	
	<div class="ccm-spacer">&nbsp;</div>

	</div>

</div>

</body>
</html>