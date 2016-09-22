<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;
use Concrete\Core\StyleCustomizer\Inline\StyleSet;

abstract class AbstractPageStructureRoutine extends AbstractRoutine
{

    public function setupPageNodeOrder($pageNodeA, $pageNodeB)
    {
        $pathA = (string) $pageNodeA['path'];
        $pathB = (string) $pageNodeB['path'];
        $numA = count(explode('/', $pathA));
        $numB = count(explode('/', $pathB));
        if ($numA == $numB) {
            if (intval($pageNodeA->originalPos) < intval($pageNodeB->originalPos)) {
                return -1;
            } else {
                if (intval($pageNodeA->originalPos) > intval($pageNodeB->originalPos)) {
                    return 1;
                } else {
                    return 0;
                }
            }
        } else {
            return ($numA < $numB) ? -1 : 1;
        }
    }



}
