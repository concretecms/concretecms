<?php
defined('C5_EXECUTE') or die('Access Denied.');
$form = app('helper/form');
$pageSelector = app('helper/form/page_selector');

$cParentID = 0;
$selectorFormFactor = '';
if (is_object($pagetype) && $pagetype->getPageTypePublishTargetTypeID() == $type->getPageTypePublishTargetTypeID()) {
    $configuredTarget = $pagetype->getPageTypePublishTargetObject();
    $cID = $configuredTarget->getParentPageID();
} elseif (!isset($cID)) {
    $cID = null;
}

?>
<div class="form-group">
    <?=$form->label('cParentID', t('Publish Beneath Page')) ?>
    <?= $pageSelector->selectPage('cParentID', $cID) ?>
</div>
