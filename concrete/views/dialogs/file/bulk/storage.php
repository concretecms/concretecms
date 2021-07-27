<?php /** @noinspection PhpComposerExtensionStubsInspection */

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Controller\Dialog\File\Bulk\Storage;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\StorageLocation\StorageLocation as StorageLocationEntity;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Utility\Service\Identifier;

/** @var Storage $controller */
/** @var StorageLocationEntity[] $locations */
/** @var File[] $files */
$app = Application::getFacadeApplication();
/** @var Identifier $idHelper */
$idHelper = $app->make(Identifier::class);
?>

<?php if (!empty($files)): ?>

    <?php
        // let's check the storage locations to see if all the files use the same and check the correct checkbox
        $usedStorageLocations = [];
        $currentStorageLocation = null;

        foreach ($files as $file) {
            $fileStorageLocationID = $file->getStorageLocationID();
            if (!in_array($fileStorageLocationID, $usedStorageLocations)) {
                $usedStorageLocations[] = $fileStorageLocationID;
            }
        }
        if (count($usedStorageLocations) === 1) {
            $currentStorageLocation = $usedStorageLocations[0];
        }
    ?>

    <form data-dialog-form="bulk-file-storage" data-dialog-form-processing="progressive" data-dialog-form-processing-title="<?=t('Update Storage Location')?>" method="post" action="<?php echo $controller->action('submit'); ?>">
        <?php foreach ($files as $file): ?>
            <input type="hidden" name="fID[]" value="<?php echo $file->getFileID(); ?>"/>
        <?php endforeach; ?>

        <div class="ccm-ui">
            <?php foreach ($locations as $fsl): ?>
                <div class="form-check">
                    <?php
                        $id = "radio-" . $idHelper->getString();
                        echo $form->radio('fslID', $fsl->getID(), $currentStorageLocation, ["id" => $id, "class" => "form-check-input"]);
                        echo $form->label($id, $fsl->getDisplayName());
                    ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="dialog-buttons">
            <button class="btn btn-secondary float-start" data-dialog-action="cancel">
                <?php echo t('Cancel'); ?>
            </button>

            <button type="button" data-dialog-action="submit" class="btn btn-primary float-end">
                <?php echo t('Move Location'); ?>
            </button>
        </div>
    </form>

    <!--suppress JSUnresolvedFunction -->
<?php endif; ?>
