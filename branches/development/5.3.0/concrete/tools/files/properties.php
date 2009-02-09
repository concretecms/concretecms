<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByPath("/dashboard/mediabrowser");
$cp = new Permissions($c);
$u = new User();
$form = Loader::helper('form');
if (!$cp->canRead()) {
	die(_("Access Denied."));
}

$f = File::getByID($_REQUEST['fID']);
if (isset($_REQUEST['fvID'])) {

} else {
	$fv = $f->getRecentVersion();
}

if ($_POST['task'] == 'update_core') {
	$fv = $f->getVersionToModify();

	switch($_POST['attributeField']) {
		case 'fvTitle':
			$text = htmlentities($_POST['fvTitle']);
			$fv->updateTitle($text);
			print $text;
			break;
		case 'fvDescription':
			$text = htmlentities($_POST['fvDescription']);
			$fv->updateDescription($text);
			print $text;
			break;
		case 'fvTags':
			$text = htmlentities($_POST['fvTags']);
			$fv->updateTags($text);
			print $text;
			break;
	}
	
	exit;
}

function printCorePropertyRow($title, $field, $value, $formText) {
	global $f;
	if ($value == '') {
		$text = '<div class="ccm-file-manager-field-none">' . t('None') . '</div>';
	} else {
		$text = $value;
	}
	
	$html = '
	<tr class="ccm-file-manager-editable-field">
		<th><a href="javascript:void(0)">' . $title . '</a></th>
		<td width="100%" class="ccm-file-manager-editable-field-central"><div class="ccm-file-manager-editable-field-text">' . $text . '</div>
		<form method="post" action="' . REL_DIR_FILES_TOOLS_REQUIRED . '/files/properties">
		<input type="hidden" name="attributeField" value="' . $field . '" />
		<input type="hidden" name="fID" value="' . $f->getFileID() . '" />
		<input type="hidden" name="task" value="update_core" />
		<div class="ccm-file-manager-editable-field-form">
		' . $formText . '
		</div>
		</form>
		</td>
		<td class="ccm-file-manager-editable-field-save"><a href="javascript:void(0)"><img src="' . ASSETS_URL_IMAGES . '/icons/edit_small.png" width="16" height="16" class="ccm-file-manager-editable-field-save-button" /></a>
		<img src="' . ASSETS_URL_IMAGES . '/throbber_white_16.gif" width="16" height="16" class="ccm-file-manager-editable-field-loading" />
		</td>
	</tr>';
	print $html;
}

?>

<h2><?=t('Basic Information')?></h2>
<table border="0" cellspacing="0" cellpadding="0" id="ccm-file-properties" class="ccm-grid">
<tr>
	<th><?=t('Filename')?></th>
	<td width="100%" colspan="2"><?=$fv->getFileName()?></td>
</tr>
<tr>
	<th><?=t('Type')?></th>
	<td colspan="2"><?=$fv->getType()?></td>
</tr>
<tr>
	<th><?=t('Size')?></th>
	<td colspan="2"><?=$fv->getSize()?> (<?=number_format($fv->getFullSize())?> <?=t('bytes')?>)</td>
</tr>
<tr>
	<th><?=t('Date Added')?></th>
	<td colspan="2"><?=$f->getDateAdded()?></td>
</tr>
<?
printCorePropertyRow(t('Title'), 'fvTitle', $fv->getTitle(), $form->text('fvTitle', $fv->getTitle()));
printCorePropertyRow(t('Description'), 'fvDescription', $fv->getDescription(), $form->textarea('fvDescription', $fv->getDescription()));
printCorePropertyRow(t('Tags'), 'fvTags', $fv->getTags(), $form->textarea('fvTags', $fv->getTags()));

?>

</table>

<br/>
<h2><?=t('Extended Information')?></h2>

<script type="text/javascript">
$(function() { ccm_alActiveEditableProperties(); });
</script>