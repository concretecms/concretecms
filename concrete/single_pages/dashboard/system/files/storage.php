<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Controller\SinglePage\Dashboard\System\Files\Storage $controller */
/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Core\Page\Page $c */
/* @var Concrete\Core\Page\View\PageView $view */
/* @var Concrete\Core\Validation\CSRF\Token $token */

switch ($controller->getTask()) {
    case 'select_type':
    case 'add':
    case 'edit':
    case 'update':
    case 'delete':
        /* @var Concrete\Core\Entity\File\StorageLocation\Type\Type $type */
        if (!isset($location) || !is_object($location)) {
            $location = null;
        }
        if ($location !== null) {
            /* @var Concrete\Core\Entity\File\StorageLocation\StorageLocation $location */
            $fslName = $location->getName();
            $fslIsDefault = $location->isDefault();
            $method = 'update';
            if (!$fslIsDefault && $type->getHandle() != 'default') {
                ?>
                <div class="ccm-dashboard-header-buttons">
                    <form method="post" action="<?= $view->action('delete') ?>">
                        <input type="hidden" name="fslID" value="<?= $location->getID() ?>" />
                        <?= $token->output('delete') ?>
                        <button type="button" class="btn btn-danger" data-action="delete-location"><?= t('Delete Location') ?></button>
                    </form>
                </div>
                <?php
            }
        } else {
            $fslName = '';
            $fslIsDefault = false;
            $method = 'add';
        }
        ?>
        <form method="post" action="<?= $view->action($method) ?>" id="ccm-attribute-key-form">
            <?= $token->output($method) ?>
            <input type="hidden" name="fslTypeID" value="<?= $type->getID() ?>" />
            <?php
            if ($location !== null) {
                ?><input type="hidden" name="fslID" value="<?= $location->getID() ?>" /><?php
            }
            ?>
            <fieldset>
                <legend><?= t('Basics') ?></legend>
                <div class="form-group">
                    <?= $form->label('fslName', t('Name')) ?>
                    <div class="input-group">
                        <?= $form->text('fslName', $fslName) ?>
                        <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
                    </div>
                </div>
                <?php
                if ($fslIsDefault) {
                    $args = ['disabled' => 'disabled'];
                } else {
                    $args = [];
                }
                ?>
                <div class="form-group">
                    <label><?= t('Default') ?></label>
                    <div class="radio">
                        <label>
                            <?= $form->radio('fslIsDefault', 1, $fslIsDefault, $args) ?>
                            <?= t('Yes, make this the default storage location for new files.') ?>
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <?= $form->radio('fslIsDefault', 0, $fslIsDefault, $args) ?>
                            <?= t('No, this is not the default storage location.') ?>
                        </label>
                    </div>
                </div>
            </fieldset>
            <?php
            if ($type->hasOptionsForm()) {
                ?>
                <fieldset>
                    <legend><?= t('Options %s Storage Type', $type->getName()) ?></legend>
                    <?php $type->includeOptionsForm($location) ?>
                </fieldset>
                <?php
            }
            ?>
            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <a href="<?= URL::to($c) ?>" class="btn pull-left btn-default"><?= t('Back') ?></a>
                    <?php
                    if ($location !== null) {
                        ?><button type="submit" class="btn btn-primary pull-right"><?= t('Save') ?></button><?php
                    } else {
                        ?><button type="submit" class="btn btn-primary pull-right"><?= t('Add') ?></button><?php
                    }
                    ?>
                </div>
            </div>
        </form>
        <script>
            $(function() {
                $('button[data-action=delete-location]').on('click', function(e) {
                    e.preventDefault();
                    if (confirm(<?= json_encode(t('Delete this storage location? All files using it will have their storage location reset to the default.')) ?>)) {
                        $(this).closest('form').submit();
                    }
                });
            });
        </script>
        <?php
        break;

    default:
        ?>
        <h3><?= t('Storage Locations') ?></h3>
        <ul class="item-select-list">
            <?php
            foreach ($locations as $location) {
                ?><li><a href="<?= $view->action('edit', $location->getID()) ?>"><i class="fa fa-hdd-o"></i> <?= $location->getDisplayName() ?></a></li><?php
            }
            ?>
        </ul>
        <form method="get" action="<?= $view->action('select_type') ?>" id="ccm-file-storage-location-type-form">
            <fieldset>
                <legend><?= t('Add Location') ?></legend>
                <label for="atID"><?= t('Choose Type') ?></label>
                <div class="form-inline">
                    <div class="form-group">
                        <?= $form->select('fslTypeID', $types) ?>
                    </div>
                    <button type="submit" class="btn btn-default"><?= t('Go') ?></button>
                </div>
            </fieldset>
        </form>
        <?php
        break;
}
