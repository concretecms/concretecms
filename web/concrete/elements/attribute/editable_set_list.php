<?php
/**
 * @var \Concrete\Core\Attribute\Key\Category $category
 */
$sets = $category->getAttributeSets();
foreach($sets AS $set) {
    echo '<h3>' . $set->getAttributeSetDisplayName() . '</h3><hr/>';
    foreach ($set->getAttributeKeys() as $key => $ak) {
        if (isset($objects)) {
            foreach ($objects as $object) {
                $value = $object->getAttributeValueObject($ak);
                if (!is_object($value)) {
                    $display = '';
                } else {
                    $display = $value->getValue('displaySanitized', 'display');
                }
                if (isset($lastDisplay) && $display != $lastDisplay) {
                    $display = t('Multiple Values');
                }
                $lastDisplay = $display;
            }
        } else {
            $value = $object->getAttributeValueObject($ak);
            if (is_object($value)) {
                $display = $value->getValue('displaySanitized', 'display');
            } else {
                $display = '';
            }
        }

        $canEdit = $permissionsCallback($ak, $permissionsArguments); ?>

        <div class="row">
            <div class="col-md-3"><p><?=$ak->getAttributeKeyDisplayName()?></p></div>
            <div class="col-md-9" <?php if ($canEdit) { ?>data-editable-field-inline-commands="true"<?php } ?>>
                <?php if ($canEdit) { ?>
                    <ul class="ccm-edit-mode-inline-commands">
                        <li><a href="#" data-key-id="<?=$ak->getAttributeKeyID()?>" data-url="<?=$clearAction?>" data-editable-field-command="clear_attribute"><i class="fa fa-trash-o"></i></a></li>
                    </ul>
                <?php } ?><p><span <?php if ($canEdit) { ?>data-title="<?=$ak->getAttributeKeyDisplayName()?>" data-key-id="<?=$ak->getAttributeKeyID()?>" data-name="<?=$ak->getAttributeKeyID()?>" data-editable-field-type="xeditableAttribute" data-url="<?=$saveAction?>" data-type="concreteattribute"<?php } ?>><?=$display?></span></p>
            </div>
        </div>

        <?php if ($canEdit) { ?>

            <div style="display: none">
                <div data-editable-attribute-key-id="<?=$ak->getAttributeKeyID()?>">
                    <?php
                    $value = $object->getAttributeValueObject($ak);
                    $ak->render('form', $value);
                    ?>
                </div>
            </div>

        <?php }
        unset($lastDisplay);
    }
}

$attributeKeys = $category->getUnassignedAttributeKeys();
if(count($attributeKeys) > 0) {
    echo '<h3>' . t('Other') . '</h3><hr/>';
    foreach ($attributeKeys as $key => $ak) {
        if (isset($objects)) {
            foreach ($objects as $object) {
                $value = $object->getAttributeValueObject($ak);
                if (!is_object($value)) {
                    $display = '';
                } else {
                    $display = $value->getValue('displaySanitized', 'display');
                }
                if (isset($lastDisplay) && $display != $lastDisplay) {
                    $display = t('Multiple Values');
                }
                $lastDisplay = $display;
            }
        } else {
            $value = $object->getAttributeValueObject($ak);
            if (is_object($value)) {
                $display = $value->getValue('displaySanitized', 'display');
            } else {
                $display = '';
            }
        }

        $canEdit = $permissionsCallback($ak, $permissionsArguments); ?>

        <div class="row">
            <div class="col-md-3"><p><?=$ak->getAttributeKeyDisplayName()?></p></div>
            <div class="col-md-9" <?php if ($canEdit) { ?>data-editable-field-inline-commands="true"<?php } ?>>
                <?php if ($canEdit) { ?>
                    <ul class="ccm-edit-mode-inline-commands">
                        <li><a href="#" data-key-id="<?=$ak->getAttributeKeyID()?>" data-url="<?=$clearAction?>" data-editable-field-command="clear_attribute"><i class="fa fa-trash-o"></i></a></li>
                    </ul>
                <?php } ?><p><span <?php if ($canEdit) { ?>data-title="<?=$ak->getAttributeKeyDisplayName()?>" data-key-id="<?=$ak->getAttributeKeyID()?>" data-name="<?=$ak->getAttributeKeyID()?>" data-editable-field-type="xeditableAttribute" data-url="<?=$saveAction?>" data-type="concreteattribute"<?php } ?>><?=$display?></span></p>
            </div>
        </div>

        <?php if ($canEdit) { ?>

            <div style="display: none">
                <div data-editable-attribute-key-id="<?=$ak->getAttributeKeyID()?>">
                    <?php
                    $value = $object->getAttributeValueObject($ak);
                    $ak->render('form', $value);
                    ?>
                </div>
            </div>

        <?php }
        unset($lastDisplay);
    }
}