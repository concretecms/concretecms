<?php defined('C5_EXECUTE') or die('Access Denied.');

$json = Loader::helper('json');
?>
<style>
i.handle {
    cursor:move;
}
tbody tr {
    cursor:pointer;
}
</style>
<?php

if ($editmode) {
    $pageTitle = t('Edit %s Authentication Type', $at->getAuthenticationTypeName());
    ?><form class="form-horizontal" method="post" action="<?=$view->action('save', $at->getAuthenticationTypeID())?>"><?php
}
if (!$editmode) {
    ?>
    <fieldset>
        <table class="table">
            <thead>
                <tr>
                    <th><?=t('ID')?></th>
                    <th><?=t('Display Name')?></th>
                    <th><?=t('Handle')?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody><?php
                foreach($ats as $at) {
                    ?><tr
                        data-authID="<?=$at->getAuthenticationTypeID()?>"
                        data-editURL="<?=h($view->action('edit', $at->getAuthenticationTypeID()))?>"
                        class="<?=$at->isEnabled() ? 'success' : 'error'?>"
                    >
                        <td><?=$at->getAuthenticationTypeID()?></td>
                        <td><?=$at->getAuthenticationTypeName()?></td>
                        <td><?=$at->getAuthenticationTypeHandle()?></td>
                        <td style="text-align:right"><i class="handle icon-resize-vertical"></i></td>
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
               handle: 'i.handle',
               helper: function(e, ui) {
                   ui.children().each(function() {
                       var me = $(this);
                       me.width(me.width());
                   });
                   return ui;
               },
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
    <fieldset>
        <?=$at->renderTypeForm()?>
    </fieldset>
    <?php
}

if ($editmode) {
    ?>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=$view->action('')?>" class="btn btn-default pull-left"><?=t('Cancel')?></a>
            <span class="pull-right">
                <a href="<?=$view->action($at->isEnabled() ? 'disable' : 'enable', $at->getAuthenticationTypeID())?>" class="btn btn-<?=$at->isEnabled() ? 'danger' : 'success'?>">
                    <?=$at->isEnabled() ? t('Disable') : t('Enable')?>
                </a>
                <button class='btn btn-primary'><?=t('Save')?></button>
            </span>
           </div>
        </div>
    </form>
    <?
}
