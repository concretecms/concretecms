<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>

<h2><?php echo t('External form file to include')?></h2>
<?php echo t('The following is a list of all the files in your external forms directory.')?>

<br/><br/>
<select name="filename" id="cstFilename">
	<option value="">** <?php echo t('Select a form')?></option>
<?php  foreach($filenames as $filename) {
	echo('<option value="' . $filename . '">' . $file->unfilename($filename) . '</option>');
} ?>
</select>