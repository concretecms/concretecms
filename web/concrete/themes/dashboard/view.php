<?php
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php');
?>

<header class="ccm-dashboard-page-header"><h1><?=(isset($pageTitle) && $pageTitle) ? t($pageTitle) : '&nbsp;' ?></h1></header>

<?php
$_error = array();
if (isset($error)) {
    if ($error instanceof Exception) {
        $_error[] = $error->getMessage();
    } elseif ($error instanceof \Concrete\Core\Error\Error) {
        if ($error->has()) {
            $_error = $error->getList();
        }
    } else {
        $_error = $error;
    }
}
if (!empty($_error)) {
    ?>
	<div class="ccm-ui"  id="ccm-dashboard-result-message">
		<?php View::element('system_errors', array('format' => 'block', 'error' => $_error));
    ?>
	</div>
	<?php

}

if (isset($message)) {
    ?>
	<div class="ccm-ui" id="ccm-dashboard-result-message">
		<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">×</button><?=nl2br(h($message))?></div>
	</div>
	<?php

} elseif (isset($success)) {
    ?>
	<div class="ccm-ui" id="ccm-dashboard-result-message">
		<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">×</button><?=nl2br(h($success))?></div>
	</div>
	<?php 
}

echo Core::make('helper/concrete/ui/help')->display('dashboard', $c->getCollectionPath());

echo $innerContent;

$this->inc('elements/footer.php');
