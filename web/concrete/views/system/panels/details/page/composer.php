<?
defined('C5_EXECUTE') or die("Access Denied.");
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
			cID: '<?=$c->getCollectionID()?>',
			onAfterSaveAndExit: function() {
				var panel = CCMPanelManager.getByIdentifier('page');
				panel.closePanelDetail();
			}
		});
		ccm_event.subscribe('panel.closeDetail',function(e) {
			var panelDetail = e.eventData;
			if (panelDetail && panelDetail.identifier == 'page-composer') {
				$.fn.ccmcomposer('disableAutoSave');
			}
		});

	});
</script>