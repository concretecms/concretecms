<?php

foreach ($attributes as $ak) {
    Loader::element(
        'attribute/editable_attribute',
        array(
            'ak' => $ak,
            'object' => isset($object) ? $object : null,
            'objects' => isset($objects) ? $objects : null,
            'saveAction' => $saveAction,
            'clearAction' => $clearAction,
            'permissionsCallback' => $permissionsCallback,
            'permissionsArguments' => isset($permissionsArguments) ? $permissionsArguments : null,
        )
    );
}
