<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Controller\SinglePage\Dashboard\System\Registration\Authentication $controller
 * @var Concrete\Core\Authentication\AuthenticationType[] $autenticationTypes
 */
?>
<table class="table" id="ccm-authentication-types">
    <thead>
        <tr>
            <th></th>
            <th><?= t('ID') ?></th>
            <th><?= t('Handle') ?></th>
            <th><?= t('Display Name') ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($autenticationTypes as $at) {
            ?>
            <tr
                data-authID="<?= $at->getAuthenticationTypeID() ?>"
                data-editURL="<?= h($controller->action('edit', $at->getAuthenticationTypeID())) ?>"
                class="<?= $at->isEnabled() ? 'table-success' : 'table-danger' ?>"
            >
                <td class="ccm-authenticationtype-icon"><div><?= $at->getAuthenticationTypeIconHTML() ?></div></td>
                <td class="ccm-authenticationtype-id"><?= $at->getAuthenticationTypeID() ?></td>
                <td><?= h($at->getAuthenticationTypeHandle()) ?></td>
                <td><?= $at->getAuthenticationTypeDisplayName() ?></td>
                <td style="text-align:right"><i style="cursor: move" class="fas fa-arrows-alt-v ccm-authenticationtype-move"></i></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>

<script>
$(document).ready(function() {
    var $sortableTable = $('#ccm-authentication-types>tbody');

    $sortableTable.sortable({
        handle: '.ccm-authenticationtype-move',
        helper: function(e, ui) {
        	ui.children().each(function() {
            	var $this = $(this);
            	$this.width($this.width());
            });
            return ui;
        },
        cursor: 'move',
        axis: 'y',
        stop: function(e, ui) {
            var order = [];
            $sortableTable.children().each(function() {
                order.push($(this).attr('data-authID'));
            });
            $.concreteAjax({
                method: 'POST',
                url: <?= json_encode((string) $controller->action('reorder')) ?>,
                data: {
                    <?= json_encode($token::DEFAULT_TOKEN_NAME)?>: <?= json_encode($token->generate('authentication_reorder')) ?>,
                    order: order,
                },
                dataType: 'json',
            });
        }
    });
    $('#ccm-authentication-types>tbody>tr').on('click', function() {
        location.href = $(this).attr('data-editURL');
    });
});
</script>
