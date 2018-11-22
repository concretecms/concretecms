<?php

defined('C5_EXECUTE') or die('Access Denied.');

// output_vars(get_defined_vars(), isset($this) ? $this : null);

/* @var Concrete\Controller\Dialog\File\Import $controller */
/* @var Concrete\Core\View\DialogView $view */

/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Core\Application\Service\UserInterface $ui */
/* @var Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface $resolverManager */

/* @var string $formID */
/* @var Concrete\Core\Tree\Node\Type\FileFolder|null $currentFolder */
/* @var Concrete\Core\Page\Page|null $originalPage */
/* @var bool $isChunkingEnabled */
/* @var int $chunkSize */
/* @var Concrete\Core\Entity\File\StorageLocation\StorageLocation $incomingStorageLocation */
/* @var string $incomingPath */
/* @var array $incomingContents */
/* @var string|null $incomingContentsError */
/* @var Concrete\Core\Entity\File\File|null $replacingFile */
?>
<div class="ccm-ui ccm-file-manager-import-files" id="<?= $formID ?>">
    <?= $ui->tabs([
        ['local', t('Your Computer'), true],
        ['incoming', t('Incoming Directory')],
        ['remote', t('Remote Files')],
    ]) ?>

    <div class="ccm-tab-content" id="ccm-tab-content-local">
        <form action="<?= $resolverManager->resolve(['/ccm/system/file/upload']) ?>" class="dropzone">
            <?php $token->output() ?>
            <input type="hidden" name="responseFormat" value="dropzone" />
            <?php
            if ($currentFolder !== null) {
                ?><input type="hidden" name="currentFolder" value="<?= $currentFolder->getTreeNodeID() ?> " /><?php
            }
            if ($originalPage !== null) {
                ?><input type="hidden" name="ocID" value="<?= $originalPage->getCollectionID() ?> " /><?php
            }
            if ($replacingFile !== null) {
                ?><input type="hidden" name="fID" value="<?= $replacingFile->getFileID() ?> " /><?php
            }
            ?>
        </form>
    </div>

    <div class="ccm-tab-content" id="ccm-tab-content-incoming">
        <?php
        if ($incomingContentsError !== null) {
            ?>
            <div class="alert alert-danger">
                <?= nl2br(h($incomingContentsError)) ?>
            </div>
            <?php
        } elseif (empty($incomingContents)) {
            ?>
            <div class="alert alert-info">
                <?= t('No files found in %s for the storage location "%s".', h($incomingPath), h($incomingStorageLocation->getName())) ?>
            </div>
            <?php
        } else {
            ?>
            <form id="ccm-file-add-incoming-form" method="POST" action="<?= $resolverManager->resolve(['/ccm/system/file/import_incoming']) ?>">
                <?php $token->output('import_incoming') ?>
                <?php
                if ($currentFolder !== null) {
                    ?><input type="hidden" name="currentFolder" value="<?= $currentFolder->getTreeNodeID() ?> " /><?php
                }
                if ($originalPage !== null) {
                    ?><input type="hidden" name="ocID" value="<?= $originalPage->getCollectionID() ?> " /><?php
                }
                if ($replacingFile !== null) {
                    ?><input type="hidden" name="fID" value="<?= $replacingFile->getFileID() ?> " /><?php
                }
                ?>
                <table class="incoming_file_table table table-striped">
                    <thead>
                        <tr>
                            <th class="incoming_file_table-checkbox">
                                <?php
                                if ($replacingFile === null) {
                                    ?>
                                    <input type="checkbox" class="ccm-check-all-incoming"/>
                                    <?php
                                }
                                ?>
                            </th>
                            <th class="incoming_file_table-thumbnail"></th>
                            <th class="incoming_file_table-filename"><?= t('Filename') ?></th>
                            <th class="incoming_file_table-size"><?= t('Size') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($incomingContents as $index => $file) {
                            ?>
                            <tr>
                                <td class="incoming_file_table-checkbox">
                                    <?php
                                    if ($file['allowed']) {
                                        if ($replacingFile === null) {
                                            ?>
                                            <input id="<?=$formID ?>-incoming-<?= $index ?>" type="checkbox" name="send_file[]" class="ccm-file-select-incoming" value="<?= h($file['basename']) ?>" />
                                            <?php
                                        } else {
                                            ?>
                                            <input id="<?=$formID ?>-incoming-<?= $index ?>" type="radio" name="send_file[]" class="ccm-file-select-incoming" value="<?= h($file['basename']) ?>" required="required" />
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <input type="<?= $replacingFile === null ? 'checkbox' : 'radio' ?>" disabled="disabled" class="launch-tooltip" title="<?= t('File extension not allowed') ?>" />
                                        <?php
                                    }
                                    ?>
                                </td>
                                <td class="incoming_file_table-thumbnail"><label for="<?=$formID ?>-incoming-<?= $index ?>"><?= $file['thumbnail'] ?></label></td>
                                <td class="incoming_file_table-filename"><?php
                                if ($file['allowed']) {
                                    ?><label for="<?=$formID ?>-incoming-<?= $index ?>"><?= h($file['basename']) ?></label><?php
                                } else {
                                    ?><span class="text-danger launch-tooltip" title="<?= h(t('Invalid file extension')) ?>"><?= h($file['basename']) ?></span><?php
                                }
                                ?></td>
                                <td class="incoming_file_table-size"><?= $file['displaySize'] ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <div class="checkbox">
                    <label>
                        <?= $form->checkbox('removeFilesAfterPost', 1) ?>
                        <?= t(/*i18n: %1$s is a directory name, %2$s is a storage location name */ 'Remove files from %1$s directory of "%2$s" storage location.', '<code>' . h($incomingPath) . '</code>', $incomingStorageLocation->getDisplayName()) ?>
                    </label>
                </div>
            </form>
            <?php
        }
        ?>
    </div>

    <div class="ccm-tab-content" id="ccm-tab-content-remote">
        <form id="ccm-file-add-remote-form" method="POST" action="<?= $resolverManager->resolve(['/ccm/system/file/import_remote']) ?>">
            <?php $token->output('import_remote') ?>
            <?php
            if ($currentFolder !== null) {
                ?><input type="hidden" name="currentFolder" value="<?= $currentFolder->getTreeNodeID() ?> " /><?php
            }
            if ($originalPage !== null) {
                ?><input type="hidden" name="ocID" value="<?= $originalPage->getCollectionID() ?> " /><?php
            }
            if ($replacingFile !== null) {
                ?><input type="hidden" name="fID" value="<?= $replacingFile->getFileID() ?> " /><?php
            }
            if ($replacingFile === null) {
                echo $form->textarea('url_upload', '', ['placeholder' => t('Enter URL to valid file(s), one URL per line'), 'required' => 'required']);
            } else {
                echo $form->text('url_upload', '', ['placeholder' => t('Enter URL to valid file'), 'required' => 'required']);
            }
            ?>
        </form>
    </div>

</div>

<script>
$(document).ready(function() {
var $dialog = $('#' + <?= json_encode($formID) ?>).closest('.ui-dialog-content'),
    $dialogContainer = $dialog.closest('.ui-dialog'),
    uploadedFiles = [];

$dialog.jqdialog('option', 'buttons', [{}]);
$dialogContainer.find('.ui-dialog-buttonset').remove();
$dialog.on('dialogclose', function() {
    if (uploadedFiles.length === 0) {
        return;
    }
    <?php
    if ($replacingFile === null) {
        ?>
        ConcreteEvent.publish('FileManagerAddFilesComplete', {files: uploadedFiles});
        <?php
    } else {
        ?>
        ConcreteEvent.publish('FileManagerReplaceFileComplete', {files: uploadedFiles});
        <?php
    }
    ?>
});

function handleImportResponse(response, isSingleUploadOperation) {
    if (!response) {
        return;
    }
    ConcreteAjaxRequest.validateResponse(
        response,
        function(failed) {
            if (isSingleUploadOperation && response.message) {
                ConcreteAlert.notify({
                    title: response.title,
                    message: response.message,
                    appendTo: document.body
                });
            }
            if (response.files && response.files.length) {
                $.each(response.files, function() {
                    uploadedFiles.push(this);
                })
                if (isSingleUploadOperation) {
                    $dialog.jqdialog('close');
                }
            }
        }
    );
}

// Setup dialog buttons
function refreshDialogButtons() {
    var $dialogButtonPane = $dialogContainer.find('.ui-dialog-buttonpane'),
        tab = $dialog.find('ul.nav-tabs li.active a[data-tab]').data('tab'),
        $leftButtons,
        $rightButtons;

    $dialogButtonPane
        .addClass('ccm-ui')
        .empty()
        .append($leftButtons = $('<div class="pull-left" />'))
        .append($rightButtons = $('<div class="pull-right" />'))
    ;
    $leftButtons.append($('<button class="btn btn-default" />')
        .text(uploadedFiles.length === 0 ? <?= json_encode(t('Cancel')) ?> : <?= json_encode(t('Close')) ?>)
        .on('click', function(e) {
            e.preventDefault();
            $dialog.jqdialog('close');
        })
    );
    switch (tab) {
        case 'incoming':
        case 'remote':
            if (<?= $replacingFile === null ? 'true' : 'false' ?> || uploadedFiles.length === 0) {
                $rightButtons.append($('<button class="btn btn-success" />')
                    .text(<?= json_encode($replacingFile === null ? t('Add Files') : t('Replace File')) ?>)
                    .on('click', function(e) {
                        e.preventDefault();
                        $dialog.find('#ccm-tab-content-' + tab + ' form').submit();
                    })
                );
            }
            break;
    }
}

$dialog.find('ul.nav-tabs a[data-tab]').on('click', function() {
    setTimeout(function() { refreshDialogButtons(); }, 0);
});
setTimeout(function() { refreshDialogButtons(); }, 0);

//Setup upload tab
var $dropzone = $dialog.find('#ccm-tab-content-local form').dropzone({
    maxFiles: <?= $replacingFile === null ? 'null' : 1 ?>,
    sending: function() {
        $dialogContainer.find('.ui-dialog-buttonpane button').attr('disabled', 'disabled');
    },
    success: function(data, r) {
        handleImportResponse(r, <?= $replacingFile ? 'true' : 'false' ?>);
    },
    <?php
    if ($replacingFile) {
        // We may need to allow people to re-try uploading a file if maxFiles === 1 and the upload of the file filed
        ?>
        error: function(files, message, xhr) {
            this.defaultOptions.error.apply(this, arguments);
            if (files) {
                if (!(files instanceof Array)) {
                    files = [files];
                }
                $.each(files, function(index, file) {
                    if (file && file.accepted) {
                        file.accepted = false;
                    }
                });
            }
        },
        <?php
    }
    ?>
    chunksUploaded: function (file, done) {
        if (file.xhr.response) {
            handleImportResponse(JSON.parse(file.xhr.response), <?= $replacingFile ? 'true' : 'false' ?>);
        }
        done();
    },
    queuecomplete: function() {
        refreshDialogButtons();
    },
    chunking: <?= $isChunkingEnabled ? 'true' : 'false' ?>,
    chunkSize: <?= $chunkSize ?>,
    retryChunks: <?= $isChunkingEnabled ? 'true' : 'false' ?>,
    previewTemplate: <?= json_encode(<<<'EOT'
<div class="dz-preview dz-file-preview">
    <div class="dz-details">
        <div class="dz-filename"><span data-dz-name></span></div>
        <div class="dz-size" data-dz-size></div>
        <img data-dz-thumbnail />
    </div>
    <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
    <div class="dz-success-mark"><span>✔</span></div>
    <div class="dz-error-mark"><span>✘</span></div>
    <div class="dz-error-message"><span data-dz-errormessage></span></div>
</div>
EOT
        ) ?>
});

// Setup incoming tab
<?php
if ($replacingFile === null) {
    ?>
    $dialog.find('input.ccm-check-all-incoming').on('click', function (e) {
        var checked = this.checked;
        $dialog.find('input.ccm-file-select-incoming').prop('checked', this.checked);
    });
    <?php
}
?>
$dialog.find('#ccm-tab-content-incoming form').concreteAjaxForm({
    skipResponseValidation: true,
    success: function(r) {
        handleImportResponse(r, true);
        refreshDialogButtons();
    }
});

// Setup incoming tab
$dialog.find('#ccm-tab-content-remote form').concreteAjaxForm({
    skipResponseValidation: true,
    success: function(r) {
        handleImportResponse(r, true);
        refreshDialogButtons();
    }
});

});
</script>
