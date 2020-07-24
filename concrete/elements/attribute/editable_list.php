<?php

foreach ($attributes as $ak) {
    View::element(
        'attribute/editable_attribute',
        [
            'ak' => $ak,
            'object' => $object ?? null,
            'objects' => $objects ?? null,
            'saveAction' => $saveAction,
            'clearAction' => $clearAction,
            'permissionsCallback' => $permissionsCallback,
            'permissionsArguments' => $permissionsArguments ?? null,
        ]
    );
}
