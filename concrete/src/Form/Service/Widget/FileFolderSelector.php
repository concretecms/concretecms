<?php
namespace Concrete\Core\Form\Service\Widget;

use Concrete\Core\File\Filesystem;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\Utility\Service\Identifier;
use Symfony\Component\HttpFoundation\JsonResponse;

class FileFolderSelector
{

    public function selectFileFolder($field, $folder = null, $updateSelectionOnPost = true)
    {
        $identifier = new Identifier();
        $identifier = $identifier->getString(32);

        $filesystem = new Filesystem();

        $args = new \stdClass();
        $selected = 0;

        if ($folder) {
            $selected = is_object($folder) ? $folder->getTreeNodeID() : $folder;
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && $updateSelectionOnPost) {
            if (isset($_POST[$field])) {
                $selected = intval($_POST[$field]);
            }
        }

        $rootTreeNodeID = $filesystem->getRootFolder()->getTreeNodeID();
        $sortByLabel = h(t('Sort by'));
        $systemOrderLabel = h(t('System order'));
        $nameAscendingLabel = h(t('Name (A-Z)'));
        $nameDescendingLabel = h(t('Name (Z-A)'));

        $html = <<<EOL
        <input type="hidden" name="{$field}" value="{$selected}">
        <div class="d-flex align-items-center gap-2 mb-2">
            <label for="file-folder-selector-sort-{$identifier}" class="form-label mb-0">{$sortByLabel}</label>
            <select id="file-folder-selector-sort-{$identifier}" class="form-select form-select-sm w-auto" data-file-folder-selector-sort="{$identifier}">
                <option value="">{$systemOrderLabel}</option>
                <option value="name_asc">{$nameAscendingLabel}</option>
                <option value="name_desc">{$nameDescendingLabel}</option>
            </select>
        </div>
        <div data-file-folder-selector="{$identifier}"></div>
        <script type="text/javascript">
        $(function() {
            var ajaxData = {
                displayOnly: 'file_folder'
            };
            var treeElement = $('[data-file-folder-selector={$identifier}]');
            treeElement.concreteTree({
                    ajaxData: ajaxData,
                    treeNodeParentID: {$rootTreeNodeID},
                    selectNodesByKey: [{$selected}],
                    onSelect : function(nodes) {
                        if (nodes.length) {
                            $('input[name={$field}]').val(nodes[0]);
                        } else {
                            $('input[name={$field}]').val('');
                        }
                    },
                    chooseNodeInForm: 'single'
            });
            $('[data-file-folder-selector-sort={$identifier}]').on('change', function() {
                var orderBy = $(this).val();
                if (orderBy) {
                    ajaxData.orderBy = orderBy;
                } else {
                    delete ajaxData.orderBy;
                }
                var tree = $.ui.fancytree.getTree(treeElement);
                tree.reload($.extend({}, tree.options.source, {
                    data: ajaxData
                }));
            });
        });
        </script>
EOL;

        return $html;
    }
}
