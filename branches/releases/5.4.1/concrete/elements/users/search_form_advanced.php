<?php  defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?php 
$searchFields = array(
	'' => '** ' . t('Fields'),
	'date_added' => t('Registered Between'),
	'is_active' => t('Activated Users')
);

Loader::model('user_attributes');
$searchFieldAttributes = UserAttributeKey::getSearchableList();
foreach($searchFieldAttributes as $ak) {
	$searchFields[$ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayHandle();
}


?>

<?php  $form = Loader::helper('form'); ?>

	
	<div id="ccm-user-search-field-base-elements" style="display: none">

		<span class="ccm-search-option"  search-field="date_added">
		<?php echo $form->text('date_from', array('style' => 'width: 86px'))?>
		<?php echo t('to')?>
		<?php echo $form->text('date_to', array('style' => 'width: 86px'))?>
		</span>

		<span class="ccm-search-option"  search-field="is_active">
		<?php echo $form->select('active', array('0' => t('Inactive Users'), '1' => t('Active Users')), array('style' => 'vertical-align: middle'))?>
		</span>
		
		<?php  foreach($searchFieldAttributes as $sfa) { 
			$sfa->render('search'); ?>
		<?php  } ?>
		
	</div>
	
	<form method="get" id="ccm-user-advanced-search" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/users/search_results">
	<?php echo $form->hidden('mode', $mode); ?>
	<div id="ccm-user-search-advanced-fields" class="ccm-search-advanced-fields" >
	
		<input type="hidden" name="search" value="1" />
		<div id="ccm-search-box-title">
			<img src="<?php echo ASSETS_URL_IMAGES?>/throbber_white_16.gif" width="16" height="16" class="ccm-search-loading"  id="ccm-user-search-loading" />
			<h2><?php echo t('Search')?></h2>			
		</div>
		
		<div id="ccm-search-advanced-fields-inner">
			<div class="ccm-search-field">
				<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
					<?php echo $form->label('keywords', t('Username/Email Address'))?>
					<?php echo $form->text('keywords', array('style' => 'width:200px')); ?>
					</td>
				</tr>
				</table>
			</div>
		
			<div class="ccm-search-field">
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td style="white-space: nowrap" align="right"><div style="width: 85px; padding-right:5px"><?php echo t('Results Per Page')?></div></td>
					<td width="100%">
						<?php echo $form->select('numResults', array(
							'10' => '10',
							'25' => '25',
							'50' => '50',
							'100' => '100',
							'500' => '500'
						), false, array('style' => 'width:65px'))?>
					</td>
					<td><a href="javascript:void(0)" id="ccm-user-search-add-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" /></a></td>
				</tr>	
				</table>
			</div>
			
			<div id="ccm-search-field-base">				
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top" style="padding-right: 4px">
						<?php echo $form->select('searchField', $searchFields, array('style' => 'width: 85px'));
						?>
						<input type="hidden" value="" class="ccm-user-selected-field" name="selectedSearchField[]" />
						</td>
						<td width="100%" valign="top" class="ccm-selected-field-content">
						<?php echo t('Select Search Field.')?>
						</td>
						<td valign="top">
						<a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a>
						</td>
					</tr>
				</table>
			</div>
			
			<div id="ccm-search-fields-wrapper">			
			</div>
			
			<div id="ccm-search-fields-submit">
				<div id="ccm-search-export"><a href="javascript:void(0)" onclick="$('#ccm-user-advanced-search').attr('action', '<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/users/search_results_export'); $('#ccm-user-advanced-search').get(0).submit(); $('#ccm-user-advanced-search').attr('action', '<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/users/search_results');"><?php echo t('Export All')?></a></div>
				<?php echo $form->submit('ccm-search-users', 'Search')?>
			</div>
		</div>
	
</div>

<?php  
Loader::model('search/group');
$gl = new GroupSearch();
$gl->setItemsPerPage(-1);
$g1 = $gl->getPage();

if (count($g1) > 0) { ?>

<div id="ccm-search-advanced-sets">
	<h2><?php echo t('Filter by Group')?></h2>
	<div style="max-height: 200px; overflow: auto">
	<?php  foreach($g1 as $g) { ?>
		<div class="ccm-user-search-advanced-groups-cb"><?php echo $form->checkbox('gID[' . $g['gID'] . ']', $g['gID'] )?> <?php echo $form->label('gID[' . $g['gID']  . ']', $g['gName'] )?></div>
	<?php  } ?>
	</div>
	
</div>

<?php  } ?>
</form>	

<script type="text/javascript">
ccm_setupUserSearch();
</script>
