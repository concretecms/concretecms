<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php echo t('Write notes to yourself using the text area below.');?>

<br/><br/>

<form method="post" action="<?php echo $this->url('/dashboard/', 'module', 'notes', 'save')?>">
<textarea style="width: 190px; height: 170px" name="dashboard_notes"><?php echo $myNotes?></textarea>


<input type="submit" class="accept" name="submit" value="<?php echo t('Save')?>" />


</form>