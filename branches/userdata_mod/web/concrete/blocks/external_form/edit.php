<?
$filename = $controller->getFilename(); ?>

<h2>External form file to include:</h2>
The following is a list of all the files in your site's external forms directory (typically blocks/external_form/forms/ off the root.)
<br/><br/>

<select name="filename" id="cstFilename">
	<option value="">** Select a form</option>
<? foreach($filenames as $ffilename) {
	$selected = ($ffilename == $filename) ? " selected" : "";
	
	echo('<option value="' . $ffilename . '"' . $selected . '>' . $file->unfilename($ffilename) . '</option>');
} ?>
</select>