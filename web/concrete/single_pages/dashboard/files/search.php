<div class="ccm-ui">
<div class="row">

<div class="ccm-pane">
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeader(t('File Manager'), t('Add, search, replace and modify the files for your website.'));?>
<? 
$c = Page::getCurrentPage();
$ocID = $c->getCollectionID();
$fp = FilePermissions::getGlobal();
if ($fp->canSearchFiles()) { ?>
<div class="ccm-pane-options" id="ccm-<?=$searchInstance?>-pane-options">

<ul class="tabs">
<li class="active"><a href="javascript:void(0)" onclick="$('#ccm-<?=$searchInstance?>-pane-options ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('#ccm-<?=$searchInstance?>-pane-options div.ccm-file-manager-add-form').hide(); $('#ccm-<?=$searchInstance?>-pane-options div.ccm-file-manager-search-form').show();"><?=t('Search Files')?></a></li>
<li><a href="javascript:void(0)" onclick="$('#ccm-<?=$searchInstance?>-pane-options ul.tabs li').removeClass('active');  $(this).parent().addClass('active'); $('#ccm-<?=$searchInstance?>-pane-options div.ccm-file-manager-search-form').hide(); $('#ccm-<?=$searchInstance?>-pane-options div.ccm-file-manager-add-form').show();"><?=t('Add Files')?></a></li>
</ul>

<div class="ccm-file-manager-search-form"><? Loader::element('files/search_form_advanced', array('searchInstance' => $searchInstance, 'searchRequest' => $searchRequest, 'searchType' => 'DASHBOARD')); ?></div>
<div class="ccm-file-manager-add-form" style="display: none">
<? Loader::element('files/upload_single', array('searchInstance' => $searchInstance, 'ocID' => $ocID)); ?>
</div>
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