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
$v->addHeaderItem($html->javascript('jquery.backstretch.js'));
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

<script type="text/javascript">
	$(function() {
	    $.backstretch("http://farm3.static.flickr.com/2443/3843020508_5325eaf761.jpg" <? if (!$_SESSION['dashboardHasSeenImage']) { ?>,  {speed: 750}<? } ?>);
	});
</script>
</head>
<body>

<? if (!$_SESSION['dashboardHasSeenImage']) { 
	$_SESSION['dashboardHasSeenImage'] = true;
} ?>

<div id="ccm-dashboard-page" class="ccm-ui">

<div id="ccm-dashboard-header">
<a href="<?=$this->url('/dashboard/')?>"><img src="<?=ASSETS_URL_IMAGES?>/logo_menu.png" height="49" width="49" alt="Concrete5" /></a>
</div>

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
			<?php Loader::element('system_errors', array('format' => 'block', 'error' => $_error)); ?>
		<? 
		}
	}
	
	if (isset($message)) { ?>
		<div class="block-message alert-message info success"><?=$message?></div>
	<? } ?>
	
	<?php print $innerContent; ?>
	
	</div>
	
</div>
</div>

<? Loader::element('footer_required', array('disableTrackingCode' => true)); ?>
</body>
</html>
