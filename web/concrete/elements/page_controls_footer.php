<?
defined('C5_EXECUTE') or die("Access Denied.");
$html = Loader::helper('html');
$dh = Loader::helper('concrete/dashboard');
$ihm = Loader::helper('concrete/ui/menu');
$valt = Loader::helper('validation/token');
$token = '&' . $valt->getParameter();
$logouttoken = Loader::helper('validation/token')->generate('logout');
$cID = $c->getCollectionID();
$permissions = new Permissions($c);

$workflowList = \Concrete\Core\Workflow\Progress\PageProgress::getList($c);

$canViewToolbar = $cp->canViewToolbar();

$show_titles = !!Config::get('concrete.accessibility.toolbar_titles');
$large_font = !!Config::get('concrete.accessibility.toolbar_large_font');

if (isset($cp) && $canViewToolbar && (!$dh->inDashboard())) {

    $canApprovePageVersions = $cp->canApprovePageVersions();
    $u = new User();
    $username = $u->getUserName();
    $vo = $c->getVersionObject();
    $pageInUseBySomeoneElse = false;

    if ($c->isCheckedOut()) {
        if (!$c->isCheckedOutByMe()) {
            $pageInUseBySomeoneElse = true;
        }
    }

    if (!$c->isEditMode()) {
       print Loader::helper('concrete/ui/help')->displayHelpDialogLauncher();
    }

    $cih = Core::make("helper/concrete/ui");
    if ($cih->showHelpOverlay()) {
        Loader::element('help/dialog/introduction');
        $v = View::getInstance();
        $v->addFooterItem('<script type="text/javascript">$(function() { new ConcreteHelpDialog().open(); });</script>');
        $cih->trackHelpOverlayDisplayed();
    }

    ?>

    <div id="ccm-page-controls-wrapper" class="ccm-ui">
    <div id="ccm-toolbar" class="<?= $show_titles ? 'titles' : '' ?> <?= $large_font ? 'large-font' : '' ?>">
    <div class="ccm-mobile-menu-overlay">
        <div class="ccm-mobile-menu-main">
            <ul class="ccm-mobile-menu-entries">
                <?php if (!$pageInUseBySomeoneElse && $c->getCollectionPointerID() == 0) { ?>
                    <?php if ($c->isEditMode()) { ?>
                        <li class="ccm-toolbar-page-edit-mode-active ccm-toolbar-page-edit"><i
                                class="fa fa-pencil mobile-leading-icon"></i><a <?php if ($c->isMasterCollection()) { ?>data-disable-panel="check-in"<?php } ?> data-toolbar-action="check-in"
                                                                                <?php if ($vo->isNew() && !$c->isMasterCollection()) { ?>href="javascript:void(0)"
                                                                                data-launch-panel="check-in"><?php echo t(
                                    'Save Changes') ?><?
                                } else {
                                    ?>href="<?= URL::to(
                                        '/ccm/system/page/check_in',
                                        $c->getCollectionID(),
                                        Loader::helper(
                                            'validation/token')
                                              ->generate()) ?>" data-panel-url="<?= URL::to(
                                        '/ccm/system/panels/page/check_in') ?>"><?php echo t(
                                        'Save Changes') ?><?
                                } ?></a></li>
                    <?php } else if ($permissions->canEditPageContents()) { ?>
                        <li class="ccm-toolbar-page-edit"><i class="fa fa-pencil mobile-leading-icon"></i><a
                                <?php if ($c->isMasterCollection()) { ?>data-disable-panel="check-in"<?php } ?>
                                data-toolbar-action="check-out"
                                href="<?= DIR_REL ?>/<?= DISPATCHER_FILENAME ?>?cID=<?= $c->getCollectionID() ?>&ctask=check-out<?= $token ?>"><?php echo t(
                                    'Edit this Page') ?></a></li>
                    <?php } ?>
                    <li class="parent-ul"><i class="fa fa-cog mobile-leading-icon"></i><a href="#"><?php echo t(
                                'Page Properties') ?><i class="fa fa-caret-down"></i></a>
                        <ul class="list-unstyled">
                            <?
                            $pagetype = PageType::getByID($c->getPageTypeID());
                            if (is_object($pagetype) && $cp->canEditPageContents()) { ?>
                                <li><a class="dialog-launch" dialog-width="640" dialog-height="640"
                                       dialog-modal="false" dialog-title="<?= t('Composer') ?>" href="<?= URL::to(
                                        '/ccm/system/panels/details/page/composer') ?>?cID=<?= $cID ?>"><?= t(
                                            'Composer') ?></a></li>
                            <?
                            }
                            if ($permissions->canEditPageProperties() ||
                                $permissions->canEditPageTheme() ||
                                $permissions->canEditPageTemplate() ||
                                $permissions->canDeletePage() ||
                                $permissions->canEditPagePermissions()) { ?>
                                <li><a class="dialog-launch" dialog-width="640" dialog-height="360"
                                       dialog-modal="false" dialog-title="<?= t('SEO') ?>" href="<?= URL::to(
                                        '/ccm/system/panels/details/page/seo') ?>?cID=<?= $cID ?>"><?= t(
                                            'SEO') ?></a></li>
                            <?
                            }
                            if ($permissions->canEditPageProperties()) {
                                if ($cID > 1) {
                                    ?>
                                    <li><a class="dialog-launch" dialog-width="500" dialog-height="500"
                                           dialog-modal="false" dialog-title="<?= t('Location') ?>"
                                           href="<?= URL::to(
                                               '/ccm/system/panels/details/page/location') ?>?cID=<?= $cID ?>"><?= t(
                                                'Location'); ?></a></li>
                                <?php } ?>
                                <li><a class="dialog-launch" dialog-width="90%" dialog-height="70%"
                                       dialog-modal="false" dialog-title="<?= t('Attributes') ?>" href="<?= URL::to(
                                        '/ccm/system/dialogs/page/attributes') ?>?cID=<?= $cID ?>"><?= t(
                                            'Attributes') ?></a></li>
                            <?php
                            }
                            if ($permissions->canEditPageSpeedSettings()) {
                                ?>
                                <li><a class="dialog-launch" dialog-width="550" dialog-height="280"
                                       dialog-modal="false" dialog-title="<?= t('Caching') ?>" href="<?= URL::to(
                                        '/ccm/system/panels/details/page/caching') ?>?cID=<?= $cID ?>>"><?= t(
                                            'Caching') ?></a></li>
                            <?php
                            }
                            if ($permissions->canEditPagePermissions()) {
                                ?>
                                <li><a class="dialog-launch" dialog-width="500" dialog-height="630"
                                       dialog-modal="false" dialog-title="<?= t('Permissions') ?>"
                                       href="<?= URL::to(
                                           '/ccm/system/panels/details/page/permissions') ?>?cID=<?= $cID ?>"><?= t(
                                            'Permissions') ?></a></li>
                            <?php
                            }
                            if ($permissions->canEditPageTheme() || $permissions->canEditPageTemplate()) {
                                ?>
                                <li><a class="dialog-launch" dialog-width="350" dialog-height="250"
                                       dialog-modal="false" dialog-title="<?= t('Design') ?>" href="<?= URL::to(
                                        '/ccm/system/dialogs/page/design') ?>?cID=<?= $cID ?>"><?= t(
                                            'Design') ?></a></li>
                            <?php
                            }
                            if ($permissions->canViewPageVersions()) {
                                ?>
                                <li><a class="dialog-launch" dialog-width="640" dialog-height="340"
                                       dialog-modal="false" dialog-title="<?= t('Versions') ?>" href="<?= URL::to(
                                        '/ccm/system/panels/page/versions') ?>?cID=<?= $cID ?>"><?= t(
                                            'Versions') ?></a></li>
                            <?php
                            }
                            if ($permissions->canDeletePage()) {
                                ?>
                                <li><a class="dialog-launch" dialog-width="360" dialog-height="250"
                                       dialog-modal="false" dialog-title="<?= t('Delete') ?>" href="<?= URL::to(
                                        '/ccm/system/dialogs/page/delete') ?>?cID=<?= $cID ?>"><?php echo t(
                                            'Delete') ?></a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <?php if ($dh->canRead()) { ?>
                <li class="parent-ul"><i class="fa fa-sliders mobile-leading-icon"></i><a
                        href="<?= URL::to('/dashboard') ?>"><?php echo t('Dashboard') ?><i
                            class="fa fa-caret-down"></i></a>
                    <ul class="list-unstyled">
                        <li class="last-li"><a href="<?= URL::to('/dashboard/sitemap') ?>"><?php echo t(
                                    'Sitemap'); ?></a></li>
                        <li class="last-li"><a href="<?= URL::to('/dashboard/files') ?>"><?php echo t(
                                    'Files'); ?></a></li>
                        <li class="last-li"><a href="<?= URL::to('/dashboard/users') ?>"><?php echo t(
                                    'Members'); ?></a></li>
                        <li class="last-li"><a href="<?= URL::to('/dashboard/reports') ?>"><?php echo t(
                                    'Reports'); ?></a></li>
                        <li class="last-li"><a href="<?= URL::to('/dashboard/pages') ?>"><?php echo t(
                                    'Pages & Themes'); ?></a></li>
                        <li class="last-li"><a href="<?= URL::to('/dashboard/workflow') ?>"><?php echo t(
                                    'Workflow'); ?></a></li>
                        <li class="last-li"><a href="<?= URL::to('/dashboard/blocks/stacks') ?>"><?php echo t(
                                    'Stacks & Blocks'); ?></a></li>
                        <li class="last-li"><a href="<?= URL::to('/dashboard/extend') ?>"><?php echo t(
                                    'Extend concrete5'); ?></a></li>
                        <li class="last-li"><a href="<?= URL::to('/dashboard/system') ?>"><?php echo t(
                                    'System & Settings'); ?></a></li>
                    </ul>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <ul class="ccm-toolbar-item-list">
        <li class="ccm-logo pull-left"><span><?= Loader::helper('concrete/ui')->getToolbarLogoSRC() ?></span></li>
        <?php if ($c->isMasterCollection()) { ?>
        <li class="pull-left"><a href="<?= URL::to('/dashboard/pages/types') ?>"><i class="fa fa-arrow-left"></i></a>
            <?php } ?>
            <?php if (!$pageInUseBySomeoneElse && $c->getCollectionPointerID() == 0) { ?>

            <?php if ($c->isEditMode()) { ?>
        <li data-guide-toolbar-action="check-in"  class="ccm-toolbar-page-edit-mode-active ccm-toolbar-page-edit pull-left hidden-xs">
            <a <?php if ($c->isMasterCollection()) { ?>data-disable-panel="check-in"<?php } ?> data-toolbar-action="check-in" <?php if ($vo->isNew() || $c->isPageDraft()) { ?>href="javascript:void(0)"
               data-launch-panel="check-in" <?php } else { ?>href="<?= URL::to(
                '/ccm/system/page/check_in',
                $c->getCollectionID(),
                Loader::helper('validation/token')->generate()) ?>"<?php } ?>
               data-panel-url="<?= URL::to('/ccm/system/panels/page/check_in') ?>"
               title="<?= t('Exit Edit Mode') ?>">
                <i class="fa fa-pencil"></i>
                <span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-edit-mode">
                    <?= tc('toolbar', 'Exit Edit Mode') ?>
                </span>
            </a>
        </li>
    <?php } else if ($permissions->canEditPageContents()) { ?>
        <li data-guide-toolbar-action="edit-page" class="ccm-toolbar-page-edit pull-left hidden-xs">
            <a <?php if ($c->isMasterCollection()) { ?>data-disable-panel="check-in"<?php } ?> data-toolbar-action="check-out"
               href="<?= DIR_REL ?>/<?= DISPATCHER_FILENAME ?>?cID=<?= $c->getCollectionID() ?>&ctask=check-out<?= $token ?>"
               title="<?= t('Edit This Page') ?>">
                <i class="fa fa-pencil"></i>
                <span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-edit-mode">
                    <?= tc('toolbar', 'Edit Mode') ?>
                </span>
            </a>
        </li>
    <?php } ?>

        <?php if (!$c->isMasterCollection() && (
                $permissions->canEditPageProperties() ||
                $permissions->canEditPageTheme() ||
                $permissions->canEditPageTemplate() ||
                $permissions->canDeletePage() ||
                $permissions->canEditPagePermissions())) { ?>
        <li data-guide-toolbar-action="page-settings" class="pull-left hidden-xs">
            <a href="#" data-launch-panel="page"
               data-panel-url="<?= URL::to('/ccm/system/panels/page') ?>"
               title="<?= t('Page Design, Location, Attributes and Settings') ?>">
                <i class="fa fa-cog"></i>
                <span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-settings">
                    <?= tc('toolbar', 'Page Settings') ?>
                </span>
            </a>
        </li>
        <?php } ?>
    <?
    }

    if ($cp->canEditPageContents() && (!$pageInUseBySomeoneElse)) {
        ?>
        <li data-guide-toolbar-action="add-content" class="ccm-toolbar-add pull-left hidden-xs">
            <?php if ($c->isEditMode()) { ?>
                <a href="#" data-launch-panel="add-block" data-panel-url="<?= URL::to('/ccm/system/panels/add') ?>"
                   title="<?= t('Add Content to The Page') ?>">
                    <i class="fa fa-plus"></i>
                    <span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-add">
                        <?= tc('toolbar', 'Add Content') ?>
                    </span>
                </a>
            <?php } else { ?>
                <a href="<?= DIR_REL ?>/<?= DISPATCHER_FILENAME ?>?cID=<?= $cID ?>&ctask=check-out-add-block<?= $token ?>"
                   title="<?= t('Add Content to The Page') ?>">
                    <i class="fa fa-plus"></i>
                    <span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-add">
                        <?= tc('toolbar', 'Add Content') ?>
                    </span>
                </a>
            <?php } ?>
        </li>
    <?
    }


    $items = $ihm->getPageHeaderMenuItems('left');
    foreach ($items as $ih) {
        $cnt = $ih->getController();
        if ($cnt->displayItem()) {
            $cnt->registerViewAssets();
            ?>
            <li class="pull-left"><?= $cnt->getMenuItemLinkElement() ?></li>
        <?
        }
    }

    if (Loader::helper('concrete/ui')->showWhiteLabelMessage()) {
        ?>
        <li class="pull-left visible-xs visible-lg" id="ccm-white-label-message"><?= t(
                'Powered by <a href="%s">concrete5</a>.',
                Config::get('concrete.urls.concrete5')) ?></li>
    <?php } ?>
        <li class="pull-right ccm-toolbar-mobile-menu-button visible-xs hidden-sm hidden-md hidden-lg
            <?php if ($c->isEditMode()) { ?> ccm-toolbar-mobile-menu-button-active<?php } ?>">
            <i class="fa fa-bars fa-2"></i>
        </li>
        <?php if ($dh->canRead()) { ?>
        <li data-guide-toolbar-action="dashboard" class="pull-right hidden-xs ">
            <a href="<?= URL::to('/dashboard') ?>" data-launch-panel="dashboard"
                                             title="<?= t('Dashboard – Change Site-wide Settings') ?>">
                <i class="fa fa-sliders"></i>
                <span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-site-settings">
                    <?= tc('toolbar', 'Dashboard') ?>
                </span>

            </a>
        </li>
        <?php } else { ?>
            <li class="pull-right hidden-xs ">
                <a href="<?=URL::to('/login', 'logout', Loader::helper('validation/token')->generate('logout'))?>" title="<?=t('Sign Out')?>"><i class="fa fa-sign-out"></i>
                <span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-site-settings">
                    <?= tc('toolbar', 'Sign Out') ?>
                </span>
                </a>
            </li>
        <?php } ?>
        <li data-guide-toolbar-action="sitemap" class="pull-right hidden-xs">
            <a href="#" data-panel-url="<?= URL::to('/ccm/system/panels/sitemap') ?>"
                                            title="<?= t('Add Pages and Navigate Your Site') ?>"
                                            data-launch-panel="sitemap">
                        <i class="fa fa-files-o"></i>
                <span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-add-page">
                    <?= tc('toolbar', 'Pages') ?>
                </span>
            </a>
        </li>
        <?php if ($cp->canEditPageMultilingualSettings() && \Core::make('multilingual/detector')->isEnabled()) {
            $section = \Concrete\Core\Multilingual\Page\Section\Section::getCurrentSection();
            $ch = Core::make('multilingual/interface/flag');
            if (is_object($section)) { ?>
                <li class="pull-right hidden-xs">
                <a href="#" data-panel-url="<?= URL::to('/ccm/system/panels/multilingual') ?>"
                   title="<?= t('Navigate this page in other languages') ?>"
                   data-launch-panel="multilingual">
                    <?php print $ch->getFlagIcon($section->getIcon()); ?>
                    <span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-add-page">
                        <?=$section->getLanguageText()?>
                    </span>
                </a>
                </li>
            <?php } ?>
        <?php } ?>
        <li data-guide-toolbar-action="intelligent-search" class="ccm-toolbar-search pull-right hidden-xs"><i class="fa fa-search"></i> <input type="search"
                                                                                                id="ccm-nav-intelligent-search" autocomplete="off"
                                                                                                tabindex="1"/></li>
        <?
        $items = $ihm->getPageHeaderMenuItems('right');
        foreach ($items as $ih) {
            $cnt = $ih->getController();
            if ($cnt->displayItem()) {
                $cnt->registerViewAssets();
                ?>
                <li class="pull-right"><?= $cnt->getMenuItemLinkElement() ?></li>
            <?
            }
        }

        ?>

    </ul>

    </div>

    <?
    print $dh->getIntelligentSearchMenu();
    ?>

    <?php if ($pageInUseBySomeoneElse) { ?>
        <?= Loader::helper('concrete/ui')->notify(
            array(
                'title'   => t('Editing Unavailable.'),
                'message' => t("%s is currently editing this page.", $c->getCollectionCheckedOutUserName()),
                'type'    => 'info',
                'icon'    => 'exclamation-sign'
            )) ?>
    <?php } else { ?>

        <?php if ($c->getCollectionPointerID() > 0) { ?>

            <?
            $buttons = array();
            $buttons[] = '<a href="' . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID() . '" class="btn btn-default btn-xs">' . t(
                    'View/Edit Original') . '</a>';
            if ($canApprovePageVersions) {
                $buttons[] = '<a href="' . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionPointerOriginalID() . '&ctask=remove-alias' . $token . '" class="btn btn-xs btn-danger">' . t(
                        'Remove Alias') . '</a>';
            }

            print Loader::helper('concrete/ui')->notify(
                array(
                    'title'   => t('Page Alias.'),
                    'message' => t("This page is an alias of one that actually appears elsewhere."),
                    'type'    => 'info',
                    'icon'    => 'info-sign',
                    'buttons' => $buttons
                ))?>

        <?
        }

        $hasPendingPageApproval = false;

        if ($canViewToolbar) {
            ?>
            <?php if (is_array($workflowList) && count($workflowList) > 0) { ?>
                <div id="ccm-notification-page-alert-workflow" class="ccm-notification ccm-notification-info">
                    <div class="ccm-notification-inner-wrapper">
                        <?php foreach ($workflowList as $i => $wl) { ?>
                            <?php $wr = $wl->getWorkflowRequestObject();
                            $wf = $wl->getWorkflowObject(); ?>

                            <form method="post" action="<?= $wl->getWorkflowProgressFormAction() ?>"
                                  id="ccm-notification-page-alert-form-<?= $i ?>">
                                <i class="ccm-notification-icon fa fa-info-circle"></i>

                                <div class="ccm-notification-inner">
                                    <p><?= $wf->getWorkflowProgressCurrentDescription($wl) ?></p>
                                    <?php $actions = $wl->getWorkflowProgressActions(); ?>
                                    <?php if (count($actions) > 0) { ?>
                                        <div class="btn-group">
                                            <?php foreach ($actions as $act) { ?>
                                                <?php if ($act->getWorkflowProgressActionURL() != '') { ?>
                                                    <a href="<?= $act->getWorkflowProgressActionURL() ?>"
							<?php } else { ?>
                                                    <button type="submit"
                                                            name="action_<?= $act->getWorkflowProgressActionTask() ?>"
							<?php } ?>

                                                <?php if (count(
                                                        $act->getWorkflowProgressActionExtraButtonParameters()) > 0
                                                ) {
                                                    ?>
                                                    <?php foreach ($act->getWorkflowProgressActionExtraButtonParameters() as $key => $value) { ?>
                                                        <?= $key ?>="<?= $value ?>"
                                                    <?php } ?>
                                                <?php } ?>

                                                class="btn btn-xs <?= $act->getWorkflowProgressActionStyleClass() ?>"><?= $act->getWorkflowProgressActionStyleInnerButtonLeftHTML() ?> <?= $act->getWorkflowProgressActionLabel() ?> <?= $act->getWorkflowProgressActionStyleInnerButtonRightHTML() ?>
                                                <?php if ($act->getWorkflowProgressActionURL() != '') { ?>
                                                    </a>
                                                <?php } else { ?>
                                                    </button>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </form>
                        <?php } ?>
                    </div>
                    <div class="ccm-notification-actions"><a href="#" data-dismiss-alert="page-alert"><?= t(
                                'Hide') ?></a></div>
                </div>
                </div>
            <?php } ?>
        <?
        }

        if (!$c->getCollectionPointerID() && (!is_array($workflowList) || count($workflowList) == 0)) {
            if (is_object($vo)) {
                if (!$vo->isApproved() && !$c->isEditMode()) {

                    if ($c->isPageDraft()) {
                    print Loader::helper('concrete/ui')->notify(
                        array(
                            'title'   => t('Page Draft.'),
                            'message' => t("This is an un-published draft."),
                            'type'    => 'info',
                            'icon'    => 'exclamation'
                        ));
                    } else {
                        $buttons = array();
                        if ($canApprovePageVersions && !$c->isCheckedOut()) {
                            $pk = \Concrete\Core\Permission\Key\PageKey::getByHandle('approve_page_versions');
                            $pk->setPermissionObject($c);
                            $pa = $pk->getPermissionAccessObject();
                            if (is_object($pa)) {
                                if (count($pa->getWorkflows()) > 0) {
                                    $appLabel = t('Submit for Approval');
                                }
                            }
                            if (!isset($appLabel) || !$appLabel) {
                                $appLabel = t('Approve Version');
                            }

                            $buttons[] = '<a href="' . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID() . '&ctask=approve-recent' . $token . '" class="btn btn-primary btn-xs">' . $appLabel . '</a>';

                        }

                        print Loader::helper('concrete/ui')->notify(
                            array(
                                'title'   => t('Page is Pending Approval.'),
                                'message' => t("This page is newer than what appears to visitors on your live site."),
                                'type'    => 'info',
                                'icon'    => 'cog',
                                'buttons' => $buttons
                            ))?>

                <?php  }
                }
            }
        } ?>

    <?php } ?>
    </div>

<?
}
