<?
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['cID']));
if (is_object($c) && !$c->isError()) {
	$cp = new Permissions($c);
	if ($cp->canEditPageContents()) { 
		$pagetype = PageType::getByID($c->getPageTypeID());
		$req = Request::get();
		$req->requireAsset('core/composer');
		?>

		<section class="ccm-ui">

			<header><?=t('Composer - %s', $pagetype->getPageTypeName())?></header>
			<form method="post" data-form="composer" class="form-horizontal">
			<div class="ccm-ui">
				<? Loader::helper('composer')->display($pagetype, $draft); ?>
			</div>
			<div class="ccm-ui ccm-pane-detail-form-actions">
				<? Loader::helper('composer')->displayButtons($pagetype, $draft); ?>
			</div>
			</form>
		</section>

		<script type="text/javascript">
		$(function() {
			$('form[data-form=composer]').ccmcomposer();
		});
		</script>
	<? }
}
?>