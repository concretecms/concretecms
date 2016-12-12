<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Workflow\Type;
use Concrete\Core\Workflow\Workflow;

class ImportWorkflowsRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'workflow_progress_categories';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->workflows)) {
            foreach ($sx->workflows->workflow as $wf) {
                $pkg = static::getPackageObject($wf['package']);
                $workflow = Workflow::getByName((string) $wf['name']);
                if (!is_object($workflow)) {
                    $type = Type::getByHandle((string) $wf['type']);
                    if (is_object($type)) {
                        Workflow::add($type, (string) $wf['name'], $pkg);
                    }
                }
            }
        }
    }

}
