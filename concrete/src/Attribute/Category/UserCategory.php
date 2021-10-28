<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Key\UserKey;
use Concrete\Core\Entity\Attribute\Type;
use Concrete\Core\Entity\Package;
use Concrete\Core\User\UserInfo;
use Symfony\Component\HttpFoundation\Request;

class UserCategory extends AbstractStandardCategory
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::createAttributeKey()
     *
     * @return \Concrete\Core\Entity\Attribute\Key\UserKey
     */
    public function createAttributeKey()
    {
        return new UserKey();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface::getIndexedSearchTable()
     */
    public function getIndexedSearchTable()
    {
        return 'UserSearchIndexAttributes';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface::getIndexedSearchPrimaryKeyValue()
     *
     * @param \Concrete\Core\Entity\User\User|\Concrete\Core\User\User|\Concrete\Core\User\UserInfo $mixed
     *
     * @return int
     */
    public function getIndexedSearchPrimaryKeyValue($mixed)
    {
        return $mixed->getUserID();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface::getSearchIndexFieldDefinition()
     */
    public function getSearchIndexFieldDefinition()
    {
        return [
            'columns' => [
                [
                    'name' => 'uID',
                    'type' => 'integer',
                    'options' => ['unsigned' => true, 'notnull' => true],
                ],
            ],
            'primary' => ['uID'],
            'foreignKeys' => [
                [
                    'foreignTable' => 'Users',
                    'localColumns' => ['uID'],
                    'foreignColumns' => ['uID'],
                    'onUpdate' => 'CASCADE',
                    'onDelete' => 'CASCADE',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::getAttributeKeyRepository()
     *
     * @return \Concrete\Core\Entity\User\AttributeRepository
     */
    public function getAttributeKeyRepository()
    {
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Key\UserKey');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::getAttributeValueRepository()
     */
    public function getAttributeValueRepository()
    {
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Value\UserValue');
    }

    /**
     * Get the attribute keys to be displayed in the member list page.
     *
     * @return \Concrete\Core\Entity\Attribute\Key\UserKey[]
     */
    public function getMemberListList()
    {
        return $this->getAttributeKeyRepository()->getMemberListList();
    }

    /**
     * Get the attribute keys to be displayed in the public profile page.
     *
     * @return \Concrete\Core\Entity\Attribute\Key\UserKey[]
     */
    public function getPublicProfileList()
    {
        return $this->getAttributeKeyRepository()->getPublicProfileList();
    }

    /**
     * Get the attribute keys to be displayed in the users' registration page.
     *
     * @return \Concrete\Core\Entity\Attribute\Key\UserKey[]
     */
    public function getRegistrationList()
    {
        return $this->getAttributeKeyRepository()->getRegistrationList();
    }

    /**
     * Get the attribute keys that can be modified in the user profile page.
     *
     * @return \Concrete\Core\Entity\Attribute\Key\UserKey[]
     */
    public function getEditableInProfileList()
    {
        return $this->getAttributeKeyRepository()->getEditableInProfileList();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::addFromRequest()
     */
    public function addFromRequest(Type $type, Request $request)
    {
        $key = parent::addFromRequest($type, $request);

        return $this->saveFromRequest($key, $request);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::updateFromRequest()
     *
     * @param \Concrete\Core\Entity\Attribute\Key\UserKey $key
     *
     * @return \Concrete\Core\Entity\Attribute\Key\UserKey
     */
    public function updateFromRequest(Key $key, Request $request)
    {
        $key = parent::updateFromRequest($key, $request);

        return $this->saveFromRequest($key, $request);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::import()
     *
     * @return \Concrete\Core\Entity\Attribute\Key\UserKey
     */
    public function import(Type $type, \SimpleXMLElement $element, Package $package = null)
    {
        $key = parent::import($type, $element, $package);
        $key->setAttributeKeyDisplayedOnProfile((string) $element['profile-displayed'] == 1);
        $key->setAttributeKeyEditableOnProfile((string) $element['profile-editable'] == 1);
        $key->setAttributeKeyRequiredOnProfile((string) $element['profile-required'] == 1);
        $key->setAttributeKeyEditableOnRegister((string) $element['register-editable'] == 1);
        $key->setAttributeKeyRequiredOnRegister((string) $element['register-required'] == 1);
        $key->setAttributeKeyDisplayedOnMemberList((string) $element['member-list-displayed'] == 1);
        // Save these settings to the database
        $this->entityManager->flush();

        return $key;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\CategoryInterface::getAttributeValues()
     *
     * @param \Concrete\Core\Entity\User\User|UserInfo $user
     *
     * @return \Concrete\Core\Entity\Attribute\Value\UserValue[]
     */
    public function getAttributeValues($user)
    {
        if ($user instanceof UserInfo) {
            $user = $user->getEntityObject();
        }
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Value\UserValue');
        $values = $r->findBy([
            'user' => $user,
        ]);

        return $values;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\CategoryInterface::getAttributeValue()
     *
     * @param \Concrete\Core\Entity\Attribute\Key\UserKey $key
     * @param \Concrete\Core\Entity\User\User|UserInfo $user
     *
     * @return \Concrete\Core\Entity\Attribute\Value\UserValue|null
     */
    public function getAttributeValue(Key $key, $user)
    {
        if ($user instanceof UserInfo) {
            $user = $user->getEntityObject();
        }
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Value\UserValue');
        $value = $r->findOneBy([
            'user' => $user,
            'attribute_key' => $key,
        ]);

        return $value;
    }

    /**
     * Update the user attribute key with the data received from POST.
     *
     * @param \Concrete\Core\Entity\Attribute\Key\UserKey $key The user attribute key to be updated
     * @param \Symfony\Component\HttpFoundation\Request $request The request containing the posted data
     *
     * @return \Concrete\Core\Entity\Attribute\Key\UserKey
     */
    protected function saveFromRequest(Key $key, Request $request)
    {
        $key->setAttributeKeyDisplayedOnProfile((string) $request->request->get('uakProfileDisplay') == 1);
        $key->setAttributeKeyEditableOnProfile((string) $request->request->get('uakProfileEdit') == 1);
        $key->setAttributeKeyRequiredOnProfile((string) $request->request->get('uakProfileEditRequired') == 1);
        $key->setAttributeKeyEditableOnRegister((string) $request->request->get('uakRegisterEdit') == 1);
        $key->setAttributeKeyRequiredOnRegister((string) $request->request->get('uakRegisterEditRequired') == 1);
        $key->setAttributeKeyDisplayedOnMemberList((string) $request->request->get('uakMemberListDisplay') == 1);
        // Actually save the changes to the database
        $this->entityManager->flush();
        return $key;
    }
}
