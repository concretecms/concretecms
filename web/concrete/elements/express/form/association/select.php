<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="control-group">
    <label><?=$label?></label>
    <?php
    if (count($entities)) { ?>
        <select class="form-control" name="express_association_<?=$control->getId()?>">
            <option value=""><?=t('** Choose %s', $control->getControlLabel())?></option>
        <?php foreach($entities as $entity) { ?>
            <option value="<?=$entity->getId()?>"><?=$entity->getFirstName()?> <?=$entity->getlastName()?></option>
        <? } ?>
        </select>
    <?php } else { ?>
        <p><?=t('None found.')?></p>
    <?php } ?>
</div>