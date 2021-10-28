<?php
defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var Concrete\Core\Page\Page $target
 * @var Concrete\Core\Page\Type\Composer\Control\Control $control
 * @var Concrete\Core\Page\Type\PublishTarget\Configuration\Configuration $configuration
 */

$form = app('helper/form');
$cParentID = false;
if (is_object($target)) {
    $cParentID = $target->getCollectionID();
}
if (is_object($pagetype) && $pagetype->getPageTypePublishTargetTypeID() == $configuration->getPageTypePublishTargetTypeID()) {
    $configuredTarget = $pagetype->getPageTypePublishTargetObject();
    $cID = $configuredTarget->getParentPageID();
    $pc = Page::getByID($cID, 'ACTIVE');
    ?>
    <span class="checkbox">
        <?= t('This page will be published beneath <a href="%s">%s</a>.', app('helper/navigation')->getLinkToCollection($pc), $pc->getCollectionName()) ?>
    </span>
<?php
} ?>