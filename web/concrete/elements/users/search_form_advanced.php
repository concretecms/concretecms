<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?
$searchFields = array(
	'' => '** ' . t('Fields'),
	'date_added' => t('Registered Between'),
	'is_active' => t('Activated Users')
);

if (PERMISSIONS_MODEL == 'advanced') { 
	$searchFields['group_set'] = t('Group Set');
}

Loader::model('user_attributes');
$searchFieldAttributes = UserAttributeKey::getSearchableList();
foreach($searchFieldAttributes as $ak) {
	$searchFields[$ak->getAttributeKeyID()] = tc('AttributeKeyName', $ak->getAttributeKeyName());
}


?>

<? $form = Loader::helper('form'); ?>

	
	<div id="ccm-user-search-field-base-elements" style="display: none">

		<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_added">
		<?=$form->text('date_from', array('style' => 'width: 86px'))?>
		<?=t('to')?>
		<?=$form->text('date_to', array('style' => 'width: 86px'))?>
		</span>

		<span class="ccm-search-option"  search-field="is_active">
		<?=$form->select('active', array('0' => t('Inactive Users'), '1' => t('Active Users')), array('style' => 'vertical-align: middle'))?>
		</span>
		
		<? if (PERMISSIONS_MODEL == 'advanced') { 
			$gsl = new GroupSetList();
			$groupsets = array();
			foreach($gsl->get() as $gs) { 
				$groupsets[$gs->getGroupSetID()] = $gs->getGroupSetName();
			}
		?>
		<span class="ccm-search-option"  search-field="group_set">
		<?=$form->select('gsID', $groupsets)?>
		</span>
		<? } ?>
		
		<? foreach($searchFieldAttributes as $sfa) { 
			$sfa->render('search'); ?>
		<? } ?>
		
	</div>
	
	<form class="form-horizontal" method="get" id="ccm-user-advanced-search" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/users/search_results">
	<?=$form->hidden('mode', $mode); ?>
	<?=$form->hidden('searchType', $searchType); ?>
	<input type="hidden" name="search" value="1" />
	
	<div class="ccm-pane-options-permanent-search">

		<div class="span3">
		<?=$form->label('keywords', t('Keywords'))?>
		<div class="controls">
			<?=$form->text('keywords', $_REQUEST['keywords'], array('placeholder' => t('Username or Email'), 'style'=> 'width: 140px')); ?>
		</div>
		</div>
				
		<? 
		$pk = PermissionKey::getByHandle('access_user_search');
		Loader::model('search/group');
		$gl = new GroupSearch();
		$gl->setItemsPerPage(-1);
		$g1 = $gl->getPage();
		?>		

		<div class="span4" >
			<?=$form->label('gID', t('Group(s)'))?>
			<div class="controls">
				<select multiple name="gID[]" class="chosen-select" style="width: 200px">
					<? foreach($g1 as $g) {
						if ($pk->validate($g['gID'])) { ?>
						<option value="<?=$g['gID']?>"  <? if (is_array($_REQUEST['gID']) && in_array($g['gID'], $_REQUEST['gID'])) { ?> selected="selected" <? } ?>><?=$g['gName']?></option>
					<? 
						}
					} ?>
				</select>
			</div>
		</div>
		
		<div class="span3" style="width: 300px; white-space: nowrap">
		<?=$form->label('numResults', t('# Per Page'))?>
		<div class="controls">
			<?=$form->select('numResults', array(
				'10' => '10',
				'25' => '25',
				'50' => '50',
				'100' => '100',
				'500' => '500'
			), $_REQUEST['numResults'], array('style' => 'width:65px'))?>
		</div>

		<?=$form->submit('ccm-search-users', t('Search'), array('style' => 'margin-left: 10px'))?>

		</div>
		
	</div>

	<a href="javascript:void(0)" onclick="ccm_paneToggleOptions(this)" class="ccm-icon-option-closed"><?=t('Advanced')?></a>
	<div class="clearfix ccm-pane-options-content">
		<br/>
		<table class="table table-bordered table-striped ccm-search-advanced-fields" id="ccm-user-search-advanced-fields">
		<tr>
			<th colspan="2" width="100%"><?=t('Additional Filters')?></th>
			<th style="text-align: right; white-space: nowrap"><a href="javascript:void(0)" id="ccm-user-search-add-option" class="ccm-advanced-search-add-field"><span class="ccm-menu-icon ccm-icon-view"></span><?=t('Add')?></a></th>
		</tr>
		<tr id="ccm-search-field-base">
			<td><?=$form->select('searchField', $searchFields);?></td>
			<td width="100%">
			<input type="hidden" value="" class="ccm-user-selected-field" name="selectedSearchField[]" />
			<div class="ccm-selected-field-content">
				<?=t('Select Search Field.')?>				
			</div></td>
			<td><a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a></td>
		</tr>

		</table>

		<div id="ccm-search-fields-submit">
			<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/users/customize_search_columns" id="ccm-list-view-customize"><span class="ccm-menu-icon ccm-icon-properties"></span><?=t('Customize Results')?></a>
		</div>

	</div>	

</form>	

<script type="text/javascript">
$(function() { 
	ccm_setupUserSearch('<?=$searchInstance?>'); 
});
</script>
