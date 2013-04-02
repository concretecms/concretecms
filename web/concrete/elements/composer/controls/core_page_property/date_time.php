<?
defined('C5_EXECUTE') or die("Access Denied.");
$user = Loader::helper('form/user_selector');
?>

<div class="control-group">
	<label class="control-label"><?=$label?></label>
	<div class="controls">
		<?=Loader::helper('form/date_time')->datetime($this->field('date_time'))?>
	</div>
</div>
