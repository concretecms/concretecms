Write notes to yourself using the textarea below.

<br/><br/>

<form method="post" action="<?php echo $this->url('/dashboard/', 'module', 'notes', 'save')?>">
<textarea style="width: 190px; height: 170px" name="dashboard_notes"><?php echo $myNotes?></textarea>


<input type="submit" class="accept" name="submit" value="Save" />


</form>