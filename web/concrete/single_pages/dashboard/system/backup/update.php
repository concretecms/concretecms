<?php
defined('C5_EXECUTE') or die("Access Denied.");
$h = Loader::helper('concrete/dashboard');
$ih = Loader::helper('concrete/ui');
$form = Loader::helper('form');
if ($downloadableUpgradeAvailable) {

    ?>
    <div class="ccm-dashboard-header-buttons">
        <a href="<?= $view->action('check_for_updates') ?>" class="btn btn-primary">
            <?= t('Check For Updates') ?>
        </a>
    </div>

    <h2><?= t('Available Update') ?></h2>
    <form method="post" action="<?= $view->action('download_update') ?>" id="ccm-download-update-form">

        <?= Loader::helper('validation/token')->output('download_update') ?>

        <legend style="line-height:40px">
            <?= t('Version: %s', $update->version) ?>.
            <?= t('Release Date: %s', date(t('F d, Y'), strtotime($update->date))) ?>
            <?= Loader::helper('concrete/ui')->submit(
                t('Download'),
                'ccm-download-update-form',
                'right',
                'btn-success') ?>
        </legend>
        <div id="ccm-release-notes">
            <?= $update->notes ?>
        </div>
        <hr/>
        <span class="help-block"><?= t('Note: Downloading an update will NOT automatically install it.') ?></span>

    </form>
    <script>
        $('header.ccm-dashboard-page-header').children().text('<?= t('Currently Running %s', config::get('SITE_APP_VERSION')) ?>');
    </script>
    <?= $h->getDashboardPaneFooterWrapper(); ?>
<?
} else {
    if (count($updates)) {
        ?>
        <?= $h->getDashboardPaneHeaderWrapper(t('Install Local Update'), false, 'span8 offset2', false); ?>
        <strong>
            <?= t(
                'Make sure you <a href="%s">backup your database</a> before updating.',
                $view->url('/dashboard/system/backup/backup')) ?>
        </strong>
        <?php
        $ih = Loader::helper('concrete/ui');

        if (count($updates) == 1) {
            ?>
            <p>
                <?= t(
                    'An update is available. Click below to update to <strong>%s</strong>.',
                    $updates[0]->getUpdateVersion()) ?>
            </p>
            <span class="label"><?= t('Current Version %s', config::get('SITE_APP_VERSION')) ?></span>

            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <form method="post" action="<?= $view->action('do_update') ?>" id="ccm-update-form">
                        <input type="hidden" name="updateVersion" value="<?= $updates[0]->getUpdateVersion() ?>"/>
                        <?= $ih->submit(t('Update'), 'maintenance-mode-form', 'right', 'btn-primary') ?>
                    </form>
                </div>
            </div>
        <? } else { ?>
            <p><?= t('Several updates are available. Please choose the desired update from the list below.') ?></p>
            <span class="label"><?= t('Current Version') ?> <?= config::get('SITE_APP_VERSION') ?></span>
            <form method="post" class="form" action="<?= $view->action('do_update') ?>" id="ccm-update-form">
                <?php
                $checked = true;
                foreach ($updates as $upd) {
                    ?>
                    <div class="radio">
                        <label>
                            <input type="radio" name="updateVersion"
                                   value="<?= $upd->getUpdateVersion() ?>" <?= (!$checked ? '' : "checked") ?> />
                            <?= $upd->getUpdateVersion() ?>
                        </label>
                    </div>
                    <?php
                    $checked = false;
                }
                ?>
                <div class="ccm-dashboard-form-actions-wrapper">
                    <div class="ccm-dashboard-form-actions">
                        <?= $ih->submit(t('Update'), false, 'right', 'btn-primary') ?>
                    </div>
                </div>
            </form>
        <? } ?>
        </div>
        <?= $h->getDashboardPaneFooterWrapper(false); ?>
        <div class="clearfix">&nbsp;</div>
    <?
    } else {
        ?>
        <?= $h->getDashboardPaneHeaderWrapper(t('Update concrete5'), false, 'span8 offset2'); ?>
        <a href="<?= $view->action('check_for_updates') ?>" class="btn" style="float: right"><?= t(
                'Check For Updates') ?></a>
        <h2><?= t('Currently Running %s', config::get('SITE_APP_VERSION')) ?></h2>
        <div class="clearfix">
        </div>
        <br/>

        <p><?= t('No updates available.') ?></p>

        <?= $h->getDashboardPaneFooterWrapper(); ?>
    <?
    }
} ?>
