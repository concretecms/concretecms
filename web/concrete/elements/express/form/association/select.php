<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="control-group">
    <label><?=$label?></label>
    <?php
    if (count($entities)) { ?>
        <select name="form-control">
        <?php foreach($entities as $entity) { ?>
            <option value="<?=$entity->getId()?>"><?=$entity->getDisplayName()?></option>
        <? } ?>
        </select>
    <?php } else { ?>
        <p><?=t('None found.')?></p>
    <?php } ?>
</div>