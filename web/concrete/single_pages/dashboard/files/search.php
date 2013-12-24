<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('File Manager'), array(t('Add, search, replace and modify the files for your website.'), 'http://www.concrete5.org/documentation/editors-guide/dashboard/file-manager/'), false, false);?>

<? 
$c = Page::getCurrentPage();
$ocID = $c->getCollectionID();
$fp = FilePermissions::getGlobal();
if ($fp->canAddFile() || $fp->canSearchFiles()) { ?>

<? if ($fp->canAddFile()) { ?>
	<div id="ccm-file-manager-upload"><?=t("<strong>Upload Files</strong> / Click to Choose or Drag &amp; Drop")?><input type="file" name="files[]" /></div>
<? } ?>

<div class="ccm-dashboard-content-full" data-search="files">
<? Loader::element('files/search', array('controller' => $searchController))?>
</div>

<? } else { ?>
<div class="ccm-pane-body">
	<p><?=t("You do not have access to the file manager.");?></p>
</div>	
<div class="ccm-pane-footer"></div>

<? } ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false); ?>
