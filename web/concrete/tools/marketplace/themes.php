<?  defined('C5_EXECUTE') or die("Access Denied.");?>
<div class="ccm-ui">
<?

Loader::library('marketplace');
$mi = Marketplace::getInstance();
$tp = new TaskPermission();
if (!$tp->canInstallPackages()) { ?>
	<p><?=t('You do not have permission to download packages from the marketplace.')?></p>
	<? exit;
} else if (!$mi->isConnected()) { ?>
	<div class="ccm-pane-body-inner">
		<? Loader::element('dashboard/marketplace_connect_failed')?>
	</div>
<? } else {	


$cnt = Loader::controller('/dashboard/extend/themes');
$cnt->view();
$list = $cnt->get('list');
$items = $list->getPage();
$pagination = $list->getPagination();
$sets = $cnt->get('sets');
$sortBy = $cnt->get('sortBy');
$bu = REL_DIR_FILES_TOOLS_REQUIRED . '/marketplace/themes';
?>

	<div class="ccm-pane-options">
		<?=Loader::element('marketplace/search_form', array('action' => $bu, 'sets' => $sets, 'sortBy' => $sortBy));?>
	</div>
	<div class="ccm-pane-body" style="margin-left: -10px; margin-right: -10px">
		<?=Loader::element('marketplace/results', array('type' => 'themes', 'items' => $items));?>
	</div>	
	<div class="ccm-pane-dialog-pagination"><?=$list->displayPagingV2($bu)?></div>

	<script type="text/javascript">

	$(function() {
		ccm_setupMarketplaceDialogForm();
	});
	</script>
<? } ?>

</div>