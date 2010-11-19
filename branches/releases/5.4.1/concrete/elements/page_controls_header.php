<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$valt = Loader::helper('validation/token');
$token = '&' . $valt->getParameter();
$html = Loader::helper('html');

if (isset($cp)) {
	if ($cp->canWrite() || $cp->canAddSubContent() || $cp->canAdminPage() || $cp->canApproveCollection()) {

?>

<script type="text/javascript">
<?php 
$valt = Loader::helper('validation/token');
print "var CCM_SECURITY_TOKEN = '" . $valt->generate() . "';";
?>
</script>

<?php 
$this->addHeaderItem($html->css('ccm.ui.css'));
$this->addHeaderItem($html->css('jquery.rating.css'));
$this->addHeaderItem($html->css('ccm.dialog.css'));
$this->addHeaderItem($html->css('ccm.menus.css'));
$this->addHeaderItem($html->css('ccm.forms.css'));
$this->addHeaderItem($html->css('ccm.search.css'));
$this->addHeaderItem($html->css('ccm.filemanager.css'));
$this->addHeaderItem($html->css('ccm.colorpicker.css'));
$this->addHeaderItem($html->css('jquery.ui.css'));

$this->addHeaderItem('<script type="text/javascript" src="' . REL_DIR_FILES_TOOLS_REQUIRED . '/i18n_js"></script>'); 
$this->addHeaderItem($html->javascript('jquery.js'));
$this->addHeaderItem($html->javascript('jquery.form.js'));
$this->addHeaderItem($html->javascript('jquery.metadata.js'));
$this->addHeaderItem($html->javascript('jquery.ui.js'));
$this->addHeaderItem($html->javascript('quicksilver.js'));
$this->addHeaderItem($html->javascript('jquery.liveupdate.js'));
$this->addHeaderItem($html->javascript('jquery.rating.js'));
$this->addHeaderItem($html->javascript('jquery.colorpicker.js'));
	
if (LANGUAGE != 'en') {
	$this->addHeaderItem($html->javascript('i18n/ui.datepicker-' . LANGUAGE . '.js'));
	$this->addHeaderItem('<script type="text/javascript">$(function() { jQuery.datepicker.setDefaults({dateFormat: \'yy-mm-dd\'}); });</script>');
}

$this->addHeaderItem($html->javascript('ccm.dialog.js'));
$this->addHeaderItem($html->javascript('ccm.themes.js'));
$this->addHeaderItem($html->javascript('ccm.filemanager.js'));
$this->addHeaderItem($html->javascript('ccm.search.js'));
$this->addHeaderItem($html->javascript('ccm.ui.js'));
$this->addHeaderItem($html->javascript('ccm.layout.js'));
$this->addHeaderItem($html->javascript('tiny_mce/tiny_mce.js'));


$cID = ($c->isAlias()) ? $c->getCollectionPointerOriginalID() : $c->getCollectionID();

$this->addHeaderItem('<script type="text/javascript" src="' . REL_DIR_FILES_TOOLS_REQUIRED . '/page_controls_menu_js?cID=' . $cID . '&amp;cvID=' . $cvID . '&amp;btask=' . $_REQUEST['btask'] . '&amp;ts=' . time() . '"></script>'); 

	}
	
}