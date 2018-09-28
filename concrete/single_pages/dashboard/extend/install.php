<?php defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Attribute\Key\Category as AttributeCategory;

$app = Concrete\Core\Support\Facade\Application::getFacadeApplication();
$valt = $app->make('helper/validation/token');
$ci = $app->make('helper/concrete/urls');
$ch = $app->make('helper/concrete/ui');
$tp = new TaskPermission();
if ($tp->canInstallPackages()) {
    $mi = Marketplace::getInstance();
}
if (!isset($mi) || !is_object($mi)) {
    $mi = null;
}
$pkgArray = Package::getInstalledList();

$ci = $app->make('helper/concrete/urls');
$txt = $app->make('helper/text');
$nav = $app->make('helper/navigation');

$catList = AttributeCategory::getList();

if ($this->controller->getTask() == 'install_package' && isset($showInstallOptionsScreen) && $showInstallOptionsScreen && $tp->canInstallPackages()) {
    ?>
    <form method="post" action="<?=$this->action('install_package', $pkg->getPackageHandle())?>">
        <?php
        echo $app->make('helper/validation/token')->output('install_options_selected');
        echo View::element('dashboard/install', null, $pkg->getPackageHandle());
        $swapper = $pkg->getContentSwapper();
        if ($swapper->allowsFullContentSwap($pkg)) {
            ?>
            <h4><?=t('Clear this Site?')?></h4>
            <p><?=t('%s can fully clear your website of all existing content and install its own custom content in its place. If you\'re installing a theme for the first time you may want to do this. Clear all site content?', t($pkg->getPackageName())) ?></p>
            <?php
            $u = new User();
            if ($u->isSuperUser()) {
                $disabled = '';
                ?>
                <div class="alert-message warning"><p><?=t('This will clear your home page, uploaded files and any content pages out of your site completely. It will completely reset your site and any content you have added will be lost.')?></p></div>
                <?php
            } else {
                $disabled = 'disabled';
                ?>
                <div class="alert-message info"><p><?=t('Only the %s user may reset the site\'s content.', USER_SUPER)?></p></div>
                <?php
            }
            ?>
            <div class="form-group">
                <label class="control-label"><?=t("Swap Site Contents")?></label>
                <div class="radio"><label><input type="radio" name="pkgDoFullContentSwap" value="0" checked="checked" <?=$disabled?> /> <?=t('No. Do <strong>not</strong> remove any content or files from this website.')?></label></div>
                <div class="radio"><label><input type="radio" name="pkgDoFullContentSwap" value="1" <?=$disabled?> /> <?=t('Yes. Reset site content with the content found in this package')?></label></div>
            </div>
            <?php
        }
        ?>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?=$this->url('/dashboard/extend/install')?>" class="btn btn-default pull-left"><?=t('Cancel')?></a>
                <input type="submit" value="<?=t('Install %s', t($pkg->getPackageName()))?>" class="btn btn-primary pull-right" />
            </div>
        </div>
    </form>
    <?php
} elseif (isset($pkg) && is_object($pkg) && $this->controller->getTask() == 'uninstall' && $tp->canUninstallPackages()) {
    $pkgID = $pkg->getPackageID();
    ?>
    <form method="post" class="form-stacked" id="ccm-uninstall-form" action="<?= $view->action('do_uninstall_package'); ?>">
        <?= $valt->output('uninstall'); ?>
        <input type="hidden" name="pkgID" value="<?=$pkgID ?>" />
        <fieldset>
            <h2><?= t('Uninstall Package'); ?></h2>
            <table class="table table-bordered table-striped">
                <tr>
                    <td class="ccm-marketplace-list-thumbnail"><img src="<?= $ci->getPackageIconURL($pkg); ?>" /></td>
                    <td class="ccm-addon-list-description" style="width: 100%"><h3><?= t($pkg->getPackageName()) ?> - <?= $pkg->getPackageVersion(); ?></h3><?= t($pkg->getPackageDescription()); ?></td>
                </tr>
            </table>
            <?php
            if ($pkg->hasUninstallNotes()) {
                View::element('dashboard/uninstall', null, $pkg->getPackageHandle());
            }
            ?>
            <div class="alert alert-danger">
                <?=t('This will remove all elements associated with the %s package. While you can reinstall the package, this may result in data loss.', t($pkg->getPackageName())) ?>
            </div>
            <div class="form-group">
                <label class="control-label"><?= t('Move package to trash directory on server?'); ?></label>
                <div class="checkbox">
                    <label><?= $app->make('helper/form')->checkbox('pkgMoveToTrash', 1); ?>
                    <span><?= t('Yes, remove the package\'s directory from the installation directory.'); ?></span></label>
                </div>
            </div>
        </fieldset>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
            <?= $ch->submit(t('Uninstall'), 'ccm-uninstall-form', 'right', 'btn-danger'); ?>
            <?= $ch->button(t('Cancel'), $view->url('/dashboard/extend/install', 'inspect_package', $pkg->getPackageID()), ''); ?>
            </div>
        </div>
    </form>
    <?php
} else {
    // grab the total numbers of updates.
    // this consists of
    // 1. All packages that have greater pkgAvailableVersions than pkgVersion
    // 2. All packages that have greater pkgVersion than getPackageCurrentlyInstalledVersion
    $local = [];
    $remote = [];
    $pkgAvailableArray = [];
    if ($tp->canInstallPackages()) {
        $local = Package::getLocalUpgradeablePackages();
        $remote = Package::getRemotelyUpgradeablePackages();
    }
    // now we strip out any dupes for the total
    $updates = 0;
    $localHandles = [];
    foreach ($local as $_pkg) {
        ++$updates;
        $localHandles[] = $_pkg->getPackageHandle();
    }
    foreach ($remote as $_pkg) {
        if (!in_array($_pkg->getPackageHandle(), $localHandles)) {
            ++$updates;
        }
    }
    if ($tp->canInstallPackages()) {
        foreach (Package::getAvailablePackages() as $_pkg) {
            if (empty($pkgAvailableArray)) {
                Localization::clearCache();
            }
            Concrete\Core\Support\Facade\Package::setupLocalization($_pkg);
            $pkgAvailableArray[] = $_pkg;
        }
    }
    $thisURL = $view->url('/dashboard/extend/install');
    $sortMe = [];
    foreach ($pkgAvailableArray as $p) {
        $sortMe[] = ['name' => t($p->getPackageName()), 'pkg' => $p];
    }
    usort($sortMe, function (array $a, array $b) {
        return strcasecmp($a['name'], $b['name']);
    });
    $availableArray = [];
    foreach ($sortMe as $p) {
        $availableArray[] = $p['pkg'];
    }
    // Load featured add-ons from the marketplace.
    if ($mi !== null && $mi->isConnected() && Config::get('concrete.marketplace.enabled') && $tp->canInstallPackages()) {
        $purchasedBlocksSource = Marketplace::getAvailableMarketplaceItems();
    } else {
        $purchasedBlocksSource = [];
    }
    $skipHandles = [];
    foreach ($availableArray as $ava) {
        foreach ($purchasedBlocksSource as $pi) {
            if ($pi->getHandle() == $ava->getPackageHandle()) {
                $skipHandles[] = $ava->getPackageHandle();
            }
        }
    }
    $purchasedBlocks = [];
    foreach ($purchasedBlocksSource as $pb) {
        if (!in_array($pb->getHandle(), $skipHandles)) {
            $purchasedBlocks[] = $pb;
        }
    }
    if (isset($pkg) && is_object($pkg)) {
        ?>
        <table class="table table-bordered table-striped">
            <tr>
                <td class="ccm-marketplace-list-thumbnail"><img src="<?= $ci->getPackageIconURL($pkg); ?>" /></td>
                <td class="ccm-addon-list-description" style="width: 100%"><h3><?= t($pkg->getPackageName()) ?> - <?= $pkg->getPackageVersion(); ?></h3><?= t($pkg->getPackageDescription()) ?></td>
            </tr>
        </table>
        <?php
        foreach ($categories as $category) {
            /** @var Concrete\Core\Package\ItemCategory\ItemInterface */
            if ($category->hasItems($pkg)) {
                $category->renderList($pkg);
            }
        }
        ?>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <?php
                $tp = new TaskPermission();
                if ($tp->canUninstallPackages()) {
                    echo $ch->button(t('Uninstall Package'), $view->url('/dashboard/extend/install', 'uninstall', $pkg->getPackageID()), 'right', 'btn-danger');
                }
                ?>
                <a href="<?= $view->url('/dashboard/extend/install'); ?>" class=" btn btn-default"><?= t('Back to Add Functionality'); ?></a>
            </div>
        </div>
        <?php
    } else {
        if (isset($installedPKG) && is_object($installedPKG) && $installedPKG->hasInstallPostScreen()) {
            ?>
            <div style="display: none">
                <div id="ccm-install-post-notes">
                    <div class="ccm-ui">
                        <?= View::element('dashboard/install_post', null, $installedPKG->getPackageHandle()); ?>
                        <div class="dialog-buttons">
                            <a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeAll()" class="btn btn-primary pull-right"><?= t('Ok'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
            $(function () {
                $('#ccm-install-post-notes').dialog({
                    width: 500,
                    modal: true,
                    height: 400,
                    title: <?= json_encode(t('Installation Notes')); ?>,
                    buttons:[{}],
                    'open': function () {
                        $(this).parent().find('.ui-dialog-buttonpane').addClass("ccm-ui").html('');
                        $(this).find('.dialog-buttons').appendTo($(this).parent().find('.ui-dialog-buttonpane'));
                        $(this).find('.dialog-buttons').remove();
                    }
                });
            });
            </script>
            <?php
        }
        if ($updates > 0) {
            ?>
            <div class="alert alert-info">
                <h5><?= t('Add-On updates are available!'); ?></h5>
                <a class="btn-xs btn-default btn pull-right" href="<?= $view->url('/dashboard/extend/update'); ?>"><?= t('Update Add-Ons'); ?></a>
                <?php
                if ($updates == 1) {
                    ?><p><?= t('There is currently <strong>1</strong> update available.'); ?></p><?php

                } else {
                    ?><p><?= t('There are currently <strong>%s</strong> updates available.', $updates); ?></p><?php

                } ?>
            </div>
            <?php
        } ?>
        <h3><?= t('Currently Installed'); ?></h3>
        <?php
        if (count($pkgArray) > 0) {
            foreach ($pkgArray as $pkg) {
                ?>
                <div class="media-row">
                    <div class="pull-left"><img style="width: 49px" src="<?= $ci->getPackageIconURL($pkg); ?>" class="media-object" /></div>
                    <div class="media-body">
                        <a href="<?= URL::to('/dashboard/extend/install', 'inspect_package', $pkg->getPackageID()); ?>" class="btn pull-right btn-sm btn-default"><?= t('Details'); ?></a>
                        <h4 class="media-heading"><?= t($pkg->getPackageName()) ?> <span class="badge badge-info" style="margin-right: 10px"><?= tc('AddonVersion', 'v.%s', $pkg->getPackageVersion()); ?></span></h4>
                        <p><?= t($pkg->getPackageDescription()) ?></p>
                    </div>
                </div>
                <?php
            }
        } else {
            ?><p><?= t('No packages have been installed.'); ?></p><?php
        }
        if ($tp->canInstallPackages()) {
            ?>
            <hr/>
            <h3><?= t('Awaiting Installation'); ?></h3>
            <?php
            if (count($availableArray) == 0 && count($purchasedBlocks) == 0) {
                if ($mi === null || !$mi->isConnected()) {
                    ?><p><?= t('Nothing currently available to install.'); ?></p><?php
                }
            } else {
                foreach ($purchasedBlocks as $pb) {
                    $file = $pb->getRemoteFileURL();
                    if (!empty($file)) {
                        ?>
                        <div class="media-row">
                            <div class="pull-left"><img style="width: 49px" src="<?= $pb->getRemoteIconURL(); ?>" class="media-object" /></div>
                            <div class="media-body">
                                <a href="<?= URL::to('/dashboard/extend/install', 'download', $pb->getMarketplaceItemID()); ?>" class="btn pull-right btn-sm btn-default"><?= t('Download'); ?></a>
                                <h4 class="media-heading"><?= $pb->getName(); ?> <span class="badge badge-info" style="margin-right: 10px"><?= tc('AddonVersion', 'v.%s', $pb->getVersion()); ?></span></h4>
                                <p><?= $pb->getDescription(); ?></p>
                            </div>
                        </div>
                        <?php
                    }
                }
                foreach ($availableArray as $obj) {
                    ?>
                    <div class="media-row">
                        <div class="pull-left"><img style="width: 49px" src="<?= $ci->getPackageIconURL($obj); ?>" class="media-object" /></div>
                        <div class="media-body">
                            <?php
                            if ($obj instanceof Concrete\Core\Package\BrokenPackage) {
                                ?>
                                <div style="display: inline-block" class="launch-tooltip pull-right" title="<?=t('This package is corrupted. Make sure it has a valid controller.php file and that it has been updated for concrete5.7 and later.')?>">
                                    <button type="button" disabled="disabled" class="btn btn-sm btn-default"><i class="fa fa-exclamation-circle"></i> <?= t('Can\'t Install!'); ?></button>
                                </div>
                                <?php
                            } else {
                                ?>
                                <a href="<?= URL::to('/dashboard/extend/install', 'install_package', $obj->getPackageHandle()); ?>" class="btn pull-right btn-sm btn-default"><?= t('Install'); ?></a>
                                <?php
                            }
                            ?>
                            <h4 class="media-heading"><?= t($obj->getPackageName()) ?> <span class="badge badge-info" style="margin-right: 10px"><?= tc('AddonVersion', 'v.%s', $obj->getPackageVersion()); ?></span></h4>
                            <p><?= t($obj->getPackageDescription()) ?></p>
                        </div>
                    </div>
                    <?php
                }
            }
            if ($mi !== null && $mi->isConnected()) {
                ?>
                <hr/>
                <h4><?= t("Project Page"); ?></h4>
                <p><?= t('Your site is currently connected to the concrete5 community. Your project page URL is:'); ?><br/><a href="<?= $mi->getSitePageURL(); ?>"><?= $mi->getSitePageURL(); ?></a></p>
                <?php
            } elseif ($mi !== null && $mi->hasConnectionError()) {
                echo View::element('dashboard/marketplace_connect_failed');
            } elseif ($tp->canInstallPackages() && Config::get('concrete.marketplace.enabled') == true) {
                ?>
                <hr/>
                <div class="well clearfix" style="padding:10px 20px;">
                    <h4><?= t('Connect to Community'); ?></h4>
                    <p><?= t('Your site is not connected to the concrete5 community. Connecting lets you easily extend a site with themes and add-ons.'); ?></p>
                    <p><a class="btn btn-primary" href="<?= $view->url('/dashboard/extend/connect', 'register_step1'); ?>"><?= t("Connect to Community"); ?></a></p>
                </div>
                <?php
            }
        }
    }
}
