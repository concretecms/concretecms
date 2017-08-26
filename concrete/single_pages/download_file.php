<?php 
defined('C5_EXECUTE') or die('Access Denied.');
?>

<h1><?=t('Download File')?></h1>

<?php
if (!isset($filename)) {
    ?>
    <p><?=t('Invalid File.'); ?></p>
    <?php
} else {
    ?>
    <p><?=t('This file requires a password to download.')?></p>
    <?php
    if (isset($error)) {
        ?>
        <div class="ccm-error-response"><?=$error?></div>
        <?php
    } ?>
	<form action="<?= View::url('/download_file', 'submit_password', $fID) ?>" method="post">
        <?php
        if (isset($force)) {
            ?>
            <input type="hidden" value="<?= $force ?>" name="force" />
            <?php
        }
        ?>
		<input type="hidden" value="<?= $rcID ?>" name="rcID" />
		<label for="password"><?=t('Password')?>: <input type="password" name="password" /></label>
		<br /><br />
		<button type="submit"><?=t('Download')?></button>
	</form>
    <?php
}

if (isset($rc) && is_object($rc)) {
    ?><p><a href="<?= (string) URL::to($rc)?>">&lt; <?=t('Back')?></a></p><?php
}
