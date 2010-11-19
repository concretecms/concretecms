<?php  

defined('C5_EXECUTE') or die("Access Denied.");

// File ID = $fID
// get the file and 
// Find out where to take the user once they're done.
// We check for a posted value, to see if this is the users first page load or after submitting a password, etc.
$returnURL = ($_POST['returnURL']) ? $_POST['returnURL'] : $_SERVER['HTTP_REFERER'];

?>

<h1><?php echo t('Download File')?></h1>

<?php  if (!isset($filename)) { ?>

	<p><?php echo t("Invalid File.");?>

<?php  } else { ?>
	
	<p><?php echo t('This file requires a password to download.')?></p>
	
	<?php  if (isset($error)) {  ?>
		<div class="ccm-error-response"><?php echo $error?></div>
	<?php  } ?>
	
	<form action="<?php echo  View::url('/download_file', 'submit_password', $fID) ?>" method="post">
		<input type="hidden" value="<?php echo $returnURL?>" name="returnURL" />
		<input type="hidden" value="<?php echo  $rcID ?>" name="rcID"/>
		<label for="password"><?php echo t('Password')?>: <input type="text" name="password" /></label>
		<br /><br />
		<button type="submit"><?php echo t('Download')?></button>
	</form>

<?php  } ?>

<?php  if ($returnURL) { ?>
<p><a href="<?php echo $returnURL?>">&lt; <?php echo t('Back')?></a></p>
<?php  } ?>
