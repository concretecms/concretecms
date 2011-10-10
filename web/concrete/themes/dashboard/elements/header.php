<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<? 
if ($_GET['_ccm_dashboard_external']) {
	return;
}

Loader::block('autonav');
$nh = Loader::helper('navigation');
$dashboard = Page::getByPath("/dashboard");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<?
$html = Loader::helper('html');
$v = View::getInstance();
if (!isset($enableEditing) || $enableEditing == false) {
	$v->disableEditing();
}

// Required JavaScript

$v->addFooterItem($html->javascript('jquery.js'));
$v->addFooterItem($html->javascript('jquery.backstretch.js'));
$v->addFooterItem($html->javascript('jquery.ui.js'));
$v->addFooterItem($html->javascript('jquery.form.js'));
$v->addFooterItem($html->javascript('ccm.base.js'));
$v->addFooterItem('<script type="text/javascript" src="' . REL_DIR_FILES_TOOLS_REQUIRED . '/i18n_js"></script>'); 
$v->addFooterItem($html->javascript('ccm.app.js'));
$v->addFooterItem($html->javascript('ccm.dashboard.js'));

if (LANGUAGE != 'en') {
	$v->addHeaderItem($html->javascript('i18n/ui.datepicker-'.LANGUAGE.'.js'));
}

// Require CSS
$v->addHeaderItem($html->css('ccm.app.css'));
$v->addHeaderItem($html->css('ccm.dashboard.css'));
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
 if ($dashboard->getCollectionID() == $c->getCollectionID()) {
		$disp .= "ccm_dashboardRequestRemoteInformation();"."\n";
	}
$disp .= "	});"."\n";
$disp .= "</script>"."\n";
//require(DIR_FILES_ELEMENTS_CORE . '/header_required.php'); 
$v->addHeaderItem($disp);

Loader::element('header_required');
?>

<script type="text/javascript">
	$(function() {
	    $.backstretch("http://farm3.static.flickr.com/2443/3843020508_5325eaf761.jpg" <? if (!$_SESSION['dashboardHasSeenImage']) { ?>,  {speed: 750}<? } ?>);
	    ccm_activateToolbar();
	    $("#ccm-page-help").popover({placement: 'below', html: true, trigger: 'manual'});
	});
</script>

</head>
<body>

<? if (!$_SESSION['dashboardHasSeenImage']) { 
	$_SESSION['dashboardHasSeenImage'] = true;
} ?>

<div class="ccm-ui">

<div id="ccm-toolbar">
<a href="<?=$this->url('/dashboard/')?>"><img id="ccm-logo" src="<?=ASSETS_URL_IMAGES?>/logo_menu.png" height="49" width="49" alt="Concrete5" /></a>

<ul id="ccm-main-nav">
<li><a class="ccm-icon-back ccm-menu-icon" href="<?=$this->url('/')?>"><?=t('Return to Website')?></a></li>
</ul>

<ul id="ccm-system-nav">
<li><a class="ccm-icon-dashboard ccm-menu-icon" id="ccm-nav-dashboard" href="<?=$this->url('/dashboard')?>"><?=t('Dashboard')?></a></li>
<li id="ccm-nav-intelligent-search-wrapper"><input type="search" placeholder="<?=t('Intelligent Search')?>" id="ccm-nav-intelligent-search" tabindex="1" /></li>
<li><a id="ccm-nav-sign-out" class="ccm-icon-sign-out ccm-menu-icon" href="<?=$this->url('/login', 'logout')?>"><?=t('Sign Out')?></a></li>
</ul>

</div>
<?
$_ih = Loader::helper('concrete/interface');
print $_ih->getQuickNavigationBar();

$dh = Loader::helper('concrete/dashboard');
print $dh->getDashboardAndSearchMenus();
?>
</div>
<div id="ccm-dashboard-page">

<div id="ccm-dashboard-content">

	<div class="ccm-dashboard-page-container">


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
			<?php Loader::element('system_errors', array('format' => 'block', 'error' => $_error)); ?>
		<? 
		}
	}
	
	if (isset($message)) { ?>
		<div class="block-message alert-message info success"><?=$message?></div>
	<? } ?>