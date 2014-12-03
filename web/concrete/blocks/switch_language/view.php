<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<form method="post" action="<?php echo $action?>">
	<?php echo $label?>
	<?php echo $form->select('ccmMultilingualChooseLanguage', $languages, $activeLanguage)?>
	<input type="hidden" name="ccmMultilingualCurrentPageID" value="<?php echo $cID?>" />
</form>