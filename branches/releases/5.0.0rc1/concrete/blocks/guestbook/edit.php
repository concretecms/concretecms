<?php  //echo var_dump($controller) ?>
Title<br />
<input type="text" name="title" value="<?php echo $title?>" /><br /><br />

Comments Require Moderator Approval?<br/>
<input type="radio" name="requireApproval" value="1" <?php echo ($requireApproval?"checked=\"checked\"":"") ?> /> Yes<br />
<input type="radio" name="requireApproval" value="0" <?php echo ($requireApproval?"":"checked=\"checked\"") ?> /> No<br /><br />

Posting Comments is Enabled?<br/>
<input type="radio" name="displayGuestBookForm" value="1" <?php echo (displayGuestBookForm?"checked=\"checked\"":"") ?> /> Yes<br />
<input type="radio" name="displayGuestBookForm" value="0" <?php echo (displayGuestBookForm?"":"checked=\"checked\"") ?> /> No<br /><br />