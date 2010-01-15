<h1><span><?=t('File Manager')?></span></h1>

<? 
$fp = FilePermissions::getGlobal();
if ($fp->canSearchFiles()) { ?>

	<div class="ccm-dashboard-inner">
	
		<table id="ccm-search-form-table" >
			<tr>
				<td valign="top" class="ccm-search-form-advanced-col">
					<? Loader::element('files/search_form_advanced', array('searchRequest' => $searchRequest, 'searchType' => 'DASHBOARD')); ?>
				</td>		
				<? /* <div id="ccm-file-search-advanced-fields-gutter">&nbsp;</div> */ ?>		
				<td valign="top" width="100%">	
					
					<div id="ccm-search-advanced-results-wrapper">
					
						<? Loader::element('files/upload_single'); ?>
						
						<div id="ccm-search-results">
						
							<? Loader::element('files/search_results', array('searchType' => 'DASHBOARD', 'files' => $files, 'fileList' => $fileList, 'pagination' => $pagination)); ?>
						
						</div>
					
					</div>
				
				</td>	
			</tr>
		</table>		
		
	</div>
	
<? } else { ?>
	<div class="ccm-dashboard-inner">
		<?=t('Unable to access file manager.'); ?>
	</div>
<? } ?>


