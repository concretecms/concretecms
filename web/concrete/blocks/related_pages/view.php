<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<? if (count($pages) > 0): ?>

    <div class="ccm-block-related-pages-wrapper">

        <div class="ccm-block-related-pages-header">
            <h5><?=$title?></h5>
        </div>

        <? foreach($pages as $page) { ?>

            <div class="ccm-block-related-pages-page">
                <a href="<?=$page->getCollectionLink()?>"><?=$page->getCollectionName()?></a>
            </div>

        <? } ?>

    </div>


<? endif; ?>
