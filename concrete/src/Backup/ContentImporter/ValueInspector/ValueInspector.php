<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector;

use Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\RoutineInterface;

/**
 * @since 5.7.5.3
 */
class ValueInspector implements ValueInspectorInterface
{
    /**
     * @since 5.7.5.4
     */
    protected $routines = array();

    /**
     * @since 5.7.5.4
     */
    public function registerInspectionRoutine(RoutineInterface $routine)
    {
        $this->routines[$routine->getHandle()] = $routine;
    }

    /**
     * @since 5.7.5.4
     */
    public function getInspectionRoutines()
    {
        return $this->routines;
    }

    /**
     * @since 5.7.5.4
     */
    public function inspect($content)
    {
        $result = new Result($content);
        foreach ($this->getInspectionRoutines() as $routine) {
            $result->addInspectionRoutine($routine);
            $items = $routine->match($content);
            foreach ($items as $item) {
                $result->addMatchedItem($item);
            }
        }

        return $result;
    }
}
