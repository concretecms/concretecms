<?php

defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Attribute\Select\OptionList;

?>

<? if ($options instanceof OptionList && $options->count() > 0): ?>

<div class="ccm-block-tags-wrapper">

    <? if ($title): ?>
        <div class="ccm-block-tags-header">
            <h5><?=$title?></h5>
        </div>
    <? endif; ?>

    <? foreach($options as $option) { ?>

        <? if ($target) { ?>
            <a href="<?=$controller->getTagLink($option) ?>">
                <span class="ccm-block-tags-tag label"><?=$option->getSelectAttributeOptionValue()?></span>
            </a>
        <? } else { ?>
            <span class="ccm-block-tags-tag label"><?=$option->getSelectAttributeOptionValue()?></span>
        <? } ?>
    <? } ?>


</div>

<? endif; ?>