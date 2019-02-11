<?php
defined('C5_EXECUTE') or die('Access Denied.');
/* @var Concrete\Controller\Dialog\Page\DeleteAlias $controller */
/* @var Concrete\Core\View\DialogView $view */
/* @var Concrete\Core\Page\Page $c */
?>
<div class="ccm-ui">

    <form class="form-stacked" data-dialog-form="delete-alias" method="post" action="<?=$controller->action('submit')?>">

        <p><?= $c->isExternalLink() ? t('Remove this external link?') : t('Remove this alias?') ?></p>

        <div class="dialog-buttons">
            <button class="btn btn-default pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
            <button type="button" data-dialog-action="submit" class="btn btn-danger pull-right"><?=t('Delete')?></button>
        </div>
    </form>

    <script type="text/javascript">
        $(function() {
            ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.sitemapDelete');
            ConcreteEvent.subscribe('AjaxFormSubmitSuccess.sitemapDelete', function(e, data) {
                if (data.form == 'delete-alias') {
                    ConcreteEvent.publish('SitemapDeleteRequestComplete', {'cID': '<?=$c->getCollectionID()?>'});
                }
            });
        });
    </script>

</div>
