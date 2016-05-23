<?php
defined('C5_EXECUTE') or die("Access Denied.");
if ($category && $category->getController()->getSetManager()->allowAttributeSets()) {
    ?>

    <div class="ccm-dashboard-header-buttons">

        <a href="<?=URL::to('/dashboard/system/attributes/sets', 'category', $category->getAttributeKeyCategoryID())?>" class="btn btn-default"><?=t('Manage Sets')?></a>
    </div>

<?php 
} ?>