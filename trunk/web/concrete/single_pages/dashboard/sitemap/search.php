<? if ($this->controller->getTask() == 'manage_index') { ?>
	<h1><span><?=t('Setup Search Index')?></span></h1>
	
	<div class="ccm-dashboard-inner">
	<form method="post" id="ccm-search-index-manage" action="<?=$this->action('manage_index')?>">
	
	<h2><?=t('Areas')?></h2>

	<? foreach($areas as $a) { ?>
		<div><?=$form->checkbox('arHandle[]', $a, in_array($a, $selectedAreas))?> <?=$a?></div>
	<? } ?>
	
	<br/>
	
	<h2><?=t('Area Indexing Method')?></h2>
	<?
	$methods = array(
		'whitelist' => t('Whitelist: Selected areas are only areas indexed.'),
		'blacklist' => t('Blacklist: Every area but the selected areas are indexed.')
	);
	?>
	<?
	print $form->select('SEARCH_INDEX_AREA_METHOD', $methods, IndexedSearch::getSearchableAreaAction());
	print '<br/>';
	print '<br/>';
	
	$ih = Loader::helper('concrete/interface');
	$b1 = $ih->button(t('Cancel'), $this->url('/dashboard/sitemap/search'), 'left');
	$b2 = $ih->submit(t('Save'), 'ccm-search-index-manage', 'right');
	print $ih->buttons($b1, $b2);
	
	?>
	</form>
	</div>

<? } else { ?>
	<h1><span><?=t('Search Pages')?></span></h1>
	
	<div class="ccm-dashboard-inner">
		
		<?
		$dh = Loader::helper('concrete/dashboard/sitemap');
		if ($dh->canRead()) { ?>
	
		<table id="ccm-search-form-table" >
			<tr>
				<td valign="top" class="ccm-search-form-advanced-col">
					<? Loader::element('pages/search_form_advanced', array('searchInstance' => $searchInstance, 'searchRequest' => $searchRequest, 'searchType' => 'DASHBOARD')); ?>
				</td>		
				<td valign="top" width="100%">	
					
					<div id="ccm-search-advanced-results-wrapper">
					
							<div id="ccm-<?=$searchInstance?>-search-results">
						
							<? Loader::element('pages/search_results', array('searchInstance' => $searchInstance, 'searchType' => 'DASHBOARD', 'pages' => $pages, 'pageList' => $pageList, 'pagination' => $pagination)); ?>
						
						</div>
					
					</div>
				
				</td>	
			</tr>
		</table>		
		
		<? } else { ?>
		
			<p><?=t("You must have access to the dashboard sitemap to search pages.")?></p>
		
		<? } ?>	
		
	</div>
<? } ?>