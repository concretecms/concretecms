<?
defined('C5_EXECUTE') or die("Access Denied.");
?>
<section class="ccm-ui">
	<header><?=t('Location')?></header>
	<form method="post" action="<?=$controller->action('submit')?>" data-dialog-form="location" data-panel-detail-form="location">

		<?=Loader::helper('concrete/ui/help')->notify('panel', '/page/location')?>

	<div style="min-height: 140px">
		<? if ($c->isPageDraft()) { ?>
			<p class="lead"><?=t('Where will this page live on the site?')?></p>
		<? } else { ?>
			<p class="lead"><?=t('Where does this page live on the site?')?></p>
		<? } ?>

		<div id="ccm-panel-detail-location-display"></div>

		<input type="hidden" name="cParentID" value="<?=$cParentID?>" />
		<button class="btn btn-info"type="button" name="location"><?=t('Choose Location')?></button>

	</div>

		<hr/>
		<p class="lead"><?=t('Current Canonical URL')?></p>
		<div class="breadcrumb">
			<? if ($c->isPageDraft()) { ?>
				<?=t('None. Pages do not have canonical URLs until they are published.')?>
			<? } else { ?>
				<?php echo Loader::helper('navigation')->getLinkToCollection($c, true)?>
			<? } ?>
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

		<div id="ccm-panel-detail-location-page-paths">

		</div>

		<button class="btn btn-info" type="button" name="addRedirect"><?=t('Add URL Redirect')?></button>

		<br/><br/>
 		<span class="help-block"><?=t('Note: Additional page paths are not versioned. They will be available immediately.')?></span>


	<? } ?>

	</form>
	<div class="ccm-panel-detail-form-actions dialog-buttons">
		<button class="pull-left btn btn-default" type="button" data-dialog-action="cancel" data-panel-detail-action="cancel"><?=t('Cancel')?></button>
		<button class="pull-right btn btn-success" type="button" data-dialog-action="submit" data-panel-detail-action="submit"><?=t('Save Changes')?></button>
	</div>

</section>

<? $requestID = mt_rand(100000000,999999999); ?>

<script type="text/template" class="breadcrumb">
	<% if (parentID > 0) { %>
	<ol class="breadcrumb">
	  <li><a href="<%=parentLink%>" target="_blank"><%=parentName%></a></li>
	  <li class="active"><?=$c->getCollectionName()?></li>
	</ol>
	<% } else { %>
		<div class="breadcrumb">
		<?=t('A location has not yet been chosen.')?>
		</div>
	<% } %>
</script>

<script type="text/template" class="pagePath">
<div class="ccm-panel-detail-location-page-path">
	<input class="control-input" type="text" name="ppURL[]" value="<%=ppPath%>" />
	<a href="#"><i class="glyphicon glyphicon-minus-sign" /></a>
</div>
</script>

<script type="text/javascript">
var renderBreadcrumb = _.template(
    $('script.breadcrumb').html()
);
var renderPagePath = _.template(
    $('script.pagePath').html()
);

$(function() {

	$('button[name=location]').on('click', function() {
		jQuery.fn.dialog.open({
			width: '90%',
			height: '70%',
			modal: true,
			title: '<?=t("Choose New Page Parent")?>',
			href: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/sitemap_search_selector?requestID=<?=$requestID?>&amp;cID=<?=$c->getCollectionID()?>'
		});
	});
	$('#ccm-panel-detail-location-display').html(renderBreadcrumb({
		parentLink: '<?=Loader::helper('navigation')->getLinkToCollection($parent);?>',
		parentName: '<?=$parent->getCollectionName()?>',
		parentID: '<?=$cParentID?>'
	}));
	$('#ccm-panel-detail-location-page-paths').on('click', '.ccm-panel-detail-location-page-path a', function() {
		$(this).parent().remove();
		return false;
	});
	$('button[name=addRedirect]').on('click', function() {
		$('#ccm-panel-detail-location-page-paths').append(
			renderPagePath({
				ppPath: ''
			})
		);
	});

	ConcreteEvent.subscribe('SitemapSelectPage', function(e, data) {
		$('#ccm-panel-detail-location-display').html(renderBreadcrumb({
			parentLink: '<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=' + data.cID,
			parentName: data.title,
			parentID: data.cID
		}));

		var container = $('form[data-panel-detail-form=location]');
		container.find('input[name=cParentID]').val(data.cID);
		$.fn.dialog.closeTop();
	});
});
</script>
