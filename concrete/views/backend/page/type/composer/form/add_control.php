<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Service\UserInterface $ui
 * @var Concrete\Core\Page\Type\Composer\Control\Type\Type[] $types
 * @var Concrete\Core\Page\Type\Composer\FormLayoutSet $set
 */

?>
<div class="ccm-ui">
    <?php
    $tabs = [];
    foreach($types as $index => $type) {
        $tabs[] = [
            $type->getPageTypeComposerControlTypeHandle(),
            $type->getPageTypeComposerControlTypeDisplayName(),
            $index === 0,
        ];
    }
    echo $ui->tabs($tabs);
    ?>
    <div class="tab-content ccm-tab-content" >
        <?php
        foreach ($types as $index => $type) {
            ?>
            <div class="tab-pane fade <?= $index == 0 ? 'active' : ''; ?> " id="<?= $type->getPageTypeComposerControlTypeHandle() ?>">
                <input class="form-control ccm-input-text" type="text" name="attribute-search" placeholder="<?= t('Search attributes') ?>" />
                <ul data-list="page-type-composer-control-type" class="item-select-list">
                    <?php
                    $controls = $type->getPageTypeComposerControlObjects();
                    foreach ($controls as $control) {
                        ?>
                        <li>
                            <a href="#" data-control-type-id="<?= $type->getPageTypeComposerControlTypeID() ?>" data-control-identifier="<?= $control->getPageTypeComposerControlIdentifier() ?>">
                                <?= $control->getPageTypeComposerControlIcon() ?>
                                <?= $control->getPageTypeComposerControlDisplayName() ?>
                            </a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $('input[name="attribute-search"]').on('keyup', function() {
        var $search = $(this),
            $list = $search.siblings('[data-list="page-type-composer-control-type"]');

        $list.find('li').each(function() {
            if (this.innerText.toLowerCase().indexOf($search.val().toLowerCase()) === -1 ) {
                $(this).hide();
            }
            else {
                $(this).show();
            }
        });
    });

    $('ul[data-list=page-type-composer-control-type] a').on('click', function() {
        var $this = $(this);
        jQuery.fn.dialog.showLoader();
        $.ajax({
            type: 'POST',
            data: {
                ptComposerControlTypeID: $this.data('control-type-id'),
                ptComposerControlIdentifier: $this.data('control-identifier'),
                ptComposerFormLayoutSetID: <?= $set->getPageTypeComposerFormLayoutSetID() ?>
            },
            url: CCM_DISPATCHER_FILENAME + '/ccm/system/page/type/composer/form/add_control/pick',
            success: function(html) {
                jQuery.fn.dialog.hideLoader();
                jQuery.fn.dialog.closeTop();
                $('div[data-page-type-composer-form-layout-control-set-id=<?= $set->getPageTypeComposerFormLayoutSetID() ?>] tbody.ccm-item-set-inner').append(html);
                $('a[data-command=edit-form-set-control]').dialog();
            }
        });
    });
});
</script>
