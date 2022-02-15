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

        $html = <<<EOL
        <input type="hidden" name="{$field}" value="{$selected}">
        <div data-file-folder-selector="{$identifier}"></div>
        <script type="text/javascript">
        $(function() {
            $('[data-file-folder-selector={$identifier}]').concreteTree({
                    ajaxData: {
                        displayOnly: 'file_folder'
                    },
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
        });
        </script>
EOL;

        return $html;
    }
}
