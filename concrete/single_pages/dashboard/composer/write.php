<?php defined('C5_EXECUTE') or die("Access Denied."); ?>


<?php if (is_object($pagetype)) {
    ?>

	<form method="post" data-form="composer" class="form-horizontal">
	<div class="ccm-pane-body">
		<?php Loader::helper('concrete/composer')->display($pagetype, $draft);
    ?>
	</div>
	<div class="ccm-pane-footer">
		<?php Loader::helper('concrete/composer')->displayButtons($pagetype, $draft);
    ?>
	</div>

	</form>

<?php
} else {
    ?>

	<?php if (count($pagetypes) > 0) {
    ?>
	<h3><?=t('What would you like to write?')?></h3>
	<ul class="item-select-list">
	<?php foreach ($pagetypes as $pt) {
    $ccp = new Permissions($pt);
    if ($ccp->canEditPageTypeInComposer()) {
        ?>
		<li class="item-select-page"><a href="<?=$view->url('/dashboard/composer/write', 'composer', $pt->getPageTypeID())?>"><?=$pt->getPageTypeName()?></a></li>
		<?php
    }
    ?>
	<?php
}
    ?>
	</ul>
	<?php
} else {
    ?>
		<p><?=t('You do not have any page types.')?></p>
	<?php
}
    ?>


<?php
} ?>
