<?
defined('C5_EXECUTE') or die("Access Denied.");
$token = Loader::helper('validation/token')->generate('composer');
$cID = $c->getCollectionID();
?>

<section class="ccm-ui">
	<header><?=t('Composer - %s', $pagetype->getPageTypeName())?></header>
	<form method="post" action="<?=$controller->action('submit')?>" data-panel-detail-form="compose">
		<?=Loader::helper('concrete/interface/help')->notify('panel', '/page/composer')?>

		<? Loader::helper('composer')->display($pagetype, $c); ?>
	</form>

	<div class="ccm-panel-detail-form-actions">
		<? Loader::helper('composer')->displayButtons($pagetype, $c); ?>
	</div>
</section>

<script type="text/javascript">
$(function() {
    $('button[data-page-type-composer-form-btn=discard]').on('click', function() {
    	$.concreteAjax({
    		'url': CCM_TOOLS_PATH + '/pages/draft/discard',
    		'data': {token: '<?=$token?>', cID: '<?=$cID?>'}, 
    		success: function(r) {
	 			console.log(r);
    		}
    	});
	});
});
</script>