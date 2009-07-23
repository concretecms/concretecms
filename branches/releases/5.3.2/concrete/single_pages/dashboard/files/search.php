<h1><span><?php echo t('File Manager')?></span></h1>

<?php  
$fp = FilePermissions::getGlobal();
if ($fp->canSearchFiles()) { ?>

	<div class="ccm-dashboard-inner">
	
		<table id="ccm-file-manager-table" >
			<tr>
				<td valign="top" class="ccm-search-form-advanced-col">
					<?php  Loader::element('files/search_form_advanced'); ?>
				</td>		
				<?php  /* <div id="ccm-file-search-advanced-fields-gutter">&nbsp;</div> */ ?>		
				<td valign="top">	
					
					<div id="ccm-file-search-advanced-results-wrapper">
					
						<?php  Loader::element('files/upload_single'); ?>
						
						<div id="ccm-file-search-results">
						
							<?php  Loader::element('files/search_results', array('files' => $files, 'fileList' => $fileList, 'pagination' => $pagination)); ?>
						
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