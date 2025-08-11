<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector;

use Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\RoutineInterface;
use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface;

class Result implements ResultInterface
{
    /**
     * The original content being inspected.
     *
     * @var string|mixed
     */
    protected $originalContent;

    /**
     * The content with values replaced by the registered inspection routines.
     *
     * @var string|mixed
     */
    protected $replacedContent;

    /**
     * The items matched by the registered inspection routines.
     *
     * @var \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface[]
     */
    protected $items = [];

    /**
     * The registered inspection routines.
     *
     * @var \Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\RoutineInterface[]
     */
    protected $routines = [];

    /**
     * @param string|mixed $originalContent The original content being inspected
     */
    public function __construct($originalContent)
    {
        $this->originalContent = $originalContent;
    }

    /**
     * Register an inspection routing.
     */
    public function addInspectionRoutine(RoutineInterface $routine)
    {
        $this->routines[$routine->getHandle()] = $routine;
    }

    /**
     * Get the original content being inspected.
     *
     * @var string|mixed
     */
    public function getOriginalContent()
    {
        return $this->originalContent;
    }

    /**
     * Set the original content being inspected.
     *
     * @param string|mixed $originalContent
     */
    public function setOriginalContent($originalContent)
    {
        $this->originalContent = $originalContent;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\ResultInterface::getReplacedContent()
     */
    public function getReplacedContent()
    {
        if ($this->replacedContent === null) {
            $replacedContent = $this->getOriginalContent();
            foreach ($this->routines as $routine) {
                $replacedContent = $routine->replaceContent($replacedContent);
            }
            $this->replacedContent = $replacedContent;
        }

        return $this->replacedContent;
    }

    /**
     * Add an item matched by the registered inspection routines.
     *
     * @param \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface $item
     */
    public function addMatchedItem(ItemInterface $item)
    {
        $this->items[] = $item;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\ResultInterface::getMatchedItems()
     */
    public function getMatchedItems()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\ResultInterface::getReplacedValue()
     */
    public function getReplacedValue()
    {
        return isset($this->items[0]) ? $this->items[0]->getFieldValue() : $this->getOriginalContent();
    }
}
