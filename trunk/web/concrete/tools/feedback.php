<?
defined('C5_EXECUTE') or die(_("Access Denied."));
require(DIR_FILES_TOOLS_REQUIRED . '/layout/header.php');

if (MENU_FEEDBACK_DISPLAY && defined('MENU_FEEDBACK_URL')) { ?>

<div class="ccmPopup">

<form method="post" action="<?=MENU_FEEDBACK_URL?>" name="blockForm">

	<div class="ccmHeader">Submit Bug, Feature Request, Question or Feedback</div>
	<div class="ccmControls">
	
	<table width="100%" border="0" cellspacing="3" cellpadding="0">
	<tr>
		<td valign="top">
			<strong>Type</strong><br>
			<select name="feedbackType">
				<option value="Feedback">Feedback</option>
				<option value="Feature Request">Feature Request</option>
				<option value="Bug">Bug</option>
				<option value="Question">Question</option>
			</select>
			<br><br>
		</td>
		<td valign="top">
			<strong>Priority</strong><br>
			<select name="feedbackPriority">
				<option value="5" selected>5 - Low Priority</option>
				<option value="4">4</option>
				<option value="3">3</option>
				<option value="2">2</option>
				<option value="1">1 - Urgent</option>
			</select>
			<br><br>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<strong>Message</strong><br>
			<textarea name="feedbackText" style="width: 100%; height: 120px"></textarea>
		</td>
	</tr>
	</table>
	<? $u = new User(); ?>
	<input type="hidden" name="_cID" value="<?=$_GET['cID']?>">
	<input type="hidden" name="_uID" value="<?=$u->getUserID()?>">
	<input type="hidden" name="_site" value="<?=SITE?>">
	<input type="hidden" name="_baseURL" value="<?=BASE_URL?>">
	
	<input type="submit" value="Submit Feedback" name="submit">
	<input type="button" value="cancel" name="cancel" onclick="self.close()">
	
	</div>
</form>


</div>

<? } ?>

<? require(DIR_FILES_TOOLS_REQUIRED . '/layout/footer.php'); ?>
