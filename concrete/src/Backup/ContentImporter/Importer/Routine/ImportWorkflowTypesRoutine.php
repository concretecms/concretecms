<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\BannedWord\BannedWord;

class ImportWorkflowTypesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'workflow_types';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->workflowtypes)) {
            foreach ($sx->workflowtypes->workflowtype as $wt) {
                $pkg = static::getPackageObject($wt['package']);
                $name = $wt['name'];
                if (!$name) {
                    $name = \Core::make('helper/text')->unhandle($wt['handle']);
                }
                $type = \Concrete\Core\Workflow\Type::getByHandle((string) $wt['handle']);
                if (!is_object($type)) {
                    $type = \Concrete\Core\Workflow\Type::add($wt['handle'], $name, $pkg);
                }
            }
        }
    }

}
