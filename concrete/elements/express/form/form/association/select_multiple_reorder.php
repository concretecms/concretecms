<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="form-group">
    <?php if ($view->supportsLabel()) { ?>
        <label class="control-label"><?=$label?></label>
    <?php } ?>
    <?php
    if (!empty($selectedEntries) && count($selectedEntries)) { ?>
        <ul class="item-select-list" data-sortable-list="items">
            <?php foreach($selectedEntries as $entry) { ?>
                <li>
                    <input type="hidden" name="express_association_<?=$control->getID()?>[]" value="<?=$entry->getID()?>">
                    <?=$formatter->getEntryDisplayName($control, $entry)?>
                    <i class="ccm-item-select-list-sort"></i>

                </li>
            <?php } ?>
        </ul>
        <?php
    } else {
        ?><p><?=t('No available entries found.')?></p><?php
    }
    ?>
</div>

<script type="text/javascript">
    $(function() {
        $('ul[data-sortable-list=items]').sortable();
    });
</script>