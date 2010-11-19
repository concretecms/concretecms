<?php  if ($this->controller->getTask() == 'manage_index') { ?>
	<h1><span><?php echo t('Setup Search Index')?></span></h1>
	
	<div class="ccm-dashboard-inner">
	<form method="post" id="ccm-search-index-manage" action="<?php echo $this->action('manage_index')?>">
	
	<h2><?php echo t('Areas')?></h2>

	<?php  foreach($areas as $a) { ?>
		<div><?php echo $form->checkbox('arHandle[]', $a, in_array($a, $selectedAreas))?> <?php echo $a?></div>
	<?php  } ?>
	
	<br/>
	
	<h2><?php echo t('Area Indexing Method')?></h2>
	<?php 
	$methods = array(
		'whitelist' => t('Whitelist: Selected areas are only areas indexed.'),
		'blacklist' => t('Blacklist: Every area but the selected areas are indexed.')
	);
	?>
	<?php 
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

<?php  } else { ?>
	<h1><span><?php echo t('Search Pages')?></span></h1>
	
	<div class="ccm-dashboard-inner">
		
		<?php 
		$dh = Loader::helper('concrete/dashboard/sitemap');
		if ($dh->canRead()) { ?>
	
		<table id="ccm-search-form-table" >
			<tr>
				<td valign="top" class="ccm-search-form-advanced-col">
					<?php  Loader::element('pages/search_form_advanced', array('searchInstance' => $searchInstance, 'searchRequest' => $searchRequest, 'searchType' => 'DASHBOARD')); ?>
				</td>		
				<td valign="top" width="100%">	
					
					<div id="ccm-search-advanced-results-wrapper">
					
							<div id="ccm-<?php echo $searchInstance?>-search-results">
						
							<?php  Loader::element('pages/search_results', array('searchInstance' => $searchInstance, 'searchType' => 'DASHBOARD', 'pages' => $pages, 'pageList' => $pageList, 'pagination' => $pagination)); ?>
						
						</div>
					
					</div>
				
				</td>	
			</tr>
		</table>		
		
		<?php  } else { ?>
		
			<p><?php echo t("You must have access to the dashboard sitemap to search pages.")?></p>
		
		<?php  } ?>	
		
	</div>
<?php  } ?>