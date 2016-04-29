<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-ui">

    <?php echo Core::make('helper/concrete/ui')->tabs(array(
        array('fields', t('Search Fields'), true),
        array('columns', t('Customize Results'))
    ));?>


    <div class="ccm-tab-content" id="ccm-tab-content-fields">
        Fields
    </div>

    <div class="ccm-tab-content" id="ccm-tab-content-columns">
        Columns
    </div>


    <div class="dialog-buttons">
        <button class="btn btn-default pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
        <button type="button" data-dialog-action="submit" class="btn btn-primary pull-right"><?=t('Search')?></button>
        <button type="button" data-dialog-action="submit" class="btn btn-success pull-right"><?=t('Save as Search Preset')?></button>
    </div>


</div>