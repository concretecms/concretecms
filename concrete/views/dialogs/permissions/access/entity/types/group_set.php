<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var string $url
 * @var Concrete\Core\User\Group\GroupSetList $gl
 */

?>
<ul class="item-select-list" id="ccm-list-wrapper">
    <?php
    if ($gl->getTotal() > 0) {
        foreach ($gl->get() as $gs) {
            ?>
            <li>
                <a href="javascript:void(0)" onclick="ccm_selectGroupSet(<?= $gs->getGroupSetID() ?>)">
                    <i class="fas fa-users"></i>
                    <?= h($gs->getGroupSetDisplayName()) ?>
                </a>
            </li>
            <?php
        }
    } else {
        ?>
        <p><?= t('No group sets found.') ?></p>
        <?php
    }
    ?>
</ul>
<script>
ccm_selectGroupSet = function(gsID) {
    $('#ccm-permissions-access-entity-form .btn-group').removeClass('open');
    $.getJSON(
        <?= json_encode((string) $url) ?>,
        {
            gsID: gsID
        },
        function(r) {
            $.fn.dialog.hideLoader();
            $.fn.dialog.closeTop();
            $('#ccm-permissions-access-entity-form input[name=peID]').val(r.peID);
            $('#ccm-permissions-access-entity-label').html('<div class="alert alert-info">' + r.label + '</div>');
        }
    );
};
</script>
