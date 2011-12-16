<div class="ccm-ui">
<div class="row">

<div class="ccm-pane">
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeader(t('File Manager'), array(t('Add, search, replace and modify the files for your website.'), 'http://www.concrete5.org/documentation/editors-guide/dashboard/file-manager/'));?>
<? 
$c = Page::getCurrentPage();
$ocID = $c->getCollectionID();
$fp = FilePermissions::getGlobal();
if ($fp->canSearchFiles()) { ?>
<div class="ccm-pane-options" id="ccm-<?=$searchInstance?>-pane-options">

<div class="ccm-file-manager-search-form"><? Loader::element('files/search_form_advanced', array('searchInstance' => $searchInstance, 'searchRequest' => $searchRequest, 'searchType' => 'DASHBOARD')); ?></div>

</div>

<? Loader::element('files/search_results', array('searchInstance' => $searchInstance, 'searchRequest' => $searchRequest, 'columns' => $columns, 'searchType' => 'DASHBOARD', 'files' => $files, 'fileList' => $fileList)); ?>

<? } else { ?>
<div class="ccm-pane-body">
	<p><?=t("You do not have access to the file manager.");?></p>
</div>	
<div class="ccm-pane-footer"></div>

</div>

<? } ?>

</div>
</div>