<?php

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\SetFactory;
use Concrete\Core\Attribute\StandardSetManager;
use Concrete\Core\Page;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Url\UrlImmutable $action
 * @var Concrete\Core\Url\UrlImmutable|null $back
 * @var Concrete\Core\Attribute\Category\CategoryInterface|null $category
 * @var Concrete\Core\Attribute\AttributeKeyInterface|null $key
 * @var Concrete\Core\Entity\Attribute\Type $type
 * @var string|null $akHandle
 */

$app = Application::getFacadeApplication();

$form = $app->make('helper/form');
$valt = $app->make('helper/validation/token');

if (!isset($category) || !$category instanceof CategoryInterface) {
    $category = null;
}
if (!isset($key) || !$key instanceof AttributeKeyInterface) {
    $key = null;
}
if (!isset($akHandle)) {
    $akHandle = $key === null ? '' : $key->getAttributeKeyHandle();
}
if (!isset($back)) {
    $back = $app->make(ResolverManagerInterface::class)->resolve([Page::getCurrentPage()]);
}

$asID = 0;
if ($key !== null) {
    $currentSets = $app->make(SetFactory::class)->getByAttributeKey($key);
    if (count($currentSets) === 1) {
        $asID = $currentSets[0]->getAttributeSetID();
    }
}
?>


<form method="post" action="<?= $action ?>" id="ccm-attribute-key-form">
    <?php
    echo $form->hidden('atID', $type->getAttributeTypeID());
    if ($key !== null) {
        echo $form->hidden('akID', $key->getAttributeKeyID());
    }
    ?>
    <fieldset>

        <legend><?= t('%s: Basic Details', $type->getAttributeTypeDisplayName()) ?></legend>

        <div class="form-group">
            <?= $form->label('akHandle', t('Handle')) ?>
            <div class="input-group">
                <?= $form->text('akHandle', $akHandle, ['autofocus' => 'autofocus']) ?>
                <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
            </div>
        </div>

        <div class="form-group">
            <?= $form->label('akName', t('Name')) ?>
            <div class="input-group">
                <?= $form->text('akName', $key === null ? '' : $key->getAttributeKeyName()) ?>
                <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
            </div>
        </div>

        <?php
        if ($category !== null && $category->getSetManager()->allowAttributeSets() == StandardSetManager::ASET_ALLOW_SINGLE) {
            ?>
            <div class="form-group">
                <?= $form->label('asID', t('Set')) ?>
                <div class="controls">
                    <?php
                    $sel = ['0' => t('** None')];
                    $sets = $category->getSetManager()->getAttributeSets();
                    foreach ($sets as $as) {
                        $sel[$as->getAttributeSetID()] = $as->getAttributeSetDisplayName();
                    }
                    echo $form->select('asID', $sel, $asID);
                    ?>
                </div>
            </div>
            <?php
        }
        ?>

        <div class="form-group">
            <label class="control-label"><?= t('Searchable') ?></label>
            <div class="checkbox">
                <label>
                    <?= $form->checkbox('akIsSearchableIndexed', 1, $key !== null && $key->isAttributeKeyContentIndexed()) ?>
                    <?= t('Content included in search index.') ?>
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <?= $form->checkbox('akIsSearchable', 1, $key === null || $key->isAttributeKeySearchable()) ?>
                    <?= t('Field available in advanced search.') ?>
                    <?php
                    if ($key && $key->isAttributeKeySearchable()) {
                        ?>
                        <div class="alert alert-danger small hide" id="akIsSearchable-warning">
                            <?= t(
                                'WARNING: you will need to re-run the %s automated job if you uncheck this value, save the attribute, and then re-check this value',
                                '<strong>' . t('Index Search Engine - All') . '</strong>'
                            ) ?>
                        </div>
                        <script>
                        $(document).ready(function() {
                            $('#akIsSearchable')
                                .on('change', function() {
                                    $('#akIsSearchable-warning').toggleClass('hide', $(this).is(':checked'))
                                })
                                .trigger('change')
                            ;
                        });
                        </script>
                        <?php
                    }
                    ?>
                </label>
            </div>
        </div>

    </fieldset>

    <?php
    if ($category && $category instanceof \Concrete\Core\Attribute\Category\StandardCategoryInterface) {
        echo $form->hidden('akCategoryID', $category->getCategoryEntity()->getAttributeKeyCategoryID());

        /** @TODO Catch the \Throwable error rather than suppressing errors */
        @View::element(
            'attribute/categories/' . $category->getCategoryEntity()->getAttributeKeyCategoryHandle(),
            ['key' => $key],
            $category->getCategoryEntity()->getPackageID() ? $category->getCategoryEntity()->getPackageHandle() : null
        );
    }
    $valt->output('add_or_update_attribute');
    $type->render(new \Concrete\Core\Attribute\Context\AttributeTypeSettingsContext(), isset($key) ? $key : null);
    ?>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?= $back ?>" class="btn pull-left btn-default"><?= t('Back') ?></a>
            <button type="submit" class="btn btn-primary pull-right"><?= $key === null ? t('Add') : t('Save') ?></button>
        </div>
    </div>

</form>
