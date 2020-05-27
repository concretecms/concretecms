<?php

use Concrete\Core\Attribute\CustomNoValueTextAttributeInterface;
use Concrete\Core\File\Set\Set as FileSet;
use Concrete\Core\User\User;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Service\Dashboard $dashboard
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Html\Service\Html $html
 * @var Concrete\Core\Application\Service\UserInterface $interface
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Controller\SinglePage\Dashboard\Files\Details $controller
 * @var Concrete\Core\Localization\Service\Date $date
 * @var Concrete\Core\Utility\Service\Number $number
 * @var Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface $resolverManager
 * @var Concrete\Core\Entity\File\Version $fileVersion
 * @var string $thumbnail
 * @var Concrete\Core\Entity\Attribute\Key\FileKey[] $attributeKeys
 * @var Concrete\Core\Entity\Statistics\UsageTracker\FileUsageRecord[] $usageRecords
 */
//output_vars(get_defined_vars(), isset($this) ? $this : null);
$file = $fileVersion->getFile();
?>
<section>
    <h3><?= t('Preview') ?></h3>
    <div class="ccm-file-manager-details-preview">
        <div class="ccm-file-manager-details-preview-thumbnail">
            <?= $thumbnail ?>
        </div>
        <div class="ccm-file-manager-details-preview-actions">
            <div class="mb-4">
                <a
                    class="btn btn-secondary dialog-launch"
                    dialog-title="<?= t('Swap') ?>"
                    dialog-width="620" dialog-height="400"
                    href="<?= h($resolverManager->resolve(['/ccm/system/dialogs/file/replace?fID=' . $file->getFileID()])) ?>"
                ><?= t('Swap') ?></a>
                <div class="text-muted"><i><?= t('Upload a new file to be used everywhere this current file is referenced.') ?></i></div>
            </div>
            <div class="mb-4">
                <button class="btn btn-secondary" onclick="alert('@todo');return false"><?= t('Rescan')?></button>
                <div class="text-muted"><i><?= t('Automatically regenerate thumbnails for all sizes of this image.') ?></i></div>
            </div>
            <div>
                <button class="btn btn-secondary" onclick="alert('@todo');return false"><?= t('Edit')?></button>
                <div class="text-muted"><i><?= t('Adjust cropping and scale of this image and all its thumbnails.') ?></i></div>
            </div>
        </div>
    </div>
</section>

<hr class="mt-5 mb-4" />

<section>
    <button class="btn btn-secondary float-right" onclick="alert('@todo');return false"><?= t('Edit') ?></button>
    <h3><?= t('Attributes') ?></h3>
    <dl class="row">
        <dt class="col-md-3"><?= t('Title') ?></dt>
        <dd class="col-md-9"><?= (string) $fileVersion->getTitle() === '' ? '<i>' . t('No title') . '</i>' : h($fileVersion->getTitle()) ?></dd>
        <dt class="col-md-3"><?= t('Description') ?></dt>
        <dd class="col-md-9"><?= (string) $fileVersion->getDescription() === '' ? '<i>' . t('No description') . '</i>' : nl2br(h($fileVersion->getDescription())) ?></dd>
        <dt class="col-md-3"><?= t('Tags') ?></dt>
        <dd class="col-md-9">
            <?php
            $tags = preg_split('/\s*\n\s*/', (string) $fileVersion->getTags(), -1, PREG_SPLIT_NO_EMPTY);
            if ($tags === []) {
                ?>
                <i><?= t('No tags') ?></i>
                <?php
            } else {
                echo implode(', ', $tags);
            }
            ?>
            <div class="text-muted"><i><?= t('Search for files with these tags using the advanced search link in the file manager.') ?></i></div>
        </dd>
        <dt class="col-md-3"><?= t('Sets') ?></dt>
        <dd class="col-md-9">
            <?php
            $fileSets = $file->getFileSets();
            if ($fileSets === []) {
                ?>
                <i><?= t('No file set') ?></i>
                <?php
            } else {
                $fileSetNames = array_map(
                    function (FileSet $fileSet) {
                        return $fileSet->getFileSetDisplayName();
                    },
                    $fileSets
                );
                echo implode(', ', $fileSetNames);
            }
            ?>
            <div class="text-muted"><i><?= t('You can add this file to many sets. Lots of image sliders/galleries use sets to determine what to display.') ?></i></div>
        </dd>
        <?php
        foreach ($attributeKeys as $attributeKey) {
            ?>
            <dt class="col-md-3"><?= $attributeKey->getAttributeKeyDisplayName() ?></dt>
            <dd class="col-md-9">
                <?php
                $attributeValue = $fileVersion->getAttributeValueObject($attributeKey);
                if ($attributeValue === null) {
                    $noValueDisplayHtml = '<i>' . t('None') . '</i>';
                    if (method_exists($attributeKey, 'getController')) {
                        $attributeController = $attributeKey->getController();
                        if ($attributeController instanceof CustomNoValueTextAttributeInterface) {
                            $noValueDisplayHtml = (string) $attributeController->getNoneTextDisplayValue();
                        }
                    }
                    echo $noValueDisplayHtml;
                } else {
                    echo (string) $attributeValue;
                }
                ?>
            </dd>
            <?php
        }
        ?>
    </dl>
</section>

<hr class="mt-5 mb-4" />

<section>
    <h3><?= t('Statistics') ?></h3>
    <dl>
        <dt><?= t('Date Added') ?></dt>
        <dd>
            <?= t(/*%1$s is a user name, %2$s is a date/time*/'Added by %1$s on %2$s', h($fileVersion->getAuthorName()), h($date->formatPrettyDateTime($fileVersion->getDateAdded(), true))) ?>
        </dd>
        <dt><?= t('Total Downloads') ?></dt>
        <dd><?= $number->format($file->getTotalDownloads(), 0) ?></dd>
        <dt><?= t('Most Recent Downloads') ?></dt>
        <dd>
            <?php
            $downloads = $file->getDownloadStatistics(3);
            if ($downloads === []) {
                ?><i><?= t('No downloads') ?></i><?php
            } else {
                foreach ($downloads as $download) {
                    ?>
                    <div class="row ccm-file-manager-details-download">
                        <div class="col-md-4">
                            <?php
                            if ($download['uID']) {
                                $downloader = User::getByUserID($download['uID']);
                                if ($downloader && $downloader->isRegistered()) {
                                    echo h($downloader->getUserName());
                                } else {
                                    ?><i><?= t('Deleted user (ID: %s)', $download['uID']) ?></i><?php
                                }
                            } else {
                                ?><i><?= t('Guest') ?></i><?php
                            }
                            ?>
                        </div>
                        <div class="col-md-4"><?= h($date->formatPrettyDateTime($download['timestamp'], true)) ?></div>
                        <div class="col-md-4"><?= t('Version %s', $download['fvID']) ?></div>
                    </div>
                    <?php
                }
                ?>
                <button class="btn btn-secondary" onclick="alert('@todo');return false"><?= t('More') ?></button>
                <?php
            }
            ?>
            <div class="text-muted"><i><?= t('If this file is downloaded through the File Block we track it here.') ?></i></div>
        </dd>
        <dt><?= t('File Usage') ?></dt>
        <dd>
            <?php
            if ($usageRecords === []) {
                ?>
                <i><?= t("It seems that this file isn't used anywhere.") ?></i>
                <?php
            } else {
                ?>
                <div class="row ccm-file-manager-details-download">
                    <div class="col-md-3"><?= t('Page ID') ?></div>
                    <div class="col-md-3"><?= t('Version') ?></div>
                    <div class="col-md-3"><?= t('Handle') ?></div>
                    <div class="col-md-3"><?= t('Location') ?></div>
                </div>
                <?php
                foreach ($usageRecords as $usageRecord) {
                    $page = Page::getByID($usageRecord->getCollectionId(), $usageRecord->getCollectionVersionId());
                    if (!$page || $page->isError()) {
                        $page = null;
                    }
                    $page->path
                    ?>
                    <div class="row ccm-file-manager-details-download">
                        <div class="col-md-3"><strong><?= $usageRecord->getCollectionId() ?></strong></div>
                        <div class="col-md-3"><strong><?= $usageRecord->getCollectionVersionId() ?></strong></div>
                        <div class="col-md-3"><?= $page === null ? '<i>' . t('n/a') . '</i>' : '<strong>' . h($page->getCollectionHandle()) . '</strong>' ?></div>
                        <div class="col-md-3">
                            <?php
                            if ($page === null) {
                                ?>
                                <i><?= t('n/a') ?></i>
                                <?php
                            } else {
                                $pagePath = '/' . ltrim((string) $page->getCollectionPath(), '/');
                                ?>
                                <a href="<?= $resolverManager->resolve([$page]) ?>"><strong><?= h($pagePath) ?></strong></a>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
            <div class="text-muted"><i style="color:#ff0;background-color:#f00"><?= t('For debugging only, this is the complete URL to the source file.') ?></i></div>
        </dd>
    </dl>
</section>

<hr class="mt-5 mb-4" />

<section>
    <button class="btn btn-secondary float-right" onclick="alert('@todo');return false"><?= t('Edit') ?></button>
    <h3><?= t('Storage') ?></h3>
    <dl>
        <dt><?= t('Tracked URL') ?></dt>
        <dd>
            <?= h($fileVersion->getDownloadURL()) ?>
            <div class="text-muted"><?= t("If you're going to hard code a link to this file, use this URL. By using this URL concrete5 will still be able to manage permissions and track statistics on its use.") ?></div>
        </dd>
        <dt><?= t('File in OS') ?></dt>
        <dd>
            <?= h($fileVersion->getURL()) ?>
            <div class="text-muted"><?= t('For debugging only, this is the complete URL to the source file.') ?></div>
        </dd>
        <dt><?= t('Storage Locations') ?></dt>
        <dd>
            <?= $file->getFileStorageLocationObject()->getDisplayName() ?>
            <div class="text-muted"><?= t('You can use S3 or other cloud storage solutions to distribute your content.') ?></div>
        </dd>
    </dl>
</section>
