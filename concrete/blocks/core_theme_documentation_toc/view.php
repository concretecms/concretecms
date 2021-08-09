<?php
defined('C5_EXECUTE') or die('Access Denied.');

?>

<ul>
    <?php foreach ($pages as $page) { ?>

        <li><a href="<?=URL::to('/dashboard/pages/themes/preview', $themeID, $page->getCollectionID())?>" target="_top"><?=$page->getCollectionName()?></a></li>

    <?php } ?>
</ul>