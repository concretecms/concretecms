<?php
namespace Concrete\Core\Export\Item\Express\Control;

use Concrete\Core\Export\Item\Express\Control;
use Concrete\Core\Export\ExportableInterface;

class AuthorControl extends Control
{

    /**
     * @param $control \Concrete\Core\Entity\Express\Control\TextControl
     * @param \SimpleXMLElement $xml
     */
    public function export($control, \SimpleXMLElement $xml)
    {
        $node = parent::export($control, $xml);
        $node->addAttribute('type-id', 'author');
    }

}
