<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector;

use Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\RoutineInterface;

class ValueInspector implements ValueInspectorInterface
{
    /**
     * @var \Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\RoutineInterface[]
     */
    protected $routines = [];

    public function registerInspectionRoutine(RoutineInterface $routine)
    {
        $this->routines[$routine->getHandle()] = $routine;
    }

    /**
     * @return \Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\RoutineInterface[]
     */
    public function getInspectionRoutines()
    {
        return $this->routines;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\ValueInspectorInterface::inspect()
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
