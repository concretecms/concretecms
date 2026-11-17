<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\File\Set\Set;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Legacy\FilePermissions;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;

/** @var int $fsType */
/** @var Set $fs */
/** @var Token $validation_token */
$app = Application::getFacadeApplication();
$app->make('helper/concrete/ui');
/** @var Date $dh */
$dh = $app->make(Date::class);

?>

<?php if ($this->controller->getTask() == 'view_detail'): ?>

    <script type="text/javascript">
        let deleteFileSet = function () {
            if (confirm('<?php echo h(t('Are you sure you want to permanently remove this file set?')); ?>')) {
                location.href = "<?php echo Url::to('/dashboard/files/sets', 'delete', $fs->getFileSetID(), $validation_token->generate('delete_file_set'))?>";
            }
        }
    </script>

    <div class="ccm-dashboard-header-buttons d-flex align-items-center gap-2">
        <div class="dropdown">
            <button type="button" class="btn btn-secondary p-2 dropdown-toggle" data-bs-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false" aria-label="<?php echo h(t('Items per page')) ?>">
                <?php echo $itemsPerPage ?>
            </button>
            <ul class="dropdown-menu">
                <li class="dropdown-header"><?php echo t('Items per page') ?></li>
                <?php foreach ($itemsPerPageOptions as $option): ?>
                    <?php $optionURL = $fileSetDetailURL->setQuery(['itemsPerPage' => $option]); ?>
                    <li data-items-per-page="<?php echo $option ?>">
                        <a class="dropdown-item <?php echo (int) $option === $itemsPerPage ? 'active' : '' ?>"
                           href="<?php echo h($optionURL) ?>">
                            <?php echo $option ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <button class="btn btn-danger" onclick="deleteFileSet()">
            <?php echo t('Delete Set') ?>
        </button>
    </div>

    <form method="post" id="file-set-remove-files"
          action="<?php echo h(Url::to('/dashboard/files/sets', 'remove_files')->setQuery($paginationQuery)) ?>">
        <?php $validation_token->output('remove_file_set_files'); ?>
        <input type="hidden" name="fsID" value="<?php echo $fs->getFileSetID() ?>">
    </form>

    <form method="post" class="form-horizontal" id="file_sets_edit"
          action="<?php echo h(Url::to('/dashboard/files/sets', 'file_sets_edit')->setQuery($paginationQuery)) ?>">

        <?php echo $validation_token->output('file_sets_edit'); ?>

        <div class="form-group">
            <?php echo $form->label('file_set_name', t('Name')) ?>
            <?php echo $form->text('file_set_name', $fs->fsName); ?>
        </div>

        <?php echo $form->hidden('fsID', $fs->getFileSetID()); ?>

        <?php
            $fileSetPermissions = FilePermissions::getGlobal();
        ?>

        <?php if (count($files) > 0): ?>

            <p class="help-block">
                <?php echo t('Click and drag to reorder the files on this page. New files added to this set will automatically be appended to the end.') ?>
            </p>

            <div class="ccm-spacer">
                &nbsp;
            </div>

            <table class="ccm-search-results-table compact-results">
                <thead>
                    <tr>
                        <th colspan="2" class="ccm-search-results-bulk-selector">
                            <div class="btn-group dropdown">
                                <label class="btn btn-secondary mb-0" for="file-set-select-all">
                                    <input type="checkbox" id="file-set-select-all" aria-label="<?php echo h(t('Select all files on this page')) ?>">
                                </label>
                                <button type="button" id="file-set-bulk-actions-toggle"
                                        class="btn btn-secondary dropdown-toggle dropdown-toggle-split"
                                        data-bs-toggle="dropdown" data-bs-reference="parent"
                                        aria-haspopup="true" aria-expanded="false"
                                        aria-label="<?php echo h(t('Actions for selected files')) ?>" disabled>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="file-set-bulk-actions-toggle">
                                    <li>
                                        <button type="button" id="file-set-remove-selected" class="dropdown-item" disabled>
                                            <?php echo t('Remove from Set') ?>
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" id="file-set-delete-selected" class="dropdown-item" disabled>
                                            <?php echo t('Delete Permanently') ?>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </th>

                        <th>
                            <span>
                                <?php echo t('Thumbnail') ?>
                            </span>
                        </th>

                        <th>
                            <a href="javascript:void(0)" class="sort-link" data-sort="type">
                                <?php echo t('Type') ?>
                            </a>
                        </th>

                        <th>
                            <a href="javascript:void(0)" class="sort-link" data-sort="title">
                                <?php echo t('Title') ?>
                            </a>
                        </th>

                        <th>
                            <a href="javascript:void(0)" class="sort-link" data-sort="filename">
                                <?php echo t('File name') ?>
                            </a>
                        </th>

                        <th>
                            <a href="javascript:void(0)" class="sort-link" data-sort="added">
                                <?php echo t('Added') ?>
                            </a>
                        </th>
                        <th>
                            <span class="visually-hidden"><?php echo t('Actions') ?></span>
                        </th>
                    </tr>
                </thead>

                <tbody class="ccm-file-set-file-list">
                    <?php foreach ($files as $f): ?>
                        <?php
                            $filePermissions = new Permissions($f);
                            $canRemove = $filePermissions->canViewFileInFileManager() && $fileSetPermissions->canAddFile($f);
                            $canDelete = $filePermissions->canDeleteFile();
                        ?>
                        <tr id="fID_<?php echo $f->getFileID() ?>" class="ccm-search-results-hoverable">
                            <td class="ccm-file-set-checkbox">
                                <input type="checkbox" name="fID[]" value="<?php echo $f->getFileID() ?>"
                                       form="file-set-remove-files" data-file-set-select
                                       data-can-remove="<?php echo $canRemove ? '1' : '0' ?>"
                                       data-can-delete="<?php echo $canDelete ? '1' : '0' ?>"
                                       aria-label="<?php echo h(t('Select %s', $f->getTitle())) ?>"
                                       <?php echo $canRemove || $canDelete ? '' : 'disabled' ?>>
                            </td>
                            <td>
                                <i class="fas fa-arrows-alt-v"></i>
                            </td>

                            <td class="ccm-file-manager-search-results-thumbnail">
                                <?php echo $f->getListingThumbnailImage() ?>

                                <input type="hidden" name="fsDisplayOrder[]" value="<?php echo $f->getFileID() ?>"/>
                            </td>

                            <td data-key="type">
                                <?php echo $f->getGenericTypetext() ?>/<?php echo $f->getType() ?>
                            </td>

                            <td data-key="title">
                                <?php echo h($f->getTitle()) ?>
                            </td>

                            <td data-key="filename">
                                <?php echo h($f->getFileName()) ?>
                            </td>

                            <td data-key="added" data-sort="<?php echo $f->getDateAdded()->getTimestamp() ?>">
                                <?php
                                    /** @noinspection PhpUnhandledExceptionInspection */
                                    echo $dh->formatDateTime($f->getDateAdded()->getTimestamp())
                                ?>
                            </td>
                            <td class="ccm-file-set-actions" data-file-set-file-id="<?php echo $f->getFileID() ?>">
                                <div class="d-flex flex-column flex-xl-row gap-1">
                                    <button type="button" class="btn btn-icon launch-tooltip" data-file-set-action="remove"
                                            title="<?php echo h(t('Remove from Set Immediately')) ?>"
                                            aria-label="<?php echo h(t('Remove %s from Set Immediately', $f->getTitle())) ?>"
                                            <?php echo $canRemove ? '' : 'disabled' ?>>
                                        <i class="fas fa-times" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="btn btn-icon launch-tooltip" data-file-set-action="delete"
                                            data-dialog-url="<?php echo h(Url::to('/ccm/system/dialogs/file/delete', $f->getFileID())) ?>"
                                            data-dialog-title="<?php echo h(t('Delete %s', $f->getFileName())) ?>"
                                            title="<?php echo h(t('Delete Permanently')) ?>"
                                            aria-label="<?php echo h(t('Delete %s Permanently', $f->getTitle())) ?>"
                                            <?php echo $canDelete ? '' : 'disabled' ?>>
                                        <i class="fas fa-trash" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php else: ?>
            <div class="alert alert-info">
                <?php echo $pagination->hasPreviousPage() ? t('There are no files on this page.') : t('There are no files in this set.') ?>
            </div>
        <?php endif; ?>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?php echo Url::to('/dashboard/files/sets') ?>" class="btn btn-secondary float-start">
                    <?php echo t('Back') ?>
                </a>

                <?php echo $form->submit('save', t('Save'), ['class' => 'btn btn-primary float-end']); ?>
            </div>
        </div>
    </form>

    <?php echo $pagination->renderView('dashboard') ?>

    <script>
        (function($) {
            $(function () {
                let checkboxes = $('#file_sets_edit input[data-file-set-select]:enabled');
                let selectAll = $('#file-set-select-all');
                let bulkActions = $('#file-set-bulk-actions-toggle');
                let removeSelected = $('#file-set-remove-selected');
                let deleteSelected = $('#file-set-delete-selected');

                function updateSelection() {
                    let selected = checkboxes.filter(':checked');
                    selectAll.prop('checked', selected.length > 0 && selected.length === checkboxes.length);
                    selectAll.prop('indeterminate', selected.length > 0 && selected.length < checkboxes.length);
                    bulkActions.prop('disabled', !selected.length);
                    removeSelected.prop('disabled', !selected.length || selected.filter('[data-can-remove="0"]').length > 0);
                    deleteSelected.prop('disabled', !selected.length || selected.filter('[data-can-delete="0"]').length > 0);
                }

                checkboxes.on('change', updateSelection);
                selectAll.on('change', function () {
                    checkboxes.prop('checked', this.checked);
                    updateSelection();
                });
                updateSelection();

                function submitRemoveFiles(fileIDs) {
                    let form = $('<form>', {method: 'post', action: $('#file-set-remove-files').attr('action')});
                    form.append($('#file-set-remove-files input').clone());
                    for (let fileID of fileIDs) {
                        form.append($('<input>', {type: 'hidden', name: 'fID[]', value: fileID}));
                    }
                    form.appendTo(document.body).get(0).submit();
                }

                function confirmRemoveFiles(fileIDs) {
                    let message = fileIDs.length === 1
                        ? <?php echo json_encode(t('Are you sure you want to remove 1 file from this set?')) ?>
                        : <?php echo json_encode(t('Are you sure you want to remove %s files from this set?')) ?>;
                    ConcreteAlert.confirm(message.replace('%s', fileIDs.length), function () {
                        $('#ccm-popup-confirmation').parent().find('[data-dialog-action="submit-confirmation-dialog"]').prop('disabled', true);
                        submitRemoveFiles(fileIDs);
                    }, 'btn-secondary', <?php echo json_encode(t('Remove from Set')) ?>);
                    $('#ccm-popup-confirmation').parent().find('.ui-dialog-buttonpane button').first().trigger('focus');
                }

                removeSelected.on('click', function () {
                    confirmRemoveFiles(checkboxes.filter(':checked').map(function () {
                        return this.value;
                    }).get());
                });

                function deleteFiles(fileIDs) {
                    if (fileIDs.length === 1) {
                        let button = $('#fID_' + fileIDs[0]).find('[data-file-set-action="delete"]');
                        $.fn.dialog.open({
                            href: button.attr('data-dialog-url'),
                            title: button.attr('data-dialog-title'),
                            width: 550,
                            height: 'auto',
                            modal: true,
                            onOpen: function (dialog) {
                                dialog.parent().find('[data-dialog-action="submit"]').text(<?php echo json_encode(t('Delete')) ?>);
                            }
                        });
                        return;
                    }
                    $.fn.dialog.open({
                        href: <?php echo json_encode((string) Url::to('/ccm/system/dialogs/file/bulk/delete')) ?> + '?' + $.param({fID: fileIDs}),
                        title: <?php echo json_encode(t('Delete Permanently')) ?>,
                        width: 500,
                        height: 400,
                        modal: true
                    });
                }

                deleteSelected.on('click', function () {
                    deleteFiles(checkboxes.filter(':checked').map(function () {
                        return this.value;
                    }).get());
                });
                $('[data-file-set-action="delete"]').on('click', function () {
                    deleteFiles([$(this).closest('[data-file-set-file-id]').attr('data-file-set-file-id')]);
                });
                $('[data-file-set-action="remove"]').on('click', function () {
                    submitRemoveFiles([$(this).closest('[data-file-set-file-id]').attr('data-file-set-file-id')]);
                });
                function reloadFiles() {
                    window.location.reload();
                }

                ConcreteEvent.subscribe('FileManagerDeleteFilesComplete', reloadFiles);
                ConcreteEvent.subscribe('ConcreteDeleteFile', reloadFiles);

                let baseClass = "ccm-results-list-active-sort-"; // asc desc

                function ccmFileSetResetSortIcons() {
                    $(".ccm-search-results-table thead tr th")
                        .removeClass(baseClass + 'asc')
                        .removeClass(baseClass + 'desc');

                    $(".ccm-search-results-table thead tr th a").css("color", "#999");
                }

                function ccmFileSetDoSort() {
                    let $this = $(this);
                    let $parent = $(this).parent();
                    let asc = $parent.hasClass(baseClass + 'asc');
                    let key = $this.attr('data-sort');

                    ccmFileSetResetSortIcons();

                    let sortableList = $('.ccm-file-set-file-list');
                    let listItems = $('tr', sortableList);

                    if (asc) {
                        $parent.addClass(baseClass + 'desc');
                        $(".ccm-search-results-table thead tr th." + baseClass + "desc a").css("color", "#333");
                    } else {
                        $parent.addClass(baseClass + 'asc');
                        $(".ccm-search-results-table thead tr th." + baseClass + "asc a").css("color", "#333");
                    }

                    listItems.sort(function (a, b) {
                        let aTD = $('td[data-key=' + key + ']', $(a));
                        let bTD = $('td[data-key=' + key + ']', $(b));

                        let aVal = typeof (aTD.attr('data-sort')) == 'undefined' ? aTD.text().toUpperCase() : parseInt(aTD.attr('data-sort'));
                        let bVal = typeof (bTD.attr('data-sort')) == 'undefined' ? bTD.text().toUpperCase() : parseInt(bTD.attr('data-sort'));

                        if (asc) {
                            return bVal < aVal ? -1 : 1;
                        } else {
                            return aVal < bVal ? -1 : 1;
                        }
                    });

                    sortableList.append(listItems);
                }

                $('.ccm-search-results-table thead th a.sort-link').click(ccmFileSetDoSort);

                $(".ccm-file-set-file-list").sortable({
                    cancel: 'input,textarea,button,select,option,.ccm-file-set-checkbox,.ccm-file-set-actions',
                    cursor: 'move',
                    opacity: 0.5,
                    axis: 'y',
                    helper: function (evt, elem) {
                        let ret = $(elem).clone();
                        let i;

                        ret.width(elem.outerWidth());

                        let retChilds = $(ret.children());
                        let elemChilds = $(elem.children());

                        for (i = 0; i < elemChilds.length; i++)
                            $(retChilds[i]).width($(elemChilds[i]).outerWidth());

                        return ret;
                    },
                    placeholder: "ccm-file-set-file-placeholder",
                    stop: function () {
                        ccmFileSetResetSortIcons();
                    }
                });
            });
        })(jQuery);
    </script>

    <!--suppress CssUnusedSymbol -->
    <style type="text/css">
        #file_sets_edit .ccm-search-results-bulk-selector {
            padding-top: 0;
            padding-right: 0;
            padding-bottom: 0;
        }

        #file_sets_edit .ccm-search-results-bulk-selector .btn-group {
            margin-left: -5px;
        }

        #file_sets_edit .ccm-search-results-bulk-selector .btn {
            padding: 2px 5px;
        }

        #file_sets_edit .ccm-file-set-actions {
            width: 1%;
        }

        #file_sets_edit .ccm-file-set-actions .btn-icon {
            min-width: 44px;
            min-height: 44px;
        }

        #file_sets_edit .ccm-file-set-file-list tr:hover td {
            cursor: move;
        }

        #file_sets_edit .ccm-file-set-file-list tr:hover td.ccm-file-set-checkbox,
        #file_sets_edit .ccm-file-set-file-list tr:hover td.ccm-file-set-actions {
            cursor: default;
        }

        .ccm-file-set-file-placeholder {
            background-color: #ffd !important;
        }

        .ccm-file-set-file-placeholder td {
            background: transparent !important;
        }
    </style>

<?php else: ?>
    <?php if (count($fileSets) > 0): ?>
        <div class="table-responsive">
            <table class="ccm-search-results-table">
                <thead>
                    <tr>
                        <th class="ccm-results-list-active-sort-asc"><a><?php echo t('Set Name') ?></a></th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($fileSets as $fs): ?>
                        <tr data-details-url="<?php echo Url::to('/dashboard/files/sets/', 'view_detail', $fs->getFileSetID()) ?>">
                            <td>
                                <?php echo $fs->getFileSetDisplayName() ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($fsl->requiresPaging()): ?>
            <?php $fsl->displayPagingV2(); ?>
        <?php endif; ?>

    <?php else: ?>
        <section>
            <p>
                <?php echo t('No file sets found.') ?>
            </p>
        </section>
    <?php endif; ?>

    <div class="ccm-dashboard-header-buttons">
        <form class="row row-cols-auto g-0 align-items-center" method="get" action="#">

            <div class="col-auto">
                <?php echo $form->search('fsKeywords', [
                    'placeholder' => t('Search'),
                    'class' => 'form-control form-control-sm',
                    'autocomplete' => 'off']);
                ?>
            </div>

            <div class="col-auto">
                <?php echo $form->select(
                    "fsType",
                    [
                        Set::TYPE_PUBLIC => t("Public Sets"),
                        Set::TYPE_PRIVATE => t("My Sets")
                    ],
                    $fsType == Set::TYPE_PRIVATE ? $fsType : Set::TYPE_PUBLIC,
                    [
                        "class" => "ms-2 form-control form-select-sm"
                    ]
                ); ?>
            </div>

            <div class="col-auto">
                <button type="submit" class="btn btn-secondary ms-2 btn-sm">
                    <svg width="16" height="16">
                        <use xlink:href="#icon-search"/>
                    </svg>
                </button>
            </div>

            <div class="col-auto">
                <a class="btn btn-secondary btn-sm ms-2"
                   href="<?php echo Url::to('/dashboard/files/add_set') ?>"
                   title="<?php echo t('Add File Set') ?>">
                    <?php echo t('Add File Set') ?> <i class="fas fa-plus-circle"></i>
                </a>
            </div>
        </form>
    </div>
<?php endif; ?>



