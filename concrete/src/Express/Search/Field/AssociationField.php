<?php
namespace Concrete\Core\Express\Search\Field;

use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\ManyToOneAssociation;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\Form\Context\DashboardFormContext;
use Concrete\Core\Form\Context\Registry\ControlRegistry;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;

class AssociationField extends AbstractField
{

    /**
     * @var Association
     */
    protected $association;

    protected $associationID;

    /**
     * {@inheritdoc}
     *
     * @see FieldInterface::getKey()
     */
    public function getKey()
    {
        if ($this->association !== null) {
            return 'association_' . $this->association->getId();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see FieldInterface::getDisplayName()
     */
    public function getDisplayName()
    {
        if ($this->association !== null) {
            return $this->association->getTargetEntity()->getName();
        }
    }

    /**
     * Initialize the instance.
     */
    public function __construct(Association $association)
    {
        $this->association = $association;
        $this->associationID = $association->getId();
        $this->requestVariables[] = 'express_association_' . $association->getId();
    }

    protected function getSelectedEntry()
    {
        if (isset($this->data['express_association_' . $this->associationID])) {
            $selected = $this->data['express_association_' . $this->associationID];
            $objectManager = \Core::make('express');
            return $objectManager->getEntry($selected);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see FieldInterface::renderSearchField()
     */
    public function renderSearchField()
    {
        if ($this->association !== null) {
            $registry = \Core::make(ControlRegistry::class);
            $control = new AssociationControl();
            $control->setAssociation($this->association);
            $control->setId($this->association->getId());
            $context = new DashboardFormContext();
            // Is there an entity selected?
            if (isset($this->data['express_association_' . $this->associationID])) {
                $selected = $this->data['express_association_' . $this->associationID];
                // We have to spoof an entry in order for this to be selected. Kind of lame but oh well.

                if (is_array($selected)) {
                    $spoofedAssociation = new Entry\ManyAssociation();
                    foreach($selected as $id) {
                        $selectedEntry = new Entry();
                        $selectedEntry->setID($id);
                        $spoofedAssociation->getSelectedEntriesCollection()->add($selectedEntry);
                    }
                    $spoofedAssociation->setAssociation($this->association);
                } else {
                    $selectedEntry = new Entry();
                    $selectedEntry->setID($selected);
                    $spoofedAssociation = new Entry\OneAssociation();
                    $spoofedAssociation->setAssociation($this->association);
                    $spoofedAssociation->setSelectedEntry($selectedEntry);
                }

                $spoofedEntry = new Entry();
                $spoofedEntry->getAssociations()->add($spoofedAssociation);
                $context->setEntry($spoofedEntry);
            }

            $view = $registry->getControlView($context, 'express_control_association', [
                $control
            ]);
            $view->setSupportsLabel(false);
            $renderer = $view->getControlRenderer();
            return $renderer->render();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see FieldInterface::filterList()
     * @var $list EntryList
     */
    public function filterList(ItemList $list)
    {
        if ($this->association !== null && $this->getSelectedEntry()) {
            $list->filterByAssociatedEntry($this->association, $this->getSelectedEntry());
        }
    }

    /**
     * Return an array with the names of the properties to be serialized.
     *
     * @return string[]
     */
    public function __sleep()
    {
        return ['data', 'associationID'];
    }

    /**
     * Initialize the instance once it has been deserialized.
     */
    public function __wakeup()
    {
        $em = \Database::connection()->getEntityManager();
        $this->association = $em->find('Concrete\Core\Entity\Express\Association', $this->associationID);
    }
}
