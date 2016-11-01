<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php
$c = Page::getCurrentPage();
$ocID = $c->getCollectionID();
$fp = FilePermissions::getGlobal();
$imageresize = Config::get('concrete.file_manager.restrict_uploaded_image_sizes');

if ($fp->canAddFile() || $fp->canSearchFiles()) { ?>



<div class="ccm-dashboard-content-full">
<?php Loader::element('files/search', array('result' => $result))?>
</div>

    <?php
} else {
    ?>
	<p><?=t("You do not have access to the file manager.");
    ?></p>
<?php
} ?>
