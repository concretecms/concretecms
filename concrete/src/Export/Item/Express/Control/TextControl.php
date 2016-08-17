<?php
namespace Concrete\Core\Export\Item\Express\Control;

use Concrete\Core\Export\Item\Express\Control;
use Concrete\Core\Export\ExportableInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class TextControl extends Control
{

    /**
     * @param $control \Concrete\Core\Entity\Express\Control\TextControl
     * @param \SimpleXMLElement $xml
     */
    public function export(ExportableInterface $control, \SimpleXMLElement $xml)
    {
        $node = parent::export($control, $xml);
        $node->addAttribute('text', $control->getText());

    }

}
