<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Tree\Node\NodeType;

class ImportTreeNodeTypesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'tree_node_types';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->treenodetypes)) {
            foreach ($sx->treenodetypes->treenodetype as $t) {
                $type = NodeType::getByHandle((string) $t['handle']);
                if (!$type) {
                    $pkg = static::getPackageObject((string)$t['package']);
                    NodeType::add((string)$t['handle'], $pkg);
                }
            }
        }
    }
}
