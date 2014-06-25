<?
defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Workflow\Progress\PageProgress as PageWorkflowProgress;

if ($controller->getTask() == 'view_details') {

    $cpc = new Permissions($stack);
    $showApprovalButton = false;
    $hasPendingPageApproval = false;
    $workflowList = PageWorkflowProgress::getList($stack);
    foreach($workflowList as $wl) {
        $wr = $wl->getWorkflowRequestObject();
        $wrk = $wr->getWorkflowRequestPermissionKeyObject();
        if ($wrk->getPermissionKeyHandle() == 'approve_page_versions') {
            $hasPendingPageApproval = true;
            break;
        }
    }

    if (!$hasPendingPageApproval) {
        $vo = $stack->getVersionObject();
        if ($cpc->canApprovePageVersions()) {
            $publishTitle = t('Approve Changes');
            $pk = PermissionKey::getByHandle('approve_page_versions');
            $pk->setPermissionObject($stack);
            $pa = $pk->getPermissionAccessObject();
            if (is_object($pa) && count($pa->getWorkflows()) > 0) {
                $publishTitle = t('Submit to Workflow');
            }
            $token = '&' . Loader::helper('validation/token')->getParameter();
            $showApprovalButton = true;
        }
    }

    ?>

    <div class="ccm-dashboard-header-buttons">
        <a href="<?=URL::to('/dashboard/blocks/stacks')?>" data-dialog="add-stack" class="btn btn-default"><i class="fa fa-angle-double-left"></i> <?=t("Back to Stacks")?></a>
    </div>

    <nav class="navbar navbar-default">
    <div class="container-fluid">
    <ul class="nav navbar-nav">
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?=t('Add Block')?></i></a>
            <ul class="dropdown-menu">
                <li><a class="dialog-launch" dialog-modal="false" dialog-width="550" dialog-height="380" dialog-title="<?=t('Add Block')?>" href="<?=URL::to('/ccm/system/dialogs/page/add_block_list')?>?cID=<?=$stack->getCollectionID()?>&arHandle=<?=STACKS_AREA_NAME?>"><?=t('Add Block')?></a></li>
                <li><a class="dialog-launch" dialog-modal="false" dialog-width="550" dialog-height="380" dialog-title="<?=t('Paste From Clipboard')?>" id="stackAddClipboard" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_area_popup?cID=<?=$stack->getCollectionID()?>&arHandle=<?=STACKS_AREA_NAME?>&atask=paste&addOnly=0"><?=t('Paste From Clipboard')?></a></li>
            </ul>
        </li>
        <li class="ccm-main-nav-edit-option"><a dialog-width="640" dialog-height="340" class="dialog-launch" id="stackVersions" dialog-title="<?=t('Version History')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/versions.php?rel=SITEMAP&cID=<?=$stack->getCollectionID()?>"><?=t('Version History')?></a></li>
        <? if ($cpc->canEditPageProperties()) { ?>
            <li class="ccm-main-nav-edit-option"><a href="<?=$view->action('rename', $stack->getCollectionID())?>"><?=t('Rename')?></a></li>
        <? } ?>
        <? if ($cpc->canEditPagePermissions() && PERMISSIONS_MODEL == 'advanced') { ?>
            <li class="ccm-main-nav-edit-option"><a dialog-width="580" dialog-append-buttons="true" dialog-height="420" dialog-title="<?=t('Stack Permissions')?>" id="stackPermissions" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_area_popup?cID=<?=$stack->getCollectionID()?>&arHandle=<?=STACKS_AREA_NAME?>&atask=groups"><?=t('Permissions')?></a></li>
        <? } ?>

        <? if ($cpc->canMoveOrCopyPage()) { ?>
            <li class="ccm-main-nav-edit-option"><a href="<?=$view->action('duplicate', $stack->getCollectionID())?>" style="margin-right: 4px;"><?=t('Duplicate Stack')?></a></li>
        <? } ?>
        <? if ($cpc->canDeletePage()) { ?>
            <li class="ccm-main-nav-edit-option"><a href="javascript:void(0)" onclick="if (confirm('<?=t('Are you sure you want to remove this stack?')?>')) { window.location.href='<?=$view->url('/dashboard/blocks/stacks/', 'delete', $stack->getCollectionID(), Loader::helper('validation/token')->generate('delete'))?>' }"><span class="text-danger"><?=t('Delete Stack')?></span></a></li>
        <? } ?>
    </ul>
    <? if ($showApprovalButton) { ?>
    <ul class="nav navbar-nav navbar-right">
        <li class="navbar-form ccm-main-nav-edit-option">
            <button class="btn btn-success" <? if ($vo->isApproved()) { ?> display: none; <? } ?>" type="button" onclick="window.location.href='<?=DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $stack->getCollectionID() . "&ctask=approve-recent" . $token?>'"><?=$publishTitle?></button>
        </li>
    </ul>
    <? } ?>
    </div>
    </nav>

 <? } else { ?>

    <? if (count($useradded) > 0) { ?>
        <ul class="item-select-list">
        <? foreach($useradded as $st) {
            $sv = CollectionVersion::get($st, 'ACTIVE');
            ?>

            <li id="stID_<?=$st->getCollectionID()?>">
                <? if ($canMoveStacks) { ?><i class="ccm-item-select-list-sort"></i><? } ?>
                <a href="<?=$view->url('/dashboard/blocks/stacks', 'view_details', $st->getCollectionID())?>">
                    <i class="fa fa-bars"></i> <?=$sv->getVersionName()?>
                </a>
            </li>
        <? } ?>
        </ul>
        <?
    } else {
        print '<p>';
        print t('No stacks have been added.');
        print '</p>';
    }
    ?>



    <div class="ccm-dashboard-header-buttons">
        <a href="javascript:void(0)" data-dialog="add-stack" class="btn btn-primary"><?=t("Add Stack")?></a>
    </div>

    <div style="display: none">
        <div id="ccm-dialog-add-stack" class="ccm-ui">
            <form method="post" class="form-stacked" style="padding-left: 0px" action="<?=$view->action('add_stack')?>">
                <?=Loader::helper("validation/token")->output('add_stack')?>
                <div class="form-group">
                    <?=Loader::helper("form")->label('stackName', t('Stack Name'))?>
                    <?=Loader::helper('form')->text('stackName')?>
                </div>
            </form>
            <div class="dialog-buttons">
                <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                <button class="btn btn-primary pull-right" onclick="$('#ccm-dialog-add-stack form').submit()"><?=t('Add Stack')?></button>
            </div>
        </div>
    </div>

    <script type="text/javascript">
    $(function() {
        $('a[data-dialog=add-stack]').on('click', function() {
            jQuery.fn.dialog.open({
                element: '#ccm-dialog-add-stack',
                modal: true,
                width: 320,
                title: '<?=t("Add Stack")?>',
                height: 'auto'
            });
        });
    });
    </script>

<? } ?>