<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<div id="HTMLBlock<?=intval($bID)?>" class="HTMLBlock" style="max-height:300px; overflow:auto">
<?=Concrete\Block\Html\Controller::xml_highlight(($content)) ?>
</div>