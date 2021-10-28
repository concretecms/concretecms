<?php

namespace Concrete\Core\Board\Instance\Slot\Planner;

use Concrete\Core\Board\Instance\Slot\Content\ItemObjectGroup;
use Concrete\Core\Board\Instance\Slot\Content\ObjectCollection;
use Concrete\Core\Design\Tag\ProvidesTagsInterface;
use Concrete\Core\Design\Tag\Tag;
use Concrete\Core\Design\Tag\TagCollection;
use Concrete\Core\Entity\Board\SlotTemplate;

/**
 * Joins content objects to a template and slot number. A simple object that we can use to store values BEFORE
 * we actually populate block content and save items to the database.
 */
class PlannedSlot implements ProvidesTagsInterface
{

    /**
     * @var int
     */
    protected $slot = 0;

    /**
     * @var PlannedSlotTemplate
     */
    protected $template;

    /**
     * @return int
     */
    public function getSlot(): int
    {
        return $this->slot;
    }

    /**
     * @param int $slot
     */
    public function setSlot(int $slot): void
    {
        $this->slot = $slot;
    }

    /**
     * @return PlannedSlotTemplate
     */
    public function getTemplate(): PlannedSlotTemplate
    {
        return $this->template;
    }

    /**
     * @param PlannedSlotTemplate $template
     */
    public function setTemplate(PlannedSlotTemplate $template): void
    {
        $this->template = $template;
    }

    public function getDesignTags(): array
    {
        $return = [];
        $slotTemplateTags = $this->getTemplate()->getSlotTemplate()->getTags();
        foreach($slotTemplateTags as $slotTemplateTag) {
            $return[] = $slotTemplateTag;
        }
        $contentObjectCollection = $this->getTemplate()->getObjectCollection();
        $contentObjects = $contentObjectCollection->getContentObjects();
        foreach($contentObjects as $contentObject) {
            $tags = $contentObject->getDesignTags();
            foreach($tags as $tag) {
                $return[] = $tag;
            }
        }
        return $return;
    }


}

