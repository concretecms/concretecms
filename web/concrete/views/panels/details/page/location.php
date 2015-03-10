<?
defined('C5_EXECUTE') or die("Access Denied.");
?>
<section class="ccm-ui">
	<header><?=t('Location')?></header>
	<form method="post" action="<?=$controller->action('submit')?>" data-dialog-form="location" data-panel-detail-form="location">

		<?=Loader::helper('concrete/ui/help')->notify('panel', '/page/location')?>
        <input type="hidden" name="cParentID" value="<?=$cParentID?>" />

        <? if (!isset($sitemap) && $sitemap == false) { ?>
            <div style="min-height: 140px">
                <? if ($c->isPageDraft()) { ?>
                    <p class="lead"><?=t('Where will this page live on the site?')?></p>
                <? } else { ?>
                    <p class="lead"><?=t('Where does this page live on the site?')?></p>
                <? } ?>

                <div id="ccm-panel-detail-location-display"></div>

                <button class="btn btn-info"type="button" name="location"><?=t('Choose Location')?></button>

            </div>
		<hr/>
        <? } ?>

	<? if ($c->isGeneratedCollection() || $c->isPageDraft()) { ?>

		<p class="lead"><?=t('Current Canonical URL')?></p>
		<div class="breadcrumb">
			<? if ($c->isPageDraft()) { ?>
				<?=t('None. Pages do not have canonical URLs until they are published.')?>
			<? } else { ?>
				<?php echo Loader::helper('navigation')->getLinkToCollection($c, true)?>
			<? } ?>
		</div>

	<? } else { ?>

		<p class="lead"><?=t('URLs to this Page')?></p>

		<div class="col-md-6">
		<table class="table table-striped">
			<thead>
			<tr>
				<th></th>
				<th><?=t('Canonical')?></th>
				<th style="width: 100%"><?=t('Path')?></th>
			</tr>
			</thead>
			<tbody>
			<? foreach($paths as $path) { ?>
				<tr>
				<td><i class="fa fa-link"></i></td>
				<td><input type="radio" /></td>
				<td><?=$path->getPagePath()?></td>
				</tr>
			<? } ?>
			</tbody>
		</table>
		</div>
		<? /*
		<hr/>
		<p class="lead"><?=t('Other URLs that should redirect to this page')?></p>

		<div id="ccm-panel-detail-location-page-paths">

		</div>

		<button class="btn btn-info" type="button" name="addRedirect"><?=t('Add URL Redirect')?></button>

		<br/><br/>
 		<span class="help-block"><?=t('Note: Additional page paths are not versioned. They will be available immediately.')?></span>
		*/
		?>


	<? } ?>

	<? if (isset($sitemap) && $sitemap) { ?>
		<input type="hidden" name="sitemap" value="1" />
	<? } ?>

	</form>
	<div class="ccm-panel-detail-form-actions dialog-buttons">
		<button class="pull-left btn btn-default" type="button" data-dialog-action="cancel" data-panel-detail-action="cancel"><?=t('Cancel')?></button>
		<button class="pull-right btn btn-success" type="button" data-dialog-action="submit" data-panel-detail-action="submit"><?=t('Save Changes')?></button>
	</div>

</section>

<script type="text/template" class="breadcrumb">
	<% if (parentID && parentID > 0) { %>
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
    <div class="form-group">
        <div class="input-group">
	    <input class="form-control" type="text" name="additionalPath[]" value="<%=path%>" />
    	<a href="#" class="input-group-addon"><i class="fa fa-minus-circle" /></a>
        </div>
    </div>
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
			href: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/sitemap_search_selector?cID=<?=$c->getCollectionID()?>'
		});
	});
	/*
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
				path: ''
			})
		);
	});

    <?/* foreach($additionalPaths as $path) { ?>
    $('#ccm-panel-detail-location-page-paths').append(
        renderPagePath({path: '<?=$path->getPagePath()?>'})
    );
    <? } */ ?>

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
	*/
});
</script>
