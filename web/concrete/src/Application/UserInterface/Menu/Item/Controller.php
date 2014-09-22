<?php
namespace Concrete\Core\Application\UserInterface\Menu\Item;

use Concrete\Core\Controller\AbstractController;
use HtmlObject\Element;
use HtmlObject\Link;

class Controller extends AbstractController
{

    public function displayItem()
    {
        return true;
    }

    /**
     * @return Link
     */
    public function getMenuItemLinkElement()
    {
        $a = new Link();
        $a->setValue('');
        if ($this->menuItem->getIcon()) {
            $icon = new Element('i');
            $icon->addClass('fa fa-' . $this->menuItem->getIcon());
            $a->appendChild($icon);
        }

        if ($this->menuItem->getLink()) {
            $a->href($this->menuItem->getLink());
        }

        foreach($this->menuItem->getLinkAttributes() as $key => $value) {
            $a->setAttribute($key, $value);
        }

        $label = new Element('span');
        $label->addClass('ccm-toolbar-accessibility-title')->setValue($this->menuItem->getLabel());
        $a->appendChild($label);
        return $a;
    }

    public function setMenuItem($obj)
    {
        $this->menuItem = $obj;
    }


}
