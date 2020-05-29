<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php

$type_menu->render();

if ($skeleton) {
?>

<div class="alert alert-info"><?=t('Attributes set here will automatically be applied to new pages of that type.')?></div>

<div data-container="editable-fields">

    <?php Loader::element('attribute/editable_set_list', array(
        'category' => $category,
        'object' => $skeleton,
        'saveAction' => $view->action('update_attribute', $type->getSiteTypeID()),
        'clearAction' => $view->action('clear_attribute', $type->getSiteTypeID()),
        'permissionsCallback' => function ($ak) {
            return true;
        },
    ));?>

</div>


<script type="text/javascript">
    $(function() {
        $('div[data-container=editable-fields]').concreteEditableFieldContainer({
            url: '<?=$view->action('save', $type->getSiteTypeID())?>',
            data: {
                ccm_token: '<?=Loader::helper('validation/token')->generate()?>'
            }
        });
    });
</script>

<?php } else { ?>

    <div class="alert alert-warning"><?=t('Unable to retrieve skeleton object. You cannot set attributes on the default site type.')?></div>

<?php } ?>