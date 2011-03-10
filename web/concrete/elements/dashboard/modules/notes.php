<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?=t('Write notes to yourself using the text area below.');?>

<br/><br/>

<form method="post" action="<?=$this->url('/dashboard/', 'module', 'notes', 'save')?>">
<textarea style="width: 190px; height: 170px" name="dashboard_notes"><?=$myNotes?></textarea>


<input type="submit" class="accept" name="submit" value="<?=t('Save')?>" />


</form>