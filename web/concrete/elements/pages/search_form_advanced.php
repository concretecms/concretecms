<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?

$searchFields = array(
	'' => '** ' . t('Fields'),
	'date_added' => t('Date Added'),
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
	$searchFields[$ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayHandle();
}

?>

<? $form = Loader::helper('form'); ?>
	
	<div id="ccm-<?=$searchInstance?>-search-field-base-elements" style="display: none">
	
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

		<span class="ccm-search-option"  search-field="owner">
		<?=$form->text('owner', array('style' => 'width: 120px'))?>
		</span>

		<span class="ccm-search-option"  search-field="version_status">
		<div><?=$form->radio('cvIsApproved', 0, false)?> <?=$form->label("cvIsApproved1", t('Unapproved'))?></div>
		<div><?=$form->radio('cvIsApproved', 1, false)?> <?=$form->label("cvIsApproved2", t('Approved'))?></div>
		</span>
			
		<? if (!$searchDialog) { ?>
		<span class="ccm-search-option" search-field="parent">
		<div style="width: 100px">
		<strong><?=t('Search All Children')?></strong><br/>
		<div><?=$form->radio('cParentAll', 1)?> <?=$form->label("cParentAll3", t('Yes'))?></div>
		<div><?=$form->radio('cParentAll', 0, 0)?> <?=$form->label("cParentAll4", t('No'))?></div>
		<? $ps = Loader::helper("form/page_selector");
		print $ps->selectPage('cParentIDSearchField');
		?>
		</div>
		</span>
		<? } ?>
		<span class="ccm-search-option"  search-field="num_children">
			<select name="cChildrenSelect" style="width: 45px">
				<option value="gt"<? if ($req['cChildrenSelect'] == 'gt') { ?> selected <? } ?>><?=t('More Than')?></option>
				<option value="eq" <? if ($req['cChildrenSelect'] == 'eq') { ?> selected <? } ?>><?=t('Equal To')?></option>
				<option value="lt"<? if ($req['cChildrenSelect'] == 'lt') { ?> selected <? } ?>><?=t('Fewer Than')?></option>
			</select>
			<input type="text" name="cChildren" value="<?=$req['cChildren']?>" style="width: 30px" />
		</span>
		
		<? foreach($searchFieldAttributes as $sfa) { 
			$sfa->render('search'); ?>
		<? } ?>
		
	</div>

	<form method="get" id="ccm-<?=$searchInstance?>-advanced-search" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/search_results">

<input type="hidden" name="searchInstance" value="<?=$searchInstance?>" />

<div id="ccm-<?=$searchInstance?>-search-advanced-fields" class="ccm-search-advanced-fields" >
	
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
		<div id="ccm-search-box-title">
			<img src="<?=ASSETS_URL_IMAGES?>/throbber_white_16.gif" width="16" height="16" class="ccm-search-loading" id="ccm-<?=$searchInstance?>-search-loading" alt="<?php echo t('Loading')?>"/>
			<h2><?=t('Search')?></h2>			
		</div>
		
		<div id="ccm-search-advanced-fields-inner">
			<div class="ccm-search-field">
				<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
					<strong><?=$form->label('cvName', t('Page Name'))?></strong>
					<?=$form->text('cvName', $searchRequest['cvName'], array('style' => 'width:200px')); ?>
					</td>
				</tr>
				</table>
			</div>

			<div class="ccm-search-field">
				<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
					<? if ($searchType == 'DASHBOARD') { ?>
						<a style="float: right; font-weight: bold" href="<?=$this->url('/dashboard/sitemap/search', 'manage_index')?>"><?=t('Setup Index')?></a>
					<? } ?>
					<strong><?=$form->label('keywords', t('Full Page Index'))?></strong>
					<?=$form->text('keywords', $searchRequest['keywords'], array('style' => 'width:200px')); ?>
					</td>
				</tr>
				</table>
			</div>

			<div class="ccm-search-field">
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td style="white-space: nowrap" align="right"><div style="width: 85px; padding-right:5px"><?=t('Page Type')?></div></td>
					<td width="100%">
						<? 
						Loader::model('collection_types');
						$ctl = CollectionType::getList();
						$ctypes = array('' => t('** All'));
						foreach($ctl as $ct) {
							$ctypes[$ct->getCollectionTypeID()] = $ct->getCollectionTypeName();
						}
						
						print $form->select('ctID', $ctypes, $searchRequest['ctID'], array('style' => 'width:120px'))?>
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
					<td><a href="javascript:void(0)" id="ccm-<?=$searchInstance?>-search-add-option"><img src="<?=ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" alt="<?php echo t('Add')?>" /></a></td>
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
						<a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" alt="<?php echo t('Remove')?>" /></a>
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
							<input type="hidden" value="<?=$req?>" class="ccm-page-selected-field" name="selectedSearchField[]" />
							</td>
							<td width="100%" valign="top" class="ccm-selected-field-content">
							
							<? if ($req == 'date_public') { ?>
								<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_public">
								<?=$form->text('date_public_from', $searchRequest['date_public_from'], array('style' => 'width: 86px'))?>
								<?=t('to')?>
								<?=$form->text('date_public_to', $searchRequest['date_public_to'], array('style' => 'width: 86px'))?>
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
								<?=$form->text('owner', $searchRequest['owner'], array('style' => 'width: 120px'))?>
								</span>
							<? } ?>

							<? if ($req == 'num_children') { ?>
								<span class="ccm-search-option"  search-field="num_children">
								<select name="cChildrenSelect" style="width: 45px">
									<option value="gt"<? if ($searchRequest['cChildrenSelect'] == 'gt') { ?> selected <? } ?>><?=t('More Than')?></option>
									<option value="eq" <? if ($searchRequest['cChildrenSelect'] == 'eq') { ?> selected <? } ?>><?=t('Equal To')?></option>
									<option value="lt"<? if ($searchRequest['cChildrenSelect'] == 'lt') { ?> selected <? } ?>><?=t('Fewer Than')?></option>
								</select>
								<input type=text name="cChildren" value="<?=$searchRequest['cChildren']?>" style="width: 30px">
								</span>
							<? } ?>

							<? if ($req == 'version_status') { ?>
								<span class="ccm-search-option"  search-field="version_status">
								<div>
								<?=$form->radio('_cvIsApproved', 0, $searchRequest['cvIsApproved'])?> <?=$form->label("cvIsApproved1", t('Unapproved'))?>
								</div>
								<div>
								<?=$form->radio('_cvIsApproved', 1, $searchRequest['cvIsApproved'])?> <?=$form->label("cvIsApproved2", t('Approved'))?>
								</div>
								</span>
							<? } ?>
							
							<? if ((!$searchDialog) && $req == 'parent') { ?>
							<span class="ccm-search-option" search-field="parent">
							<div style="width: 100px">
							<strong><?=t('Search All Children')?></strong><br/>
							<div><?=$form->radio('_cParentAll', 1, $searchRequest['cParentAll'])?> <?=$form->label("cParentAll3", t('Yes'))?></div>
							<div><?=$form->radio('_cParentAll', 0, $searchRequest['cParentAll'])?> <?=$form->label("cParentAll4", t('No'))?></div>
							<? $ps = Loader::helper("form/page_selector");
							print $ps->selectPage('cParentIDSearchField', $searchRequest['cParentIDSearchField']);
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
							<a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" alt="<?php echo t('Remove')?>"/></a>
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
				<?=$form->submit('ccm-search-pages', t('Search'))?>
			</div>
		</div>
	
</div>

</form>