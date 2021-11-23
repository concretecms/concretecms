<?php

use Concrete\Core\View\View;
use Concrete\Core\Error\UserMessageException;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Entity\File\File $file
 * @var Concrete\Core\Entity\File\Version|null $fileVersion
 * @var Concrete\Core\Application\Application $app
 */

if ($fileVersion === null) {
    ?>
    <div><?= t('File without approved version.') ?></div>
    <?php
} else if (!$fileVersion->canEdit()) {
    throw new UserMessageException(t('No valid editor found for file ID %s.', $fileVersion->getFileID()));
} else {
    $fileType = $fileVersion->getTypeObject();
    View::element('files/edit/' . $fileType->getEditor(), ['fv' => $fileVersion, 'app' => $app], $fileType->getPackageHandle());
}
