<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\Workflow\Dialogs\ApprovePagePreview $controller
 * @var Concrete\Core\View\View $view
 * @var Concrete\Core\Page\Collection\Version\Version $requestedVersion
 * @var int $liveVersionID
 * @var int $recentVersionID
 * @var Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface $resolverManager
 * @var Concrete\Core\Application\Service\UserInterface $ui
 */

$tabs = [
    ['ccm-workflow-requested-version', t('Requested Version: %s', $requestedVersion->getVersionComments()), true],
    ['ccm-workflow-live-version', t('Live Version')],
];
if ($liveVersionID !== $recentVersionID) {
    $tabs[] = ['ccm-workflow-recent-version', t('Most Recent Version')];
}
?>
<div class="ccm-ui ccm-workflow-dialog-approve-page-preview">
    <?= $ui->tabs($tabs) ?>
    <div class="tab-content">
        <div class="tab-pane active" id="ccm-workflow-requested-version" role="tabpanel">
            <iframe src="<?= h($resolverManager->resolve(['/ccm/system/page/preview_version']) . '?cvID=' . $requestedVersion->getVersionID() . '&cID=' . $requestedVersion->getCollectionID()) ?>"></iframe>
       </div>
       <div class="tab-pane" id="ccm-workflow-live-version" role="tabpanel">
           <iframe src="<?= h($resolverManager->resolve(['/ccm/system/page/preview_version']) . '?cvID=' . $liveVersionID . '&cID=' . $requestedVersion->getCollectionID()) ?>"></iframe>
       </div>
    <?php
    if ($liveVersionID !== $recentVersionID) {
        ?>
        <div class="tab-pane" id="ccm-workflow-recent-version" role="tabpanel">
            <iframe src="<?= h($resolverManager->resolve(['/ccm/system/page/preview_version']) . '?cvID=' . $recentVersionID . '&cID=' . $requestedVersion->getCollectionID()) ?>"></iframe>
        </div>
        <?php
    }
    ?>
</div>
