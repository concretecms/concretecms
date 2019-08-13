<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine;

/**
 * @since 5.7.5.4
 */
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
