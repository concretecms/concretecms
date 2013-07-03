<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
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
$text = Loader::helper('text');

Loader::model('file_attributes');
$searchFieldAttributes = FileAttributeKey::getSearchableList();
foreach($searchFieldAttributes as $ak) {
	$searchFields[$ak->getAttributeKeyID()] = tc('AttributeKeyName', $ak->getAttributeKeyName());
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

		<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_added">
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

	<form method="get" class="form-horizontal" id="ccm-<?=$searchInstance?>-advanced-search" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/search_results">
	<? if ($_REQUEST['fType'] != false) {
		$showTypes = array();
		if(is_array($_REQUEST['fType'])) {
			foreach($_REQUEST['fType'] as $showTypeId) {
				$showTypes[] = FileType::getGenericTypeText($showTypeId);
			}
		}
		else {
			$showTypes[] = FileType::getGenericTypeText($_REQUEST['fType']);
		}
		?>
		<div class="ccm-file-manager-pre-filter"><?=t('Only displaying %s files.', implode(', ', $showTypes))?></div>
	<? } else if ($_REQUEST['fExtension'] != false) {
		if(is_array($_REQUEST['fExtension'])) {
			$showExtensions = $_REQUEST['fExtension'];
		}
		else {
			$showExtensions = array($_REQUEST['fExtension']);
		}
		?>
		<div class="ccm-file-manager-pre-filter"><?=t('Only displaying files with extension .%s.', implode(', ', $showExtensions))?></div>
	<? } ?>

	<input type="hidden" name="submit_search" value="1" />
	<?
		foreach(array('fType', 'fExtension') as $filterName) {
			$filterValues = '';
			if(is_array($_REQUEST[$filterName])) {
				foreach($_REQUEST[$filterName] as $filterValue) {
					print '<input type="hidden" name="' . $filterName . '[]" value="' . $text->entities($filterValue) . '" />';
				}
			}
			else {
				print $form->hidden($filterName);
			}
		}
		print $form->hidden('searchType', $searchType); 
		print $form->hidden('ccm_order_dir', $searchRequest['ccm_order_dir']); 
		print $form->hidden('ccm_order_by', $searchRequest['ccm_order_by']); 
		print $form->hidden('fileSelector', $fileSelector); 
	?>	
	<input type="hidden" name="searchInstance" value="<?=$searchInstance?>" />
	<div class="ccm-pane-options-permanent-search">

	<?
		$s2 = FileSet::getSavedSearches();
		if (count($s2) > 0) { 
			if ($_REQUEST['fssID'] < 1) {
				$savedSearches = array('' => t('** Select a saved search.'));
			} else {
				$savedSearches = array('' => t('** None (Exit Saved Search)'));
			}
			
			foreach($s2 as $fss) {
				$savedSearches[$fss->getFileSetID()] = $fss->getFileSetName();
			}
		?>
			<div class="control-group">
			<?=$form->label('fssID', t('Saved Search'))?>
			<div class="controls">
				<?=$form->select('fssID', $savedSearches, $fssID, array('class' => 'span3', 'style' => 'vertical-align: middle'))?>
				<? if ($_REQUEST['fssID'] > 0) { ?>
					<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/delete_set?fsID=<?=$_REQUEST['fssID']?>&searchInstance=<?=$searchInstance?>" class="ccm-file-set-delete-saved-search" dialog-append-buttons="true" dialog-title="<?=t('Delete File Set')?>" dialog-width="320" dialog-height="110" dialog-modal="false" style="vertical-align: middle"><img src="<?=ASSETS_URL_IMAGES?>/icons/delete_small.png" style="vertical-align: middle" width="16" height="16" border="0" /></a>
				<? } ?>
			</div>
			</div>
			
		<? } ?>

		<div class="span3">
		<?=$form->label('fvKeywords', t('Keywords'))?>
		<div class="controls">
			<?=$form->text('fKeywords', $searchRequest['fKeywords'], array('style'=> 'width: 130px')); ?>
		</div>
		</div>
		
		<div id="ccm-<?=$searchInstance?>-sets-search-wrapper">
		<?
		$s1 = FileSet::getMySets();
		if (count($s1) > 0) { ?>
		<div class="span4" style="width: 280px">
			<?=$form->label('fsID', t('In Set(s)'))?>
			<? if ($_REQUEST['fssID'] > 0) { ?>
			
				<div class="controls">
					<? foreach($s1 as $s) { ?>
						<? if (is_array($searchRequest['fsID']) && in_array($s->getFileSetID(), $searchRequest['fsID'])) { ?>
						<label class="checkbox">
						<input type="checkbox"  checked disabled><?=wordwrap($s->getFileSetName(), '23', '&shy;', true)?>
						</label>
						<? } ?>
					<? } ?>
					<? if (is_array($searchRequest['fsID']) && in_array(-1, $searchRequest['fsID'])) { ?>					
					<label class="checkbox">
					<input type="checkbox"  checked disabled><?=t('Files in no sets.')?>
					</label>
					<? } ?> 
				</div>

			<? } else { ?>
			
			<div class="input">
				<select multiple name="fsID[]" class="chosen-select">
					<optgroup label="<?=t('Sets')?>">
					<? foreach($s1 as $s) { ?>
						<option value="<?=$s->getFileSetID()?>"  <? if (is_array($searchRequest['fsID']) && in_array($s->getFileSetID(), $searchRequest['fsID'])) { ?> selected="selected" <? } ?>><?=wordwrap($s->getFileSetName(), '23', '&shy;', true)?></option>
					<? } ?>
					</optgroup>
					<optgroup label="<?=t('Other')?>">
						<option value="-1" <? if (is_array($searchRequest['fsID']) && in_array(-1, $searchRequest['fsID'])) { ?> selected="selected" <? } ?>><?=t('Files in no sets.')?></option>
					</optgroup>
				</select>
			</div>
			
			<? } ?>
		</div>
		<? } ?>
		</div>
		
		<div class="span3">
		<?=$form->label('numResults', t('# Per Page'))?>
		<div class="input">
			<?=$form->select('numResults', array(
				'10' => '10',
				'25' => '25',
				'50' => '50',
				'100' => '100',
				'500' => '500'
			), $searchRequest['numResults'], array('style' => 'width:65px'))?>

		</div>
		<?=$form->submit('ccm-search-files', t('Search'), array('style' => 'margin-left: 10px'))?>

		</div>

		
	</div>
	
	<a href="javascript:void(0)" onclick="ccm_paneToggleOptions(this)" class="ccm-icon-option-<? if (is_array($searchRequest['selectedSearchField']) && count($searchRequest['selectedSearchField']) > 1) { ?>open<? } else { ?>closed<? } ?>"><?=t('Advanced Search')?></a>
	<div class="control-group ccm-pane-options-content" <? if (is_array($searchRequest['selectedSearchField']) && count($searchRequest['selectedSearchField']) > 1) { ?>style="display: block" <? } ?>>
		<br/>
		<table class="table table-striped ccm-search-advanced-fields" id="ccm-<?=$searchInstance?>-search-advanced-fields">
		<? if ($_REQUEST['fssID'] < 1) { ?>
		<tr>
			<th colspan="2" width="100%"><?=t('Additional Filters')?></th>
			<th style="text-align: right; white-space: nowrap"><a href="javascript:void(0)" id="ccm-<?=$searchInstance?>-search-add-option" class="ccm-advanced-search-add-field"><span class="ccm-menu-icon ccm-icon-view"></span><?=t('Add')?></a></th>
		</tr>
		<? } ?>
		<tr id="ccm-search-field-base">
			<td><?=$form->select('searchField', $searchFields);?></td>
			<td width="100%">
			<input type="hidden" value="" class="ccm-<?=$searchInstance?>-selected-field" name="selectedSearchField[]" />
			<div class="ccm-selected-field-content">
				<?=t('Select Search Field.')?>				
			</div></td>
			<? if ($_REQUEST['fssID'] < 1) { ?><td><a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a></td><? } ?>
		</tr>
		<? 
		$i = 1;
		if (is_array($searchRequest['selectedSearchField'])) { 
			foreach($searchRequest['selectedSearchField'] as $req) { 
				if ($req == '') {
					continue;
				}
				?>
				
				<tr class="ccm-search-field ccm-search-request-field-set" ccm-search-type="<?=$req?>" id="ccm-<?=$searchInstance?>-search-field-set<?=$i?>">
				<td><?=$form->select('searchField' . $i, $searchFields, $req); ?></td>
				<td width="100%"><input type="hidden" value="<?=$req?>" class="ccm-<?=$searchInstance?>-selected-field" name="selectedSearchField[]" />
					<div class="ccm-selected-field-content">
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
						<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_added">
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
					
					</div>
					</td>
					<? if ($_REQUEST['fssID'] < 1) { ?><td><a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a></td><? } ?>
					</tr>
				<? 
					$i++;
				} 
				
				} ?>
		</table>

		<? if ($_REQUEST['fssID'] < 1) { ?>
		<div id="ccm-search-fields-submit">
				<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/customize_search_columns?searchInstance=<?=$searchInstance?>" id="ccm-list-view-customize"><span class="ccm-menu-icon ccm-icon-properties"></span><?=t('Customize Results')?></a>
				<a class="ccm-search-save" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/save_search?searchInstance=<?=$searchInstance?>" id="ccm-<?=$searchInstance?>-launch-save-search" dialog-title="<?=t('Save Search')?>" dialog-width="320" dialog-height="200" dialog-modal="false"><span class="ccm-menu-icon ccm-icon-search-pages"></span><?=t('Save Search')?></a>
		</div>
		<? } ?>

	</div>
</form>	

<script type="text/javascript">$(function() {
	$('a#ccm-<?=$searchInstance?>-launch-save-search').dialog();
	$('a.ccm-file-set-delete-saved-search').dialog();
	
	<? if ($_REQUEST['fssID'] > 0) { ?>
	$('#ccm-<?=$searchInstance?>-advanced-search input, #ccm-<?=$searchInstance?>-advanced-search select, #ccm-<?=$searchInstance?>-advanced-search textarea').attr('disabled',true);
	$('#ccm-<?=$searchInstance?>-advanced-search select[name=fssID]').attr('disabled', false);
	<? } ?>
	
	$(".chosen-select").chosen();	

});</script>
