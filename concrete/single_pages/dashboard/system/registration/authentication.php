<?php defined('C5_EXECUTE') or die('Access Denied.');

$json = Loader::helper('json');
if (!isset($editmode)) {
    $editmode = null;
}
?>
<style>
    .table.authentication-types i.handle {
        cursor:move;
    }
    .table.authentication-types tbody tr {
        cursor:pointer;
    }
    .table.authentication-types .ccm-concrete-authentication-type-svg > svg {
        width:20px;
        display:inline-block;
    }
</style>
<?php

if ($editmode) {
    $pageTitle = t('Edit %s Authentication Type', $at->getAuthenticationTypeDisplayName());
    ?><form class="form-stacked" method="post" action="<?=$view->action('save', $at->getAuthenticationTypeID())?>"><?php

    $token = \Core::make('token');
    $token->output("auth_type_save.{$at->getAuthenticationTypeID()}");
    echo $form->getAutocompletionDisabler();
}
if (!$editmode) {
    ?>
    <fieldset>
        <table class="table authentication-types">
            <thead>
                <tr>
                    <th></th>
                    <th><?=t('ID')?></th>
                    <th><?=t('Handle')?></th>
                    <th><?=t('Display Name')?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody><?php
                foreach ($ats as $at) {
                    ?><tr
                        data-authID="<?=$at->getAuthenticationTypeID()?>"
                        data-editURL="<?=h($view->action('edit', $at->getAuthenticationTypeID()))?>"
                        class="<?=$at->isEnabled() ? 'success' : 'error'?>">
                        <td style="overflow:hidden; text-align: center; width: 50px">
                            <div style='height:15px'>
                                <?=$at->getAuthenticationTypeIconHTML()?>
                            </div>
                        </td>
                        <td style="width: 100px"><?=$at->getAuthenticationTypeID()?></td>
                        <td><?=$at->getAuthenticationTypeHandle()?></td>
                        <td><?=$at->getAuthenticationTypeDisplayName()?></td>
                        <td style="text-align:right"><i style="cursor: move" class="fa fa-arrows"></i></td>
                    </tr><?php

                }
    ?></tbody>
        </table>
    </fieldset>
    <script type="text/javascript">
    (function($,location){
        'use strict';
        $(function(){
            var sortableTable = $('table.table tbody');
            sortableTable.sortable({
               handle: 'i.fa-arrows',
               helper: function(e, ui) {
                   ui.children().each(function() {
                       var me = $(this);
                       me.width(me.width());
                   });
                   return ui;
               },
               cursor: 'move',
               stop: function(e, ui) {
                   var order = [];
                   sortableTable.children().each(function() {
                       var me = $(this);
                       order.push(me.attr('data-authID'));
                   });
                   $.post('<?=$view->action('reorder')?>', {order: order});
               }
            });
            $('tbody tr').click(function() {
                location.href = $(this).attr('data-editURL');
            });
        });
    })(jQuery, window.location);
    </script>
    <?php

} else {
    ?>
    <?=$at->renderTypeForm()?>
    <?php

}

if ($editmode) {
    $url = $view->action($at->isEnabled() ? 'disable' : 'enable', $at->getAuthenticationTypeID());
    $url = Concrete\Core\Url\Url::createFromUrl($url);
    $url = $url->setQuery(array('ccm_token' => $token->generate("auth_type_toggle.{$at->getAuthenticationTypeID()}")));
    ?>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=$view->action('')?>" class="btn btn-default pull-left"><?=t('Cancel')?></a>
            <span class="pull-right">
                <a href="<?= $url ?>" class="btn btn-<?=$at->isEnabled() ? 'danger' : 'success'?>">
                    <?=$at->isEnabled() ? t('Disable') : t('Enable')?>
                </a>
                <button class='btn btn-primary'><?=t('Save')?></button>
            </span>
           </div>
        </div>
    </form>
    <?php

}
