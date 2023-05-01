<?php

namespace Concrete\Core\Tree\Type;

use Concrete\Core\Tree\Tree;
use SimpleXMLElement;

class Menu extends Tree implements MenuInterface
{

    /**
     * Returns the standard name for this tree.
     *
     * @return string
     */
    public function getTreeName()
    {
        return 'Menu';
    }

    /**
     * Returns the display name for this tree (localized and escaped accordingly to $format).
     *
     * @param  string $format = 'html' Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display name won't be escaped.
     *
     * @return string
     */
    public function getTreeDisplayName($format = 'html')
    {
        $value = tc('TreeName', 'Menu');
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    public function loadDetails()
    {
        return false;
    }

    public function deleteDetails()
    {
        return false;
    }

    public function exportDetails(SimpleXMLElement $sx)
    {
        return false;
    }

}
