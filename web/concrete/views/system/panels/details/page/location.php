<?
defined('C5_EXECUTE') or die("Access Denied.");
?>
<section class="ccm-ui">
	<header><?=t('Location')?></header>
	<form method="post" action="<?=$controller->action('submit')?>" data-panel-detail-form="location">

		<?=Loader::helper('concrete/interface/help')->notify('panel', '/page/location')?>

	<div style="min-height: 140px">
		<p class="lead"><?=t('Where does this page live on the site?')?></p>

		<button class="btn btn-info"><?=t('Choose Location')?></button>

	</div>

	<? if (!$c->isGeneratedCollection()) { ?>

		<hr/>
		<p class="lead"><?=t('Other URLs that should redirect to this page')?></p>

		<?php
			$paths = $c->getPagePaths();
			foreach ($paths as $path) {
				if (!$path['ppIsCanonical']) {
					$ppID = $path['ppID'];
					$cPath = $path['cPath'];
					echo '<div class="input ccm-meta-path">' .
		     			'<input type="text" name="ppURL-' . $ppID . '" class="ccm-input-text" value="' . $cPath . '" id="ppID-'. $ppID . '"> ' .
		     			'<a href="javascript:void(0)" class="ccm-meta-path-del">' . t('Remove Path') . '</a></div>'."\n";
				}
			}
		?>

		<button class="btn btn-info"><?=t('Add URL Redirect')?></button>

		<br/><br/>
 		<span class="help-block"><?=t('Note: Additional page paths are not versioned. They will be available immediately.')?></span>


	<? } ?>



	</form>
	<div class="ccm-panel-detail-form-actions">
		<button class="pull-left btn btn-default" type="button" data-panel-detail-action="cancel"><?=t('Cancel')?></button>
		<button class="pull-right btn btn-success" type="button" data-panel-detail-action="submit"><?=t('Save Changes')?></button>
	</div>

</section>