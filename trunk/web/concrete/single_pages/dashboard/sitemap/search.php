<? if ($this->controller->getTask() == 'manage_index') { ?>
	<h1><span><?=t('Setup Search Index')?></span></h1>
	
	<div class="ccm-dashboard-inner">
	
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