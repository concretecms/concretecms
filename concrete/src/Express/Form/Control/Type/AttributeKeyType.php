<?php
namespace Concrete\Core\Express\Form\Control\Type;

use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\Form\Control\Type\Item\AttributeKeyItem;
use Concrete\Core\Express\Form\Control\Validator\AttributeKeyControlValidator;
use Doctrine\ORM\EntityManager;

class AttributeKeyType implements TypeInterface
{
    protected $entityManager;

    public function getType()
    {
        return 'attribute_key';
    }

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getValidator()
    {
        return new AttributeKeyControlValidator();
    }

    public function getPluralDisplayName()
    {
        return t('Attributes');
    }

    public function getDisplayName()
    {
        return t('Attribute Key');
    }

    public function getItems(Entity $entity)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Key\ExpressKey');
        $keys = $r->findByEntity($entity, array('akID' => 'asc'));
        $items = array();
        foreach ($keys as $key) {
            $item = new AttributeKeyItem($key);
            $items[] = $item;
        }

        return $items;
    }

    public function createControlByIdentifier($id)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Key\Key');
        $key = $r->findOneBy(array('akID' => $id));
        $control = new AttributeKeyControl();
        $control->setAttributeKey($key);

        return $control;
    }

    public function getSaveHandler(Control $control)
    {
        return \Core::make('\Concrete\Core\Express\Form\Control\SaveHandler\AttributeKeySaveHandler');
    }

    public function getImporter()
    {
        return \Core::make('\Concrete\Core\Import\Item\Express\Control\AttributeKeyControl');
    }

}
