<?php defined('C5_EXECUTE') or die("Access Denied.");

$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
$bp = new Permissions($b);
?>
<div class="ccm-ui">
    <form method="post" data-dialog-form="block-cache" action="<?=$controller->action('submit')?>">

    <? if ($bp->canEditBlockName()) { ?>
    <fieldset>
        <legend><?=t('Name')?></legend>
        <div class="form-group">
            <label class="control-label" for="bName">
                <?=t('Block Name')?>
                <i class="fa fa-question-circle launch-tooltip" title="<?=t('This can be useful when working with a block programmatically. This is rarely used or needed.')?>"></i>
            </label>
            <input type="text" class="form-control" name="bName" id="bName" value="<?=$bName?>">
        </div>
    </fieldset>
    <? } ?>

    <? if ($bp->canEditBlockCacheSettings()) { ?>
    <fieldset>
        <legend><?=t('Caching')?></legend>
        <? if (!Config::get('concrete.cache.blocks')) { ?>
            <div class="alert alert-warning"><?=t('Block caching is currently disabled globally. These settings won\'t take affect until block caching is turned on. You can turn these settings on from the <a href="%s">Cache and Speed Settings</a> page in the Dashboard.', URL::to('/dashboard/system/optimization/cache'))?></div>
        <? } ?>

        <div class="form-group">
            <label class="control-label" for="bName">
                <?=t('Block Name')?>
                <i class="fa fa-question-circle launch-tooltip" title="<?=t('This can be useful when working with a block programmatically. This is rarely used or needed.')?>"></i>
            </label>
            <input type="text" class="form-control" name="bName" id="bName" value="<?=$bName?>">
        </div>
    </fieldset>
    <? } ?>

    <div class="dialog-buttons">
        <button class="btn btn-default pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
        <button type="button" data-dialog-action="submit" class="btn btn-primary pull-right"><?=t('Save')?></button>
    </div>

    </form>
</div>