<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?
Loader::model('file_set');

$searchFields = array(
	'' => '** ' . t('Fields'),
	'size' => t('Size'),
	'type' => t('Type'),
	'extension' => t('Extension'),
	'date_added' => t('Added Between'),
);

Loader::model('file_attributes');
$searchFieldAttributes = FileAttributeKey::getSearchableList();
foreach($searchFieldAttributes as $ak) {
	$searchFields[$ak->getAttributeKeyID()] = $ak->getAttributeKeyName();
}

$ext1 = FileList::getExtensionList();
$extensions = array();
foreach($ext1 as $value) {
	$extensions[$value] = $value;
}

$t1 = FileList::getTypeList();
$types = array();
foreach($t1 as $value) {
	$types[$value] = FileType::getGenericTypeText($value);
}

$s1 = FileSet::getMySets();

?>

<? $form = Loader::helper('form'); ?>

	
	<div id="ccm-file-search-field-base-elements" style="display: none">
	
		<span class="ccm-search-option" search-field="size">
		<?=$form->text('size_from', array('style' => 'width: 30px'))?>
		<?=t('to')?>
		<?=$form->text('size_to', array('style' => 'width: 30px'))?>
		KB
		</span>
	
		<span class="ccm-search-option"  search-field="type">
		<?=$form->select('type', $types)?>
		</span>
	
		<span class="ccm-search-option"  search-field="extension">
		<?=$form->select('extension', $extensions)?>
		</span>

		<span class="ccm-search-option"  search-field="date_added">
		<?=$form->text('date_from', array('style' => 'width: 86px'))?>
		<?=t('to')?>
		<?=$form->text('date_to', array('style' => 'width: 86px'))?>
		</span>
		
		<? foreach($searchFieldAttributes as $sfa) { 
			$sfa->render('search'); ?>
		<? } ?>
		
	</div>
	
	<form method="get" id="ccm-file-advanced-search" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/search_results">

<div id="ccm-file-search-advanced-fields" class="ccm-search-advanced-fields" >
	
		<input type="hidden" name="search" value="1" />
	<?	/** 
		 * Here are all the things that could be passed through the asset library that we need to account for, as hidden form fields
		 */
		print $form->hidden('fType'); 
		print $form->hidden('fileSelector', $fileSelector); 
	?>	
		<div id="ccm-search-box-title">
			<? if ($_REQUEST['fType'] != false) { ?>
				<div class="ccm-file-manager-pre-filter"><?=t('Only displaying %s files.', FileType::getGenericTypeText($_REQUEST['fType']))?></div>
			<? } ?>
	
			<img src="<?=ASSETS_URL_IMAGES?>/throbber_white_16.gif" width="16" height="16" id="ccm-search-loading" />
			
			<h2><?=t('Search')?></h2>			
		</div>
		
		<div id="ccm-search-advanced-fields-inner">
			<div class="ccm-search-field">
				<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
					<?=$form->text('fKeywords', array('style' => 'width:200px')); ?>
					</td>
				</tr>
				</table>
			</div>
		
			<div class="ccm-search-field">
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td style="white-space: nowrap" align="right"><div style="width: 85px; padding-right:5px"><?=t('Results Per Page')?></div></td>
					<td width="100%">
						<?=$form->select('numResults', array(
							'10' => '10',
							'25' => '25',
							'50' => '50',
							'100' => '100',
							'500' => '500'
						), false, array('style' => 'width:65px'))?>
					</td>
					<td><a href="javascript:void(0)" id="ccm-file-search-add-option"><img src="<?=ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" /></a></td>
				</tr>	
				</table>
			</div>
			
			<div id="ccm-search-field-base">				
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top" style="padding-right: 4px">
						<?=$form->select('searchField', $searchFields, array('style' => 'width: 85px'));
						?>
						<input type="hidden" value="" class="ccm-file-selected-field" name="selectedSearchField[]" />
						</td>
						<td width="100%" valign="top" class="ccm-selected-field-content">
						<?=t('Select Search Field.')?>
						</td>
						<td valign="top">
						<a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a>
						</td>
					</tr>
				</table>
			</div>
			
			<div id="ccm-search-fields-wrapper">			
			</div>
			
			<div id="ccm-search-fields-submit">
				<?=$form->submit('ccm-search-files', 'Search')?>
			</div>
		</div>
	
</div>

<? if (count($s1) > 0) { ?>

<div id="ccm-search-advanced-sets">
	<h2><?=t('Filter by File Set')?></h2>
	<div style="max-height: 200px; overflow: auto">
	<? foreach($s1 as $fs) { ?>
		<div class="ccm-file-search-advanced-sets-cb"><?=$form->checkbox('fsID[' . $fs->getFileSetID() . ']', $fs->getFileSetID())?> <?=$form->label('fsID[' . $fs->getFileSetID() . ']', $fs->getFileSetName())?></div>
	<? } ?>
	</div>
	
	<hr/>
	
	<div><?=$form->checkbox('fsIDNone', '1')?> <?=$form->label('fsIDNone', t('Display files in no sets.'))?></div>
</div>

<? } ?>
</form>	
