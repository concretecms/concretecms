<?php
namespace Concrete\Core\Application\UserInterface\Menu\Item;

use Concrete\Core\Controller\AbstractController;

class Controller extends AbstractController
{

    public function displayItem() {
        return true;
    }

    public function getMenuLinkHTML() {
        $attribs = '';
        if (is_array($this->menuItem->getLinkAttributes())) {
            foreach($this->menuItem->getLinkAttributes() as $key => $value) {
                if ($key == 'class') {
                    $value = 'ccm-header-nav-package-item ' . $value;
                }
                $attribs .= $key . '="' . $value . '" ';
            }
        }
        $html = '<a id="ccm-page-edit-nav-' . $this->menuItem->getHandle() . '" ' . $attribs . '>' . $this->menuItem->getName() . '</a>';
        return $html;
    }

    public function setMenuItem($obj) {
        $this->menuItem = $obj;
    }


}
