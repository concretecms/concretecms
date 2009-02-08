<h1><span><?=t('File Manager')?></span></h1>

<div class="ccm-dashboard-inner">
<div id="ccm-file-manager-advanced">

<div id="ccm-file-search-advanced-fields">
<form method="get" id="ccm-dashboard-file-search" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/search_results">
	<input type="hidden" name="search" value="1" />
	
	<div style="position: relative">

		<h2><?=t('Advanced Search')?></h2>
		<img src="<?=ASSETS_URL_IMAGES?>/throbber_white_16.gif" width="16" height="16" id="ccm-file-search-advanced-loading" />

	</div>
	
	<div id="ccm-file-search-advanced-fields-inner">
	<div class="ccm-file-search-field">

	<?=$form->label('fKeywords', 'Keywords')?><br/>

	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%">
		<?=$form->text('fKeywords', array('style' => 'width:175px')); ?>
		</td>
		<td><a href="javascript:void(0)" id="ccm-file-search-add-option"><img src="<?=ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" /></a></td>
	</tr>
	</table>
	
	</div>
	
	<div id="ccm-file-search-field-base">
		
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td valign="top" style="padding-right: 4px">
		<?=$form->select('fvField', array(
			'file_set' => t('Set'),
			'size' => t('Size'),
			'type' => t('Type'),
			'extension' => t('Extension'),
			'date_added' => t('Added Between')
		), array('style' => 'width: 85px'));
		?>
		<input type="hidden" value="" class="ccm-file-selected-field" name="fvSelectedField[]" />
		</td>
		<td width="100%" valign="top">
		
		
		<span class="ccm-file-search-option" search-field="file_set">
		<?=$form->select('file_set[]', $sets)?>
		</span>
		
		<span class="ccm-file-search-option" style="display: none" search-field="size">
		<?=$form->text('size_from[]', array('style' => 'width: 30px'))?>
		to
		<?=$form->text('size_to[]', array('style' => 'width: 30px'))?>
		KB
		</span>
	
		<span class="ccm-file-search-option" style="display: none"  search-field="type">
		<?=$form->select('type[]', $types)?>
		</span>
	
		<span class="ccm-file-search-option" style="display: none"  search-field="extension">
		<?=$form->select('extension[]', $extensions)?>
		</span>

		<span class="ccm-file-search-option" style="display: none"  search-field="date_added">
		<?=$form->text('date_from[]', array('style' => 'width: 86px'))?>
		and
		<?=$form->text('date_to[]', array('style' => 'width: 86px'))?>

		</span>

		</td>
		<td valign="top">
		<a href="javascript:void(0)" class="ccm-file-search-remove-option"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a>
		</td>
		</tr></table>
	</div>
	
	<div id="ccm-file-search-fields-wrapper">
	
	</div>
	
	<?=$form->submit('ccm-search-files', 'Search')?>
	</div>

</form>	
</div>

<div id="ccm-file-search-advanced-fields-gutter">&nbsp;</div>

<div id="ccm-file-search-advanced-results-wrapper">

<? Loader::element('files/upload_single'); ?>

<div id="ccm-file-search-advanced-results">

<? Loader::element('files/search_results', array('files' => $files, 'fileList' => $fileList, 'pagination' => $pagination)); ?>

</div>

</div>

</div>
</div>