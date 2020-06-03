<?php /** @noinspection PhpUnusedParameterInspection */
/** @noinspection PhpUndefinedMethodInspection */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Attribute\Key\FileKey;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\Application\Service\UserInterface;
use Concrete\Core\View\View;
use Concrete\Controller\Dialog\File\Properties;

/** @var Properties $controller */
/** @var bool $previewMode */
/** @var Version $fv */

$app = Application::getFacadeApplication();
/** @var Token $token */
$token = $app->make(Token::class);
/** @var Date $dh */
$dh = $app->make(Date::class);
/** @var UserInterface $ui */
$ui = $app->make(UserInterface::class);
/** @var UserInfoRepository $userInfo */
$userInfo = $app->make(UserInfoRepository::class);
?>

<div class="ccm-ui">
    <div id="ccm-file-properties-response">
        &nbsp;
    </div>

    <?php
        if (!$previewMode) {
            echo $ui->tabs([
                ['details', t('Details'), true],
                ['versions', t('Versions')],
                ['statistics', t('Statistics')]
            ]);
        }
    ?>

    <?php if (!$previewMode): ?>
        <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
    <?php else: ?>
        <div class="container">
    <?php endif; ?>
        <section>
            <?php if (!$previewMode && $fp->canEditFileContents()):?>
                <a href="#" class="btn float-right btn-secondary btn-xs" data-action="rescan">
                    <?php echo t('Rescan') ?>
                </a>
            <?php endif; ?>

            <h4>
                <?php echo t('Basic Properties') ?>
            </h4>

            <?php
                /** @noinspection PhpUnhandledExceptionInspection */
                View::element('files/properties', [
                    'fv' => $fv,
                    'mode' => $previewMode ? 'preview' : null
                ]);
            ?>
        </section>

        <?php $attribs = FileKey::getList(); ?>

        <?php if (count($attribs) > 0): ?>
            <section>
                <h4>
                    <?php echo t('Attributes') ?>
                </h4>

                <?php
                    /** @noinspection PhpUnhandledExceptionInspection */
                    View::element(
                        'attribute/editable_list',
                        [
                            'attributes' => $attribs,
                            'object' => $fv,
                            'saveAction' => $controller->action('update_attribute'),
                            'clearAction' => $controller->action('clear_attribute'),
                            'permissionsArguments' => $fp->canEditFileProperties(),
                            'permissionsCallback' => function ($ak, $permissionsArguments) {
                                return $permissionsArguments;
                            },
                        ]
                    );
                ?>
            </section>
        <?php endif; ?>

        <section>
            <h4>
                <?php echo t('File Preview') ?>
            </h4>

            <div class="text-center">
                <?php echo $fv->getDetailThumbnailImage() ?>
            </div>
        </section>
    </div>

    <?php if (!$previewMode): ?>
        <div class="tab-pane fade" id="versions" role="tabpanel" aria-labelledby="versions-tab">
            <h4>
                <?php echo t('Versions') ?>
            </h4>

            <table id="ccm-file-versions" class="table">
                <thead>
                    <tr>
                        <th>
                            &nbsp;
                        </th>

                        <th>
                            <?php echo t('Filename') ?>
                        </th>

                        <th>
                            <?php echo t('Title') ?>
                        </th>

                        <th>
                            <?php echo t('Comments') ?>
                        </th>

                        <th>
                            <?php echo t('Creator') ?>
                        </th>

                        <th>
                            <?php echo t('Added On') ?>
                        </th>

                        <?php if ($fp->canEditFileContents()): ?>
                            <th>
                                &nbsp;
                            </th>
                        <?php endif; ?>
                    </tr>
                </thead>

                <?php $versions = $f->getVersionList(); ?>

                <tbody>
                    <?php foreach ($versions as $fvv): ?>
                        <tr data-file-version-id="<?php echo $fvv->getFileVersionID() ?>" <?php echo ($fvv->getFileVersionID() == $fv->getFileVersionID()) ? " class=\"success\" " : ""; ?>>
                            <td class="text-center">
                                <!--suppress HtmlFormInputWithoutLabel -->
                                <input
                                    type="radio"
                                    name="fvID"
                                    value="<?php echo $fvv->getFileVersionID() ?>"
                                    <?php echo ($fvv->getFileVersionID() == $fv->getFileVersionID()) ? " checked" : "" ?>
                                />
                            </td>

                            <td>
                                <div>
                                    <!--suppress HtmlUnknownAttribute -->
                                    <a
                                        href="<?php echo Url::to('/ccm/system/dialogs/file/properties')->setQuery([
                                            "fID" => $f->getFileID(),
                                            "fvID" => $fvv->getFileVersionID()
                                        ]); ?>"
                                        dialog-modal="false"
                                        dialog-width="630"
                                        dialog-height="450"
                                        dialog-title="<?php echo t('Preview File') ?>"
                                        class="dialog-launch">

                                        <?php echo h($fvv->getFilename()) ?>
                                    </a>
                                </div>
                            </td>

                            <td>
                                <div>
                                    <?php echo h($fvv->getTitle()) ?>
                                </div>
                            </td>

                            <td>
                                <?php
                                    $comments = $fvv->getVersionLogComments();

                                    if (count($comments) > 0) {
                                        echo t('Updated ');

                                        for ($i = 0; $i < count($comments); ++$i) {
                                            echo $comments[$i];
                                            if (count($comments) > ($i + 1)) {
                                                echo ', ';
                                            }
                                        }

                                        echo '.';
                                    }
                                ?>
                            </td>

                            <td>
                                <?php echo $fvv->getAuthorName() ?>
                            </td>

                            <td>
                                <?php
                                    /** @noinspection PhpUnhandledExceptionInspection */
                                    echo $dh->formatDateTime($fvv->getDateAdded(), true);
                                ?>
                            </td>

                            <?php if ($fp->canEditFileContents()): ?>
                                <td>
                                    <a data-action="delete-version"
                                       data-file-version-id="<?php echo $fvv->getFileVersionID() ?>"
                                       data-token="<?php echo $token->generate('version/delete/' . $fvv->getFileID() . "/" . $fvv->getFileVersionId()) ?>"
                                       href="javascript:void(0)">

                                        <i class="fa fa-trash-o"></i>
                                    </a>
                                </td>
                            <?php endif;?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="tab-pane fade" id="statistics" role="tabpanel" aria-labelledby="statistics-tab">
            <?php $downloadStatistics = $f->getDownloadStatistics(); ?>

            <section>
                <h4>
                    <?php echo t('Total Downloads') ?>
                </h4>

                <div>
                    <?php echo $f->getTotalDownloads() ?>
                </div>
            </section>

            <section>
                <h4>
                    <?php echo t('Most Recent Downloads') ?>
                </h4>

                <table class="table">
                    <thead>
                        <tr>
                            <th>
                                <?php echo t('User') ?>
                            </th>

                            <th>
                                <?php echo t('Download Time') ?>
                            </th>

                            <th>
                                <?php echo t('File Version ID') ?>
                            </th>
                        </tr>
                    </thead>

                    <?php $downloadStatsCounter = 0; ?>

                    <tbody>
                        <?php foreach ($downloadStatistics as $download): ?>
                            <?php
                                ++$downloadStatsCounter;
                                if ($downloadStatsCounter > 20) {
                                    break;
                                }
                            ?>

                            <tr>
                                <td>
                                    <?php
                                        $uID = intval($download['uID']);

                                        if (!$uID) {
                                            echo t('Anonymous');
                                        } else {
                                            $downloadUI = $userInfo->getById($uID);

                                            if ($downloadUI instanceof UserInfo) {
                                                echo $downloadUI->getUserName();
                                            } else {
                                                echo t('Deleted User');
                                            }
                                        }
                                    ?>
                                </td>

                                <td>
                                    <?php
                                        /** @noinspection PhpUnhandledExceptionInspection */
                                        echo $dh->formatDateTime($download['timestamp'], true)
                                    ?>
                                </td>

                                <td>
                                    <?php echo intval($download['fvID']) ?>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </section>
        </div>
    <?php endif; ?>
</div>

<!--suppress CssUnusedSymbol -->
<style type="text/css">
    #ccm-file-versions tbody td:nth-child(2) div {
        width: 150px;
        word-wrap: break-word;
    }

    #ccm-file-versions tbody td:nth-child(3) div {
        width: 150px;
        word-wrap: break-word;
    }

    #ccm-file-properties-response #ccm-notification-hud {
        position: relative;
        margin-bottom: 20px;
        top: 0;
        left: 0;
    }

    #ccm-file-properties-response #ccm-notification-hud .ccm-notification-inner {
        padding: 15px 10px 5px 60px;
        color: #fff;
    }

    #ccm-file-properties-response #ccm-notification-hud i {
        top: 2px;
        left: 8px;
        border: 0;
    }

    tr.success a[data-action=delete-version] {
        display: none;
    }

    a[data-action=delete-version] {
        color: #333;
    }

    a[data-action=delete-version]:hover {
        color: #000;
        text-decoration: none;
    }
</style>

<!--suppress JSUnusedLocalSymbols -->
    <script type="text/javascript">
    let ConcreteFilePropertiesDialog = function () {
        let my = this;

        $('div[data-container=editable-fields]').concreteEditableFieldContainer({
            url: '<?php echo $controller->action('save')?>'
        });

        my.setupFileVersionsTable();
        my.setupFileRescan();
    }

    ConcreteFilePropertiesDialog.prototype = {
        handleAjaxResponse: function (r, callback) {
            if (callback) {
                callback(r);
            } else {
                ConcreteAlert.notify({
                    'message': r.message,
                    'appendTo': '#ccm-file-properties-response'
                });
            }
        },

        setupFileRescan: function () {
            let my = this;

            $('a[data-action=rescan]').on('click', function () {
                $.concreteAjax({
                    url: '<?php echo URL::to('/ccm/system/file/rescan')?>',
                    data: {'fID': '<?php echo $f->getFileID()?>'},
                    success: function (r) {
                        my.handleAjaxResponse(r);
                    }
                });

                return false;
            });
        },

        setupFileVersionsTable: function () {
            let my = this;

            let $versions = $('#ccm-file-versions');

            $versions.on('click', 'input[name=fvID]', function () {
                let fvID = $(this).val();

                $.concreteAjax({
                    url: '<?php echo Url::to('/ccm/system/file/approve_version')?>',
                    data: {
                        'fID': '<?php echo $f->getFileID()?>',
                        'fvID': fvID
                    },
                    success: function (r) {
                        my.handleAjaxResponse(r, function () {
                            $versions.find('tr[class=success]').removeClass();
                            $versions.find('tr[data-file-version-id=' + fvID + ']').addClass('success');
                        });
                    }
                });
            });

            $versions.on('click', 'a[data-action=delete-version]', function () {
                let fvID = $(this).attr('data-file-version-id');

                $.concreteAjax({
                    url: '<?php echo Url::to('/ccm/system/file/delete_version')?>',
                    data: {'fID': '<?php echo $f->getFileID()?>', 'fvID': fvID, ccm_token: $(this).data('token')},
                    success: function (r) {
                        my.handleAjaxResponse(r, function () {
                            let $row = $versions.find('tr[data-file-version-id=' + fvID + ']');

                            $row.queue(function () {
                                $(this).addClass('animated fadeOutDown');
                                $(this).dequeue();
                            }).delay(500).queue(function () {
                                $(this).remove();
                                $(this).dequeue();
                            });
                        });
                    }
                });
            });
        }
    }

    <?php if (!$previewMode): ?>
        $(function () {
            let dialog = new ConcreteFilePropertiesDialog();
        });
    <?php endif; ?>
</script>
