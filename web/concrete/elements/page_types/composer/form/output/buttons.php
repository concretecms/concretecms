<?php
defined('C5_EXECUTE') or die("Access Denied.");
$cmpp = new Permissions($pagetype);
$cp = new Permissions($page)
?>

<?php if ($cp->canApprovePageVersions()) {
    if ($page->isPageDraft()) {
        $publishTitle = t('Publish');
    } else {
        $publishTitle = t('Publish');
        $pk = PermissionKey::getByHandle('approve_page_versions');
        $pk->setPermissionObject($page);
        $pa = $pk->getPermissionAccessObject();
        if (is_object($pa) && count($pa->getWorkflows()) > 0) {
            $publishTitle = t('Submit');
        }
    }
    ?>
    <button type="button" data-page-type-composer-form-btn="publish" class="btn btn-primary pull-right"><?=$publishTitle?></button>
    <?php

} ?>

<?php if (!is_object($page) || $page->isPageDraft()) {
    ?>
    <button type="button" data-page-type-composer-form-btn="preview" class="btn btn-success pull-right"><?=t('Edit Mode')?></button>
<?php 
} else {
    ?>
    <button type="button" data-page-type-composer-form-btn="preview" class="btn btn-success pull-right"><?=t('Save')?></button>
<?php 
} ?>

<?php if (is_object($page) && $page->isPageDraft()) {
    if ($cp->canDeletePage()) {
        ?>
        <button type="button" data-page-type-composer-form-btn="discard" class="btn btn-danger pull-left"><?=t('Discard Draft')?></button>
    <?php 
    }
    ?>
    <button type="button" data-page-type-composer-form-btn="exit" class="btn btn-default pull-left"><?=t('Save and Exit')?></button>
<?php 
} ?>


<style type="text/css">
    button[data-page-type-composer-form-btn=save] {
        margin-left: 10px;
    }
    button[data-page-type-composer-form-btn=permissions] {
        margin-left: 10px;
    }
    button[data-page-type-composer-form-btn=exit] {
        margin-left: 10px;
    }
    button[data-page-type-composer-form-btn=preview] {
        margin-left: 10px;
    }
    button[data-page-type-composer-form-btn=publish] {
        margin-left: 10px;
    }
</style>
