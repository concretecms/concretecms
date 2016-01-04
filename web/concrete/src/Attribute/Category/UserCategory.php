<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Type;
use Concrete\Core\Entity\User\Attribute;
use Symfony\Component\HttpFoundation\Request;

class UserCategory extends AbstractCategory implements StandardSearchIndexerInterface
{

    public function getIndexedSearchTable()
    {
        return 'UserSearchIndexAttributes';
    }

    public function getIndexedSearchPrimaryKeyValue($mixed)
    {
        return $mixed->getUserID();
    }


    public function getSearchIndexFieldDefinition()
    {
        return array(
            'columns' => array(
                array(
                    'name' => 'uID',
                    'type' => 'integer',
                    'options' => array('unsigned' => true, 'default' => 0, 'notnull' => true)
                )
            ),
            'primary' => array('uID')
        );
    }

    public function getAttributeRepository()
    {
        return $this->entityManager->getRepository('\Concrete\Core\Entity\User\Attribute');
    }

    public function getRegistrationList()
    {
        return $this->getAttributeRepository()->getRegistrationList();
    }

    /**
     * Takes an attribute key as created by the subroutine and assigns it to the category.
     * @param Key $key
     */
    protected function assignToCategory(Key $key, Attribute $attribute)
    {
        $this->entityManager->persist($key);
        $this->entityManager->flush();
        $attribute->setAttributeKey($key);
        $this->entityManager->persist($attribute);
        $this->entityManager->flush();
    }

    public function addFromRequest(Type $type, Request $request)
    {
        $key = parent::addFromRequest($type, $request);
        $attribute = new Attribute();
        $this->assignToCategory($key, $attribute);
    }

    public function import(Type $type, \SimpleXMLElement $element)
    {
        $key = parent::import($type, $element);
        $attribute = new Attribute();
        $attribute->setAttributeKeyDisplayedInProfile((string) $element['profile-displayed'] == 1);
        $attribute->setAttributeKeyEditableInProfile((string) $element['profile-editable'] == 1);
        $attribute->setAttributeKeyRequiredInProfile((string) $element['profile-required'] == 1);
        $attribute->setAttributeKeyEditableInRegistration((string) $element['register-editable'] == 1);
        $attribute->setAttributeKeyRequiredOnRegister((string) $element['register-required'] == 1);
        $attribute->setAttributeKeyDisplayedInMemberList((string) $element['member-list-displayed'] == 1);
        $this->assignToCategory($key, $attribute);
    }

    public function delete(Key $key)
    {
        parent::delete($key);
        $query = $this->entityManager->createQuery(
            'select a from Concrete\Core\Entity\User\Attribute a where a.attribute_key = :key'
        );
        $query->setParameter('key', $key);
        $attribute = $query->getSingleResult();
        if (is_object($attribute)) {
            $this->entityManager->remove($attribute);
            $this->entityManager->flush();
        }
    }

    public function getAttributeValues($user)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\User\AttributeValue');
        $values = $r->findBy(array(
            'uID' => $user->getUserID()
        ));
        return $values;
    }




}
