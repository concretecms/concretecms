<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

interface RoutineInterface
{
    public function getHandle();
    public function import(\SimpleXMLElement $element);
}
