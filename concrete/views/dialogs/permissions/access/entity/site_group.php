<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Entity\Site\Group\Group $groups
 * @var string $url
 * @var Concrete\Core\Validation\CSRF\Token $token
 */

if ($groups !== []) {
    ?>
    <ul class="item-select-list">
        <?php
        foreach ($groups as $group) {
            ?>
            <li><a href="#" data-access-entity-group="<?= $group->getSiteGroupID() ?>"><i class="fas fa-users"></i> <?= h($group->getSiteType()->getSiteTypeName()) ?>: <?= h($group->getSiteGroupName()) ?></a></li>
            <?php
        }
        ?>
    </ul>
    <?php
} else {
    ?>
    <p><?= t('You have not added any site groups.') ?></p>
    <?php
}
?>
<script>
$(function() {
    $('a[data-access-entity-group]').on('click', function(e) {
        e.preventDefault();
        var groupID = $(this).attr('data-access-entity-group');
        $('#ccm-permissions-access-entity-form .btn-group').removeClass('open');
        $.concreteAjax({
            url: <?= json_encode((string) $url) ?>,
            data: {
                siteGID: groupID,
                ccm_token: <?= json_encode($token->generate('get_or_create')) ?>
           },
           success: function(r) {
               jQuery.fn.dialog.hideLoader();
               jQuery.fn.dialog.closeTop();
               $('#ccm-permissions-access-entity-form input[name=peID]').val(r.peID);
               $('#ccm-permissions-access-entity-label').html('<div class="alert alert-info">' + r.label + '</div>');
           }
       });
   });
});
</script>
