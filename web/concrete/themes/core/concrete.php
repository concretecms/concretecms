<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<!DOCTYPE html>
<html>
<head>
<? 
$this->addHeaderItem(Loader::helper("html")->css('ccm.default.theme.css'));
$this->addHeaderItem(Loader::helper("html")->css('ccm.app.css'));

if (is_object($c)) {
	$v = View::getInstance();
	$v->disableEditing();
 	Loader::element('header_required');
} else { 
	print Loader::helper('html')->javascript('jquery.js');
	$this->outputHeaderItems();
}
$this->addFooterItem(Loader::helper('html')->javascript('bootstrap.js'));

?>
</head>
<body>
<div class="ccm-ui">

<div id="ccm-logo"><?=Loader::helper('concrete/interface')->getToolbarLogoSRC()?></div>




<div class="container">
<div class="row">
<div class="span12">
<?php Loader::element('system_errors', array('format' => 'block', 'error' => $error)); ?>
</div>
</div>
<?php print $innerContent ?>

</div>
</div>

<? 
if (is_object($c)) {
	Loader::element('footer_required');
}
?>

</body>
</html>
