<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>


<form method="post" action="<?= $action ?>" id="ccm-attribute-key-form">
    <?php

    $c = Page::getCurrentPage();

    $form = Loader::helper('form');
    $ih = Loader::helper("concrete/ui");
    $valt = Loader::helper('validation/token');
    $akName = '';
    $akIsSearchable = 1;
    $asID = 0;

    if (is_object($key)) {
        if (!isset($akHandle)) {
            $akHandle = $key->getAttributeKeyHandle();
        }
        $akName = $key->getAttributeKeyName();
        $akIsSearchable = $key->isAttributeKeySearchable();
        $akIsSearchableIndexed = $key->isAttributeKeyContentIndexed();
        $sets = $category->getAttributeSets();
        if (count($sets) == 1) {
            $asID = $sets[0]->getAttributeSetID();
        }
        print $form->hidden('akID', $key->getAttributeKeyID());
    }
    ?>


    <fieldset>
        <legend><?= t('%s: Basic Details', $type->getAttributeTypeDisplayName()) ?></legend>

        <div class="form-group">
            <?= $form->label('akHandle', t('Handle')) ?>
            <div class="input-group">
                <?= $form->text('akHandle', $akHandle) ?>
                <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
            </div>
        </div>


        <div class="form-group">
            <?= $form->label('akName', t('Name')) ?>
            <div class="input-group">
                <?= $form->text('akName', $akName) ?>
                <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
            </div>
        </div>

        <? if ($category && $category->allowAttributeSets() == \Concrete\Core\Attribute\EntityInterface::ASET_ALLOW_SINGLE) { ?>
            <div class="form-group">
                <?= $form->label('asID', t('Set')) ?>
                <div class="controls">
                    <?
                    $sel = array('0' => t('** None'));
                    $sets = $category->getAttributeSets();
                    foreach ($sets as $as) {
                        $sel[$as->getAttributeSetID()] = $as->getAttributeSetDisplayName();
                    }
                    print $form->select('asID', $sel, $asID);
                    ?>
                </div>
            </div>
        <? } ?>

        <div class="form-group">
            <label class="control-label"><?= t('Searchable') ?></label>

            <?php
            $keyword_label = t('Content included in search index.');
            $advanced_label = t('Field available in advanced search.');

            if (is_object($category)) {
                $category_handle = $category->getAttributeKeyCategoryHandle();
                $keyword_label = t('Content included in "Keyword Search".');
                $advanced_label = t('Field available in "Advanced Search".');
                switch ($category_handle) {
                    case 'collection':
                        $keyword_label = t('Content included in sitewide page search index.');
                        $advanced_label = t('Field available in Dashboard Page Search.');
                        break;
                    case 'file':
                        $keyword_label = t('Content included in file search index.');
                        $advanced_label = t('Field available in File Manager Search.');
                        break;
                    case 'user':
                        $keyword_label = t('Content included in user keyword search.');
                        $advanced_label = t('Field available in Dashboard User Search.');
                        break;
                }
            }
            ?>
            <div class="checkbox"><label><?= $form->checkbox('akIsSearchableIndexed', 1,
                        $akIsSearchableIndexed) ?> <?= $keyword_label ?></label></div>
            <div class="checkbox"><label><?= $form->checkbox('akIsSearchable', 1,
                        $akIsSearchable) ?> <?= $advanced_label ?></label></div>
        </div>

    </fieldset>

    <?= $form->hidden('atID', $type->getAttributeTypeID()) ?>
    <? if ($category) { ?>
        <?= $form->hidden('akCategoryID', $category->getAttributeKeyCategoryID()); ?>

        <?

        if ($category->getPackageID() > 0) {
            @Loader::packageElement('attribute/categories/' . $category->getAttributeKeyCategoryHandle(),
                $category->getPackageHandle(), array('key' => $key));
        } else {
            @Loader::element('attribute/categories/' . $category->getAttributeKeyCategoryHandle(),
                array('key' => $key));
        }
        ?>

    <? } ?>

    <?= $valt->output('add_or_update_attribute') ?>
    <? $type->render('type_form', $key); ?>

    <? if (!isset($back)) {
        $back = URL::page($c);
    }
    ?>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?= $back ?>" class="btn pull-left btn-default"><?= t('Back') ?></a>
            <? if (is_object($key)) { ?>
                <button type="submit" class="btn btn-primary pull-right"><?= t('Save') ?></button>
            <? } else { ?>
                <button type="submit" class="btn btn-primary pull-right"><?= t('Add') ?></button>
            <? } ?>
        </div>
    </div>


</form>
