<?php 

defined('C5_EXECUTE') or die("Access Denied.");

// File ID = $fID
// get the file and 
// Find out where to take the user once they're done.
// We check for a posted value, to see if this is the users first page load or after submitting a password, etc.
$returnURL = ($_POST['returnURL']) ? $_POST['returnURL'] : $_SERVER['HTTP_REFERER'];

?>

<h1><?=t('Download File')?></h1>

<? if (!isset($filename)) { ?>

	<p><?=t("Invalid File.");?></p>

<? } else { ?>
	
	<p><?=t('This file requires a password to download.')?></p>
	
	<? if (isset($error)) {  ?>
		<div class="ccm-error-response"><?=$error?></div>
	<? } ?>
	
	<form action="<?= View::url('/download_file', 'submit_password', $fID) ?>" method="post">
		<? if(isset($force)) { ?>
			<input type="hidden" value="<?= $force ?>" name="force" />
		<? } ?>
		<input type="hidden" value="<?= $returnURL ?>" name="returnURL" />
		<input type="hidden" value="<?= $rcID ?>" name="rcID"/>
		<label for="password"><?=t('Password')?>: <input type="password" name="password" /></label>
		<br /><br />
		<button type="submit"><?=t('Download')?></button>
	</form>

<? } ?>

<? if ($returnURL) { ?>
<p><a href="<?=$returnURL?>">&lt; <?=t('Back')?></a></p>
<? } ?>
