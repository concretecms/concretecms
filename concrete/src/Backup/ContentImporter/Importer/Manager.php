<?php
namespace Concrete\Core\Backup\ContentImporter\Importer;

use Concrete\Core\Backup\ContentImporter\Importer\Routine\RoutineInterface;

class Manager
{
    protected $routines = array();
    protected $additionalRoutines = array();
    protected $sortedRoutines;

    /**
     * Registers a core importer routine. Add-on developers should use addImporterRoutine
     * You can use this if you want to swap out a core routine.
     * @param RoutineInterface $routine
     */
    public function registerImporterRoutine(RoutineInterface $routine)
    {
        if (in_array($routine, $this->routines)) {
            $this->routines[array_search($routine, $this->routines)] = $routine;
        } else {
            $this->routines[] = $routine;
        }
    }

    public function addImporterRoutine(RoutineInterface $routine, $addAfter)
    {
        $this->additionalRoutines[$addAfter] = $routine;
    }

    public function getImporterRoutines()
    {
        if (!isset($this->sortedRoutines)) {
            $sortedRoutines = $this->routines;
            $replacements = array();
            foreach($this->additionalRoutines as $addAfter => $routine) {
                foreach($sortedRoutines as $i => $sortedRoutine) {
                    $handle = $sortedRoutine->getHandle();
                    if ($handle == $addAfter) {
                        $replacements[] = array($i, $routine);
                    }
                }
            }

            // This code sucks, but I'm not sure why it wasn't working in the more elegant way.

            foreach($replacements as $replacement) {
                $position = $replacement[0];
                $routine = $replacement[1];
                array_splice($sortedRoutines, $position + 1, 0, [$routine]);
            }

            $this->sortedRoutines = $sortedRoutines;
        }
        return $this->sortedRoutines;
    }

}
