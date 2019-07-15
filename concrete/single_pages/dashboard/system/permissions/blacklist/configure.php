<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Page\View\PageView $view */
/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Controller\SinglePage\Dashboard\System\Permissions\Blacklist\Configure $controller */

/* @var Concrete\Core\Entity\Permission\IpAccessControlCategory $category */
/* @var array $units */

$view->element('dashboard/system/permissions/blacklist/menu', ['category' => $category, 'type' => null]);
?>

<form method="post" id="ipblacklist-form" action="<?= $view->action('update_ipblacklist', $category->getIpAccessControlCategoryID()) ?>">
    <?php $token->output('update_ipblacklist-' . $category->getIpAccessControlCategoryID()) ?>
    <div class="ccm-pane-body">
        <div class="form-group form-inline">
            <?= $form->checkbox('banEnabled', 1, $category->isEnabled()) ?>
            <?php
            list($selectedUnit, $unitValue) = $controller->splitSeconds($category->getTimeWindow());
            echo t(
                /* i18n: %1$s is the number of events, %2$s is the number of seconds/minutes/hours/days, %3$s is "seconds", "minutes", "hours" or "days" */
                'Lock IP after %1$s events in %2$s %3$s',
                $form->number('maxEvents', $category->getMaxEvents(), ['style' => 'width:90px', 'required' => 'required', 'min' => 1]),
                $form->number('timeWindowValue', $unitValue, ['style' => 'width:90px', 'min' => 1]),
                $form->select('timeWindowUnit', $units, $selectedUnit)
            );
            ?>
        </div>

        <div class="form-group form-inline">
            <?= $form->label('banDurationUnlimited', t('Ban Duration'))?>
            <br />
            <div class="radio">
                <label>
                    <?= $form->radio('banDurationUnlimited', '0', $category->getBanDuration() === null ? '1' : '0') ?>
                    <?php
                    list($selectedUnit, $unitValue) = $controller->splitSeconds($category->getBanDuration() === null ? 300 : $category->getBanDuration());
                    echo t(
                        /* i18n: %1$s is the number of seconds/minutes/hours/days, %2$s is "seconds", "minutes", "hours" or "days" */
                        'Ban IP for %1$s %2$s',
                        $form->number('banDurationValue', $unitValue, ['style' => 'width:90px', 'min' => 1]),
                        $form->select('banDurationUnit', $units, $selectedUnit)
                    );
                    ?>
                </label>
            </div>
            <br />
            <div class="radio">
                <label>
                    <?= $form->radio('banDurationUnlimited', '1', $category->getBanDuration() === null ? '1' : '0') ?>
                    <?= t('Ban Forever') ?>
                </label>
            </div>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <input type="submit" class="btn btn-primary pull-right" value="<?= t('Save') ?>" />
        </div>
    </div>
</form>
