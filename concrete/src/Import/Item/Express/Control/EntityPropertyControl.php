<?php
namespace Concrete\Core\Import\Item\Express\Control;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Import\Item\Express\ItemInterface;
use Concrete\Core\Utility\Service\Xml;

defined('C5_EXECUTE') or die("Access Denied.");

class EntityPropertyControl implements ItemInterface
{

    public function import(\SimpleXMLElement $xml, Entity $entity)
    {
        $xmlService = app(Xml::class);
        switch((string) $xml['type-id']) {
            case 'text':
            default:
                $control = new \Concrete\Core\Entity\Express\Control\TextControl();
                $control->setHeadline((string) $xml->headline);
                $control->setBody((string) $xml->body);
                break;
        }

        $control->setCustomLabel((string) $xml['custom-label']);
        $control->setIsRequired($xmlService->getBool($xml['required']));
        $control->setId((string) $xml['id']);
        return $control;
    }

}
