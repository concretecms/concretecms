<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-block-share-this-page">
    <h4><?=t('Share This Article')?></h4>

    <?php foreach ($selected as $service) { ?>
        <a href="<?php echo h($service->getServiceLink()) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo h($service->getDisplayName()) ?>"><?php echo $service->getServiceIconHTML()?></a>
    <?php } ?>

</div>
