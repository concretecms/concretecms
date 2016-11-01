<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

<?php if (count($options) > 0): ?>

<div class="ccm-block-tags-wrapper">

    <?php if ($title): ?>
        <div class="ccm-block-tags-header">
            <h5><?=$title?></h5>
        </div>
    <?php endif; ?>

    <?php foreach ($options as $option) {
    ?>

        <?php if ($target) {
    ?>
            <a href="<?=$controller->getTagLink($option) ?>">
                <span class="ccm-block-tags-tag label"><?=$option->getSelectAttributeOptionValue()?></span>
            </a>
        <?php 
} else {
    ?>
            <span class="ccm-block-tags-tag label"><?=$option->getSelectAttributeOptionValue()?></span>
        <?php 
}
    ?>
    <?php 
} ?>


</div>

<?php endif; ?>