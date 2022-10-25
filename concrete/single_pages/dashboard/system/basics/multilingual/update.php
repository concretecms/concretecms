<?php

use Concrete\Core\Localization\Translation\PackageLocaleStatus;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Controller\SinglePage\Dashboard\System\Basics\Multilingual\Update $controller
 * @var Concrete\Core\Localization\Translation\LocaleStatus[] $data
 * @var Concrete\Core\Page\View\PageView $view
 */

$someUpdateAvailable = false;
$packageUrl = rtrim(Config::get('concrete.i18n.community_translation.package_url'), '/');
?>
<div class="accordion" id="ccm-packages">
    <?php
    $open = true;
    foreach ($data as $details) {
        if ($details instanceof PackageLocaleStatus) {
            $handle = $details->getPackage()->getPackageHandle();
            $name = $details->getPackage()->getPackageName();
        } else {
            $handle = 'concrete';
            $name = t('Concrete');
        }
        ?>
        <div class="accordion-item">
            <div class="accordion-header" id="ccm-package-<?= $handle ?>-header">
                <h4 class="panel-title">
                    <button type="button" class="h2 accordion-button<?= $open ? '' : ' collapsed' ?>" data-bs-toggle="collapse" data-bs-target="#ccm-package-<?= $handle ?>-body" aria-expanded="<?= $open ? 'true' : 'false' ?>" aria-controls="ccm-package-<?= $handle ?>-body">
                        <span class="position-relative">
                            <?= $name ?>
                            <?php
                            if (!empty($details->getInstalledOutdated())) {
                                ?><span class="position-absolute top-0 translate-middle badge rounded-pill bg-info ms-3 small"><?= count($details->getInstalledOutdated()) ?></span><?php
                            }
                            ?>
                        </span>
                    </button>
                </h4>
            </div>
            <div id="ccm-package-<?= $handle ?>-body" class="accordion-collapse collapse<?= $open ? ' show' : '' ?>" aria-labelledby="ccm-package-<?= $handle ?>-header" data-bs-parent="#ccm-packages">
                <div class="accordion-body">
                    <?php
                    if ($packageUrl) {
                        ?>
                        <a target="_blank" class="float-end" href="<?= h("{$packageUrl}/{$handle}") ?>"><?= t('more details') ?></a>
                        <?php
                    }
                    ?>
                    <table class="table table-hover table-condensed">
                        <colgroup>
                            <col width="60" />
                            <col width="1" />
                            <col />
                            <col />
                            <col width="1" />
                        </colgroup>
                        <tbody>
                            <?php
                            if (!empty($details->getInstalledOutdated())) {
                                $someUpdateAvailable = true;
                                ?>
                                <tr><th colspan="5"><?= t('Updates to installed languages') ?></th></tr>
                                <?php
                                foreach ($details->getInstalledOutdated() as $localeID => $rl) {
                                    echo $controller->getLocaleRowHtml($localeID, $handle, $rl->getRemoteStats(), $rl->getLocalStats(), 'update');
                                }
                            }
                            if (!empty($details->getOnlyRemote())) {
                                ?>
                                <tr><th colspan="5"><?= t('Installable languages') ?></th></tr>
                                <?php
                                foreach ($details->getOnlyRemote() as $localeID => $remoteStats) {
                                    echo $controller->getLocaleRowHtml($localeID, $handle, $remoteStats, null, 'install');
                                }
                            }
                            if (!empty($details->getInstalledUpdated())) {
                                ?>
                                <tr><th colspan="5"><?= t('Up-to-date languages') ?></th></tr>
                                <?php
                                foreach ($details->getInstalledUpdated() as $localeID => $rl) {
                                    echo $controller->getLocaleRowHtml($localeID, $handle, $rl->getRemoteStats(), $rl->getLocalStats(), '');
                                }
                            }
                            if (!empty($details->getOnlyLocal())) {
                                ?>
                                <tr><th colspan="5"><?= t('Only local languages') ?></th></tr>
                                <?php
                                foreach ($details->getOnlyLocal() as $localeID => $l) {
                                    echo $controller->getLocaleRowHtml($localeID, $handle, null, $l, '');
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
        $open = false;
    }
    ?>
</div>
<?php
if ($someUpdateAvailable) {
    ?>
    <form method="post" action="<?= h($view->action('update_all_outdated')) ?>">
        <?php $token->output('update-all-outdated') ?>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <input type="submit" class="btn btn-primary float-end" value="<?= h(t('Update all outdated languages')) ?>" />
            </div>
        </div>
    </form>
    <?php
}
?>
<script>
$(document).ready(function() {
    $('.ccm-install-package-locale').on('click', function() {
        var $btn = $(this);
        $.concreteAjax({
            url: $btn.data('action'),
            data: {ccm_token: $btn.data('token')},
            success: function(r) {
                $btn
                    .text($btn.data('is-update') ? <?= json_encode(t('Updated')) ?> : <?= json_encode(t('Installed')) ?>)
                    .attr('disabled', 'disabled')
                    .off('click')
                ;
            }
        });
    });
});
</script>
