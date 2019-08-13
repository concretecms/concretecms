<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

/**
 * @since 8.0.0
 */
interface RoutineInterface
{
    public function getHandle();
    public function import(\SimpleXMLElement $element);
}
