<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Entity\File\Version $fileVersion
 * @var Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface $resolverManager
 * @var Concrete\Core\Form\Service\Form $form
 */

if ($fileVersion->hasFileUUID()) {
    $fID = $fileVersion->getFileUUID();
} else {
    $fID = $fileVersion->getFileID();
}

?>
<div class="text-center">
    <?php
    $fileType = $fileVersion->getTypeObject();
    View::element('files/view/' . $fileType->getView(), ['fv' => $fileVersion], (string) $fileType->getPackageHandle());
?>
</div>
<div class="dialog-buttons">
    <form method="post" action="<?= h($resolverManager->resolve(['ccm/system/file/download']) . '?fID=' . $fID . '&fvID=' . $fileVersion->getFileVersionID()) ?>">
        <?= $form->submit('submit', t('Download'), ['class' => 'btn btn-primary float-end']) ?>
    </form>
</div>
