<?
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php');
?>

<header class="ccm-dashboard-page-header"><h1><?=t($pageTitle) ?: '&nbsp;' ?></h1></header>

<?
if (isset($error)) {
	if ($error instanceof Exception) {
		$_error[] = $error->getMessage();
	} else if ($error instanceof \Concrete\Core\Error\Error) {
		$_error = array();
		if ($error->has()) {
			$_error = $error->getList();
		}
	} else {
		$_error = $error;
	}
}
if (count($_error) > 0) { ?>
	<div class="ccm-ui"  id="ccm-dashboard-result-message">
	<?php Loader::element('system_errors', array('format' => 'block', 'error' => $_error)); ?>
	</div>
	<?
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
<?php } ?>

<?=Loader::helper('concrete/ui/help')->display('dashboard', $c->getCollectionPath())?>

<?php print $innerContent; ?>

<?
$this->inc('elements/footer.php');
