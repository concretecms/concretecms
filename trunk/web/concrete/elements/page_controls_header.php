<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$valt = Loader::helper('validation/token');
$token = '&' . $valt->getParameter();
if (isset($cp)) {
	if ($cp->canWrite() || $cp->canAddSubContent() || $cp->canAdminPage()) {
	
$this->addHeaderItem('<script type="text/javascript" src="' . ASSETS_URL_JAVASCRIPT . '/jquery.form.2.0.2.js"></script>'); 
$this->addHeaderItem('<script type="text/javascript" src="' . ASSETS_URL_JAVASCRIPT . '/jquery.ui.1.5.2.no_datepicker.js"></script>'); 
$this->addHeaderItem('<script type="text/javascript" src="' . ASSETS_URL_JAVASCRIPT . '/jquery.ui.datepicker.js"></script>');
if (LANGUAGE != 'en') {
	$this->addHeaderItem('<script type="text/javascript" src="' . ASSETS_URL_JAVASCRIPT . '/i18n/ui.datepicker-' . LANGUAGE . '.js"></script>'); 
}
?>

<script type="text/javascript">
<?
$valt = Loader::helper('validation/token');
print "var CCM_SECURITY_TOKEN = '" . $valt->generate() . "';";
?>
</script>

<?

$this->addHeaderItem('<script type="text/javascript" src="' . ASSETS_URL_JAVASCRIPT . '/ccm.dialog.js"></script>'); 
$this->addHeaderItem('<script type="text/javascript" src="' . ASSETS_URL_JAVASCRIPT . '/ccm.ui.js"></script>'); 
$this->addHeaderItem('<script type="text/javascript" src="' . ASSETS_URL_JAVASCRIPT . '/ccm.themes.js"></script>'); 
$this->addHeaderItem('<script type="text/javascript" src="' . ASSETS_URL_JAVASCRIPT . '/tiny_mce_309/tiny_mce.js"></script>'); 

$this->addHeaderItem('<style type="text/css">@import "' . ASSETS_URL_CSS . '/ccm.dialog.css";</style>');
$this->addHeaderItem('<style type="text/css">@import "' . ASSETS_URL_CSS . '/ccm.ui.css";</style>');
$this->addHeaderItem('<style type="text/css">@import "' . ASSETS_URL_CSS . '/ccm.calendar.css";</style>');
$this->addHeaderItem('<style type="text/css">@import "' . ASSETS_URL_CSS . '/ccm.menus.css";</style>');
$this->addHeaderItem('<style type="text/css">@import "' . ASSETS_URL_CSS . '/ccm.forms.css";</style>');
$this->addHeaderItem('<style type="text/css">@import "' . ASSETS_URL_CSS . '/ccm.asset.library.css";</style>');

	}
	
}