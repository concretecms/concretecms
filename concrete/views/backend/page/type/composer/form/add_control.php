<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Page\Type\Composer\Control\Type\CollectionAttributeType;

/**
 * @var Concrete\Core\Application\Service\UserInterface $ui
 * @var Concrete\Core\Page\Type\Composer\Control\Type\Type[] $types
 * @var Concrete\Core\Page\Type\Composer\FormLayoutSet $set
 */

?>
<div class="ccm-ui">
    <?php
    $tabs = [];
    foreach ($types as $index => $type) {
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
            <div class="tab-pane fade <?= $index == 0 ? 'active show' : '' ?> " id="<?= $type->getPageTypeComposerControlTypeHandle() ?>">
                <input class="form-control ccm-input-text" type="text" name="attribute-search" placeholder="<?= t('Search attributes') ?>" />
                <ul data-list="page-type-composer-control-type" class="item-select-list">
                    <?php
                    if ($type instanceof CollectionAttributeType) {
                        $grouped = $type->getPageTypeComposerControlObjectsBySet();
                        foreach ($grouped['sets'] as $group) {
                            ?>
                            <li class="ccm-attribute-set-header" data-set-header><strong><?= $group['set']->getAttributeSetDisplayName() ?></strong></li>
                            <?php
                            foreach ($group['controls'] as $control) {
                                ?>
                                <li data-attribute-row>
                                    <a href="#" data-control-type-id="<?= $type->getPageTypeComposerControlTypeID() ?>" data-control-identifier="<?= $control->getPageTypeComposerControlIdentifier() ?>">
                                        <?= $control->getPageTypeComposerControlIcon() ?>
                                        <?= $control->getPageTypeComposerControlDisplayName() ?>
                                        <span class="text-muted small">(<?= h($control->getAttributeKeyObject()->getAttributeKeyHandle()) ?>)</span>
                                    </a>
                                </li>
                                <?php
                            }
                        }
                        if (!empty($grouped['unassigned'])) {
                            if (!empty($grouped['sets'])) {
                                ?>
                                <li class="ccm-attribute-set-header" data-set-header><strong><?= t('Other') ?></strong></li>
                                <?php
                            }
                            foreach ($grouped['unassigned'] as $control) {
                                ?>
                                <li data-attribute-row>
                                    <a href="#" data-control-type-id="<?= $type->getPageTypeComposerControlTypeID() ?>" data-control-identifier="<?= $control->getPageTypeComposerControlIdentifier() ?>">
                                        <?= $control->getPageTypeComposerControlIcon() ?>
                                        <?= $control->getPageTypeComposerControlDisplayName() ?>
                                        <span class="text-muted small">(<?= h($control->getAttributeKeyObject()->getAttributeKeyHandle()) ?>)</span>
                                    </a>
                                </li>
                                <?php
                            }
                        }
                    } else {
                        // Third-party packages register custom control types that only implement the
                        // abstract getPageTypeComposerControlObjects(). They will never be instances of
                        // CollectionAttributeType, so this flat path must remain to avoid breaking them.
                        $controls = $type->getPageTypeComposerControlObjects();
                        foreach ($controls as $control) {
                            ?>
                            <li data-attribute-row>
                                <a href="#" data-control-type-id="<?= $type->getPageTypeComposerControlTypeID() ?>" data-control-identifier="<?= $control->getPageTypeComposerControlIdentifier() ?>">
                                    <?= $control->getPageTypeComposerControlIcon() ?>
                                    <?= $control->getPageTypeComposerControlDisplayName() ?>
                                </a>
                            </li>
                            <?php
                        }
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

        // data-attribute-row scopes the selector to real items only — set-header <li> elements
        // have no data-attribute-row and must not be toggled here.
        $list.find('li[data-attribute-row]').each(function() {
            $(this).toggle(this.innerText.toLowerCase().indexOf($search.val().toLowerCase()) !== -1);
        });

        // After filtering rows, hide any set-header whose visible rows have all been filtered out.
        $list.find('li[data-set-header]').each(function() {
            $(this).toggle(
                $(this).nextUntil('li[data-set-header]', 'li[data-attribute-row]:visible').length > 0
            );
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
            // Note: this endpoint has no CSRF token — it's a pre-existing gap, not introduced here.
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
