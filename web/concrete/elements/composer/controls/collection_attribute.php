<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="control-group">
	<label class="control-label"><?=$label?></label>
	<div class="controls">
		<?=$ak->render('composer', $value, true)?>
	</div>
</div>
