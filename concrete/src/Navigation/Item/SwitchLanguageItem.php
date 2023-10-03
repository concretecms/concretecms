<?php

namespace Concrete\Core\Navigation\Item;


use Concrete\Core\Multilingual\Page\Section\Section;

class SwitchLanguageItem extends Item
{
    protected $sectionID;

    public function __construct(Section $section = null, string $url, bool $isActive = false)
    {
        if ($section) {
            $this->sectionID = $section->getCollectionID();
            $locale = $section->getLocaleObject();
            parent::__construct($url, $locale->getLanguageText($locale->getLocale()), $isActive);
        }
    }

    public function getSectionID(): ?int
    {
        return $this->sectionID;
    }

    public function setSectionID(int $sectionID): void
    {
        $this->sectionID = $sectionID;
    }
}