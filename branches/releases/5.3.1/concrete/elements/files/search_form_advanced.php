<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?php 
Loader::model('file_set');
$s1 = FileSet::getMySets();
$sets = array('' =>'** ' . t('All'));
foreach($s1 as $s) {
	$sets[$s->getFileSetID()] = $s->getFileSetName();
}

$searchFields = array(
	'' => '** ' . t('Fields'),
	'size' => t('Size'),
	'type' => t('Type'),
	'extension' => t('Extension'),
	'date_added' => t('Added Between'),
);

Loader::model('file_attributes');
$searchFieldAttributes = FileAttributeKey::getList();
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
$sets = array();
$sets[] = '** ' . t('All');
foreach($s1 as $s) {
	$sets[$s->getFileSetID()] = $s->getFileSetName();
}

?>

<?php  $form = Loader::helper('form'); ?>

<div id="ccm-file-search-advanced-fields" >
	
	<div id="ccm-file-search-field-base-elements" style="display: none">
	
		<span class="ccm-file-search-option" search-field="size">
		<?php echo $form->text('size_from', array('style' => 'width: 30px'))?>
		<?php echo t('to')?>
		<?php echo $form->text('size_to', array('style' => 'width: 30px'))?>
		KB
		</span>
	
		<span class="ccm-file-search-option"  search-field="type">
		<?php echo $form->select('type', $types)?>
		</span>
	
		<span class="ccm-file-search-option"  search-field="extension">
		<?php echo $form->select('extension', $extensions)?>
		</span>
	
		<span class="ccm-file-search-option"  search-field="date_added">
		<?php echo $form->text('date_from', array('style' => 'width: 86px'))?>
		<?php echo t('to')?>
		<?php echo $form->text('date_to', array('style' => 'width: 86px'))?>
		</span>
		
		<?php  foreach($searchFieldAttributes as $sfa) { ?>
			<span class="ccm-file-search-option ccm-file-search-option-type-<?php echo strtolower($sfa->getAttributeKeyType())?>" search-field="<?php echo $sfa->getAttributeKeyID()?>">
			<?php echo $sfa->outputSearchHTML()?>
			</span>
		<?php  } ?>	
	</div>
	
	
	<form method="get" class="ccm-dashboard-file-search" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/search_results">
		<input type="hidden" name="search" value="1" />
	<?php 	/** 
		 * Here are all the things that could be passed through the asset library that we need to account for, as hidden form fields
		 */
		print $form->hidden('fType'); 
		print $form->hidden('fileSelector', $fileSelector); 
	?>	
		<div id="ccm-search-box-title">
			<?php  if ($_REQUEST['fType'] != false) { ?>
				<div class="ccm-file-manager-pre-filter"><?php echo t('Only displaying %s files.', FileType::getGenericTypeText($_REQUEST['fType']))?></div>
			<?php  } ?>
	
			<img src="<?php echo ASSETS_URL_IMAGES?>/throbber_white_16.gif" width="16" height="16" id="ccm-file-search-loading" />
			
			<h2><?php echo t('Search')?></h2>
			
		</div>
		
		<div id="ccm-file-search-advanced-fields-inner">
			<div class="ccm-file-search-field">
				<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
					<?php echo $form->text('fKeywords', array('style' => 'width:200px')); ?>
					</td>
				</tr>
				</table>
			</div>
		
			<div class="ccm-file-search-field">
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td style="white-space: nowrap" align="right"><div style="width: 85px; padding-right:5px"><?php echo t('Results Per Page')?></div></td>
					<td width="100%">
						<?php echo $form->select('fNumResults', array(
							'10' => '10',
							'25' => '25',
							'50' => '50',
							'100' => '100',
							'500' => '500'
						), false, array('style' => 'width:65px'))?>
					</td>
					<td>&nbsp;</td>			
				</tr>	
				</table>
			</div>
			
			<div class="ccm-file-search-field">
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td style="white-space: nowrap" align="right"><div style="width: 85px; padding-right:5px"><?php echo t('Found in Set')?></div></td>
						<td width="100%">
							<?php echo $form->select('fSet', $sets, false, array('style' => 'width:95px'))?>
						</td>
						<td><a href="javascript:void(0)" id="ccm-file-search-add-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" /></a></td>
				
					</tr>	
				</table>
			</div>
			
			<div id="ccm-file-search-field-base">				
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top" style="padding-right: 4px">
						<?php echo $form->select('fvField', $searchFields, array('style' => 'width: 85px'));
						?>
						<input type="hidden" value="" class="ccm-file-selected-field" name="fvSelectedField[]" />
						</td>
						<td width="100%" valign="top" class="ccm-file-selected-field-content">
						<?php echo t('Select Search Field.')?>
						</td>
						<td valign="top">
						<a href="javascript:void(0)" class="ccm-file-search-remove-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a>
						</td>
					</tr>
				</table>
			</div>
			
			<div id="ccm-file-search-fields-wrapper">			
			</div>
			
			<div id="ccm-file-search-fields-submit">
				<?php echo $form->submit('ccm-search-files', 'Search')?>
			</div>
		</div>
	
	</form>	
</div>
