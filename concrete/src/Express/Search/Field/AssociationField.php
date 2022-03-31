<?php
namespace Concrete\Core\Express\Search\Field;

use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\ManyToOneAssociation;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\Form\Context\DashboardFormContext;
use Concrete\Core\Form\Context\Registry\ControlRegistry;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

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
            return 'express_association_' . $this->association->getId();
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
    public function __construct(Association $association = null)
    {
        if ($association) {
            $this->loadAssociation($association);
        }
    }

    protected function loadAssociation(Association $association)
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
            $form = \Core::make(Form::class);
            $name = 'express_association_' . $this->associationID;
            $list = new EntryList($this->association->getTargetEntity());
            $results = $list->getresults();
            $entries = ['' => t('** Select an entry')];
            foreach($results as $result) {
                $entries[$result->getId()] = $result->getLabel();
            }
            return $form->select($name, $entries, $this->getData('express_association_' . $this->associationID));
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
     * @return array|mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $json = parent::jsonSerialize();
        $json['associationID'] = $this->association->getId();
        return $json;
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        $em = \Database::connection()->getEntityManager();
        $association = $em->find('Concrete\Core\Entity\Express\Association', $data['associationID']);
        $this->loadAssociation($association);
        parent::denormalize($denormalizer, $data, $format, $context);

    }

    /**
     * Initialize the instance once it has been deserialized.
     */
    public function __wakeup()
    {
        $em = \Database::connection()->getEntityManager();
        $association = $em->find('Concrete\Core\Entity\Express\Association', $this->associationID);
        if ($association) {
            $this->loadAssociation($association);
        }
    }
}
