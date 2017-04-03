<?php
namespace Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry;

use Concrete\Core\Entity\Site\Locale;
use HtmlObject\Element;

abstract class Entry implements EntryInterface
{

    abstract public function getLabel();
    abstract public function getIconElement();

    public function getOptionElement()
    {
        $element = new Element('div');
        if ($this->getIconElement()) {
            $element->appendChild($this->getIconElement());
        }
        $element->appendChild(new Element('div', $this->getLabel(), ['class' => 'ccm-sitemap-tree-menu-label']));
        return $element;
    }

}
