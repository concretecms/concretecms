<h1><span><?php echo t('File Manager')?></span></h1>

<?php  
$fp = FilePermissions::getGlobal();
$c = Page::getCurrentPage();
$ocID = $c->getCollectionID();
if ($fp->canSearchFiles()) { ?>

	<div class="ccm-dashboard-inner">
	
		<table id="ccm-search-form-table" >
			<tr>
				<td valign="top" class="ccm-search-form-advanced-col">
					<?php  Loader::element('files/search_form_advanced', array('searchInstance' => $searchInstance, 'searchRequest' => $searchRequest, 'searchType' => 'DASHBOARD')); ?>
				</td>		
				<?php  /* <div id="ccm-<?php echo $searchInstance?>-search-advanced-fields-gutter">&nbsp;</div> */ ?>		
				<td valign="top" width="100%">	
					
					<div id="ccm-search-advanced-results-wrapper">
					
						<?php  Loader::element('files/upload_single', array('searchInstance' => $searchInstance, 'ocID' => $ocID)); ?>
						
						<div id="ccm-<?php echo $searchInstance?>-search-results" class="ccm-file-list">
						
							<?php  Loader::element('files/search_results', array('searchInstance' => $searchInstance, 'searchRequest' => $searchRequest, 'columns' => $columns, 'searchType' => 'DASHBOARD', 'files' => $files, 'fileList' => $fileList, 'pagination' => $pagination)); ?>
						
						</div>
					
					</div>
				
				</td>	
			</tr>
		</table>		
		
	</div>
	
<?php  } else { ?>
	<div class="ccm-dashboard-inner">
		<?php echo t('Unable to access file manager.'); ?>
	</div>
<?php  } ?>


