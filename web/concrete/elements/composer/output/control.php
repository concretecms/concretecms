<?
defined('C5_EXECUTE') or die("Access Denied.");
?>
<div class="ccm-composer-output-control" data-composer-output-control-id="<?=$control->getComposerOutputControlID()?>">
<div class="ccm-composer-item-control-bar">
	<ul class="ccm-composer-item-controls">
		<li><a href="#" data-command="move-output-control" style="cursor: move"><i class="glyphicon glyphicon-move"></i></a></li>
	</ul>
<div class="ccm-composer-output-control-inner">
	<?
	print $control->getComposerControlOutputLabel();
	?>
</div>
</div>
</div>
