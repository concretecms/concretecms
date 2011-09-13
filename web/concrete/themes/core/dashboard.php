<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<? 
Loader::block('autonav');
$nh = Loader::helper('navigation');
$dashboard = Page::getByPath("/dashboard");
$nav = AutonavBlockController::getChildPages($dashboard);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<?
$html = Loader::helper('html');
$v = View::getInstance();
$v->disableEditing();

// Required JavaScript

$v->addHeaderItem($html->javascript('jquery.js'));
$v->addHeaderItem($html->javascript('jquery.ui.js'));
$v->addHeaderItem($html->javascript('ccm.dialog.js'));
$v->addHeaderItem($html->javascript('ccm.base.js'));
$v->addHeaderItem('<script type="text/javascript" src="' . REL_DIR_FILES_TOOLS_REQUIRED . '/i18n_js"></script>'); 

$v->addHeaderItem($html->javascript('jquery.rating.js'));
$v->addHeaderItem($html->javascript('jquery.form.js'));
$v->addHeaderItem($html->javascript('ccm.ui.js'));
$v->addHeaderItem($html->javascript('quicksilver.js'));
$v->addHeaderItem($html->javascript('jquery.liveupdate.js'));
$v->addHeaderItem($html->javascript('ccm.search.js'));
$v->addHeaderItem($html->javascript('ccm.filemanager.js'));
$v->addHeaderItem($html->javascript('ccm.themes.js'));
$v->addHeaderItem($html->javascript('jquery.ui.js'));
$v->addHeaderItem($html->javascript('jquery.colorpicker.js'));
$v->addHeaderItem($html->javascript('tiny_mce/tiny_mce.js'));

if (LANGUAGE != 'en') {
	$v->addHeaderItem($html->javascript('i18n/ui.datepicker-'.LANGUAGE.'.js'));
}

// Require CSS
$v->addHeaderItem($html->css('ccm.twitter.bootstrap.css'));
$v->addHeaderItem($html->css('ccm.dashboard.css'));
$v->addHeaderItem($html->css('ccm.colorpicker.css'));
$v->addHeaderItem($html->css('ccm.menus.css'));
$v->addHeaderItem($html->css('ccm.forms.css'));
$v->addHeaderItem($html->css('ccm.search.css'));
$v->addHeaderItem($html->css('ccm.filemanager.css'));
$v->addHeaderItem($html->css('ccm.dialog.css'));
$v->addHeaderItem($html->css('jquery.rating.css'));
$v->addHeaderItem($html->css('jquery.ui.css'));

$valt = Loader::helper('validation/token');
$disp = '<script type="text/javascript">'."\n";
$disp .=  "var CCM_SECURITY_TOKEN = '" . $valt->generate() . "';"."\n";
$disp .= '$(function() {'."\n";
$disp .= '	$("div.message").animate({'."\n";
$disp .= "		backgroundColor: 'white'"."\n";
$disp .= "	}, 'fast').animate({"."\n";
$disp .= "		backgroundColor: '#eeeeee'"."\n";
$disp .= "	}, 'fast');"."\n";
$disp .= "	ccm_setupDashboardHeaderMenu();"."\n";
 if ($dashboard->getCollectionID() == $c->getCollectionID()) {
		$disp .= "ccm_dashboardRequestRemoteInformation();"."\n";
	}
$disp .= "	});"."\n";
$disp .= "</script>"."\n";
//require(DIR_FILES_ELEMENTS_CORE . '/header_required.php'); 
$v->addHeaderItem($disp);

Loader::element('header_required');
?>
</head>
<body>

<div id="ccm-dashboard-page">

<div id="ccm-dashboard-header">
<a href="<?=$this->url('/dashboard/')?>"><img src="<?=ASSETS_URL_IMAGES?>/logo_menu.png" height="49" width="49" alt="Concrete5" /></a>
</div>

<div id="ccm-system-nav-wrapper1">
<div id="ccm-system-nav-wrapper2">
<ul id="ccm-system-nav">
<li><a id="ccm-nav-return" href="<?=$this->url('/')?>"><?=t('Return to Website')?></a></li>
<li><a id="ccm-nav-dashboard-help" dialog-title="<?=t('Help')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/help/" dialog-width="500" dialog-height="350" dialog-modal="false"><?=t('Help')?></a></li>
<li class="ccm-last"><a id="ccm-nav-logout" href="<?=$this->url('/login/', 'logout')?>"><?=t('Sign Out')?></a></li>
</ul>
</div>
</div>

<div id="ccm-dashboard-nav">
<ul>
<?
foreach($nav as $n2) { 
	$cp = new Permissions($n2);
	if ($cp->canRead()) {
		$isActive = ($c->getCollectionPath() == $n2->getCollectionPath() || strpos($c->getCollectionPath(), $n2->getCollectionPath() . '/') === 0);
?>
	<li <? if ($isActive) { ?> class="ccm-nav-active" <? } ?>><a href="<?=$nh->getLinkToCollection($n2, false, true)?>"><?=t($n2->getCollectionName())?> <span><?=t($n2->getCollectionDescription())?></span></a></li>
<? }

}?>
</ul>
</div>

<? if (!$disableSecondLevelNav) { ?>

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
	$subpagesP = array();
	foreach($subpages as $sc) {
		$cp = new Permissions($sc);
		if ($cp->canRead()) { 
			$subpagesP[] = $sc;
		}
	
		
	}
	
	if (count($subpagesP) > 0) { 
	?>	
		<div id="ccm-dashboard-subnav">
		<ul><? foreach($subpagesP as $sc) { 
			$isActive = ($c->getCollectionPath() == $sc->getCollectionPath() || strpos($c->getCollectionPath(), $sc->getCollectionPath() . '/') === 0);
			
		?><li <? if ($isActive) { ?> class="nav-selected" <? } ?>><a href="<?=$nh->getLinkToCollection($sc, false, true)?>"><?=t($sc->getCollectionName())?></a></li><? } ?></ul>
		<br/><div class="ccm-spacer">&nbsp;</div>
		</div>
	
	
	<?
		}
	} 
} ?>


<?
	if (isset($latest_version)){ 
		print Loader::element('dashboard/notification_update', array('latest_version' => $latest_version));
	}
?>

<? if(strlen(APP_VERSION)){ ?>
<div id="ccm-dashboard-version">
	<?= t('Version') ?>: <?=APP_VERSION ?>
</div>
<? } ?>

<? if (count($pcs) > 2 && (!$disableThirdLevelNav)) { 

	if (count($pcs) == 3) {
		$parent = $c;
	} else {
		$parent = $pcs[3];
	}
	$subpages = AutonavBlockController::getChildPages($parent);
	$subpagesP = array();
	foreach($subpages as $sc) {
		$cp = new Permissions($sc);
		if ($cp->canRead()) { 
			$subpagesP[] = $sc;
		}	
	}
	
	if (count($subpagesP) > 0) { 
	?>	
	<div id="ccm-dashboard-subnav-third">
		<ul><? foreach($subpagesP as $sc) { 
		
			if ($c->getCollectionPath() == $sc->getCollectionPath() || (strpos($c->getCollectionPath(), $sc->getCollectionPath()) == 0) && strpos($c->getCollectionPath(), $sc->getCollectionPath()) !== false) {
				$isActive = true;
			} else {
				$isActive = false;
			}
			
		?><li <? if ($isActive) { ?> class="nav-selected" <? } ?>><a href="<?=$nh->getLinkToCollection($sc, false, true)?>"><?=t($sc->getCollectionName())?></a></li><? } ?></ul>
	</div>
	
	
	<?
	}
}

?>

<div id="ccm-dashboard-content">

	<div id="ccm-dashboard-content-inner">
	<? if (isset($error)) { ?>
		<? 
		if ($error instanceof Exception) {
			$_error[] = $error->getMessage();
		} else if ($error instanceof ValidationErrorHelper) {
			$_error = array();
			if ($error->has()) {
				$_error = $error->getList();
			}
		} else {
			$_error = $error;
		}
		
		if (count($_error) > 0) {
			?>
			<div class="ccm-ui">
			<?php Loader::element('system_errors', array('format' => 'block', 'error' => $_error)); ?>
			</div>
		<? 
		}
	}
	
	if (isset($message)) { ?>
		<div class="message success"><?=$message?></div>
	<? } ?>
	
	<?php print $innerContent; ?>
	</div>
	
	<div class="ccm-spacer">&nbsp;</div>

	</div>

</div>
<? Loader::element('footer_required', array('disableTrackingCode' => true)); ?>
</body>
</html>
