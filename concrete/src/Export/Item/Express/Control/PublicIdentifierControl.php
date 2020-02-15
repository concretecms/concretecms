<?php
namespace Concrete\Core\Export\Item\Express\Control;

use Concrete\Core\Export\Item\Express\Control;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Utility\Service\Xml;

class PublicIdentifierControl extends Control
{

    /**
     * @param $control \Concrete\Core\Entity\Express\Control\PublicIdentifierControl
     * @param \SimpleXMLElement $xml
     */
    public function export($control, \SimpleXMLElement $xml)
    {
        $node = parent::export($control, $xml);
        $node->addAttribute('type-id', 'public-identifier');
    }

}
