<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? if ($linkURL) { ?>
    <a href="<?=$linkURL?>">
<? } ?>
<div class="ccm-block-feature-item-hover-wrapper" data-toggle="tooltip" data-placement="bottom" title="<?=h($paragraph)?>">
    <div class="ccm-block-feature-item-hover">
        <div class="ccm-block-feature-item-hover-icon"><i class="fa fa-<?=$icon?>"></i></div>
    </div>
    <div class="ccm-block-feature-item-hover-title"><?=h($title)?></div>
</div>

<? if ($linkURL) { ?>
    </a>
<? } ?>