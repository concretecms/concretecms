<?php  defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?php 

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

<?php  $form = Loader::helper('form'); ?>
	
	<div id="ccm-<?php echo $searchInstance?>-search-field-base-elements" style="display: none">
	
		<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_public">
		<?php echo $form->text('date_public_from', array('style' => 'width: 86px'))?>
		<?php echo t('to')?>
		<?php echo $form->text('date_public_to', array('style' => 'width: 86px'))?>
		</span>

		<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_added">
		<?php echo $form->text('date_added_from', array('style' => 'width: 86px'))?>
		<?php echo t('to')?>
		<?php echo $form->text('date_added_to', array('style' => 'width: 86px'))?>
		</span>

		<span class="ccm-search-option"  search-field="owner">
		<?php echo $form->text('owner', array('style' => 'width: 120px'))?>
		</span>

		<span class="ccm-search-option"  search-field="version_status">
		<div><?php echo $form->radio('cvIsApproved', 0, false)?> <?php echo $form->label("cvIsApproved1", t('Unapproved'))?></div>
		<div><?php echo $form->radio('cvIsApproved', 1, false)?> <?php echo $form->label("cvIsApproved2", t('Approved'))?></div>
		</span>
			
		<?php  if (!$searchDialog) { ?>
		<span class="ccm-search-option" search-field="parent">
		<div style="width: 100px">
		<strong><?php echo t('Search All Children')?></strong><br/>
		<div><?php echo $form->radio('cParentAll', 1)?> <?php echo $form->label("cParentAll3", t('Yes'))?></div>
		<div><?php echo $form->radio('cParentAll', 0, 0)?> <?php echo $form->label("cParentAll4", t('No'))?></div>
		<?php  $ps = Loader::helper("form/page_selector");
		print $ps->selectPage('cParentIDSearchField');
		?>
		</div>
		</span>
		<?php  } ?>
		<span class="ccm-search-option"  search-field="num_children">
			<select name="cChildrenSelect" style="width: 45px">
				<option value="gt"<?php  if ($req['cChildrenSelect'] == 'gt') { ?> selected <?php  } ?>><?php echo t('More Than')?></option>
				<option value="eq" <?php  if ($req['cChildrenSelect'] == 'eq') { ?> selected <?php  } ?>><?php echo t('Equal To')?></option>
				<option value="lt"<?php  if ($req['cChildrenSelect'] == 'lt') { ?> selected <?php  } ?>><?php echo t('Fewer Than')?></option>
			</select>
			<input type=text name="cChildren" value="<?php echo $req['cChildren']?>" style="width: 30px">
		</span>
		
		<?php  foreach($searchFieldAttributes as $sfa) { 
			$sfa->render('search'); ?>
		<?php  } ?>
		
	</div>

	<form method="get" id="ccm-<?php echo $searchInstance?>-advanced-search" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/pages/search_results">

<input type="hidden" name="searchInstance" value="<?php echo $searchInstance?>" />

<div id="ccm-<?php echo $searchInstance?>-search-advanced-fields" class="ccm-search-advanced-fields" >
	
		<input type="hidden" name="submit_search" value="1" />
	<?php 	
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
			<img src="<?php echo ASSETS_URL_IMAGES?>/throbber_white_16.gif" width="16" height="16" class="ccm-search-loading" id="ccm-<?php echo $searchInstance?>-search-loading" />
			<h2><?php echo t('Search')?></h2>			
		</div>
		
		<div id="ccm-search-advanced-fields-inner">
			<div class="ccm-search-field">
				<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
					<strong><?php echo $form->label('cvName', t('Page Name'))?></strong>
					<?php echo $form->text('cvName', $searchRequest['cvName'], array('style' => 'width:200px')); ?>
					</td>
				</tr>
				</table>
			</div>

			<div class="ccm-search-field">
				<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
					<?php  if ($searchType == 'DASHBOARD') { ?>
						<a style="float: right; font-weight: bold" href="<?php echo $this->url('/dashboard/sitemap/search', 'manage_index')?>"><?php echo t('Setup Index')?></a>
					<?php  } ?>
					<strong><?php echo $form->label('keywords', t('Full Page Index'))?></strong>
					<?php echo $form->text('keywords', $searchRequest['keywords'], array('style' => 'width:200px')); ?>
					</td>
				</tr>
				</table>
			</div>

			<div class="ccm-search-field">
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td style="white-space: nowrap" align="right"><div style="width: 85px; padding-right:5px"><?php echo t('Page Type')?></div></td>
					<td width="100%">
						<?php  
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
					<td style="white-space: nowrap" align="right"><div style="width: 85px; padding-right:5px"><?php echo t('Results Per Page')?></div></td>
					<td width="100%">
						<?php echo $form->select('numResults', array(
							'10' => '10',
							'25' => '25',
							'50' => '50',
							'100' => '100',
							'500' => '500'
						), $searchRequest['numResults'], array('style' => 'width:65px'))?>
					</td>
					<td><a href="javascript:void(0)" id="ccm-<?php echo $searchInstance?>-search-add-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" /></a></td>
				</tr>	
				</table>
			</div>

			<div id="ccm-search-field-base">				
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top" style="padding-right: 4px">
						<?php echo $form->select('searchField', $searchFields, array('style' => 'width: 85px'));
						?>
						<input type="hidden" value="" class="ccm-<?php echo $searchInstance?>-selected-field" name="selectedSearchField[]" />
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
			<?php  
			$i = 1;
			if (is_array($searchRequest['selectedSearchField'])) { 
				foreach($searchRequest['selectedSearchField'] as $req) { 
					
					if ($req == '') {
						continue;
					}
					
					?>
					
					<div class="ccm-search-field ccm-search-request-field-set" ccm-search-type="<?php echo $req?>" id="ccm-<?php echo $searchInstance?>-search-field-set<?php echo $i?>">
					<table border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="top" style="padding-right: 4px">
							<?php echo $form->select('searchField' . $i, $searchFields, $req, array('style' => 'width: 85px')); ?>
							<input type="hidden" value="<?php echo $req?>" class="ccm-page-selected-field" name="selectedSearchField[]" />
							</td>
							<td width="100%" valign="top" class="ccm-selected-field-content">
							
							<?php  if ($req == 'date_public') { ?>
								<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_public">
								<?php echo $form->text('date_public_from', $searchRequest['date_public_from'], array('style' => 'width: 86px'))?>
								<?php echo t('to')?>
								<?php echo $form->text('date_public_to', $searchRequest['date_public_to'], array('style' => 'width: 86px'))?>
								</span>
							<?php  } ?>

							<?php  if ($req == 'date_added') { ?>
								<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_added">
								<?php echo $form->text('date_added_from', $searchRequest['date_added_from'], array('style' => 'width: 86px'))?>
								<?php echo t('to')?>
								<?php echo $form->text('date_added_to', $searchRequest['date_added_to'], array('style' => 'width: 86px'))?>
								</span>
							<?php  } ?>

							<?php  if ($req == 'owner') { ?>
								<span class="ccm-search-option"  search-field="owner">
								<?php echo $form->text('owner', $searchRequest['owner'], array('style' => 'width: 120px'))?>
								</span>
							<?php  } ?>

							<?php  if ($req == 'num_children') { ?>
								<span class="ccm-search-option"  search-field="num_children">
								<select name="cChildrenSelect" style="width: 45px">
									<option value="gt"<?php  if ($searchRequest['cChildrenSelect'] == 'gt') { ?> selected <?php  } ?>><?php echo t('More Than')?></option>
									<option value="eq" <?php  if ($searchRequest['cChildrenSelect'] == 'eq') { ?> selected <?php  } ?>><?php echo t('Equal To')?></option>
									<option value="lt"<?php  if ($searchRequest['cChildrenSelect'] == 'lt') { ?> selected <?php  } ?>><?php echo t('Fewer Than')?></option>
								</select>
								<input type=text name="cChildren" value="<?php echo $searchRequest['cChildren']?>" style="width: 30px">
								</span>
							<?php  } ?>

							<?php  if ($req == 'version_status') { ?>
								<span class="ccm-search-option"  search-field="version_status">
								<div>
								<?php echo $form->radio('_cvIsApproved', 0, $searchRequest['cvIsApproved'])?> <?php echo $form->label("cvIsApproved1", t('Unapproved'))?>
								</div>
								<div>
								<?php echo $form->radio('_cvIsApproved', 1, $searchRequest['cvIsApproved'])?> <?php echo $form->label("cvIsApproved2", t('Approved'))?>
								</div>
								</span>
							<?php  } ?>
							
							<?php  if ((!$searchDialog) && $req == 'parent') { ?>
							<span class="ccm-search-option" search-field="parent">
							<div style="width: 100px">
							<strong><?php echo t('Search All Children')?></strong><br/>
							<div><?php echo $form->radio('_cParentAll', 1, $searchRequest['cParentAll'])?> <?php echo $form->label("cParentAll3", t('Yes'))?></div>
							<div><?php echo $form->radio('_cParentAll', 0, $searchRequest['cParentAll'])?> <?php echo $form->label("cParentAll4", t('No'))?></div>
							<?php  $ps = Loader::helper("form/page_selector");
							print $ps->selectPage('cParentIDSearchField', $searchRequest['cParentIDSearchField']);
							?>
							</div>
							</span>
							<?php  } ?>
							
							<?php  foreach($searchFieldAttributes as $sfa) { 
								if ($sfa->getAttributeKeyID() == $req) {
									$at = $sfa->getAttributeType();
									$at->controller->setRequestArray($searchRequest);
									$at->render('search', $sfa);
								}
							} ?>
							</td>
							<td valign="top">
							<a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a>
							</td>
						</tr>
					</table>
					</div>
					
				<?php  
					$i++;
				}
			}?>
			
			</div>
			
			<div id="ccm-search-fields-submit">
				<?php echo $form->submit('ccm-search-pages', t('Search'))?>
			</div>
		</div>
	
</div>

</form>