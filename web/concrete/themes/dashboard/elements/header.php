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
Loader::library('3rdparty/mobile_detect');
$md = new Mobile_Detect();

$html = Loader::helper('html');
$v = View::getInstance();
if (!isset($enableEditing) || $enableEditing == false) {
	$v->disableEditing();
}

// Required JavaScript

$v->addFooterItem($html->javascript('jquery.backstretch.js'));
$v->addFooterItem($html->javascript('jquery.ui.js'));
$v->addFooterItem($html->javascript('jquery.form.js'));
$v->addFooterItem($html->javascript('jquery.rating.js'));
$v->addFooterItem('<script type="text/javascript" src="' . REL_DIR_FILES_TOOLS_REQUIRED . '/i18n_js"></script>'); 
$v->addFooterItem($html->javascript('bootstrap.js'));
$v->addFooterItem($html->javascript('ccm.app.js'));
$v->addFooterItem(Loader::helper('html')->javascript('tiny_mce/tiny_mce.js'));

if (LANGUAGE != 'en') {
	$v->addFooterItem($html->javascript('i18n/ui.datepicker-'.LANGUAGE.'.js'));
}

// Require CSS
$v->addHeaderItem($html->css('ccm.app.css'));
if ($md->isMobile() == true) {
	$v->addHeaderItem($html->css('ccm.app.mobile.css')); ?>
	<?		
}
$v->addHeaderItem($html->css('ccm.dashboard.css'));
$v->addHeaderItem($html->css('jquery.ui.css'));

$valt = Loader::helper('validation/token');
$disp = '<script type="text/javascript">'."\n";
$disp .=  "var CCM_SECURITY_TOKEN = '" . $valt->generate() . "';"."\n";
$disp .= "</script>"."\n";
//require(DIR_FILES_ELEMENTS_CORE . '/header_required.php'); 
$v->addHeaderItem($disp);
Loader::element('header_required', array('disableTrackingCode' => true));
$backgroundImage = Loader::helper('concrete/dashboard')->getDashboardBackgroundImage();
?>
<script type="text/javascript">
	var lastSizeCheck = 9999999;		
	ccm_testFixForms = function() {
		if ($(window).width() <= 560 && lastSizeCheck > 560) {
			ccm_fixForms();
		} else if ($(window).width() > 560 && lastSizeCheck <= 560) {
			ccm_fixForms(true);
		}
		lastSizeCheck = $(window).width();
	}
	ccm_fixForms = function(horizontal) {
		$('form').each(function() {
			var f = $(this);
			if (horizontal) {
				if (f.attr('original-class') == 'form-horizontal') {
					f.attr('class', '').addClass('form-horizontal');
				}
			} else {
				f.removeClass('form-horizontal');
			}
		});
	}

	$(function() {
		<? if ($backgroundImage->image) { ?>
		    $.backstretch("<?=$backgroundImage->image?>" <? if (!$_SESSION['dashboardHasSeenImage']) { ?>,  {speed: 750}<? } ?>);
	    <? } ?>
	    <? if ($backgroundImage->checkData) { ?>
		    ccm_getDashboardBackgroundImageData('<?=$backgroundImage->filename?>', <? if ($backgroundImage->displayCaption) { ?> true <? } else { ?> false <? } ?>);
		<? } ?>

		$(window).on('resize', function() {
			ccm_testFixForms();
		});
		$('form').each(function() {
			$(this).attr('original-class', $(this).attr('class'));
		});
		ccm_testFixForms();
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
<li id="ccm-logo-wrapper"><?=Loader::helper('concrete/interface')->getToolbarLogoSRC()?></li>
<li><a class="ccm-icon-back ccm-menu-icon" href="<?=$this->url('/')?>"><? if ($md->isMobile()) { ?><?=t('Back')?><? } else { ?><?=t('Return to Website')?><? } ?></a></li>
<? if (Loader::helper('concrete/interface')->showWhiteLabelMessage()) { ?>
	<li id="ccm-white-label-message"><?=t('Powered by <a href="%s">concrete5</a>.', CONCRETE5_ORG_URL)?></li>
<? } ?>
</ul>

<ul id="ccm-system-nav">
<li><a class="ccm-icon-dashboard ccm-menu-icon" id="ccm-nav-dashboard<? if ($md->isMobile()) { ?>-mobile<? } ?>" href="<?=$this->url('/dashboard')?>"><?=t('Dashboard')?></a></li>
<li id="ccm-nav-intelligent-search-wrapper"><input type="search" placeholder="<?=t('Intelligent Search')?>" id="ccm-nav-intelligent-search" tabindex="1" /></li>
<? if ($md->isMobile() == false) { ?>
	<li><a id="ccm-nav-sign-out" class="ccm-icon-sign-out ccm-menu-icon" href="<?=$this->url('/login', 'logout')?>"><?=t('Sign Out')?></a></li>
<? } ?>
</ul>

</div>
<?
$_ih = Loader::helper('concrete/interface');
$dh = Loader::helper('concrete/dashboard');
$html = $dh->getDashboardAndSearchMenus();
print $dh->addQuickNavToMenus($html);
?>
</div>
<div id="ccm-dashboard-page">

<div id="ccm-dashboard-content">

	<div class="container">


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
			<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">×</button><?=nl2br(Loader::helper('text')->entities($message))?></div>
		</div>
	<? 
	} else if (isset($success)) { ?>
		<div class="ccm-ui" id="ccm-dashboard-result-message">
			<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">×</button><?=nl2br(Loader::helper('text')->entities($success))?></div>
		</div>
	<? } ?>