<?php 

defined('C5_EXECUTE') or die("Access Denied.");

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
		<input type="hidden" value="<?= $rcID ?>" name="rcID"/>
		<label for="password"><?=t('Password')?>: <input type="password" name="password" /></label>
		<br /><br />
		<button type="submit"><?=t('Download')?></button>
	</form>

<? } ?>

<? if (is_object($rc)) { ?>
<p><a href="<?=Loader::helper('navigation')->getLinkToCollection($rc)?>">&lt; <?=t('Back')?></a></p>
<? } ?>
