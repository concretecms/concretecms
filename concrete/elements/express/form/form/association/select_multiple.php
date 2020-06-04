<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="form-group">
    <?php if ($view->supportsLabel()) { ?>
        <label class="control-label"><?=$label?></label>
    <?php } ?>
    <?php if ($view->isRequired()) { ?>
        <span class="text-muted small"><?=t('Required')?></span>
    <?php } ?>
    <?php
    if (!empty($allEntries)) {
        foreach ($allEntries as $entry) {
            ?>
            <div class="checkbox">
                <label>
                    <input
                        type="checkbox"
                        <?php
                        if (isset($selectedEntries)) {
                            foreach($selectedEntries as $selectedEntry) {
                                if ($selectedEntry->getID() == $entry->getID()) {
                                    echo 'checked';
                                }
                            }
                        }
                        ?>
                        name="express_association_<?=$control->getId()?>[]"
                        value="<?=$entry->getId()?>"
                    >
                    <?=$formatter->getEntryDisplayName($control, $entry)?>
                </label>
            </div>
            <?php
        }
    } else {
        ?><p><?=t('No available entries found.')?></p><?php
    }
    ?>
</div>
