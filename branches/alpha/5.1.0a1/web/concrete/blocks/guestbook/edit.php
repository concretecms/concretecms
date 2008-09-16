<? //echo var_dump($controller) ?>
Title<br />
<input type="text" name="title" value="<?=$title?>" /><br /><br />

Comments Require Moderator Approval?<br/>
<input type="radio" name="requireApproval" value="1" <?=($requireApproval?"checked=\"checked\"":"") ?> /> Yes<br />
<input type="radio" name="requireApproval" value="0" <?=($requireApproval?"":"checked=\"checked\"") ?> /> No<br /><br />

Posting Comments is Enabled?<br/>
<input type="radio" name="displayGuestBookForm" value="1" <?=(displayGuestBookForm?"checked=\"checked\"":"") ?> /> Yes<br />
<input type="radio" name="displayGuestBookForm" value="0" <?=(displayGuestBookForm?"":"checked=\"checked\"") ?> /> No<br /><br />