<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByPath("/dashboard/mediabrowser");
$cp = new Permissions($c);
$u = new User();
if (!$cp->canRead()) {
	die(_("Access Denied."));
}

$f = File::getByID($_REQUEST['fID']);
if (isset($_REQUEST['fvID'])) {

} else {
	$fv = $f->getActiveVersion();
}

?>

<h2><?=t('Basic Information')?></h2>
<table border="0" cellspacing="0" cellpadding="0" id="ccm-file-properties" class="ccm-grid">
<tr>
	<th><?=t('Filename')?></th>
	<td width="100%"><?=$fv->getFileName()?></td>
</tr>
<tr>
	<th><?=t('Title')?></th>
	<td><?=$fv->getTitle()?></td>
</tr>
<tr>
	<th><?=t('Size')?></th>
	<td><?=$fv->getSize()?> (<?=number_format($fv->getFullSize())?> <?=t('bytes')?>)</td>
</tr>
<tr>
	<th><?=t('Date Added')?></th>
	<td><?=$f->getDateAdded()?></td>
</tr>

</table>

