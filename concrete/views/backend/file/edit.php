<?php

use Concrete\Core\View\View;

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
} else {
    $fileType = $fileVersion->getTypeObject();
    View::element('files/edit/' . $fileType->getEditor(), ['fv' => $fileVersion, 'app' => $app], $fileType->getPackageHandle());
}
