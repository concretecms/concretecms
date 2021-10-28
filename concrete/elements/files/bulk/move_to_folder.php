<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\View\View;
use HtmlObject\Input;

/** @noinspection PhpUnhandledExceptionInspection */
View::element(
    'files/move_to_folder',

    [
        'isCurrentFolder' => function ($folder) use ($files) {
            if (isset($files[0])) {
                $fileFolderObject = $files[0]->getFileFolderObject();

                if (is_object($fileFolderObject) && $fileFolderObject->getTreeNodeID() === $folder->getTreeNodeID()) {
                    return true;
                }
            }

            return false;
        },

        'getRadioButton' => function ($folder, $checked = false) use ($f) {
            return id(new Input('radio', 'folderID', $folder->getTreeNodeID(), ['checked' => $checked]));
        }
    ]
);
