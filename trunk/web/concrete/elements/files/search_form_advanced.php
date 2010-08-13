<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?
Loader::model('file_set');

$searchFields = array(
	'' => '** ' . t('Fields'),
	'size' => t('Size'),
	'type' => t('Type'),
	'extension' => t('Extension'),
	'date_added' => t('Added Between'),
	'added_to' => t('Added to Page')
);

if ($_REQUEST['fType'] != false) {
	unset($searchFields['type']);
}
if ($_REQUEST['fExtension'] != false) {
	unset($searchFields['extension']);
}

$html = Loader::helper('html');

Loader::model('file_attributes');
$searchFieldAttributes = FileAttributeKey::getSearchableList();
foreach($searchFieldAttributes as $ak) {
	$searchFields[$ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayHandle();
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

?>

<? $form = Loader::helper('form'); ?>

	
	<div id="ccm-<?=$searchInstance?>-search-field-base-elements" style="display: none">
	
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

		<span class="ccm-search-option" search-field="added_to">
		<div style="width: 100px">
		<? $ps = Loader::helper("form/page_selector");
		print $ps->selectPage('ocIDSearchField');
		?>
		</div>
		</span>
		
		<? foreach($searchFieldAttributes as $sfa) { 
			$sfa->render('search'); ?>
		<? } ?>
		
	</div>
	<? /*
	
	<? if ($searchType == 'DASHBOARD') { ?>
		<form method="get" id="ccm-file-advanced-search" action="<?=$this->url('/dashboard/files/search')?>">
	<? } else { ?>
		<form method="get" id="ccm-file-advanced-search" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/search_results">
	<? } ?>
	
	*/
	?>

	<form method="get" id="ccm-<?=$searchInstance?>-advanced-search" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/search_results">

<?
$s2 = FileSet::getSavedSearches();
if (count($s2) > 0) { 
	if ($_REQUEST['fssID'] < 1) {
		$savedSearches = array('' => t('** Select a saved search.'));
	}
	
	foreach($s2 as $fss) {
		$savedSearches[$fss->getFileSetID()] = $fss->getFileSetName();
	}
?>
	<div class="ccm-search-advanced-fields">
		<h2><?=t('Saved Searches')?></h2>
		
		<?=$form->select('fssID', $savedSearches, $fssID, array('style' => 'vertical-align: middle'))?>
		<? if (isset($_REQUEST['fssID'])) { ?>
			<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/delete_set?fsID=<?=$_REQUEST['fssID']?>&searchInstance=<?=$searchInstance?>" class="ccm-file-set-delete-saved-search" dialog-title="<?=t('Delete File Set')?>" dialog-width="320" dialog-height="200" dialog-modal="false" style="vertical-align: middle"><img src="<?=ASSETS_URL_IMAGES?>/icons/delete_small.png" style="vertical-align: middle" width="16" height="16" border="0" /></a>
		<? } ?>
		
		<? if (isset($_REQUEST['fssID'])) { ?>
			<br/><br/>
			
			<a class="ccm-search-saved-exit" href="#" onclick="javascript:void(0)"><?=t("&lt; Exit Saved Search")?></a>
		<? } ?>
	</div>
	<br/>
	
<? } ?>

<input type="hidden" name="searchInstance" value="<?=$searchInstance?>" />
	
<div id="ccm-<?=$searchInstance?>-search-advanced-fields" class="ccm-search-advanced-fields" >
	
		<input type="hidden" name="submit_search" value="1" />
	<?	/** 
		 * Here are all the things that could be passed through the asset library that we need to account for, as hidden form fields
		 */
		print $form->hidden('fType'); 
		print $form->hidden('fExtension'); 
		print $form->hidden('ccm_order_dir', $searchRequest['ccm_order_dir']); 
		print $form->hidden('ccm_order_by', $searchRequest['ccm_order_by']); 
		print $form->hidden('fileSelector', $fileSelector); 
	?>	
		<div id="ccm-search-box-title">
			<? if ($_REQUEST['fType'] != false) { ?>
				<div class="ccm-file-manager-pre-filter"><?=t('Only displaying %s files.', FileType::getGenericTypeText($_REQUEST['fType']))?></div>
			<? } else if ($_REQUEST['fExtension'] != false) { ?>
				<div class="ccm-file-manager-pre-filter"><?=t('Only displaying files with extension .%s.', $_REQUEST['fExtension'])?></div>
			<? } ?>
	
			<img src="<?=ASSETS_URL_IMAGES?>/throbber_white_16.gif" width="16" height="16" class="ccm-search-loading" id="ccm-<?=$searchInstance?>-search-loading" />
			
			<h2><?=t('Search')?></h2>			
		</div>
		
		<div id="ccm-search-advanced-fields-inner">
			<div class="ccm-search-field">
				<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
					<?=$form->text('fKeywords', $searchRequest['fKeywords'], array('style' => 'width:200px')); ?>
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
						), $searchRequest['numResults'], array('style' => 'width:65px'))?>
					</td>
					<td><? if ($_REQUEST['fssID'] < 1) { ?><a href="javascript:void(0)" id="ccm-<?=$searchInstance?>-search-add-option"><img src="<?=ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" /></a><? } ?></td>
				</tr>	
				</table>
			</div>
			
			<div id="ccm-search-field-base">				
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top" style="padding-right: 4px">
						<?=$form->select('searchField', $searchFields, array('style' => 'width: 85px'));
						?>
						<input type="hidden" value="" class="ccm-<?=$searchInstance?>-selected-field" name="selectedSearchField[]" />
						</td>
						<td width="100%" valign="top" class="ccm-selected-field-content">
						<?=t('Select Search Field.')?>
						</td>
						<td valign="top">
						<? if ($_REQUEST['fssID'] < 1) { ?><a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a><? } ?>
						</td>
					</tr>
				</table>
			</div>
			
			<div id="ccm-search-fields-wrapper">
			<? 
			$i = 1;
			if (is_array($searchRequest['selectedSearchField'])) { 
				foreach($searchRequest['selectedSearchField'] as $req) { 
					
					if ($req == '') {
						continue;
					}
					
					?>
					
					<div class="ccm-search-field ccm-search-request-field-set" ccm-search-type="<?=$req?>" id="ccm-<?=$searchInstance?>-search-field-set<?=$i?>">
					<table border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="top" style="padding-right: 4px">
							<?=$form->select('searchField' . $i, $searchFields, $req, array('style' => 'width: 85px')); ?>
							<input type="hidden" value="<?=$req?>" class="ccm-<?=$searchInstance?>-selected-field" name="selectedSearchField[]" />
							</td>
							<td width="100%" valign="top" class="ccm-selected-field-content">
							<? if ($req == 'size') { ?>
								<span class="ccm-search-option" search-field="size">
								<?=$form->text('size_from', $searchRequest['size_from'], array('style' => 'width: 30px'))?>
								<?=t('to')?>
								<?=$form->text('size_to', $searchRequest['size_to'], array('style' => 'width: 30px'))?>
								KB
								</span>
							<? } ?>
							
							<? if ($req == 'type') { ?>
								<span class="ccm-search-option"  search-field="type">
								<?=$form->select('type', $types, $searchRequest['type'])?>
								</span>
							<? } ?>
							
							<? if ($req == 'extension') { ?>
								<span class="ccm-search-option"  search-field="extension">
								<?=$form->select('extension', $extensions, $searchRequest['extension'])?>
								</span>
							<? } ?>
							
							<? if ($req == 'date_added') { ?>
								<span class="ccm-search-option"  search-field="date_added">
								<?=$form->text('date_from', $searchRequest['date_from'], array('style' => 'width: 86px'))?>
								<?=t('to')?>
								<?=$form->text('date_to', $searchRequest['date_to'], array('style' => 'width: 86px'))?>
								</span>
							<? } ?>

							<? if ($req == 'added_to') { ?>
							<span class="ccm-search-option" search-field="parent">
							<div style="width: 100px">
							<? $ps = Loader::helper("form/page_selector");
							print $ps->selectPage('ocIDSearchField', $searchRequest['ocIDSearchField']);
							?>
							</div>
							</span>
							<? } ?>
							
							<? foreach($searchFieldAttributes as $sfa) { 
								if ($sfa->getAttributeKeyID() == $req) {
									$at = $sfa->getAttributeType();
									$at->controller->setRequestArray($searchRequest);
									$at->render('search', $sfa);
								}
							} ?>
							</td>
							<td valign="top">
							<? if ($_REQUEST['fssID'] < 1) { ?><a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a><? } ?>
							</td>
						</tr>
					</table>
					</div>
					
				<? 
					$i++;
				}
			}?>
			
			</div>
			
			<div id="ccm-search-fields-submit">
				<? if ($_REQUEST['fssID'] < 1) { ?><div id="ccm-search-save"><a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/save_search?searchInstance=<?=$searchInstance?>" id="ccm-<?=$searchInstance?>-launch-save-search" dialog-title="<?=t('Save Search')?>" dialog-width="320" dialog-height="200" dialog-modal="false"><?=t('Save Search')?></a></div><? } ?>
				<?=$form->submit('ccm-search-files', 'Search')?>
			</div>
		</div>
	
</div>

<div id="ccm-<?=$searchInstance?>-sets-search-wrapper">
	<? Loader::element('files/search_form_sets', array('searchInstance' => $searchInstance, 'searchRequest' => $searchRequest)) ?>
</div>

</form>	

<script type="text/javascript">$(function() {
	$('a#ccm-<?=$searchInstance?>-launch-save-search').dialog();
	$('a.ccm-file-set-delete-saved-search').dialog();
	
	<? if ($_REQUEST['fssID'] > 0) { ?>
	$('#ccm-<?=$searchInstance?>-advanced-search input, #ccm-<?=$searchInstance?>-advanced-search select, #ccm-<?=$searchInstance?>-advanced-search textarea').attr('disabled',true);
	$('#ccm-<?=$searchInstance?>-advanced-search select[name=fssID]').attr('disabled', false);
	<? } ?>
	

});</script>
