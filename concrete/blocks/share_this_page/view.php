<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-block-share-this-page">
    <ul class="list-inline">
    <?php foreach ($selected as $service) { ?>
        <li>
            <a href="<?php echo h($service->getServiceLink()) ?>" target="_blank" aria-label="<?php echo h($service->getDisplayName()) ?>"><?php echo $service->getServiceIconHTML()?></a>
        </li>
    <?php } ?>
    </ul>
</div>
