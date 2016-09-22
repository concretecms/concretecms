<?php
namespace Concrete\Core\Backup\ContentImporter\Importer;

use Concrete\Core\Backup\ContentImporter\Importer\Routine\RoutineInterface;

class Manager
{
    protected $routines = array();

    public function registerImporterRoutine(RoutineInterface $routine)
    {
        $this->routines[$routine->getHandle()] = $routine;
    }

    public function getImporterRoutines()
    {
        return $this->routines;
    }

}
