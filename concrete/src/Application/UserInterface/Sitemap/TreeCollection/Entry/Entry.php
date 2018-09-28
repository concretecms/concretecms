<?php
namespace Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry;

use Concrete\Core\Entity\Site\Locale;
use HtmlObject\Element;

abstract class Entry implements EntryInterface
{

    protected $isSelected = false;

    abstract public function getIconElement();

    /**
     * @return boolean
     */
    public function isSelected()
    {
        return $this->isSelected;
    }

    /**
     * @param boolean $isSelected
     */
    public function setIsSelected($isSelected)
    {
        $this->isSelected = $isSelected;
    }

    public function getOptionElement()
    {
        $element = new Element('div', null, ['class' => 'ccm-sitemap-tree-selector-option']);
        if ($this->getIconElement()) {
            $element->appendChild($this->getIconElement());
        }
        $element->appendChild(new Element('span', h($this->getLabel()), ['class' => 'ccm-sitemap-tree-menu-label']));
        return $element;
    }

}
