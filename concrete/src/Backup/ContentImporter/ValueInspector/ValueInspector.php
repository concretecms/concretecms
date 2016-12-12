<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector;


use Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\RoutineInterface;
use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\FileItem;
use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageFeedItem;
use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageItem;
use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageTypeItem;
use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PictureItem;
use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ImageItem;

class ValueInspector implements ValueInspectorInterface
{

    protected $routines = array();

    public function registerInspectionRoutine(RoutineInterface $routine)
    {
        $this->routines[$routine->getHandle()] = $routine;
    }

    public function getInspectionRoutines()
    {
        return $this->routines;
    }

    public function inspect($content)
    {
        $result = new Result($content);
        foreach($this->getInspectionRoutines() as $routine) {
            $result->addInspectionRoutine($routine);
            $items = $routine->match($content);
            foreach($items as $item) {
                $result->addMatchedItem($item);
            }
        }
        return $result;
    }

}
