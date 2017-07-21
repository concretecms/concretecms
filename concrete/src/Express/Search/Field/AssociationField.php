<?php
namespace Concrete\Core\Express\Search\Field;

use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Control\AssociationControl;
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
            $view = $registry->getControlView(new DashboardFormContext(), 'express_control_association', [
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
     */
    public function filterList(ItemList $list)
    {
        if ($this->association !== null) {
            /*
            $type = $this->attributeKey->getAttributeType();
            $cnt = $type->getController();
            $cnt->setRequestArray($this->data);
            $cnt->setAttributeKey($this->attributeKey);
            $cnt->searchForm($list);
            */
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
