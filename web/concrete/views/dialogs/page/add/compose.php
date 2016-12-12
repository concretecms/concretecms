<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-ui">
    <form data-dialog-form="add-page-compose" action="<?=$controller->action('submit')?>">
        <? $pagetype->renderComposerOutputForm(null, $parent); ?>
        <input type="hidden" name="addPageComposeAction" value="preview" />
        <div class="dialog-buttons">
            <button type="button" data-dialog-action="cancel" class="btn btn-default pull-left"><?=t('Cancel')?></button>
            <button type="button" data-composer-dialog-action="publish" value="publish" class="btn btn-primary pull-right"><?=t('Publish Page')?></button>
            <button type="button" data-dialog-action="submit" value="preview" data-page-type-composer-form-btn="preview" class="btn btn-success pull-right"><?=t('Edit Mode')?></button>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(function() {
        $('form[data-dialog-form=add-page-compose]').concreteAjaxForm();
        $('button[data-composer-dialog-action=publish]').on('click', function() {
            $('form[data-dialog-form=add-page-compose] input[name=addPageComposeAction]').val('publish');
            $('form[data-dialog-form=add-page-compose]').submit();
        });
        ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.addPageCompose');
        ConcreteEvent.subscribe('AjaxFormSubmitSuccess.addPageCompose', function(e, data) {
            if (data.response.cParentID) {
                ConcreteEvent.publish('SitemapAddPageRequestComplete', {'cParentID': data.response.cParentID});
            }
        });
    });
</script>