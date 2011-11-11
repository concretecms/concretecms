<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<? 
if ($_GET['_ccm_dashboard_external']) {
	return;
}

if (!defined('WHITE_LABEL_DASHBOARD_BACKGROUND_SRC')) {
	Config::getOrDefine('WHITE_LABEL_DASHBOARD_BACKGROUND_SRC', false);
}

if (!defined('WHITE_LABEL_DASHBOARD_BACKGROUND_CAPTION')) {
	Config::getOrDefine('WHITE_LABEL_DASHBOARD_BACKGROUND_CAPTION', false);
}

if (!defined('WHITE_LABEL_DASHBOARD_BACKGROUND_FEED')) {
	Config::getOrDefine('WHITE_LABEL_DASHBOARD_BACKGROUND_FEED', false);
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

$v->addFooterItem($html->javascript('jquery.backstretch.js'));
$v->addFooterItem($html->javascript('jquery.ui.js'));
$v->addFooterItem($html->javascript('jquery.form.js'));
$v->addFooterItem('<script type="text/javascript" src="' . REL_DIR_FILES_TOOLS_REQUIRED . '/i18n_js"></script>'); 
$v->addFooterItem($html->javascript('ccm.app.js'));
$v->addFooterItem($html->javascript('ccm.dashboard.js'));
$v->addFooterItem(Loader::helper('html')->javascript('tiny_mce/tiny_mce.js'));

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
if ($dashboard->getCollectionID() == $c->getCollectionID()) {
	$disp .= "ccm_dashboardRequestRemoteInformation();"."\n";
}
$disp .= "	});"."\n";
$disp .= "</script>"."\n";
//require(DIR_FILES_ELEMENTS_CORE . '/header_required.php'); 
$v->addHeaderItem($disp);

Loader::element('header_required');

$backgroundImage = Loader::helper('concrete/dashboard')->getDashboardBackgroundImageSRC();
?>

<script type="text/javascript">
	$(function() {
	    $.backstretch("<?=$backgroundImage?>" <? if (!$_SESSION['dashboardHasSeenImage']) { ?>,  {speed: 750}<? } ?>);
	    ccm_activateToolbar();
	    $("#ccm-page-help").popover({placement: 'below', html: true, trigger: 'manual'});
	    $('.tooltip').twipsy({placement: 'below'});
	    if ($('#ccm-dashboard-result-message').length > 0) { 
			if ($('.ccm-pane').length > 0) { 
				var pclass = $('.ccm-pane').parent().attr('class');
				var gpclass = $('.ccm-pane').parent().parent().attr('class');
				var html = $('#ccm-dashboard-result-message').html();
				$('#ccm-dashboard-result-message').html('<div class="' + gpclass + '"><div class="' + pclass + '">' + html + '</div></div>').fadeIn(400);
			}
	    } else {
	    	$("#ccm-dashboard-result-message").fadeIn(200);
	    }
	});
</script>

</head>
<body>

<? if (!$_SESSION['dashboardHasSeenImage']) { 
	$_SESSION['dashboardHasSeenImage'] = true;
} ?>

<? if (isset($backgroundImage->caption) && $backgroundImage->caption) { ?>
	<div id="ccm-dashboard-background-caption" class="ccm-ui"><div id="ccm-dashboard-background-caption-inner"><? if ($backgroundImage->url) { ?><a target="_blank" href="<?=$backgroundImage->url?>"><? } ?><?=$backgroundImage->caption?><? if ($backgroundImage->url) { ?></a><? } ?></div></div>
<? } ?>

<div class="ccm-ui">

<div id="ccm-toolbar">
<ul id="ccm-main-nav">
<li id="ccm-logo-wrapper"><a href="<?=$this->url('/dashboard/')?>"><?=Loader::helper('concrete/interface')->getToolbarLogoSRC()?></a></li>
<li><a class="ccm-icon-back ccm-menu-icon" href="<?=$this->url('/')?>"><?=t('Return to Website')?></a></li>
<? if (Loader::helper('concrete/interface')->showWhiteLabelMessage()) { ?>
	<li id="ccm-white-label-message"><?=t('Powered by <a href="%s">concrete5</a>.', CONCRETE5_ORG_URL)?></li>
<? } ?>
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
			<div class="ccm-ui"  id="ccm-dashboard-result-message">
				<?php Loader::element('system_errors', array('format' => 'block', 'error' => $_error)); ?>
			</div>
		<? 
		}
	}
	
	if (isset($message)) { ?>
		<div class="ccm-ui" id="ccm-dashboard-result-message">
			<div class="message alert-message info success"><?=Loader::helper('text')->entities($message)?></div>
		</div>
	<? } ?>