<?php
/**
 * @var \Concrete\Core\Entity\Attribute\Category $category
 * @var array $objects
 * @var object $object
 * @var callback $permissionsCallback
 * @var array $permissionsArguments
 * @var string $clearAction
 * @var string $saveAction
 */

$setManager = $category->getController()->getSetManager();
$sets = $setManager->getAttributeSets();
foreach ($sets as $set) {
    echo '<h3>' . $set->getAttributeSetDisplayName() . '</h3><hr/>';
    foreach ($set->getAttributeKeys() as $key => $ak) {
        View::element(
            'attribute/editable_attribute',
            [
                'ak' => $ak,
                'object' => $object ?? null,
                'objects' => $objects ?? null,
                'saveAction' => $saveAction,
                'clearAction' => $clearAction,
                'permissionsCallback' => $permissionsCallback,
                'permissionArguments' => $permissionsArguments ?? null,
            ]
        );
    }
}

$attributeKeys = $setManager->getUnassignedAttributeKeys();
if (count($attributeKeys) > 0) {
    echo '<h3>' . t('Other') . '</h3><hr/>';
    foreach ($attributeKeys as $key => $ak) {
        View::element(
            'attribute/editable_attribute',
            [
                'ak' => $ak,
                'object' => $object ?? null,
                'objects' => $objects ?? null,
                'saveAction' => $saveAction,
                'clearAction' => $clearAction,
                'permissionsCallback' => $permissionsCallback,
                'permissionArguments' => $permissionsArguments,
            ]
        );
    }
}
