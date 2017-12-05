<?php
namespace Concrete\Core\Export\Item\Express;

use Concrete\Core\Entity\Express\Entry as ExpressEntry;
use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Utility\Service\Identifier;

defined('C5_EXECUTE') or die("Access Denied.");

class Entry implements ItemInterface
{

    protected $identifier;

    public function __construct(Identifier $identifier)
    {
        $this->identifier = $identifier;
    }

    static $entryIDs = array();

    protected function convertNumericEntryIdToIdentifier($id)
    {
        if (isset(self::$entryIDs[$id])) {
            return self::$entryIDs[$id];
        } else {
            $identifier = $this->identifier->getString(12);
            self::$entryIDs[$id] = $identifier;
            return $identifier;
        }
    }

    /**
     * @param $entry ExpressEntry
     * @param \SimpleXMLElement $xml
     */
    public function export($entry, \SimpleXMLElement $xml)
    {
        $node = $xml->addChild('entry');
        $node->addAttribute('id', $this->convertNumericEntryIdToIdentifier($entry->getId()));
        $node->addAttribute('entity', $entry->getEntity()->getHandle());
        $node->addAttribute('display-order', $entry->getEntryDisplayOrder());
        $attribs = $entry->getAttributes();

        if (count($attribs) > 0) {
            $attributes = $node->addChild('attributes');
            foreach ($attribs as $av) {
                $cnt = $av->getController();
                $cnt->setAttributeValue($av);
                $akx = $attributes->addChild('attributekey');
                $akx->addAttribute('handle', $av->getAttributeKey()->getAttributeKeyHandle());
                $cnt->exportValue($akx);
            }
        }

        $associations = $entry->getAssociations();
        if (count($associations)) {
            $associationsNode = $node->addChild('associations');
            foreach($associations as $association) {
                $child = $associationsNode->addChild('association');
                $child->addAttribute('target', $association->getAssociation()->getComputedTargetPropertyName());
                $associationEntriesNode = $child->addChild('entries');
                foreach($association->getSelectedEntries() as $associationEntry) {
                    $id = $this->convertNumericEntryIdToIdentifier($associationEntry->getId());
                    $associationEntryNode = $associationEntriesNode->addChild('entry');
                    $associationEntryNode->addAttribute('entry', $id);
                }
            }
        }

        /*
        $node->addAttribute('id', $entity->getID());
        $node->addAttribute('handle', $entity->getHandle());
        $node->addAttribute('plural_handle', $entity->getPluralHandle());
        $node->addAttribute('name', $entity->getName());
        $node->addAttribute('include_in_public_list', $entity->getIncludeInPublicList() ? '1' : '');
        $node->addAttribute('description', h($entity->getDescription()));
        $node->addAttribute('default_view_form', $entity->getDefaultViewForm()->getID());
        $node->addAttribute('default_edit_form', $entity->getDefaultEditForm()->getID());

        $results = Node::getByID($entity->getEntityResultsNodeId());
        if (is_object($results)) {
            $parent = $results->getTreeNodeParentObject();
            $path = $parent->getTreeNodeDisplayPath();
            $node->addAttribute('results-folder', $path);
        }

        $associations = $entity->getAssociations();
        if (count($associations)) {
            $asn = $node->addChild('associations');
            foreach($associations as $association) {
                $exporter = $association->getExporter();
                $exporter->export($association, $asn);
            }
        }

        $forms = $entity->getForms();
        if (count($forms)) {
            $fsn = $node->addChild('forms');
            foreach($forms as $form) {
                $exporter = $form->getExporter();
                $exporter->export($form, $fsn);
            }
        }
        */
        return $node;

    }

}
