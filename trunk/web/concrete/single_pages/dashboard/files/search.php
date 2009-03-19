<h1><span><?=t('File Manager')?></span></h1>

<? 
$fp = FilePermissions::getGlobal();
if ($fp->canSearchFiles()) { ?>
	<div class="ccm-dashboard-inner">
	
	<? Loader::element('files/search_form_advanced'); ?>
	
	
	<div id="ccm-file-search-advanced-fields-gutter">&nbsp;</div>
	
	<div id="ccm-file-search-advanced-results-wrapper">
	
	<? Loader::element('files/upload_single'); ?>
	
	<div id="ccm-file-search-results">
	
	<? Loader::element('files/search_results', array('files' => $files, 'fileList' => $fileList, 'pagination' => $pagination)); ?>
	
	</div>
	
	</div>
	
	</div>
	</div>
	
<? } else { ?>
	<div class="ccm-dashboard-inner">
	<?=t('Unable to access file manager.'); ?>
	</div>
<? } ?>