<?
defined('C5_EXECUTE') or die("Access Denied.");
$user = Loader::helper('form/user_selector');
?>

<div class="form-group">
	<label class="control-label"><?=$label?></label>
	<?=Loader::helper('form/date_time')->datetime($this->field('date_time'))?>
</div>
