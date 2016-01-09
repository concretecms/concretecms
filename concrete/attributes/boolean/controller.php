<?php

namespace Concrete\Attribute\Boolean;

use Core;
use Database;
use Concrete\Core\Search\ItemList\Database\AttributedItemList;
use Concrete\Core\Attribute\Controller as AttributeTypeController;

class Controller extends AttributeTypeController
{
    /**
     * Field definition in the ADODB Format. We omit the first column (name) though, since it's
     * automatically generated.
     *
     * @var array
     */
    protected $searchIndexFieldDefinition = array('type' => 'boolean', 'options' => array('default' => 0, 'notnull' => false));

    public function searchForm($list)
    {
        $list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), 1);

        return $list;
    }

    public function filterByAttribute(AttributedItemList $list, $boolean, $comparison = '=')
    {
        $qb = $list->getQueryObject();
        $column = sprintf('ak_%s', $this->attributeKey->getAttributeKeyHandle());
        switch ($comparison) {
            case '<>':
            case '!=':
                $boolean = $boolean ? false : true;
                break;
        }
        if ($boolean) {
            $qb->andWhere("{$column} = 1");
        } else {
            $qb->andWhere("{$column} <> 1 or {$column} is null");
        }
    }

    public function getValue()
    {
        $db = Database::get();
        $value = $db->GetOne("select value from atBoolean where avID = ?", array($this->getAttributeValueID()));

        return $value;
    }

    public function exportKey($akey)
    {
        $this->load();
        $type = $akey->addChild('type');
        $type->addAttribute('checked', $this->akCheckedByDefault);

        return $akey;
    }

    public function importKey($akey)
    {
        if (isset($akey->type)) {
            $data = array();
            $checked = (string) $akey->type['checked'];
            if ($checked != '') {
                $data['akCheckedByDefault'] = 1;
            } else {
                $data['akCheckedByDefault'] = 0;
            }
            $this->saveKey($data);
        }
    }

    public function getDisplayValue()
    {
        $v = $this->getValue();

        return ($v == 1) ? t('Yes') : t('No');
    }

    protected function load()
    {
        $ak = $this->getAttributeKey();
        if (!is_object($ak)) {
            return false;
        }

        $db = Database::get();
        $row = $db->GetRow('select akCheckedByDefault from atBooleanSettings where akID = ?', array($ak->getAttributeKeyID()));
        $this->akCheckedByDefault = $row['akCheckedByDefault'];
        $this->set('akCheckedByDefault', $this->akCheckedByDefault);
    }

    public function label($customText = false)
    {
    }

    public function form()
    {
        if (is_object($this->attributeValue)) {
            $value = $this->getAttributeValue()->getValue();
            $checked = $value == 1 ? true : false;
        } else {
            $this->load();
            if ($this->akCheckedByDefault) {
                $checked = true;
            }
        }

        $cb = Core::make('helper/form')->checkbox($this->field('value'), 1, $checked);
        print $cb . ' <span>' . $this->attributeKey->getAttributeKeyDisplayName() .  '</span>';
    }

    public function composer()
    {
        print '<div class="ccm-attribute ccm-attribute-boolean checkbox"><label>';
        $this->form();
        print '</label></div>';
    }

    public function search()
    {
        print '<div class="ccm-attribute ccm-attribute-boolean checkbox"><label>' . Core::make('helper/form')->checkbox($this->field('value'), 1, $this->request('value') == 1) . ' ' . $this->attributeKey->getAttributeKeyDisplayName() . '</label></div>';
    }

    public function type_form()
    {
        $this->set('form', Core::make('helper/form'));
        $this->load();
    }

    // run when we call setAttribute(), instead of saving through the UI
    public function saveValue($value)
    {
        $db = Database::get();
        $value = ($value == false || $value == '0') ? 0 : 1;
        $db->Replace('atBoolean', array('avID' => $this->getAttributeValueID(), 'value' => $value), 'avID', true);
    }

    public function deleteKey()
    {
        $db = Database::get();
        $db->Execute('delete from atBooleanSettings where akID = ?', array($this->getAttributeKey()->getAttributeKeyID()));

        $arr = $this->attributeKey->getAttributeValueIDList();
        foreach ($arr as $id) {
            $db->Execute('delete from atBoolean where avID = ?', array($id));
        }
    }

	public function validateValue()
	{
		$v = $this->getValue();
		return $v == 1;
	}

	public function duplicateKey($newAK)
    {
        $this->load();
        $db = Database::get();
        $db->Execute('insert into atBooleanSettings (akID, akCheckedByDefault) values (?, ?)', array($newAK->getAttributeKeyID(), $this->akCheckedByDefault));
    }

    public function saveKey($data)
    {
        $ak = $this->getAttributeKey();
        $db = Database::get();
        $akCheckedByDefault = 0;
        if (isset($data['akCheckedByDefault']) && $data['akCheckedByDefault']) {
            $akCheckedByDefault = 1;
        }

        $db->Replace('atBooleanSettings', array(
            'akID' => $ak->getAttributeKeyID(),
            'akCheckedByDefault' => $akCheckedByDefault,
        ), array('akID'), true);
    }

    public function saveForm($data)
    {
        $this->saveValue(isset($data['value']) ? $data['value'] : false);
    }

    // if this gets run we assume we need it to be validated/checked
    public function validateForm($data)
    {
        return $data['value'] == 1;
    }

    public function deleteValue()
    {
        $db = Database::get();
        $db->Execute('delete from atBoolean where avID = ?', array($this->getAttributeValueID()));
    }
}
