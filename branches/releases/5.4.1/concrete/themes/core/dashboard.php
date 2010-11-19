<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php  
Loader::block('autonav');
$nh = Loader::helper('navigation');
$dashboard = Page::getByPath("/dashboard");
$nav = AutonavBlockController::getChildPages($dashboard);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php 
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
$v->addHeaderItem($html->css('ccm.dashboard.css'));
$v->addHeaderItem($html->css('ccm.colorpicker.css'));
$v->addHeaderItem($html->css('ccm.menus.css'));
$v->addHeaderItem($html->css('ccm.forms.css'));
$v->addHeaderItem($html->css('ccm.search.css'));
$v->addHeaderItem($html->css('ccm.filemanager.css'));
$v->addHeaderItem($html->css('ccm.dialog.css'));
$v->addHeaderItem($html->css('jquery.rating.css'));
$v->addHeaderItem($html->css('jquery.ui.css'));

require(DIR_FILES_ELEMENTS_CORE . '/header_required.php'); 

?>

<script type="text/javascript">
<?php 
$valt = Loader::helper('validation/token');
print "var CCM_SECURITY_TOKEN = '" . $valt->generate() . "';";
?>

</script>

<script type="text/javascript">
$(function() {
	$("div.message").animate({
		backgroundColor: 'white'
	}, 'fast').animate({
		backgroundColor: '#eeeeee'
	}, 'fast');
	
	ccm_setupDashboardHeaderMenu();
	<?php  if ($dashboard->getCollectionID() == $c->getCollectionID()) { ?>
		ccm_dashboardRequestRemoteInformation();
	<?php  } ?>
});
</script>
</head>
<body>

<div id="ccm-dashboard-page">

<div id="ccm-dashboard-header">
<a href="<?php echo $this->url('/dashboard/')?>"><img src="<?php echo ASSETS_URL_IMAGES?>/logo_menu.png" height="49" width="49" alt="Concrete5" /></a>
</div>

<div id="ccm-system-nav-wrapper1">
<div id="ccm-system-nav-wrapper2">
<ul id="ccm-system-nav">
<li><a id="ccm-nav-return" href="<?php echo $this->url('/')?>"><?php echo t('Return to Website')?></a></li>
<li><a id="ccm-nav-dashboard-help" dialog-title="<?php echo t('Help')?>" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/help/" dialog-width="500" dialog-height="350" dialog-modal="false"><?php echo t('Help')?></a></li>
<li class="ccm-last"><a id="ccm-nav-logout" href="<?php echo $this->url('/login/', 'logout')?>"><?php echo t('Sign Out')?></a></li>
</ul>
</div>
</div>

<div id="ccm-dashboard-nav">
<ul>
<?php 
foreach($nav as $n2) { 
	$cp = new Permissions($n2);
	if ($cp->canRead()) { 
		if ($c->getCollectionPath() == $n2->getCollectionPath() || (strpos($c->getCollectionPath(), $n2->getCollectionPath()) == 0) && strpos($c->getCollectionPath(), $n2->getCollectionPath()) !== false) {
			$isActive = true;
		} else {
			$isActive = false;
		}
?>
	<li <?php  if ($isActive) { ?> class="ccm-nav-active" <?php  } ?>><a href="<?php echo $nh->getLinkToCollection($n2, false, true)?>"><?php echo t($n2->getCollectionName())?> <span><?php echo t($n2->getCollectionDescription())?></span></a></li>
<?php  }

}?>
</ul>
</div>

<?php  if (isset($subnav)) { ?>

<div id="ccm-dashboard-subnav">
<ul><?php  foreach($subnav as $item) { ?><li <?php  if (isset($item[2]) && $item[2] == true) { ?> class="nav-selected" <?php  } ?>><a href="<?php echo $item[0]?>"><?php echo $item[1]?></a></li><?php  } ?></ul>
<br/><div class="ccm-spacer">&nbsp;</div>
</div>
<?php  } else if ($c->getCollectionID() != $dashboard->getCollectionID()) {
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
		<ul><?php  foreach($subpagesP as $sc) { 
		
			if ($c->getCollectionPath() == $sc->getCollectionPath() || (strpos($c->getCollectionPath(), $sc->getCollectionPath()) == 0) && strpos($c->getCollectionPath(), $sc->getCollectionPath()) !== false) {
				$isActive = true;
			} else {
				$isActive = false;
			}
			
		?><li <?php  if ($isActive) { ?> class="nav-selected" <?php  } ?>><a href="<?php echo $nh->getLinkToCollection($sc, false, true)?>"><?php echo t($sc->getCollectionName())?></a></li><?php  } ?></ul>
		<br/><div class="ccm-spacer">&nbsp;</div>
		</div>
	
	
	<?php 
		}
} ?>

<?php 
	if (isset($latest_version)){ 
		print Loader::element('dashboard/notification_update', array('latest_version' => $latest_version));
	}
?>

<?php  if(strlen(APP_VERSION)){ ?>
<div id="ccm-dashboard-version">
	<?php echo  t('Version') ?>: <?php echo APP_VERSION ?>
</div>
<?php  } ?>

<?php  if (count($pcs) > 2 && (!$disableThirdLevelNav)) { 

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
		<ul><?php  foreach($subpagesP as $sc) { 
		
			if ($c->getCollectionPath() == $sc->getCollectionPath() || (strpos($c->getCollectionPath(), $sc->getCollectionPath()) == 0) && strpos($c->getCollectionPath(), $sc->getCollectionPath()) !== false) {
				$isActive = true;
			} else {
				$isActive = false;
			}
			
		?><li <?php  if ($isActive) { ?> class="nav-selected" <?php  } ?>><a href="<?php echo $nh->getLinkToCollection($sc, false, true)?>"><?php echo t($sc->getCollectionName())?></a></li><?php  } ?></ul>
	</div>
	
	
	<?php 
	}
}

?>

<div id="ccm-dashboard-content">

	<div id="ccm-dashboard-content-inner">
	<?php  if (isset($error)) { ?>
		<?php  
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
			<div class="message error">
			<strong><?php echo t('The following errors occurred when attempting to process your request:')?></strong>
			<ul>
			<?php  foreach($_error as $e) { ?><li><?php echo $e?></li><?php  } ?>
			</ul>
			</div>
		<?php  
		}
	}
	
	if (isset($message)) { ?>
		<div class="message success"><?php echo $message?></div>
	<?php  } ?>
	
	<?php  print $innerContent; ?>
	</div>
	
	<div class="ccm-spacer">&nbsp;</div>

	</div>

</div>

</body>
</html>