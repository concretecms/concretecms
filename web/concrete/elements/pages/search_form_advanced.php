<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?

$searchFields = array(
	'' => '** ' . t('Fields'),
	'keywords' => t('Full Page Index'),
	'date_added' => t('Date Added'),
	'theme' => t('Theme'),
	'last_modified' => t('Last Modified'),
	'date_public' => t('Public Date'),
	'owner' => t('Page Owner'),
	'num_children' => t('# Children'),
	'version_status' => t('Approved Version')
);

if (!$searchDialog) {
	$searchFields['parent'] = t('Parent Page');
}

Loader::model('attribute/categories/collection');
$searchFieldAttributes = CollectionAttributeKey::getSearchableList();
foreach($searchFieldAttributes as $ak) {
	$searchFields[$ak->getAttributeKeyID()] = tc('AttributeKeyName', $ak->getAttributeKeyName());
}

?>

<? $form = Loader::helper('form'); ?>
	
	<div id="ccm-<?=$searchInstance?>-search-field-base-elements" style="display: none">
	
		<span class="ccm-search-option"  search-field="keywords">
		<?=$form->text('keywords', $searchRequest['keywords'], array('style' => 'width: 120px'))?>
		</span>

		<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_public">
		<?=$form->text('date_public_from', array('style' => 'width: 86px'))?>
		<?=t('to')?>
		<?=$form->text('date_public_to', array('style' => 'width: 86px'))?>
		</span>

		<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_added">
		<?=$form->text('date_added_from', array('style' => 'width: 86px'))?>
		<?=t('to')?>
		<?=$form->text('date_added_to', array('style' => 'width: 86px'))?>
		</span>

		<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="last_modified">
		<?=$form->text('last_modified_from', array('style' => 'width: 86px'))?>
		<?=t('to')?>
		<?=$form->text('last_modified_to', array('style' => 'width: 86px'))?>
		</span>

		<span class="ccm-search-option"  search-field="owner">
		<?=$form->text('owner', array('class'=>'span5'))?>
		</span>

		<span class="ccm-search-option"  search-field="version_status">
		<label class="checkbox"><?=$form->radio('cvIsApproved', 0, false)?> <span><?=t('Unapproved')?></span></label>
		<label class="checkbox"><?=$form->radio('cvIsApproved', 1, false)?> <span><?=t('Approved')?></span></label>
		</span>
			
		<? if (!$searchDialog) { ?>
		<span class="ccm-search-option" search-field="parent">

		<? $ps = Loader::helper("form/page_selector");
		print $ps->selectPage('cParentIDSearchField');
		?>
		
		<br/><strong><?=t('Search All Children?')?></strong><br/>
		<label class="checkbox"><?=$form->radio('cParentAll', 0, false)?> <span><?=t('No')?></span></label>
		<label class="checkbox"><?=$form->radio('cParentAll', 1, false)?> <span><?=t('Yes')?></span></label>
		</span>
		<? } ?>
		<span class="ccm-search-option"  search-field="num_children">
			<select name="cChildrenSelect">
				<option value="gt"<? if ($req['cChildrenSelect'] == 'gt') { ?> selected <? } ?>><?=t('More Than')?></option>
				<option value="eq" <? if ($req['cChildrenSelect'] == 'eq') { ?> selected <? } ?>><?=t('Equal To')?></option>
				<option value="lt"<? if ($req['cChildrenSelect'] == 'lt') { ?> selected <? } ?>><?=t('Fewer Than')?></option>
			</select>
			<input type="text" name="cChildren" value="<?=$req['cChildren']?>" />
		</span>
		
		<span class="ccm-search-option"  search-field="theme">
			<select name="ptID">
			<? $themes = PageTheme::getList(); ?>
			<? foreach($themes as $pt) { ?>
				<option value="<?=$pt->getThemeID()?>"><?=$pt->getThemeName()?></option>			
			<? } ?>
			</select>
		</span>		
		
		<? foreach($searchFieldAttributes as $sfa) { 
			$sfa->render('search'); ?>
		<? } ?>
		
	</div>

	<form method="get" id="ccm-<?=$searchInstance?>-advanced-search" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/search_results" class="form-horizontal">

	<input type="hidden" name="searchInstance" value="<?=$searchInstance?>" />

	<div class="ccm-pane-options-permanent-search">
	
		<input type="hidden" name="submit_search" value="1" />
	<?	
		print $form->hidden('ccm_order_dir', $searchRequest['ccm_order_dir']); 
		print $form->hidden('ccm_order_by', $searchRequest['ccm_order_by']); 
		if ($searchDialog) {
			print $form->hidden('searchDialog', true);
		}
		if ($sitemap_select_mode) {
			print $form->hidden('sitemap_select_mode', $sitemap_select_mode);
		}
		if ($sitemap_select_callback) {
			print $form->hidden('sitemap_select_callback', $sitemap_select_callback);
		}
		if ($sitemap_display_mode) {
			print $form->hidden('sitemap_display_mode', $sitemap_display_mode);
		}
	?>

		<div class="span3">
		<?=$form->label('cvName', t('Page Name'))?>
		<div class="controls">
			<?=$form->text('cvName', $searchRequest['cvName'], array('style'=> 'width: 120px')); ?>
		</div>
		</div>

		<div class="span3">
		<?=$form->label('ctID', t('Page Type'))?>
		<div class="controls">
			<? 
			Loader::model('collection_types');
			$ctl = CollectionType::getList();
			$ctypes = array('' => t('** All'));
			foreach($ctl as $ct) {
				$ctypes[$ct->getCollectionTypeID()] = $ct->getCollectionTypeName();
			}
			
			print $form->select('ctID', $ctypes, $searchRequest['ctID'], array('style' => 'width:120px'))?>

		</div>
		</div>

		<div class="span3">
		<?=$form->label('numResults', t('# Per Page'))?>
		<div class="controls">
			<?=$form->select('numResults', array(
				'10' => '10',
				'25' => '25',
				'50' => '50',
				'100' => '100',
				'500' => '500'
			), $searchRequest['numResults'], array('style' => 'width:65px'))?>
		</div>
		<?=$form->submit('ccm-search-pages', t('Search'), array('style' => 'margin-left: 10px'))?>
		</div>

	</div>
	<a href="javascript:void(0)" onclick="ccm_paneToggleOptions(this)" class="ccm-icon-option-<? if (is_array($searchRequest['selectedSearchField']) && count($searchRequest['selectedSearchField']) > 1) { ?>open<? } else { ?>closed<? } ?>"><?=t('Advanced Search')?></a>
	<div class="clearfix ccm-pane-options-content" <? if (is_array($searchRequest['selectedSearchField']) && count($searchRequest['selectedSearchField']) > 1) { ?>style="display: block" <? } ?>>
		<br/>
		<table class="table-striped table ccm-search-advanced-fields" id="ccm-<?=$searchInstance?>-search-advanced-fields">
		<tr>
			<th colspan="2" width="100%"><?=t('Additional Filters')?></th>
			<th style="text-align: right; white-space: nowrap"><a href="javascript:void(0)" id="ccm-<?=$searchInstance?>-search-add-option" class="ccm-advanced-search-add-field"><span class="ccm-menu-icon ccm-icon-view"></span><?=t('Add')?></a></th>
		</tr>
		<tr id="ccm-search-field-base">
			<td><?=$form->select('searchField', $searchFields);?></td>
			<td width="100%">
			<input type="hidden" value="" class="ccm-<?=$searchInstance?>-selected-field" name="selectedSearchField[]" />
			<div class="ccm-selected-field-content">
				<?=t('Select Search Field.')?>				
			</div></td>
			<td><a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a></td>
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
						<? if ($req == 'date_public') { ?>
							<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_public">
							<?=$form->text('date_public_from', $searchRequest['date_public_from'], array('style' => 'width: 86px'))?>
							<?=t('to')?>
							<?=$form->text('date_public_to', $searchRequest['date_public_to'], array('style' => 'width: 86px'))?>
							</span>
						<? } ?>

						<? if ($req == 'keywords') { ?>
							<span class="ccm-search-option"  search-field="keywords">
							<?=$form->text('keywords', $searchRequest['keywords'], array('style' => 'width: 120px'))?>
							</span>
						<? } ?>

						<? if ($req == 'date_added') { ?>
							<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_added">
							<?=$form->text('date_added_from', $searchRequest['date_added_from'], array('style' => 'width: 86px'))?>
							<?=t('to')?>
							<?=$form->text('date_added_to', $searchRequest['date_added_to'], array('style' => 'width: 86px'))?>
							</span>
						<? } ?>

						<? if ($req == 'owner') { ?>
							<span class="ccm-search-option"  search-field="owner">
							<?=$form->text('owner', $searchRequest['owner'], array('class' => 'span5'))?>
							</span>
						<? } ?>

						<? if ($req == 'num_children') { ?>
							<span class="ccm-search-option"  search-field="num_children">
							<select name="cChildrenSelect">
								<option value="gt"<? if ($searchRequest['cChildrenSelect'] == 'gt') { ?> selected <? } ?>><?=t('More Than')?></option>
								<option value="eq" <? if ($searchRequest['cChildrenSelect'] == 'eq') { ?> selected <? } ?>><?=t('Equal To')?></option>
								<option value="lt"<? if ($searchRequest['cChildrenSelect'] == 'lt') { ?> selected <? } ?>><?=t('Fewer Than')?></option>
							</select>
							<input type=text name="cChildren" value="<?=$searchRequest['cChildren']?>">
							</span>
						<? } ?>

						<? if ($req == 'version_status') { ?>
							<span class="ccm-search-option"  search-field="version_status">
							<ul class="inputs-list">
							<li><label><?=$form->radio('_cvIsApproved', 0, $searchRequest['cvIsApproved'])?> <span><?=t('Unapproved')?></span></label></li>
							<li><label><?=$form->radio('_cvIsApproved', 1, $searchRequest['cvIsApproved'])?> <span><?=t('Approved')?></span></label></li>
							</ul>
							</span>
						<? } ?>
						
						<? if ((!$searchDialog) && $req == 'parent') { ?>
						<span class="ccm-search-option" search-field="parent">

						<? $ps = Loader::helper("form/page_selector");
						print $ps->selectPage('cParentIDSearchField', $searchRequest['cParentIDSearchField']);
						?>
						
						<br/><strong><?=t('Search All Children?')?></strong><br/>

						<ul class="inputs-list">
						<li><label><?=$form->radio('_cParentAll', 0, $searchRequest['cParentAll'])?> <span><?=t('No')?></span></label></li>
						<li><label><?=$form->radio('_cParentAll', 1, $searchRequest['cParentAll'])?> <span><?=t('Yes')?></span></label></li>
						</ul>
						</span>
						<? } ?>
						
						<? foreach($searchFieldAttributes as $sfa) { 
							if ($sfa->getAttributeKeyID() == $req) {
								$at = $sfa->getAttributeType();
								$at->controller->setRequestArray($searchRequest);
								$at->render('search', $sfa);
							}
						} ?>					</div>
					</td>
					<td><a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a></td>
					</tr>
				<? 
					$i++;
				} 
				
				} ?>
		</table>
		<div id="ccm-search-fields-submit">
			<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/customize_search_columns?searchInstance=<?=$searchInstance?>" id="ccm-list-view-customize"><span class="ccm-menu-icon ccm-icon-properties"></span><?=t('Customize Results')?></a>
		</div>
	</div>
</form>	
