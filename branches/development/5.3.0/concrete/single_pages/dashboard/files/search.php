<h1><span><?=t('File Manager')?></span></h1>

<div class="ccm-dashboard-inner">
<div id="ccm-file-manager-advanced">

<div id="ccm-file-search-advanced-fields">
<form method=get id="ccm-dashboard-search" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/dashboard/sitemap_data.php">
	<input type="hidden" name="search" value="1" />
	<h2><?=t('Advanced Search')?></h2>
	
	<div id="ccm-file-search-advanced-fields-inner">
	<?=$form->label('fKeywords', 'Keywords')?>
	<?=$form->text('fKeywords')?>
	</div>

</form>	
</div>

<div id="ccm-file-search-advanced-fields-gutter">&nbsp;</div>

<div id="ccm-file-search-advanced-results-wrapper">

<? Loader::element('files/upload_single'); ?>

<? Loader::element('files/search_results'); ?>

</div>

</div>
</div>