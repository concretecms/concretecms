<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php

if (isset($previewElement) && $previewElement instanceof \Concrete\Core\Filesystem\Element) { ?>

<div class="mt-4 mb-4">
    <?php $previewElement->render(); ?>
</div>

<?php } ?>
