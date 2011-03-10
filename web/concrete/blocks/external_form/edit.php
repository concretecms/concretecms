<?
defined('C5_EXECUTE') or die("Access Denied.");
$filename = $controller->getFilename(); ?>

<h2><?=t('External form file to include')?></h2>
<?=t('The following is a list of all the files in your external forms directory.')?>

<br/><br/>

<select name="filename" id="cstFilename">
	<option value="">** <?=t('Select a form')?></option>
<? foreach($filenames as $ffilename) {
	$selected = ($ffilename == $filename) ? " selected" : "";
	
	echo('<option value="' . $ffilename . '"' . $selected . '>' . $file->unfilename($ffilename) . '</option>');
} ?>
</select>