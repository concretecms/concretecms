<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine;

interface RoutineInterface
{
    public function getHandle();

    /**
     * @param $content
     *
     * @return \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface[]
     */
    public function match($content);

    public function replaceContent($content);
}
