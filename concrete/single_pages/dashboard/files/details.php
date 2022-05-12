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
 * @var Concrete\Core\Permission\Checker $filePermissions
 * @var string $thumbnail
 * @var Concrete\Core\Entity\Attribute\Key\FileKey[] $attributeKeys
 * @var Concrete\Core\Entity\Statistics\UsageTracker\FileUsageRecord[] $usageRecords
 * @var Concrete\Core\Entity\File\DownloadStatistics[][] $recentDownloads
 */

$file = $fileVersion->getFile();
$genericType = $fileVersion->getTypeObject()->getGenericType();
if ($view->controller->getAction() == 'preview_version') { ?>
    <div class="alert alert-info d-flex align-items-center"><div><?=t('You are currently previewing file version %s.', $fileVersion->getFileVersionID())?></div>
    <a href="<?=URL::to('/dashboard/files', 'details', $file->getFileID())?>" class="btn-sm btn btn-secondary d-flex ms-auto"><?=t('Exit Preview')?></a>
    </div>
<?php } ?>

<section>
    <div class="row gx-5">
        <div class="col-lg-6">
            <div class="ccm-file-manager-details-preview-thumbnail">
                <?= $thumbnail ?>
            </div>
        </div>
        <div class="col-lg-6">
            <?php if ($view->controller->getAction() != 'preview_version') { ?>

                <?php
                if ($filePermissions->canEditFileProperties() || (
                        $filePermissions->canEditFileContents() && (
                            $genericType === \Concrete\Core\File\Type\Type::T_IMAGE ||
                            $fileVersion->canEdit()
                        )
                    )
                ) {
                ?>
                <div class="dropdown float-end">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        <?=t('Edit')?>
                    </button>
                    <ul class="dropdown-menu">
                        <?php
                        if ($filePermissions->canEditFileProperties()) {
                            ?>
                            <li>
                                    <a
                                            data-bs-placement="left"
                                            class="dropdown-item launch-tooltip dialog-launch"
                                            dialog-title="<?= t('Attributes') ?>"
                                            dialog-width="850" dialog-height="80%"
                                            title="<?=t('Change the name, description, tags and custom attributes of this file.')?>"
                                            href="<?=URL::to('/ccm/system/dialogs/file/properties')?>?fID=<?=$file->getFileID()?>"
                                    ><?= t('Edit Attributes') ?></a>
                            </li>
                            <?php
                        }
                        if ($genericType === \Concrete\Core\File\Type\Type::T_IMAGE
                            && $filePermissions->canEditFileContents()) {
                            ?>
                            <li><a
                                    data-bs-placement="left"
                                    class="dropdown-item launch-tooltip dialog-launch"
                                    dialog-title="<?= t('Edit') ?>"
                                    dialog-width="90%" dialog-height="75%"
                                    title="<?= t('Adjust the thumbnails for this image.') ?>"
                                    href="<?=URL::to('/ccm/system/dialogs/file/thumbnails?fID=' . $file->getFileID())?>"
                            ><?= t('Thumbnails') ?></a></li>
                            <?php
                        }
                        if ($fileVersion->canEdit() && $filePermissions->canEditFileContents()) {
                            ?>
                            <li>
                                <a
                                        data-bs-placement="left"
                                        class="dropdown-item launch-tooltip dialog-launch"
                                        dialog-title="<?= t('Edit') ?>"
                                        dialog-width="90%" dialog-height="75%"
                                        <?php
                                        if ($genericType === \Concrete\Core\File\Type\Type::T_IMAGE) { ?>
                                            title="<?= t('Resize, crop or apply filters to this image.') ?>"
                                        <?php } else { ?>
                                            title="<?= t('Edit this file.') ?>"
                                            <?php
                                        }
                                        ?>
                                        href="<?=URL::to('/ccm/system/file/edit')?>?fID=<?=$file->getFileID()?>">
                                        <?php
                                        if ($genericType === \Concrete\Core\File\Type\Type::T_IMAGE) { ?>
                                            <?= t('Open Image Editor') ?>
                                        <?php } else { ?>
                                            <?= t('Edit File Contents') ?>
                                            <?php
                                        }
                                        ?>
                                </a>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>


                <?php } ?>
            <?php } ?>

            <h3 class="mb-4"><?=t('Attributes')?></h3>
            <dl class="ccm-file-manager-details-attributes">
                <dt><?= t('Title') ?></dt>
                <dd>
                    <div><?= (string)$fileVersion->getTitle() === '' ? '<i>' . t('No title') . '</i>' : h($fileVersion->getTitle()) ?></div>
                </dd>
                <dt><?= t('Description') ?></dt>
                <dd>
                    <div><?= (string)$fileVersion->getDescription() === '' ? '<i>' . t('No description') . '</i>' : nl2br(h($fileVersion->getDescription())) ?></div>
                </dd>
                <dt><?= t('Tags') ?></dt>
                <dd>
                    <?php
                    $tags = preg_split('/\s*\n\s*/', (string)$fileVersion->getTags(), -1, PREG_SPLIT_NO_EMPTY);
                    if ($tags === []) {
                        ?>
                        <i><?= t('No tags') ?></i>
                        <?php
                    } else {
                        ?>
                        <span><?= implode(', ', $tags) ?></span>
                        <?php
                    }
                    ?>
                </dd>
                <dt><?= t('Size') ?></dt>
                <dd>
                    <div>
                        <?php
                        echo sprintf(
                            '%s (%s)',
                            $fileVersion->getSize(),
                            t2(
                            /*i18n: %s is a number */
                                '%s byte',
                                '%s bytes',
                                $fileVersion->getFullSize(),
                                $number->format($fileVersion->getFullSize())
                            )
                        );
                        ?>
                    </div>
                </dd>
                <?php
                foreach ($attributeKeys as $attributeKey) {
                    ?>
                    <dt><?= $attributeKey->getAttributeKeyDisplayName() ?></dt>
                    <dd>
                        <div>
                            <?php
                            $attributeValue = $fileVersion->getAttributeValueObject($attributeKey);
                            if ($attributeValue === null) {
                                $noValueDisplayHtml = '<i>' . t('None') . '</i>';
                                if (method_exists($attributeKey, 'getController')) {
                                    $attributeController = $attributeKey->getController();
                                    if ($attributeController instanceof CustomNoValueTextAttributeInterface) {
                                        $noValueDisplayHtml = (string)$attributeController->getNoneTextDisplayValue();
                                    }
                                }
                                echo $noValueDisplayHtml;
                            } else {
                                echo (string)$attributeValue;
                            }
                            ?>
                        </div>
                    </dd>
                    <?php
                }
                ?>
            </dl>
        </div>
    </div>
</section>

<hr class="mt-5 mb-4"/>

<section>
    <h3 class="mb-4"><?=t('URLs')?></h3>
    <dl>
        <dt><?= t('Direct URL') ?></dt>
        <dd class="mb-5">
            <input type="text" class="bg-white form-control" readonly onclick="this.select()" value="<?= h($fileVersion->getURL()) ?>">
            <div class="text-muted mt-2"><i><?= t('If you need to embed an image directly in HTML, use this URL.') ?></i></div>
        </dd>
        <dt><?= t('Tracking URL') ?></dt>
        <dd>
            <input type="text" class="bg-white form-control" readonly onclick="this.select()" value="<?= h($fileVersion->getDownloadURL()) ?>">
            <div class="text-muted mt-2"><i><?= t("By using this URL Concrete will still be able to manage permissions and track statistics on its use.") ?></i></div>
        </dd>
    </dl>
</section>

<hr class="mt-5 mb-4"/>

<section>
    <h3 class="mb-4"><?=t('Sets')?></h3>
    <?php if ($view->controller->getAction() != 'preview_version') { ?>
        <a
                class="btn btn-secondary btn-section dialog-launch"
                dialog-title="<?= t('Sets') ?>"
                dialog-width="850" dialog-height="600"
                href="<?= h($resolverManager->resolve(['/ccm/system/dialogs/file/sets?fID=' . $file->getFileID()])) ?>">
            <?=t('Edit')?>
        </a>
    <?php } ?>
    <dl class="ccm-file-manager-details-sets">
        <dt><?= t('Sets') ?></dt>
        <dd>
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
                ?>
                <span><?= implode(', ', $fileSetNames) ?></span>
                <?php
            }
            ?>
            <div class="text-muted mt-2">
                <i><?= t('You can add this file to many sets. Lots of image sliders/galleries use sets to determine what to display.') ?></i>
            </div>
        </dd>
    </dl>
</section>

<hr class="mt-5 mb-4"/>

<section>
    <h3><?= t('Statistics') ?></h3>
    <dl class="ccm-file-manager-details-statistics">
        <dt><?= t('Date Added') ?></dt>
        <dd>
            <i><?= t(/*%1$s is a user name, %2$s is a date/time*/ 'Added by %1$s on %2$s', h($fileVersion->getAuthorName()), h($date->formatPrettyDateTime($fileVersion->getDateAdded(), true))) ?></i>
        </dd>
        <dt><?= t('Total Downloads') ?></dt>
        <dd><i><?= $number->format($file->getTotalDownloads(), 0) ?></i></dd>
        <dt><?= t('Most Recent Downloads') ?></dt>
        <dd>
            <?php
            if ($recentDownloads === []) {
                ?><i><?= t('No downloads') ?></i><?php
            } else {
                ?>
                <table class="table table-bordered">
                    <tbody>
                    <?php
                    foreach ($recentDownloads as $recentDownload) {
                        ?>
                        <tr>
                            <td>
                                <?php
                                if ($recentDownload->getDownloaderID() === null) {
                                    ?><i><?= t('Guest') ?></i><?php
                                } else {
                                    $downloader = User::getByUserID($recentDownload->getDownloaderID());
                                    if ($downloader && $downloader->isRegistered()) {
                                        echo h($downloader->getUserName());
                                    } else {
                                        ?>
                                        <i><?= t('Deleted user (ID: %s)', $recentDownload->getDownloaderID()) ?></i><?php
                                    }
                                }
                                ?>
                            </td>
                            <td><?= h($date->formatPrettyDateTime($recentDownload->getDownloadDateTime(), true)) ?></td>
                            <td><?= t('Version %s', $recentDownload->getFileVersion()) ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <a
                        class="btn btn-secondary dialog-launch"
                        dialog-title="<?= t('Download Statistics') ?>"
                        dialog-width="620" dialog-height="400"
                        href="<?= h($resolverManager->resolve(['/ccm/system/dialogs/file/statistics', $file->getFileID()])) ?>"
                ><?= t('More') ?></a>
                <?php
            }
            ?>
            <div class="text-muted mt-2">
                <i><?= t('If this file is downloaded through the File Block we track it here.') ?></i></div>
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
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th><?= t('Page ID') ?></th>
                        <th><?= t('Version') ?></th>
                        <th><?= t('Handle') ?></th>
                        <th><?= t('Location') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($usageRecords as $usageRecord) {
                        $page = Page::getByID($usageRecord->getCollectionId(), $usageRecord->getCollectionVersionId());
                        if (!$page || $page->isError()) {
                            $page = null;
                        }
                        ?>
                        <tr>
                            <td><?= $usageRecord->getCollectionId() ?></td>
                            <td><?= $usageRecord->getCollectionVersionId() ?></strong></td>
                            <td><?= $page === null ? '<i>' . t('n/a') . '</i>' : '<strong>' . h($page->getCollectionHandle()) . '</strong>' ?></td>
                            <td>
                                <?php
                                if ($page === null) {
                                    ?>
                                    <i><?= t('n/a') ?></i>
                                    <?php
                                } else {
                                    $pagePath = '/' . ltrim((string)$page->getCollectionPath(), '/');
                                    ?>
                                    <a href="<?= $resolverManager->resolve([$page]) ?>"><strong><?= h($pagePath) ?></strong></a>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <?php
            }
            ?>
        </dd>
    </dl>
</section>

<hr class="mt-5 mb-4"/>

<section>
    <?php if ($view->controller->getAction() != 'preview_version') { ?>
        <?php
        if ($filePermissions->canEditFilePermissions()) {
            ?>
            <a
                    class="btn btn-secondary float-end dialog-launch"
                    dialog-title="<?= t('Storage Location') ?>"
                    dialog-width="500" dialog-height="400"
                    href="<?= h($resolverManager->resolve(['/ccm/system/dialogs/file/bulk/storage?fID[]=' . $file->getFileID()])) ?>"
            ><?= t('Edit') ?></a>
            <?php
        }
        ?>
    <?php } ?>
    <h3><?= t('Storage') ?></h3>
    <dl class="ccm-file-manager-details-storage">
        <dt><?= t('Storage Locations') ?></dt>
        <dd>
            <?= $file->getFileStorageLocationObject()->getDisplayName() ?>
            <div class="text-muted"><?= t('You can use S3 or other cloud storage solutions to distribute your content.') ?></div>
        </dd>
    </dl>
</section>

<script>
    $(document).ready(function () {
        ConcreteEvent.subscribe('FileManagerReplaceFileComplete FileManagerBulkFileStorageComplete', function (e, data) {
            location.reload();
        });
    });
</script>
