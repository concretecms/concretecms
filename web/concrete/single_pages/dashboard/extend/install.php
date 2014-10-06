<?php defined('C5_EXECUTE') or die('Access Denied.');

$valt = Loader::helper('validation/token');
$ci = Loader::helper('concrete/urls');
$ch = Loader::helper('concrete/ui');
$tp = new TaskPermission();
if ($tp->canInstallPackages()) {
    $mi = Marketplace::getInstance();
}
$pkgArray = Package::getInstalledList(); ?>

<?php
if ($this->controller->getTask() == 'install_package' && $showInstallOptionsScreen && $tp->canInstallPackages()) {
    echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Install %s', $pkg->getPackageName()), false, 'span10 offset1', false);
    ?>
    <form method="post" action="<?php echo $view->action('install_package', $pkg->getPackageHandle()); ?>">
        <?php echo Loader::helper('validation/token')->output('install_options_selected'); ?>
        <div class="ccm-pane-body">
            <?php echo Loader::packageElement('dashboard/install', $pkg->getPackageHandle()); ?>
            <?php if ($pkg->allowsFullContentSwap()) { ?>
                <h4><?php echo t('Clear this Site?'); ?></h4>
                <p><?php echo t('%s can fully clear you website of all existing content and install its own custom content in its place. If you\'re installing a theme for the first time you may want to do this. Clear all site content?', $pkg->getPackageName()); ?></p>
                <?php
                $u = new User();
                if ($u->isSuperUser()) {
                    $disabled = '';
                    ?><div class="alert-message warning"><p><?php echo t('This will clear your home page, uploaded files and any content pages out of your site completely. It will completely reset your site and any content you have added will be lost.'); ?></p></div><?php
                } else {
                    $disabled = 'disabled';
                    ?><div class="alert-message info"><p><?php echo t('Only the %s user may reset the site\'s content.', USER_SUPER); ?></p></div><?php
                }
                ?>
                <div class="clearfix">
                    <label><?php echo t("Swap Site Contents"); ?></label>
                    <div class="input">
                        <ul class="inputs-list">
                            <li><label><input type="radio" name="pkgDoFullContentSwap" value="0" checked="checked" <?php echo $disabled; ?> /> <span><?php echo t('No. Do <strong>not</strong> remove any content or files from this website.'); ?></span></label></li>
                            <li><label><input type="radio" name="pkgDoFullContentSwap" value="1" <?php echo $disabled; ?> /> <span><?php echo t('Yes. Reset site content with the content found in this package'); ?></span></label></li>
                        </ul>
                    </div>
                </div>
                <?php
            } ?>
        </div>
        <div class="ccm-pane-footer">
            <a href="<?php echo $view->url('/dashboard/extend/install'); ?>" class="btn btn-default pull-left"><?php echo t('Cancel'); ?></a>
            <input type="submit" value="<?php echo t('Install %s', $pkg->getPackageName()); ?>" class="btn btn-primary pull-right" />
        </div>
    </form>
    <?php
    echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);

} elseif ($this->controller->getTask() == 'uninstall' && $tp->canUninstallPackages()) {
    $removeBTConfirm = t('This will remove all elements associated with the %s package. This cannot be undone. Are you sure?', $pkg->getPackageHandle());
    ?>
    <form method="post" class="form-stacked" id="ccm-uninstall-form" action="<?php echo $view->action('do_uninstall_package'); ?>" onsubmit="<?php echo h('return confirm(' . json_encode($removeBTConfirm) . ')'); ?>">
        <?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Uninstall Package'), false, 'span10 offset1', false); ?>
        <div class="ccm-pane-body">
            <?php echo $valt->output('uninstall'); ?>
            <input type="hidden" name="pkgID" value="<?php echo $pkg->getPackageID(); ?>" />
            <h3><?php echo t('Items To Uninstall'); ?></h3>
            <p><?php echo t('Uninstalling %s will remove the following data from your system.', $pkg->getPackageName()); ?></p>
            <?php
            foreach ($items as $k => $itemArray) {
                if (count($itemArray) == 0) {
                    continue;
                }
                ?><h5><?php echo $pkg->getPackageItemsCategoryDisplayName($k); ?></h5><?php
                foreach ($itemArray as $item) {
                    echo $pkg->getItemName($item);
                    ?><br/><?php
                }
            }
            ?>
            <br/>
            <div class="form-group">
                <h3><?php echo t('Move package to trash directory on server?'); ?></h3>
                <div class="checkbox">
                    <label><?php echo Loader::helper('form')->checkbox('pkgMoveToTrash', 1); ?>
                    <span><?php echo t('Yes, remove the package\'s directory from the installation directory.'); ?></span></label>
                </div>
            </div>
            <?php @Loader::packageElement('dashboard/uninstall', $pkg->getPackageHandle()); ?>
        </div>
        <div class="ccm-pane-footer">
            <?php print $ch->submit(t('Uninstall'), 'ccm-uninstall-form', 'right', 'btn-danger'); ?>
            <?php print $ch->button(t('Cancel'), $view->url('/dashboard/extend/install', 'inspect_package', $pkg->getPackageID()), ''); ?>
        </div>
        <?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(); ?>
    </form>
    <?php

} else {

    // grab the total numbers of updates.
    // this consists of
    // 1. All packages that have greater pkgAvailableVersions than pkgVersion
    // 2. All packages that have greater pkgVersion than getPackageCurrentlyInstalledVersion
    $local = array();
    $remote = array();
    $pkgAvailableArray = array();
    if ($tp->canInstallPackages()) {
        $local = Package::getLocalUpgradeablePackages();
        $remote = Package::getRemotelyUpgradeablePackages();
    }

    // now we strip out any dupes for the total
    $updates = 0;
    $localHandles = array();
    foreach ($local as $_pkg) {
        $updates++;
        $localHandles[] = $_pkg->getPackageHandle();
    }
    foreach ($remote as $_pkg) {
        if (!in_array($_pkg->getPackageHandle(), $localHandles)) {
            $updates++;
        }
    }
    if ($tp->canInstallPackages()) {
        foreach (Package::getAvailablePackages() as $_pkg) {
            $_pkg->setupPackageLocalization();
            $pkgAvailableArray[] = $_pkg;
        }
        if(count($pkgAvailableArray) > 0) {
            $translate = Localization::getTranslate();
            if(is_object($translate)) {
                $cache = $translate->getCache();
                if(is_object($cache) && method_exists($cache, 'flush')) {
                    $cache->flush();
                }
            }
        }
    }

    $thisURL = $view->url('/dashboard/extend/install');
    $availableArray = $pkgAvailableArray;
    usort($availableArray, function($a, $b) {
        return strcasecmp($a->getPackageName(), $b->getPackageName());
    });

    /* Load featured add-ons from the marketplace.
     */

    $db = Loader::db();

    if (Config::get('concrete.marketplace.enabled') && $tp->canInstallPackages()) {
        $purchasedBlocksSource = Marketplace::getAvailableMarketplaceItems();
    } else {
        $purchasedBlocksSource = array();
    }

    $skipHandles = array();
    foreach ($availableArray as $ava) {
        foreach ($purchasedBlocksSource as $pi) {
            if ($pi->getHandle() == $ava->getPackageHandle()) {
                $skipHandles[] = $ava->getPackageHandle();
            }
        }
    }

    $purchasedBlocks = array();
    foreach ($purchasedBlocksSource as $pb) {
        if (!in_array($pb->getHandle(), $skipHandles)) {
            $purchasedBlocks[] = $pb;
        }
    }

    if (is_object($pkg)) {
        ?>
        <?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Inspect Package'), false, 'span10 offset1', false); ?>
        <div class="ccm-pane-body">
            <table class="table table-bordered table-striped">
                <tr>
                    <td class="ccm-marketplace-list-thumbnail"><img src="<?php echo $ci->getPackageIconURL($pkg); ?>" /></td>
                    <td class="ccm-addon-list-description" style="width: 100%"><h3><?php echo $pkg->getPackageName(); ?> - <?php echo $pkg->getPackageVersion(); ?></h3><?php echo $pkg->getPackageDescription(); ?></td>
                </tr>
            </table>
            <?php
            $items = $pkg->getPackageItems();
            $blocks = array();
            if (isset($items['block_types']) && is_array($items['block_types'])) {
                $blocks = $items['block_types'];
            }
            if (count($blocks) > 0) { ?>
                <h5><?php echo $pkg->getPackageItemsCategoryDisplayName('block_types'); ?></h5>
                <div class="form-group">
                <?php
                foreach ($blocks as $bt) {
                    $btIcon = $ci->getBlockTypeIconURL($bt);
                    ?>
                    <a href="<?php echo $view->url('/dashboard/blocks/types', 'inspect', $bt->getBlockTypeID()); ?>"><?php echo t($bt->getBlockTypeName()); ?></a>
                    <div class="ccm-block-type-description"  id="ccm-bt-help<?php echo $bt->getBlockTypeID(); ?>"><?php echo t($bt->getBlockTypeDescription()); ?></div>
                    <?php
                }
                ?>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="ccm-pane-footer">
            <?php
            $tp = new TaskPermission();
            if ($tp->canUninstallPackages()) {
                print $ch->button(t('Uninstall Package'), $view->url('/dashboard/extend/install', 'uninstall', $pkg->getPackageID()), 'right', 'btn-danger');
            }
            ?>
            <a href="<?php echo $view->url('/dashboard/extend/install'); ?>" class=" btn btn-default"><?php echo t('Back to Add Functionality'); ?></a>
        </div>
        <?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false); ?>
        <?php
     } else {
        if (is_object($installedPKG) && $installedPKG->hasInstallPostScreen()) {
            ?>
            <div style="display: none">
                <div id="ccm-install-post-notes">
                    <div class="ccm-ui">
                        <?php echo Loader::element('dashboard/install_post', false, $installedPKG->getPackageHandle()); ?>
                        <div class="dialog-buttons">
                            <a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeAll()" class="btn pull-right"><?php echo t('Ok'); ?></a>
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
                    title: <?php echo json_encode(t('Installation Notes')); ?>,
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
                <h5><?php echo t('Add-On updates are available!'); ?></h5>
                <a class="btn-xs btn-default btn pull-right" href="<?php echo $view->url('/dashboard/extend/update'); ?>"><?php echo t('Update Add-Ons'); ?></a>
                <?php
                if ($updates == 1) {
                    ?><p><?php echo t('There is currently <strong>1</strong> update available.'); ?></p><?php
                } else {
                    ?><p><?php echo t('There are currently <strong>%s</strong> updates available.', $updates); ?></p><?php
                }
                ?>
            </div>
            <?php
        }
        ?>
        <h3><?php echo t('Currently Installed'); ?></h3>
        <?php
        if (count($pkgArray) > 0) {
            foreach ($pkgArray as $pkg) {
                ?>
                <div class="media">
                    <div class="pull-left"><img style="width: 49px" src="<?php echo $ci->getPackageIconURL($pkg); ?>" class"media-object" /></div>
                    <div class="media-body">
                        <a href="<?php echo URL::to('/dashboard/extend/install', 'inspect_package', $pkg->getPackageID()); ?>" class="btn pull-right btn-sm btn-default"><?php echo t('Details'); ?></a>
                        <h4 class="media-heading"><?php echo $pkg->getPackageName(); ?> <span class="badge badge-info" style="margin-right: 10px"><?php echo tc('AddonVersion', 'v.%s', $pkg->getPackageVersion()); ?></span></h4>
                        <p><?php echo $pkg->getPackageDescription(); ?></p>
                    </div>
                </div>
                <?php
            }
        } else {
            ?><p><?php echo t('No packages have been installed.'); ?></p><?php
        }
        if ($tp->canInstallPackages()) {
            ?>
            <hr/>
            <h3><?php echo t('Awaiting Installation'); ?></h3>
            <?php
            if (count($availableArray) == 0 && count($purchasedBlocks) == 0) {
                if (!$mi->isConnected()) {
                    ?><p><?php echo t('Nothing currently available to install.'); ?></p><?php
                }
            } else {
                foreach ($purchasedBlocks as $pb) {
                    $file = $pb->getRemoteFileURL();
                    if (!empty($file)) {
                        ?>
                        <div class="media">
                            <div class="pull-left"><img style="width: 49px" src="<?php echo $pb->getRemoteIconURL(); ?>" class"media-object" /></div>
                            <div class="media-body">
                                <a href="<?php echo URL::to('/dashboard/extend/install', 'download', $pb->getMarketplaceItemID()); ?>" class="btn pull-right btn-sm btn-default"><?php echo t('Download'); ?></a>
                                <h4 class="media-heading"><?php echo $pb->getName(); ?> <span class="badge badge-info" style="margin-right: 10px"><?php echo tc('AddonVersion', 'v.%s', $pb->getVersion()); ?></span></h4>
                                <p><?php echo $pb->getDescription(); ?></p>
                            </div>
                        </div>
                        <?php
                    }
                }
                foreach ($availableArray as $obj) {
                    ?>
                    <div class="media">
                        <div class="pull-left"><img style="width: 49px" src="<?php echo $ci->getPackageIconURL($obj); ?>" class"media-object" /></div>
                        <div class="media-body">
                            <a href="<?php echo URL::to('/dashboard/extend/install', 'install_package', $obj->getPackageHandle()); ?>" class="btn pull-right btn-sm btn-default"><?php echo t('Install'); ?></a>
                            <h4 class="media-heading"><?php echo $obj->getPackageName(); ?> <span class="badge badge-info" style="margin-right: 10px"><?php echo tc('AddonVersion', 'v.%s', $obj->getPackageVersion()); ?></span></h4>
                            <p><?php echo $obj->getPackageDescription(); ?></p>
                        </div>
                    </div>
                    <?php
                }
            }
            if (is_object($mi) && $mi->isConnected()) {
                ?>
                <hr/>
                <h3><?php echo t("Project Page"); ?></h3>
                <p><?php echo t('Your site is currently connected to the concrete5 community. Your project page URL is:'); ?><br/><a href="<?php echo $mi->getSitePageURL(); ?>"><?php echo $mi->getSitePageURL(); ?></a></p>
                <?php
            } elseif (is_object($mi) && $mi->hasConnectionError()) {
                echo Loader::element('dashboard/marketplace_connect_failed');
            } elseif ($tp->canInstallPackages() && Config::get('concrete.marketplace.enabled') == true) {
                ?>
                <hr/>
                <div class="well clearfix" style="padding:10px 20px;">
                    <h4><?php echo t('Connect to Community'); ?></h4>
                    <p><?php echo t('Your site is not connected to the concrete5 community. Connecting lets you easily extend a site with themes and add-ons.'); ?></p>
                    <p><a class="btn btn-primary pull-right" href="<?php echo $view->url('/dashboard/extend/connect', 'register_step1'); ?>"><?php echo t("Connect to Community"); ?></a></p>
                </div>
                <?php
            }
        }
    }
}
