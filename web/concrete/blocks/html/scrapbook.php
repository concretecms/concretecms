<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div id="HTMLBlock<?php echo $bID ?>" class="HTMLBlock" style="max-height:300px; overflow:auto">
<?php echo Concrete\Block\Html\Controller::xml_highlight($content); ?>
</div>