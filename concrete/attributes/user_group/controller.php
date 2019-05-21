<?php
namespace Concrete\Attribute\UserGroup;

use Concrete\Core\Attribute\Controller as AttributeTypeController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Entity\Attribute\Key\Settings\UserGroupSettings;
use Concrete\Core\Entity\Attribute\Value\Value\NumberValue;
use Concrete\Core\Error\ErrorList\Error\Error;
use Concrete\Core\Error\ErrorList\Error\FieldNotPresentError;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\ErrorList\Field\AttributeField;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\GroupSelector;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\GroupList;
use Concrete\Core\User\User;
use SimpleXMLElement;

class Controller extends AttributeTypeController
{

    protected $akAllowSelectionFromMyGroupsOnly = false;
    protected $akDisplayGroupsBeneathSpecificParent = false;
    protected $akDisplayGroupsBeneathParentID = 0;

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('users');
    }

    public function form()
    {
        $this->loadSettings();
        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->getAttributeValue()->getValue();
        }
        if (!$value) {
            if ($this->request->query->has($this->attributeKey->getAttributeKeyHandle())) {
                $value = $this->createAttributeValue((int)$this->request->query->get($this->attributeKey->getAttributeKeyHandle()));
            }
        }

        $groupList = new GroupList();
        if ($this->akDisplayGroupsBeneathSpecificParent) {
            $parent = Group::getByID($this->akDisplayGroupsBeneathParentID);
            if ($parent) {
                $groupList->filterByParentGroup($parent);
            }
        }
        if ($this->akAllowSelectionFromMyGroupsOnly) {
            $u = $this->app->make(User::class);
            if (!$u->isSuperUser()) {
                $groupList->filterByHavingMembership();
            }
        }
        $groupSelector = $this->app->make(GroupSelector::class, ['groupList' => $groupList]);
        $this->set('value', $value);
        $this->set('selector', $groupSelector);
    }

    public function getAttributeValueClass()
    {
        return NumberValue::class;
    }

    public function saveKey($data)
    {
        /**
         * @var $type UserGroupSettings
         */
        $type = $this->getAttributeKeySettings();
        $type->setAllowSelectionFromMyGroupsOnly(intval($data['akAllowSelectionFromMyGroupsOnly']) > 0 ? true : false);
        $type->setDisplayGroupsBeneathSpecificParent(intval($data['akDisplayGroupsBeneathSpecificParent']) > 0 ? true : false);
        if ($type->displayGroupsBeneathSpecificParent()) {
            $widget = $this->app->make(GroupSelector::class);
            $group = $widget->getGroupFromGroupTreeRequestValue(intval($data['akDisplayGroupsBeneathParentID']));
            if ($group) {
                $type->setDisplayGroupsBeneathParentID($group->getGroupID());
            }
        } else {
            $type->setDisplayGroupsBeneathParentID(0);
        }
        return $type;
    }


    public function getDisplayValue()
    {
        /** @var \Concrete\Core\User\Group\Group $group */
        $group = $this->getGroup($this->getAttributeValue()->getValue());
        if ($group) {
            return $group->getGroupDisplayName();
        }
        return t('None');
    }

    public function getPlainTextValue()
    {
        /** @var \Concrete\Core\User\Group\Group $group */
        $group = $this->getGroup($this->getAttributeValue()->getValue());
        if ($group) {
            return $group->getGroupName();
        }
        return '';
    }

    /**
     * @return Group
     */
    public function getValue()
    {
        $group = $this->getGroup($this->getAttributeValue()->getValueObject()->getValue());
        return $group;
    }

    public function createAttributeValue($value)
    {
        $av = new NumberValue();
        if ($value instanceof Group) {
            $value = $value->getGroupID();
        }
        $av->setValue($value);

        return $av;
    }

    public function createAttributeValueFromRequest()
    {
        $data = $this->post();
        if (isset($data['value'])) {
            return $this->createAttributeValue((int) $data['value']);
        }
    }

    public function importValue(\SimpleXMLElement $akv)
    {
        if (isset($akv->value)) {
            $group = Group::getByPath($akv->value);
            if (is_object($group)) {
                return $group->getGroupID();
            }
        }
    }

    protected function loadSettings()
    {
        /**
         * @var $settings UserGroupSettings
         */
        $ak = $this->getAttributeKey();
        $settings = $ak->getAttributeKeySettings();
        if ($settings) {
            $this->akAllowSelectionFromMyGroupsOnly = $settings->allowSelectionFromMyGroupsOnly();
            $this->akDisplayGroupsBeneathSpecificParent = $settings->displayGroupsBeneathSpecificParent();
            $this->akDisplayGroupsBeneathParentID = $settings->getDisplayGroupsBeneathParentID();
        }
    }

    public function type_form()
    {
        $this->loadSettings();
        $this->set('akAllowSelectionFromMyGroupsOnly', $this->akAllowSelectionFromMyGroupsOnly);
        $this->set('akDisplayGroupsBeneathSpecificParent', $this->akDisplayGroupsBeneathSpecificParent);
        $this->set('akDisplayGroupsBeneathParentID', $this->akDisplayGroupsBeneathParentID);
        $this->set('form', $this->app->make(Form::class));
        $this->set('groupSelector', $this->app->make(GroupSelector::class));
    }

    public function exportValue(\SimpleXMLElement $akn)
    {
        if (is_object($this->attributeValue)) {
            $gID = $this->getAttributeValue()->getValue();
            $group = Group::getByID($gID);
            $akn->addChild('value', $group->getGroupPath());
        }
    }

    public function exportKey($akey)
    {
        $this->loadSettings();
        $type = $akey->addChild('type');
        $type->addAttribute('force-selection-from-my-groups', $this->akAllowSelectionFromMyGroupsOnly ? "1" : "");
        $type->addAttribute('display-groups-beneath-specific-parent',
            $this->akDisplayGroupsBeneathSpecificParent ? "1" : "");
        if ($this->akDisplayGroupsBeneathSpecificParent) {
            $parent = Group::getByID($this->akDisplayGroupsBeneathParentID);
            if ($parent) {
                $type->addAttribute('display-groups-parent-group', $parent->getGroupPath());
            }
        }

        return $akey;
    }

    public function validateForm($data)
    {
        $this->loadSettings();
        $selectedGroup = null;
        if (isset($data['value'])) {
            $selectedGroup = Group::getByID(intval($data['value']));
        }
        if ($selectedGroup) {
            $errorList = new ErrorList();
            if ($this->akDisplayGroupsBeneathSpecificParent) {
                $allowedParent = Group::getByID($this->akDisplayGroupsBeneathParentID);
                if ($allowedParent) {
                    $parentIDs = [];
                    foreach($selectedGroup->getParentGroups() as $parentGroup) {
                        $parentIDs[] = $parentGroup->getGroupID();
                    }
                    if (!in_array($allowedParent->getGroupID(), $parentIDs)) {
                        $errorList->add(t('You must select a sub-group found in the %s group',
                            $allowedParent->getGroupPath()));
                    }
                }
            }
            if ($this->akAllowSelectionFromMyGroupsOnly) {
                $u = $this->app->make(User::class);
                if (!$u->isSuperUser()) {
                    if (!$u->inGroup($selectedGroup)) {
                        $errorList->add(t('You must be a member of the group %s to add a customer to it.',
                            $selectedGroup->getGroupPath()));
                    }
                }
            }
            return $errorList;
        } else {
            return new FieldNotPresentError(new AttributeField($this->getAttributeKey()));
        }
    }


    public function importKey(\SimpleXMLElement $key)
    {
        $settings = $this->getAttributeKeySettings();
        /**
         * @var $settings UserGroupSettings
         */
        if (isset($key->type)) {
            $akAllowSelectionFromMyGroupsOnly = (string) $key->type['force-selection-from-my-groups'] == '1'
                ? true : false;
            $akDisplayGroupsBeneathSpecificParent = (string) $key->type['display-groups-beneath-specific-parent'] == '1'
                ? true : false;
            $settings->setAllowSelectionFromMyGroupsOnly($akAllowSelectionFromMyGroupsOnly);
            $settings->setDisplayGroupsBeneathSpecificParent($akDisplayGroupsBeneathSpecificParent);
            if ($akDisplayGroupsBeneathSpecificParent) {
                $parentGroupPath = (string) $key->type['display-groups-parent-group'];
                if ($parentGroupPath) {
                    $parentGroup = Group::getByPath($parentGroupPath);
                    if ($parentGroup) {
                        $settings->setDisplayGroupsBeneathParentID($parentGroup->getGroupID());
                    }
                }
            }
        }

        return $settings;
    }


    public function getAttributeKeySettingsClass()
    {
        return UserGroupSettings::class;
    }

    private function getGroup($id)
    {
        $group = Group::getByID($id);

        if (!is_object($group)) {
            //TODO Return group not found error/exception?
            return null;
        }

        return $group;
    }

}
