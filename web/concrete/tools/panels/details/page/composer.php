<?
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['cID']));
if (is_object($c) && !$c->isError()) {
	$cp = new Permissions($c);
	if ($cp->canEditPageContents()) { 
		$pagetype = PageType::getByID($c->getPageTypeID());
		$req = Request::get();
		$req->requireAsset('core/composer');

		$id = $c->getCollectionID();
		$saveURL = View::url('/dashboard/composer/write', 'save', 'draft', $id);
		$viewURL = View::url('/dashboard/composer/write', 'draft', $id);

		?>

		<section class="ccm-ui">
			<header><?=t('Composer - %s', $pagetype->getPageTypeName())?></header>
			<form method="post" data-form="composer" class="ccm-panel-detail-content-form">
				<?=Loader::helper('concrete/interface/help')->notify('panel', '/page/composer')?>

				<? Loader::helper('composer')->display($pagetype, $c); ?>
			</form>

			<div class="ccm-panel-detail-form-actions">
				<? Loader::helper('composer')->displayButtons($pagetype, $c); ?>
			</div>
		</section>

		<script type="text/javascript">
			$(function() { 
				$('form[data-form=composer]').ccmcomposer({
					token: '<?=Loader::helper('validation/token')->generate('composer')?>', 
					cID: '<?=$id?>',
					onAfterSaveAndExit: function() {
						var panel = CCMPanelManager.getByIdentifier('page');
						panel.closePanelDetail();
					}
				});
			});
		</script>
	<? }
}