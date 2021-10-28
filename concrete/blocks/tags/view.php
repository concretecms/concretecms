<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php if (isset($options) && count($options) > 0) { ?>
    <div class="ccm-block-tags-wrapper">
        <?php if ($title) { ?>
            <div class="ccm-block-tags-header">
				<<?php echo $titleFormat; ?>><?=$title?></<?php echo $titleFormat; ?>>
            </div>
        <?php } ?>

        <?php foreach ($options as $option) { ?>
            <?php if (isset($target) && $target) { ?>
                <a href="<?=$controller->getTagLink($option) ?>">
                    <?php if (isset($selectedTag) && mb_strtolower($option->getSelectAttributeOptionValue()) == mb_strtolower($selectedTag)) { ?>
                        <span class="ccm-block-tags-tag ccm-block-tags-tag-selected label"><?=$option->getSelectAttributeOptionValue()?></span>
                    <?php } else { ?>
                        <span class="ccm-block-tags-tag label"><?=$option->getSelectAttributeOptionValue()?></span>
                    <?php } ?>
                </a>
            <?php } else { ?>
                <?php if (isset($selectedTag) && mb_strtolower($option->getSelectAttributeOptionValue()) == mb_strtolower($selectedTag)) { ?>
                    <span class="ccm-block-tags-tag ccm-block-tags-tag-selected label"><?=$option->getSelectAttributeOptionValue()?></span>
                <?php } else { ?>
                    <span class="ccm-block-tags-tag label"><?=$option->getSelectAttributeOptionValue()?></span>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </div>
<?php } ?>
