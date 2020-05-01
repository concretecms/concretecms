<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="btn-group">
    <a href="<?=URL::to('/dashboard/system/basics/name')?>" class="btn btn-secondary"><?=t('Back')?></a>
    <?php
        if ($category && $category->getController()->getSetManager()->allowAttributeSets()) {
?>
        <a href="<?=URL::to('/dashboard/system/attributes/sets', 'category', $category->getAttributeKeyCategoryID())?>" class="btn btn-secondary"><?=t('Manage Sets')?></a>
    <?php } ?>
</div>
