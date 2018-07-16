<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="form-group">
    <?php if ($view->supportsLabel()) { ?>
        <label class="control-label"><?=$label?></label>
    <?php } ?>
    <?php
    if (!empty($entities)) {
        foreach ($entities as $entity) {
            ?>
            <div class="checkbox">
                <label>
                    <input
                        type="checkbox"
                        <?php
                        if (isset($selectedEntities)) {
                            foreach($selectedEntities as $selectedEntity) {
                                if ($selectedEntity->getID() == $entity->getID()) {
                                    print 'checked';
                                }
                            }
                        }
                        ?>
                        name="express_association_<?=$control->getId()?>[]"
                        value="<?=$entity->getId()?>"
                    >
                    <?=$formatter->getEntryDisplayName($control, $entity)?>
                </label>
            </div>
            <?php
        }
    } else {
        ?><p><?=t('No entity found.')?></p><?php
    }
    ?>
</div>
