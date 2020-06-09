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
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\GroupList;
use Concrete\Core\User\User;

class Controller extends AttributeTypeController
{
    protected $akGroupSelectionMethod = false;
    protected $akDisplayGroupsBeneathSpecificParent = false;
    protected $akDisplayGroupsBeneathParentID = 0;

    protected $searchIndexFieldDefinition = [
        'type' => 'integer',
        'options' => ['default' => 0, 'notnull' => false],
    ];

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('users');
    }

    public function getAssignableGroups()
    {
        $groupList = new GroupList();
        if ($this->akDisplayGroupsBeneathSpecificParent) {
            $parent = Group::getByID($this->akDisplayGroupsBeneathParentID);
            if ($parent) {
                $groupList->filterByParentGroup($parent);
            }
        }
        $groups = $groupList->getResults();
        $u = $this->app->make(User::class);
        $filteredList = [];
        if ($this->akGroupSelectionMethod == UserGroupSettings::GROUP_SELECTION_METHOD_ALL) {
            $filteredList = $groups;
        } else {
            $akGroupSelectionMethod = str_split((string) $this->akGroupSelectionMethod);
            foreach ($groups as $g) {
                if (in_array(UserGroupSettings::GROUP_SELECTION_METHOD_IN_GROUP, $akGroupSelectionMethod)) {
                    if ($u->inGroup($g) && !in_array($g, $filteredList)) {
                        $filteredList[] = $g;
                    }
                }
                if (in_array(UserGroupSettings::GROUP_SELECTION_METHOD_PERMISSIONS, $akGroupSelectionMethod)) {
                    $permissions = new Checker($g);
                    if ($permissions->canAssignGroup($g) && !in_array($g, $filteredList)) {
                        $filteredList[] = $g;
                    }
                }
            }
        }

        return $filteredList;
    }

    public function form()
    {
        $this->loadSettings();
        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->getAttributeValue()->getValueObject();
        }
        if (!$value) {
            if ($this->request->query->has($this->attributeKey->getAttributeKeyHandle())) {
                $value = $this->createAttributeValue((int) $this->request->query->get($this->attributeKey->getAttributeKeyHandle()));
            }
        }

        $this->set('value', $value);
        $this->set('groups', $this->getAssignableGroups());
        $this->set('form', $this->app->make('helper/form'));
    }

    public function getAttributeValueClass()
    {
        return NumberValue::class;
    }

    public function saveKey($data)
    {
        /**
         * @var UserGroupSettings
         */
        $type = $this->getAttributeKeySettings();
        $akGroupSelectionMethod = UserGroupSettings::GROUP_SELECTION_METHOD_ALL;
        if (isset($data['akGroupSelectionMethodType']) && $data['akGroupSelectionMethodType'] == 'custom') {
            if (isset($data['akGroupSelectionMethod']) && is_array($data['akGroupSelectionMethod'])) {
                $akGroupSelectionMethod = implode('', $data['akGroupSelectionMethod']);
            }
        }
        $type->setGroupSelectionMethod($akGroupSelectionMethod);
        $type->setDisplayGroupsBeneathSpecificParent((int) ($data['akDisplayGroupsBeneathSpecificParent']) > 0 ? true : false);
        if ($type->displayGroupsBeneathSpecificParent()) {
            $widget = $this->app->make(GroupSelector::class);
            $group = $widget->getGroupFromGroupTreeRequestValue((int) ($data['akDisplayGroupsBeneathParentID']));
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
        $group = $this->getAttributeValue()->getValue();
        if ($group) {
            return $group->getGroupDisplayName();
        }

        return t('None');
    }

    public function getPlainTextValue()
    {
        /** @var \Concrete\Core\User\Group\Group $group */
        $group = $this->getAttributeValue()->getValue();
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

    public function type_form()
    {
        $this->loadSettings();
        $this->set('akDisplayGroupsBeneathSpecificParent', $this->akDisplayGroupsBeneathSpecificParent);
        $this->set('akDisplayGroupsBeneathParentID', $this->akDisplayGroupsBeneathParentID);
        $this->set('form', $this->app->make(Form::class));
        $this->set('groupSelector', $this->app->make(GroupSelector::class));

        $akGroupSelectionMethodInGroup = false;
        $akGroupSelectionMethodPermissions = false;

        if ($this->akGroupSelectionMethod == UserGroupSettings::GROUP_SELECTION_METHOD_ALL) {
            $akGroupSelectionMethodType = 'all';
        } else {
            $akGroupSelectionMethodType = 'custom';
            $selectionMethod = str_split($this->akGroupSelectionMethod);
            if (in_array(UserGroupSettings::GROUP_SELECTION_METHOD_IN_GROUP, $selectionMethod)) {
                $akGroupSelectionMethodInGroup = true;
            }
            if (in_array(UserGroupSettings::GROUP_SELECTION_METHOD_PERMISSIONS, $selectionMethod)) {
                $akGroupSelectionMethodPermissions = true;
            }
        }
        $this->set('akGroupSelectionMethodType', $akGroupSelectionMethodType);
        $this->set('akGroupSelectionMethodInGroup', $akGroupSelectionMethodInGroup);
        $this->set('akGroupSelectionMethodPermissions', $akGroupSelectionMethodPermissions);
    }

    public function exportValue(\SimpleXMLElement $akn)
    {
        if (is_object($this->attributeValue)) {
            $group = $this->getAttributeValue()->getValue();
            if ($group) {
                $akn->addChild('value', $group->getGroupPath());
            }
        }
    }

    public function searchForm($list)
    {
        $gID = $this->request('gID');
        if ($gID && is_scalar($gID)) {
            $group = Group::getByID($gID);
            if ($group) {
                $list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), $group->getGroupID(), '=');
            }
        }
    }

    public function search()
    {
        $gl = new GroupList();
        $g1 = $gl->getResults();
        $groups = [];
        foreach ($g1 as $g) {
            $gp = new \Permissions($g);
            if ($gp->canSearchUsersInGroup($g)) {
                $groups[$g->getGroupID()] = $g->getGroupDisplayName();
            }
        }

        $form = $this->app->make('helper/form');
        echo $form->select($this->field('gID'), $groups);
    }

    public function getSearchIndexValue()
    {
        $group = $this->getAttributeValue()->getValue();
        if ($group) {
            return $group->getGroupID();
        }
    }

    public function exportKey($akey)
    {
        $this->loadSettings();
        $type = $akey->addChild('type');
        $type->addAttribute('group-selection-method', $this->akGroupSelectionMethod);
        $type->addAttribute('display-groups-beneath-specific-parent',
            $this->akDisplayGroupsBeneathSpecificParent ? '1' : '');
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
            $selectedGroup = Group::getByID((int) ($data['value']));
        }
        if ($selectedGroup) {
            $errorList = new ErrorList();
            if ($this->akDisplayGroupsBeneathSpecificParent) {
                $allowedParent = Group::getByID($this->akDisplayGroupsBeneathParentID);
                if ($allowedParent) {
                    $parentIDs = [];
                    foreach ($selectedGroup->getParentGroups() as $parentGroup) {
                        $parentIDs[] = $parentGroup->getGroupID();
                    }
                    if (!in_array($allowedParent->getGroupID(), $parentIDs)) {
                        $errorList->add(t('You must select a sub-group found in the %s group',
                            $allowedParent->getGroupPath()));
                    }
                }
            }
            if ($this->akGroupSelectionMethod == UserGroupSettings::GROUP_SELECTION_METHOD_IN_GROUP) {
                $u = $this->app->make(User::class);
                if (!$u->isSuperUser()) {
                    if (!$u->inGroup($selectedGroup)) {
                        $errorList->add(t('You must be a member of the group %s to add a user to it.',
                            $selectedGroup->getGroupPath()));
                    }
                }
            } elseif ($this->akGroupSelectionMethod == UserGroupSettings::GROUP_SELECTION_METHOD_PERMISSIONS) {
                $gp = new Checker($selectedGroup);
                if (!$gp->canAssignGroup()) {
                    $errorList->add(t('You do not have permission to assign the group %s.',
                        $selectedGroup->getGroupPath()));
                }
            }

            return $errorList;
        }

        return new FieldNotPresentError(new AttributeField($this->getAttributeKey()));
    }

    public function importKey(\SimpleXMLElement $key)
    {
        $settings = $this->getAttributeKeySettings();
        /**
         * @var UserGroupSettings
         */
        if (isset($key->type)) {
            $akGroupSelectionMethod = (string) $key->type['group-selection-method'];
            $akDisplayGroupsBeneathSpecificParent = (string) $key->type['display-groups-beneath-specific-parent'] == '1'
                ? true : false;
            $settings->setGroupSelectionMethod($akGroupSelectionMethod);
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

    protected function loadSettings()
    {
        /**
         * @var UserGroupSettings
         */
        $ak = $this->getAttributeKey();
        if ($ak) {
            $settings = $ak->getAttributeKeySettings();
            if ($settings) {
                $this->akGroupSelectionMethod = $settings->getGroupSelectionMethod();
                $this->akDisplayGroupsBeneathSpecificParent = $settings->displayGroupsBeneathSpecificParent();
                $this->akDisplayGroupsBeneathParentID = $settings->getDisplayGroupsBeneathParentID();
            }
        }
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
