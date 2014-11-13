<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
if ($linkURL) {
    $title = '<a href="' . $linkURL . '">' . $title . '</a>';
}
?>
<div class="ccm-block-feature-item">
    <? if ($title) { ?>
        <h4><i class="fa fa-<?=$icon?>"></i> <?=$title?></h4>
    <? } ?>
    <? if ($paragraph) { ?>
        <p><?=$paragraph?></p>
    <? } ?>
</div>