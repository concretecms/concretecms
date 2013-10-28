<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<?
$v = View::getInstance();
$v->requireAsset('dashboard');
$v->addFooterItem('<script type="text/javascript" src="' . REL_DIR_FILES_TOOLS_REQUIRED . '/i18n_js"></script>'); 
$v->addFooterItem('<script type="text/javascript">$(function() { CCMToolbar.start(); });</script>');
if (ENABLE_PROGRESSIVE_PAGE_REINDEX && Config::get('DO_PAGE_REINDEX_CHECK')) {
	$v->addFooterItem('<script type="text/javascript">$(function() { ccm_doPageReindexing(); });</script>');
}
if (LANGUAGE != 'en') {
	$v->addFooterItem($html->javascript('i18n/ui.datepicker-'.LANGUAGE.'.js'));
}

$valt = Loader::helper('validation/token');
$disp = '<script type="text/javascript">'."\n";
$disp .=  "var CCM_SECURITY_TOKEN = '" . $valt->generate() . "';"."\n";
$disp .= "</script>"."\n";
//require(DIR_FILES_ELEMENTS_CORE . '/header_required.php'); 
$v->addHeaderItem($disp);
Loader::element('header_required', array('disableTrackingCode' => true));
//$v->addFooterItem('<script type="text/javascript">$(function() { CCMDashboard.start(' . $options . '); });</script>');
?>
</head>
<body>

<div id="ccm-dashboard-page">