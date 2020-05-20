<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Service\Dashboard $dashboard
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Html\Service\Html $html
 * @var Concrete\Core\Application\Service\UserInterface $interface
 * @var Concrete\Controller\SinglePage\Dashboard\System\Update\Update $controller
 * @var bool $downloadableUpgradeAvailable
 * @var bool $hideDashboardPanel
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Updater\ApplicationUpdate[] $updates
 */

?>
<div class="ccm-dashboard-header-buttons">
    <a href="<?= $controller->action('check_for_updates') ?>" class="btn btn-primary">
        <?= t('Check For Updates') ?>
    </a>
</div>
<?php

if ($downloadableUpgradeAvailable) {
    ?>
    <h2><?= t('Available Update for Download') ?></h2>
    <form method="post" action="<?= $controller->action('download_update') ?>" id="ccm-download-update-form">
        <?php $token->output('download_update') ?>
        <legend style="line-height:40px">
            <?= t('Version: %s', $remoteUpdate->getVersion()) ?>.
            <?= t('Release Date: %s', date(t('F d, Y'), strtotime($remoteUpdate->getDate()))) ?>
            <?= $interface->submit(t('Download'), 'ccm-download-update-form', 'right', 'btn-success') ?>
        </legend>
        <div id="ccm-release-notes">
            <?= $remoteUpdate->getNotes() ?>
        </div>
        <hr />
        <span class="help-block"><?= t('Note: Downloading an update will NOT automatically install it.') ?></span>
    </form>
    <script>
        $('header.ccm-dashboard-page-header').children().text(<?= json_encode(t('Currently Running %s', Config::get('concrete.version'))) ?>);
    </script>
    <?php
}
?>
<h2><?= t('Apply Downloaded Update') ?></h2>
<?php
if (count($updates)) {
    ?>
    <p><?= t('Several updates are available. Please choose the desired update from the list below.') ?></p>
    <span class="label"><?= t('Current Version') ?> <?= Config::get('concrete.version') ?></span>
    <form method="post" class="form" action="<?= $controller->action('start') ?>" id="ccm-update-form">
        <?php
        $checked = true;
        foreach ($updates as $upd) {
            ?>
            <div class="radio">
                <label>
                    <input type="radio" name="updateVersion" value="<?= $upd->getUpdateVersion() ?>" <?= (!$checked ? '' : 'checked') ?> />
                    <?= $upd->getUpdateVersion() ?>
                </label>
            </div>
            <?php
            $checked = false;
        }
        ?>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <?= $interface->submit(t('Update'), false, 'right', 'btn-primary') ?>
            </div>
        </div>
    </form>
    <div class="clearfix">&nbsp;</div>
    <?php
} else {
    ?>
    <p><?= t('No updates are ready to be installed.') ?></p>
    <?php
}
