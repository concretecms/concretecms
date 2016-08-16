<?php
namespace Concrete\Core\Export\Item\Express;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\Export\ExportableInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class Association implements ItemInterface
{

    /**
     * @param $association \Concrete\Core\Entity\Express\Association
     * @param \SimpleXMLElement $xml
     */
    public function export(ExportableInterface $association, \SimpleXMLElement $xml)
    {
        $node = $xml->addChild('association');

        // turns fully qualified class name into one_to_many
        $class = substr(get_class($association), strrpos(get_class($association), '\\') + 1);
        $type = uncamelcase(substr($class, 0, strpos($class, 'Association')));

        $node->addAttribute('type', $type);
        $node->addAttribute('source_entity', $association->getSourceEntity()->getID());
        $node->addAttribute('target_entity', $association->getTargetEntity()->getID());
        $node->addAttribute('target_property_name', $association->getTargetPropertyName());
        $node->addAttribute('inversed_by_property_name', $association->getInversedByPropertyName());

        return $node;

    }

}
