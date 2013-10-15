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
			<form method="post" data-form="composer" class="form-horizontal">
			<div class="container" style="margin-left: 0px; margin-right: 0px">
			<div class="row">
			<div class="col-lg-8">
				<? Loader::helper('composer')->display($pagetype, $c); ?>
			</div>
			</div>
			</div>

			<div class="ccm-pane-detail-form-actions">
				<? Loader::helper('composer')->displayButtons($pagetype, $c); ?>
			</div>
			</form>
		</section>

		<script type="text/javascript">
			$(function() { 
				$('form[data-form=composer]').ccmcomposer({token: '<?=Loader::helper('validation/token')->generate('composer')?>', cID: '<?=$id?>'});
			});
		</script>
	<? }
}