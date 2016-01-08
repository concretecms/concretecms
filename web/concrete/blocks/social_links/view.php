<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div id="ccm-block-social-links<?=$bID?>" class="ccm-block-social-links">
    <ul class="list-inline">
    <?php foreach($links as $link) {
        $service = $link->getServiceObject();
        ?>
        <li><a href="<?= h($link->getURL()) ?>"><?=$service->getServiceIconHTML()?></a></li>
    <?php } ?>
    </ul>
</div>
