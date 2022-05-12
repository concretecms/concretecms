<?php

use Concrete\Core\Entity\Permission\IpAccessControlCategory;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Page\View\PageView $view
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Controller\SinglePage\Dashboard\System\Permissions\Denylist\Configure $controller
 * @var Concrete\Core\Entity\Permission\IpAccessControlCategory $category
 * @var array $units
 */

$view->element('dashboard/system/permissions/denylist/menu', ['category' => $category, 'type' => null]);
?>

<form method="post" id="ipdenylist-form" action="<?= $view->action('update_ipdenylist', $category->getIpAccessControlCategoryID()) ?>">
    <?php $token->output('update_ipdenylist-' . $category->getIpAccessControlCategoryID()) ?>
    <div class="ccm-pane-body">
        <div class="form-group row row-cols-auto gx-3 align-items-center">
            <div class="col-auto">
                <?= $form->checkbox('banEnabled', 1, $category->isEnabled()) ?>
            </div>
            <?php
            $timeWindowSplitted = IpAccessControlCategory::splitTimeWindow($category->getTimeWindow());
            [$unitValue, $selectedUnit] = $timeWindowSplitted === null ? ['', 'duration/minute'] : $timeWindowSplitted;
            ?>
            <?php
            echo t(
                /* i18n: %1$s is the number of events, %2$s is the number of seconds/minutes/hours/days, %3$s is "seconds", "minutes", "hours" or "days" */
                'Lock IP after %1$s events in %2$s %3$s',
                sprintf('<div class="col-auto">%s</div>', $form->number('maxEvents', $category->getMaxEvents(), ['style' => 'width:90px', 'required' => 'required', 'min' => 1])),
                sprintf('<div class="col-auto">%s</div>', $form->number('timeWindowValue', $unitValue, ['style' => 'width:90px', 'min' => 1])),
                sprintf('<div class="col-auto">%s</div>', $form->select('timeWindowUnit', $units, $selectedUnit))
            );
            ?>
        </div>

        <div class="form-group row row-cols-auto gx-3 align-items-center">
            <?= $form->label('banDurationUnlimited', t('Ban Duration:'), ['class'=>'form-check-label','style'=>'margin-right:9px;'])?>
            <div class="col-auto form-check">
                <label>
                    <?= $form->radio('banDurationUnlimited', '0', $category->getBanDuration() === null ? '1' : '0') ?>
                </label>
            </div>
            <?php
            [$unitValue, $selectedUnit] = IpAccessControlCategory::splitTimeWindow($category->getBanDuration() ?: 300);
            echo t(
                /* i18n: %1$s is the number of seconds/minutes/hours/days, %2$s is "seconds", "minutes", "hours" or "days" */
                'Ban IP for %1$s %2$s',
                sprintf('<div class="col-auto">%s</div>', $form->number('banDurationValue', $unitValue, ['style' => 'width:90px', 'min' => 1])),
                sprintf('<div class="col-auto">%s</div>', $form->select('banDurationUnit', $units, $selectedUnit))
            );
            ?>
            <div class="col-auto" style="margin: 0 9px;"><?=t('or')?></div>
            <div class="col-auto form-check">
                <label>
                    <?= $form->radio('banDurationUnlimited', '1', $category->getBanDuration() === null ? '1' : '0') ?>
                    <?= t('Ban Forever') ?>
                </label>
            </div>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <input type="submit" class="btn btn-primary float-end" value="<?= t('Save') ?>" />
        </div>
    </div>
</form>
