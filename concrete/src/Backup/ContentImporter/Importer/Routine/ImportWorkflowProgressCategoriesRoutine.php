<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\BannedWord\BannedWord;

class ImportWorkflowProgressCategoriesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'workflow_progress_categories';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->workflowprogresscategories)) {
            foreach ($sx->workflowprogresscategories->category as $wpc) {
                $pkg = static::getPackageObject($wpc['package']);
                $category = \Concrete\Core\Workflow\Progress\Category::getByHandle((string) $wpc['handle']);
                if (!is_object($category)) {
                    \Concrete\Core\Workflow\Progress\Category::add((string) $wpc['handle'], $pkg);
                }
            }
        }
    }

}
