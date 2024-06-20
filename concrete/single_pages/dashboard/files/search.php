<?php

/** @noinspection PhpUndefinedMethodInspection */

/** @noinspection PhpDeprecationInspection */

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\MenuInterface;
use Concrete\Core\File\Search\ColumnSet\Column\NameColumn;
use Concrete\Core\File\Search\Result\Result;
use Concrete\Core\File\Upload\Dropzone;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Legacy\FilePermissions;
use Concrete\Core\Search\Result\Column;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Tree\Node\Type\FileFolder;

/** @var MenuInterface $menu */
/** @var Result $result */
/** @var int $folderID */
/** @var DropdownMenu $resultsBulkMenu */

$fp = FilePermissions::getGlobal();
$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);

if ($fp->canAddFile() || $fp->canSearchFiles()) { ?>

    <div id="ccm-search-results-table">
        <table class="ccm-search-results-table" data-search-results="files">
            <thead>
            <tr>
                <th colspan="3" class="ccm-search-results-bulk-selector">
                    <div class="btn-group dropdown">
                            <span class="btn btn-secondary" data-search-checkbox-button="select-all">
                                <!--suppress HtmlFormInputWithoutLabel -->
                                <input type="checkbox" data-search-checkbox="select-all"/>
                            </span>

                        <button
                                type="button"
                                disabled="disabled"
                                data-search-checkbox-button="dropdown"
                                class="btn btn-secondary dropdown-toggle dropdown-toggle-split"
                                data-bs-toggle="dropdown"
                                data-reference="parent">

                                <span class="sr-only">
                                    <?php
                                    echo t("Toggle Dropdown"); ?>
                                </span>
                        </button>

                        <?php
                        echo $resultsBulkMenu->getMenuElement(); ?>
                    </div>
                </th>

                <?php
                foreach ($result->getColumns() as $column) { ?>
                    <?php
                    /** @var Column $column */ ?>
                    <th class="<?php
                    echo $column->getColumnStyleClass() ?>">
                        <?php
                        if ($column->isColumnSortable()) { ?>
                            <a href="<?php
                            echo h($column->getColumnSortURL()) ?>">
                                <?php
                                echo $column->getColumnTitle() ?>
                            </a>
                        <?php } else { ?>
                            <span>
                                    <?php
                                    echo $column->getColumnTitle() ?>
                                </span>
                        <?php
                        } ?>
                    </th>
                <?php
                } ?>
            </tr>
            </thead>

            <tbody>
            <?php
            foreach ($result->getItems() as $item) { ?>
                <tr data-details-url="<?=$item->getDetailsURL()?>" data-details-fid="<?=$item->getResultFileID()?>"
                    <?php if (isset($highlightResults)
                        && in_array($item->getItem()->getTreeNodeID(), $highlightResults)) { ?>
                            class="table-row-highlight"<?php } ?>
                    >
                    <td class="ccm-search-results-checkbox">
                        <?php
                        if ($item->getResultFileID() > 0) { ?>
                            <!--suppress HtmlFormInputWithoutLabel -->
                            <input data-search-checkbox="individual"
                                   type="checkbox"
                                   data-node-type="<?php
                                   echo $item->getItem()->getTreeNodeTypeHandle() ?>"
                                   data-item-uuid="<?php echo $item->getResultFileUUID() ?>"
                                   data-item-id="<?php echo $item->getResultFileID() ?>"/>
                        <?php
                        } ?>
                    </td>

                    <td class="ccm-search-results-icon">
                        <?php
                        echo $item->getListingThumbnailImage() ?>
                    </td>

                    <td class="ccm-search-results-favorite-switcher">
                        <?php if ($item->getItem() instanceof FileFolder) { ?>
                            <label class="ccm-fancy-checkbox">
                                <?php echo $form->checkbox("", $item->getItem()->getTreeNodeId(), $item->isFavoredItem(), ["class" => "ccm-favorite-folder-switch"]); ?>
                                <i class="fas fa-star checked"></i>
                                <?php
                                /* This was changed back because someone said that Font Awesome Regular icons require the pro license.
                                 * That may be true for some icons, but this icon explicitly is listed in the free icons on fontawesome.com,
                                 * and does not have a pro badge beneath the regular listing. Therefore I am going to use the regular icon in order
                                 * to have an outline only star, and begin the process of moving away from a less restrictive and byzantine
                                 * icon system
                                 */
                                 ?>
                                <i class="far fa-star unchecked"></i>
                            </label>
                        <?php }?>
                    </td>

                    <?php
                    foreach ($item->getColumns() as $column) { ?>
                        <?php
                        /** @var \Concrete\Core\Search\Column\Column $column */ ?>
                        <?php
                        /** @noinspection PhpUndefinedMethodInspection */
                        if ($column->getColumn() instanceof NameColumn) { ?>
                            <td class="ccm-search-results-name">
                                <?php echo $column->getColumnValue($item); ?>
                            </td>
                        <?php } else { ?>
                            <td class="<?=$class?? '' ?>">
                                <?php
                                echo $column->getColumnValue($item); ?>
                            </td>
                        <?php
                        } ?>
                    <?php
                    } ?>

                    <?php
                    $menu = $item->getItem()->getTreeNodeMenu(); ?>

                    <?php
                    if ($menu && $menu->hasItems()) { ?>
                        <td class="ccm-search-results-menu-launcher">
                            <div class="dropdown" data-menu="search-result">

                                <button class="btn btn-icon"
                                        data-boundary="viewport"
                                        type="button"
                                        data-bs-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false">

                                    <svg width="16" height="4">
                                        <use xlink:href="#icon-menu-launcher"/>
                                    </svg>
                                </button>

                                <?php
                                echo $menu->getMenuElement(); ?>
                            </div>
                        </td>
                    <?php
                    } else { ?>
                        <td></td>
                    <?php
                    } ?>
                </tr>
            <?php
            } ?>
            </tbody>
        </table>
    </div>

    <?php
    echo $result->getPagination()->renderView('dashboard'); ?>

    <script type="text/javascript">
        (function ($) {
            $(function () {
                $('table[data-search-results=files]').concreteFileManagerTable({
                    folderID: '<?php echo $folderID ?? null; ?>',
                    dropzone: <?= json_encode($app->make(Dropzone::class)->getConfigurationOptions(true)) ?>,
                });
            });
        })(jQuery);
    </script>

<?php } else { ?>
    <p>
        <?php
        echo t('You do not have access to the file manager.') ?>
    </p>
<?php
} ?>
