<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<div id="HTMLBlock<?=intval($bID)?>" class="HTMLBlock" style="max-height:300px; overflow:auto">
<?=HtmlBlockController::xml_highlight(($content)) ?>
</div>