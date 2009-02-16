<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$valt = Loader::helper('validation/token');
$token = '&' . $valt->getParameter();
$html = Loader::helper('html');

if (isset($cp)) {
	if ($cp->canWrite() || $cp->canAddSubContent() || $cp->canAdminPage()) {

$this->addHeaderItem($html->javascript('jquery.form.js'));
$this->addHeaderItem($html->javascript('jquery.ui.js'));
$this->addHeaderItem($html->javascript('jquery.ui.datepicker.js'));
	
if (LANGUAGE != 'en') {
	$this->addHeaderItem($html->javascript('i18n/ui.datepicker-' . LANGUAGE . '.js'));
}
?>

<script type="text/javascript">
<?
$valt = Loader::helper('validation/token');
print "var CCM_SECURITY_TOKEN = '" . $valt->generate() . "';";
?>
</script>

<?

$this->addHeaderItem($html->javascript('ccm.dialog.js'));
$this->addHeaderItem($html->javascript('ccm.ui.js'));
$this->addHeaderItem($html->javascript('ccm.themes.js'));
$this->addHeaderItem($html->javascript('ccm.filemanager.js'));
$this->addHeaderItem($html->javascript('tiny_mce_309/tiny_mce.js'));

$this->addHeaderItem($html->css('ccm.dialog.css'));
$this->addHeaderItem($html->css('ccm.ui.css'));
$this->addHeaderItem($html->css('ccm.calendar.css'));
$this->addHeaderItem($html->css('ccm.menus.css'));
$this->addHeaderItem($html->css('ccm.forms.css'));
$this->addHeaderItem($html->css('ccm.filemanager.css'));

	}
	
}