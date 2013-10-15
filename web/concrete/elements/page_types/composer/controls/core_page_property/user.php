<?
defined('C5_EXECUTE') or die("Access Denied.");
$user = Loader::helper('form/user_selector');
?>

<div class="form-group">
	<label class="control-label"><?=$label?></label>
	<?=$user->selectUser($this->field('user'), $control->getPageTypeComposerControlDraftValue())?>
</div>
