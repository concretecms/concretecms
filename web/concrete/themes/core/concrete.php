<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?=LANGUAGE?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<? 
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

<!-- insert CSS for Default Concrete Theme //-->
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm.default.theme.css";</style>
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm.install.css";</style>
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm.app.css";</style>
</head>
<body>
<div class="ccm-ui">

<div id="ccm-logo"><?=Loader::helper('concrete/interface')->getToolbarLogoSRC()?></div>




<div class="container">
<div class="row">
<div class="span10 offset1">
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
