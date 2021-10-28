<?php
defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\Dialog\Type\Attributes $controller
 * @var Concrete\Core\Filesystem\Element $keySelector
 */
?>

<form method="post" action="<?=$controller->action('submit')?>" data-dialog-form="site-type-attributes">

    <div class="ccm-ui">
        <?php

            $keySelector->render();

        ?>
    </div>
    <div class="dialog-buttons">
        <button class="btn btn-secondary" data-dialog-action="cancel"><?=t('Cancel')?></button>
        <button type="button" data-dialog-action="submit" class="btn btn-primary"><?=t('Save')?></button>
    </div>

</form>


<script type="text/javascript">
    $(function() {
        ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.saveAttributesForm');
        ConcreteEvent.subscribe('AjaxFormSubmitSuccess.saveAttributesForm', function(e, data) {
            setTimeout(function() {
                window.location.reload()
            }, 2000);
        });
    });
</script>

