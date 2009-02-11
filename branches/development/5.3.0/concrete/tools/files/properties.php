<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByPath("/dashboard/mediabrowser");
$cp = new Permissions($c);
$u = new User();
$form = Loader::helper('form');
if (!$cp->canRead()) {
	die(_("Access Denied."));
}

if (!isset($_REQUEST['reload'])) { ?>
	<div id="ccm-file-properties-wrapper">
<? }


Loader::model("file_attributes");

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

if ($_POST['task'] == 'update_extended_attribute') {
	$fv = $f->getVersionToModify();
	$fakID = $_REQUEST['fakID'];
	$value = '';
	$ak = FileAttributeKey::get($fakID);
	
	if ($ak->getAttributeKeyType() == 'DATE') {
		$dt = Loader::helper('form/date_time');
		$value = $dt->translate('fakID_' . $fakID);
	} else if (is_array($_REQUEST['fakID_' . $fakID])) {
		foreach($_REQUEST['fakID_' . $fakID] as $val) {
			$value .= htmlentities($val) . "\n";
		}
	} else {
		$value = htmlentities($_REQUEST['fakID_' . $fakID]);
	}
	$fv->setAttribute($ak, $value);
	$fv->populateAttributes();	
	print $fv->getAttribute($ak, true) ;	
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
		<div class="ccm-file-manager-editable-field-form ccm-file-manager-editable-field-type-text">
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

function printFileAttributeRow($ak, $fv) {
	global $f;
	$value = $fv->getAttribute($ak, true);
	if ($value == '') {
		$text = '<div class="ccm-file-manager-field-none">' . t('None') . '</div>';
	} else {
		$text = $value;
	}
	if ($ak->isAttributeKeyEditable()) { 
	
	$html = '
	<tr class="ccm-file-manager-editable-field">
		<th><a href="javascript:void(0)">' . $ak->getAttributeKeyName() . '</a></th>
		<td width="100%" class="ccm-file-manager-editable-field-central"><div class="ccm-file-manager-editable-field-text">' . $text . '</div>
		<form method="post" action="' . REL_DIR_FILES_TOOLS_REQUIRED . '/files/properties">
		<input type="hidden" name="fakID" value="' . $ak->getAttributeKeyID() . '" />
		<input type="hidden" name="fID" value="' . $f->getFileID() . '" />
		<input type="hidden" name="task" value="update_extended_attribute" />
		<div class="ccm-file-manager-editable-field-form ccm-file-manager-editable-field-type-' . strtolower($ak->getAttributeKeyType()) . '">
		' . $ak->outputHTML($fv) . '
		</div>
		</form>
		</td>
		<td class="ccm-file-manager-editable-field-save"><a href="javascript:void(0)"><img src="' . ASSETS_URL_IMAGES . '/icons/edit_small.png" width="16" height="16" class="ccm-file-manager-editable-field-save-button" /></a>
		<img src="' . ASSETS_URL_IMAGES . '/throbber_white_16.gif" width="16" height="16" class="ccm-file-manager-editable-field-loading" />
		</td>
	</tr>';
	
	} else {

	$html = '
	<tr>
		<th>' . $ak->getAttributeKeyName() . '</th>
		<td width="100%" colspan="2">' . $text . '</td>
	</tr>';	
	}
	print $html;
}

?>

<ul class="ccm-dialog-tabs" id="ccm-file-properties-tabs">
<li class="ccm-nav-active"><a href="javascript:void(0)" id="ccm-file-properties-details"><?=t('Details')?></a></li>
<li><a href="javascript:void(0)" id="ccm-file-properties-versions"><?=t('Versions')?></a></li>
<li><a href="javascript:void(0)" id="ccm-file-properties-statistics"><?=t('Statistics')?></a></li>
</ul>

<script type="text/javascript">
var ccm_fiActiveTab = "ccm-file-properties-details";
$("#ccm-file-properties-tabs a").click(function() {
	$("li.ccm-nav-active").removeClass('ccm-nav-active');
	$("#" + ccm_fiActiveTab + "-tab").hide();
	ccm_fiActiveTab = $(this).attr('id');
	$(this).parent().addClass("ccm-nav-active");
	$("#" + ccm_fiActiveTab + "-tab").show();
});
</script>

<div id="ccm-file-properties-details-tab">

<h1><?=t('File Details')?></h1>

<div id="ccm-file-properties">
<h2><?=t('Basic Properties')?></h2>
<table border="0" cellspacing="0" cellpadding="0" class="ccm-grid">
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
	<td colspan="2"><?=t('Added by <strong>%s</strong> on %s', $fv->getAuthorName(), $f->getDateAdded())?></td>
</tr>
<?
printCorePropertyRow(t('Title'), 'fvTitle', $fv->getTitle(), $form->text('fvTitle', $fv->getTitle()));
printCorePropertyRow(t('Description'), 'fvDescription', $fv->getDescription(), $form->textarea('fvDescription', $fv->getDescription()));
printCorePropertyRow(t('Tags'), 'fvTags', $fv->getTags(), $form->textarea('fvTags', $fv->getTags()));

?>

</table>


<? 
$attribs = FileAttributeKey::getImporterList($fv);
$ft = $fv->getType();

if (count($attribs) > 0) { ?>

<br/>

<h2><?=t('%s File Properties', $ft)?></h2>
<table border="0" cellspacing="0" cellpadding="0" class="ccm-grid">
<?

foreach($attribs as $at) {

	printFileAttributeRow($at, $fv);

}

?>
</table>
<? } ?>

<? 
$attribs = FileAttributeKey::getUserAddedList();

if (count($attribs) > 0) { ?>

<br/>

<h2><?=t('Other Properties')?></h2>
<table border="0" cellspacing="0" cellpadding="0" class="ccm-grid">
<?

foreach($attribs as $at) {

	printFileAttributeRow($at, $fv);

}

?>
</table>
<br/>
<? }

$h = Loader::helper('concrete/interface');
$b1 = $h->button_js(t('Rescan'), 'ccm_alRescanFiles(' . $f->getFileID() . ')');
print $b1;

?>


</div>
</div>

<div id="ccm-file-properties-versions-tab" style="display: none">
<h1><?=t('File Versions')?></h1>

	<table border="0" cellspacing="0" width="100%" class="ccm-grid" cellpadding="0">
	<tr>
		<th>&nbsp;</th>
		<th><?=t('Name')?></th>
		<th><?=t('Comments')?></th>
		<th><?=t('Creator')?></th>
		<th><?=t('Added On')?></th>
	</tr>
	
	</table>

</div>
<div id="ccm-file-properties-statistics-tab" style="display: none">
asdf2
</div>

<script type="text/javascript">
$(function() { ccm_alActiveEditableProperties(); });
</script>

<?
if (!isset($_REQUEST['reload'])) { ?>
</div>
<? }
