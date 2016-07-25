<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector;

use Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\RoutineInterface;
use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface;

class Result implements ResultInterface
{
    protected $originalContent;
    protected $replacedContent;
    protected $items = array();
    protected $routines = array();

    public function addInspectionRoutine(RoutineInterface $routine)
    {
        $this->routines[$routine->getHandle()] = $routine;
    }

    /**
     * @return mixed
     */
    public function getOriginalContent()
    {
        return $this->originalContent;
    }

    /**
     * @param mixed $originalContent
     */
    public function setOriginalContent($originalContent)
    {
        $this->originalContent = $originalContent;
    }

    public function __construct($originalContent)
    {
        $this->originalContent = $originalContent;
    }

    public function getReplacedContent()
    {
        if (!isset($this->replacedContent)) {
            $this->replacedContent = $this->originalContent;
            foreach ($this->routines as $routine) {
                $this->replacedContent = $routine->replaceContent($this->replacedContent);
            }
        }

        return $this->replacedContent;
    }

    public function addMatchedItem(ItemInterface $item)
    {
        $this->items[] = $item;
    }

    public function getMatchedItems()
    {
        return $this->items;
    }

    public function getReplacedValue()
    {
        if (isset($this->items[0])) {
            return $this->items[0]->getFieldValue();
        } else {
            return $this->originalContent;
        }
    }
}
