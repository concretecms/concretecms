<h1><span><?=t('Search Pages')?></span></h1>

<div class="ccm-dashboard-inner">

	<table id="ccm-search-form-table" >
		<tr>
			<td valign="top" class="ccm-search-form-advanced-col">
				<? Loader::element('pages/search_form_advanced', array('searchRequest' => $searchRequest, 'searchType' => 'DASHBOARD')); ?>
			</td>		
			<td valign="top" width="100%">	
				
				<div id="ccm-search-advanced-results-wrapper">
				
					<div id="ccm-page-search-results">
					
						<? Loader::element('pages/search_results', array('searchType' => 'DASHBOARD', 'pages' => $pages, 'pageList' => $pageList, 'pagination' => $pagination)); ?>
					
					</div>
				
				</div>
			
			</td>	
		</tr>
	</table>		
	
</div>