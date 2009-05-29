<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?
Loader::model('file_set');
$s1 = FileSet::getMySets();
$sets = array('' =>'** ' . t('All'));
foreach($s1 as $s) {
	$sets[$s->getFileSetID()] = $s->getFileSetName();
}
?>
<? $form = Loader::helper('form'); ?>
<div id="ccm-file-manager-search-simple">
<? if ($_REQUEST['fType'] != false) { ?>
	<div class="ccm-file-manager-pre-filter"><?=t('Only displaying %s files.', FileType::getGenericTypeText($_REQUEST['fType']))?></div>
<? } ?>
<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td valign="top">
	<? /* I'm not proud of this */ ?>
	<form method="get" class="ccm-dashboard-file-search" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/search_results">
	<?
	/** 
	 * Here are all the things that could be passed through the asset library that we need to account for, as hidden form fields
	 */
	print $form->hidden('fType'); 
	?>

	<input type="hidden" name="search" value="1" />
	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top">
			<div style="position: relative">
			<div><?=$form->label('fKeywords', t('Search'))?></div>
			<div><?=$form->text('fKeywords', array('style' => 'width:145px')); ?></div>
			<img src="<?=ASSETS_URL_IMAGES?>/throbber_white_16.gif" width="16" height="16" id="ccm-file-search-loading" />
			</div>
		</td>
		<td valign="top">
			<div><?=$form->label('fsID', t('Set'))?></div>
			<div style="white-space: nowrap"><?=$form->select('fsID', $sets)?>&nbsp;<?=$form->submit('ccm-search-files', t('Go'))?></div>
		</td>
	</tr>
	</table>
	</form>
	</td>
	<td valign="top"><? Loader::element('files/upload_single'); ?></td>
</tr>
</table>

</div>

<script type="text/javascript">
$(function() {
	ccm_activateFileManager();
});
</script>