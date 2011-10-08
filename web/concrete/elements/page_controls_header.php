<?
defined('C5_EXECUTE') or die("Access Denied.");
$valt = Loader::helper('validation/token');
$token = '&' . $valt->getParameter();
$html = Loader::helper('html');

if (isset($cp)) {
	if ($cp->canWrite() || $cp->canAddSubContent() || $cp->canAdminPage() || $cp->canApproveCollection()) {

?>

<script type="text/javascript">
<?
$valt = Loader::helper('validation/token');
print "var CCM_SECURITY_TOKEN = '" . $valt->generate() . "';";
?>
</script>

<?
$dh = Loader::helper('concrete/dashboard');
if (!$dh->inDashboard()) {
	$this->addHeaderItem($html->css('ccm.app.css'));
	$this->addHeaderItem($html->css('jquery.ui.css'));
	$this->addFooterItem('<div id="ccm-page-controls-wrapper"><div id="ccm-toolbar"></div></div>');
	
	$this->addFooterItem('<script type="text/javascript" src="' . REL_DIR_FILES_TOOLS_REQUIRED . '/i18n_js"></script>'); 
	$this->addHeaderItem($html->javascript('jquery.js'));
	$this->addFooterItem($html->javascript('jquery.ui.js'));
	$this->addFooterItem($html->javascript('jquery.form.js'));
	$this->addFooterItem($html->javascript('ccm.app.js'));
	$cih = Loader::helper('concrete/interface');
	if ($cih->showNewsflowOverlay()) {
		$this->addFooterItem('<script type="text/javascript">$(function() { ccm_showNewsflow(); });</script>');
		$u = new User();
		$u->saveConfig('NEWSFLOW_LAST_VIEWED', time());
	}	
	if (ACTIVE_LOCALE != 'en_US') {
		$dlocale = str_replace('_', '-', ACTIVE_LOCALE);
		$this->addFooterItem($html->javascript('i18n/ui.datepicker-' . $dlocale . '.js'));
		$this->addFooterItem('<script type="text/javascript">$(function() { jQuery.datepicker.setDefaults({dateFormat: \'yy-mm-dd\'}); });</script>');
	}
	$this->addFooterItem($html->javascript('tiny_mce/tiny_mce.js'));
}

$cID = ($c->isAlias()) ? $c->getCollectionPointerOriginalID() : $c->getCollectionID();

$this->addHeaderItem('<script type="text/javascript" src="' . REL_DIR_FILES_TOOLS_REQUIRED . '/page_controls_menu_js?cID=' . $cID . '&amp;cvID=' . $cvID . '&amp;btask=' . $_REQUEST['btask'] . '&amp;ts=' . time() . '"></script>'); 

	}
	
}