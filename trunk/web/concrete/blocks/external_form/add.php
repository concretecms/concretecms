<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<h2><?=t('External form file to include')?></h2>
<?=t('The following is a list of all the files in your external forms directory.')?>

<br/><br/>
<select name="filename" id="cstFilename">
	<option value="">** <?=t('Select a form')?></option>
<? foreach($filenames as $filename) {
	echo('<option value="' . $filename . '">' . $file->unfilename($filename) . '</option>');
} ?>
</select>