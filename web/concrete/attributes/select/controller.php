<?php

namespace Concrete\Attribute\Select;

use Concrete\Core\Search\ItemList\Database\AttributedItemList;
use Core;
use Database;
use Concrete\Core\Attribute\Controller as AttributeTypeController;

class Controller extends AttributeTypeController
{
    private $akSelectAllowMultipleValues;
    private $akSelectAllowOtherValues;
    private $akSelectOptionDisplayOrder;

    protected $searchIndexFieldDefinition = array(
        'type' => 'string',
        'options' => array('default' => null, 'notnull' => false)
    );

    public function type_form()
    {
        $this->set('form', Core::make('helper/form'));
        $this->load();
        //$akSelectValues = $this->getSelectValuesFromPost();
        //$this->set('akSelectValues', $akSelectValues);

        if ($this->isPost()) {
            $akSelectValues = $this->getSelectValuesFromPost();
            $this->set('akSelectValues', $akSelectValues);
        } elseif (isset($this->attributeKey)) {
            $options = $this->getOptions();
            $this->set('akSelectValues', $options);
        } else {
            $this->set('akSelectValues', array());
        }
    }

    protected function load()
    {
        $ak = $this->getAttributeKey();
        if (!is_object($ak)) {
            return false;
        }

        $db = Database::get();
        $row = $db->GetRow('select akSelectAllowMultipleValues, akSelectOptionDisplayOrder, akSelectAllowOtherValues from atSelectSettings where akID = ?',
            array($ak->getAttributeKeyID()));
        $this->akSelectAllowMultipleValues = $row ? $row['akSelectAllowMultipleValues'] : null;
        $this->akSelectAllowOtherValues = $row ? $row['akSelectAllowOtherValues'] : null;
        $this->akSelectOptionDisplayOrder = $row ? $row['akSelectOptionDisplayOrder'] : null;

        $this->set('akSelectAllowMultipleValues', $this->akSelectAllowMultipleValues);
        $this->set('akSelectAllowOtherValues', $this->akSelectAllowOtherValues);
        $this->set('akSelectOptionDisplayOrder', $this->akSelectOptionDisplayOrder);
    }

    public function duplicateKey($newAK)
    {
        $this->load();
        $db = Database::get();
        $db->Execute('insert into atSelectSettings (akID, akSelectAllowMultipleValues, akSelectOptionDisplayOrder, akSelectAllowOtherValues) values (?, ?, ?, ?)',
            array(
                $newAK->getAttributeKeyID(),
                $this->akSelectAllowMultipleValues,
                $this->akSelectOptionDisplayOrder,
                $this->akSelectAllowOtherValues
            ));
        $r = $db->Execute('select value, displayOrder, isEndUserAdded from atSelectOptions where akID = ?',
            $this->getAttributeKey()->getAttributeKeyID());
        while ($row = $r->FetchRow()) {
            $db->Execute('insert into atSelectOptions (akID, value, displayOrder, isEndUserAdded) values (?, ?, ?, ?)',
                array(
                    $newAK->getAttributeKeyID(),
                    $row['value'],
                    $row['displayOrder'],
                    $row['isEndUserAdded'],
                ));
        }
    }

    public function exportKey($akey)
    {
        $this->load();
        $db = Database::get();
        $type = $akey->addChild('type');
        $type->addAttribute('allow-multiple-values', $this->akSelectAllowMultipleValues);
        $type->addAttribute('display-order', $this->akSelectOptionDisplayOrder);
        $type->addAttribute('allow-other-values', $this->akSelectAllowOtherValues);
        $r = $db->Execute('select value, displayOrder, isEndUserAdded from atSelectOptions where akID = ? order by displayOrder asc',
            $this->getAttributeKey()->getAttributeKeyID());
        $options = $type->addChild('options');
        while ($row = $r->FetchRow()) {
            $opt = $options->addChild('option');
            $opt->addAttribute('value', $row['value']);
            $opt->addAttribute('is-end-user-added', $row['isEndUserAdded']);
        }

        return $akey;
    }

    public function exportValue(\SimpleXMLElement $akn)
    {
        $list = $this->getSelectedOptions();
        if ($list->count() > 0) {
            $av = $akn->addChild('value');
            foreach ($list as $l) {
                $av->addChild('option', (string)$l);
            }
        }
    }

    public function importValue(\SimpleXMLElement $akv)
    {
        if (isset($akv->value)) {
            $vals = array();
            foreach ($akv->value->children() as $ch) {
                $vals[] = (string)$ch;
            }

            return $vals;
        }
    }

    public function setAllowedMultipleValues($allow)
    {
        $db = Database::get();
        $this->akSelectAllowMultipleValues = $allow;
        $db->Replace('atSelectSettings', array(
            'akID' => $this->attributeKey->getAttributeKeyID(),
            'akSelectAllowMultipleValues' => intval($allow)
        ), array('akID'), true);
    }

    public function setAllowOtherValues($allow)
    {
        $db = Database::get();
        $this->akSelectAllowOtherValues = $allow;
        $db->Replace('atSelectSettings', array(
            'akID' => $this->attributeKey->getAttributeKeyID(),
            'akSelectAllowOtherValues' => intval($allow)
        ), array('akID'), true);
    }

    public function setOptionDisplayOrder($order)
    {
        $db = Database::get();
        $this->akSelectOptionDisplayOrder = $order;
        $db->Replace('atSelectSettings', array(
            'akID' => $this->attributeKey->getAttributeKeyID(),
            'akSelectOptionDisplayOrder' => $order
        ), array('akID'), true);
    }

    public function setOptions($options)
    {
        foreach ($options as $option) {
            Option::add($this->attributeKey, $option, 0);
        }
    }

    public function importKey($akey)
    {
        if (isset($akey->type)) {
            $akSelectAllowMultipleValues = $akey->type['allow-multiple-values'];
            $akSelectOptionDisplayOrder = $akey->type['display-order'];
            $akSelectAllowOtherValues = $akey->type['allow-other-values'];
            $db = Database::get();
            $db->Replace('atSelectSettings', array(
                'akID' => $this->attributeKey->getAttributeKeyID(),
                'akSelectAllowMultipleValues' => $akSelectAllowMultipleValues,
                'akSelectAllowOtherValues' => $akSelectAllowOtherValues,
                'akSelectOptionDisplayOrder' => $akSelectOptionDisplayOrder,
            ), array('akID'), true);

            if (isset($akey->type->options)) {
                foreach ($akey->type->options->children() as $option) {
                    Option::add($this->attributeKey, $option['value'], $option['is-end-user-added']);
                }
            }
        }
    }

    private function getSelectValuesFromPost()
    {
        $options = new OptionList();
        $displayOrder = 0;
        foreach ($_POST as $key => $value) {
            if (!strstr($key, 'akSelectValue_') || $value == 'TEMPLATE') {
                continue;
            }
            $opt = false;
            // strip off the prefix to get the ID
            $id = substr($key, 14);
            // now we determine from the post whether this is a new option
            // or an existing. New ones have this value from in the akSelectValueNewOption_ post field
            if ($_POST['akSelectValueNewOption_' . $id] == $id) {
                $opt = new Option(0, $value, $displayOrder);
                $opt->tempID = $id;
            } elseif ($_POST['akSelectValueExistingOption_' . $id] == $id) {
                $opt = new Option($id, $value, $displayOrder);
            }

            if (is_object($opt)) {
                $options->add($opt);
                $displayOrder++;
            }
        }

        return $options;
    }

    public function form()
    {
        $this->load();
        $options = $this->getSelectedOptions();
        $selectedOptions = array();
        $selectedOptionValues = array();
        foreach ($options as $opt) {
            $selectedOptions[] = $opt->getSelectAttributeOptionID();
            $selectedOptionValues[$opt->getSelectAttributeOptionID()] = $opt->getSelectAttributeOptionValue();
        }
        $this->set('selectedOptionValues', $selectedOptionValues);
        $this->set('selectedOptions', $selectedOptions);
        $this->requireAsset('select2');
    }

    public function search()
    {
        $this->load();
        $selectedOptions = $this->request('atSelectOptionID');
        if (!is_array($selectedOptions)) {
            $selectedOptions = array();
        }
        $this->set('selectedOptions', $selectedOptions);
    }

    public function deleteValue()
    {
        $db = Database::get();
        $db->Execute('delete from atSelectOptionsSelected where avID = ?', array($this->getAttributeValueID()));
    }

    public function deleteKey()
    {
        $db = Database::get();
        $db->Execute('delete from atSelectSettings where akID = ?', array($this->attributeKey->getAttributeKeyID()));
        $r = $db->Execute('select ID from atSelectOptions where akID = ?',
            array($this->attributeKey->getAttributeKeyID()));
        while ($row = $r->FetchRow()) {
            $db->Execute('delete from atSelectOptionsSelected where atSelectOptionID = ?', array($row['ID']));
        }
        $db->Execute('delete from atSelectOptions where akID = ?', array($this->attributeKey->getAttributeKeyID()));
    }

    public function saveForm($data)
    {
        $this->load();

        $akSelectAllowMultipleValues = $this->akSelectAllowMultipleValues;
        $akSelectAllowOtherValues = $this->akSelectAllowOtherValues;
        if (!$akSelectAllowMultipleValues && !$akSelectAllowOtherValues) {
            // select list. Only one option possible. No new options.
            $option = Option::getByID($data['atSelectOptionValue']);
            if (is_object($option)) {
                $this->saveValue($option);
            } else {
                $this->saveValue(null);
            }

        } else {
            if ($akSelectAllowMultipleValues && !$akSelectAllowOtherValues) {
                // checkbox list.  No new options.
                $options = array();
                if (is_array($data['atSelectOptionValue'])) {
                    foreach ($data['atSelectOptionValue'] as $optionID) {
                        $option = Option::getByID($optionID);
                        if (is_object($option)) {
                            $options[] = $option;
                        }
                    }
                }
                $this->saveValue($options);

            } else {
                if (!$akSelectAllowMultipleValues && $akSelectAllowOtherValues) {

                    // The post comes through in the select2 format. Either a SelectAttributeOption:ID item
                    // or a new item.
                    $option = false;
                    if ($data['atSelectOptionValue']) {
                        if (preg_match('/SelectAttributeOption\:(.+)/i',
                            $data['atSelectOptionValue'], $matches)) {
                            $option = Option::getByID($matches[1]);
                        } else {
                            $option = Option::getByValue(trim($data['atSelectOptionValue']), $this->attributeKey);
                            if (!is_object($option)) {
                                $option = Option::add($this->attributeKey, trim($data['atSelectOptionValue']), true);
                            }
                        }
                    }
                    if (is_object($option)) {
                        $this->saveValue($option);
                    } else {
                        $this->saveValue(null);
                    }

                } else {
                    if ($akSelectAllowMultipleValues && $akSelectAllowOtherValues) {

                        // The post comes through in the select2 format. A comma-separated
                        // list of SelectAttributeOption:ID items and new items.
                        $options = array();
                        if ($data['atSelectOptionValue']) {
                            foreach (explode(',', $data['atSelectOptionValue']) as $value) {
                                if (preg_match('/SelectAttributeOption\:(.+)/i', $value, $matches)) {
                                    $option = Option::getByID($matches[1]);
                                } else {
                                    $option = Option::getByValue(trim($value), $this->attributeKey);
                                    if (!is_object($option)) {
                                        $option = Option::add($this->attributeKey, trim($value), true);
                                    }
                                }

                                if (is_object($option)) {
                                    $options[] = $option;
                                }
                            }
                        }

                        if (count($options)) {
                            $this->saveValue($options);
                        } else {
                            $this->saveValue(null);
                        }
                    }
                }
            }
        }

    }

    // Sets select options for a particular attribute
    // If the $value == string, then 1 item is selected
    // if array, then multiple, but only if the attribute in question is a select multiple
    // Note, items CANNOT be added to the pool (even if the attribute allows it) through this process.
    // Items should now be added to the database if they don't exist already & if the allow checkbox is checked under the attribute settings
    // Code from this bug - http://www.concrete5.org/index.php?cID=595692
    public function saveValue($value)
    {
        $db = Database::get();
        $this->load();
        $options = array();

        if ($value != null) {
            if (is_array($value) && $this->akSelectAllowMultipleValues) {
                foreach ($value as $v) {
                    if ($v instanceof Option) {
                        $opt = $v;
                    } else {
                        $opt = Option::getByValue($v, $this->attributeKey);
                    }
                    if (is_object($opt)) {
                        $options[] = $opt;
                    } elseif ($this->akSelectAllowOtherValues) {
                        $options[] = Option::add($this->attributeKey, $v, true);
                    }
                }
            } else {
                if (is_array($value)) {
                    $value = $value[0];
                }

                if ($value instanceof Option) {
                    $opt = $value;
                } else {
                    $opt = Option::getByValue($value, $this->attributeKey);
                }

                if (is_object($opt)) {
                    $options[] = $opt;
                }
            }
        }

        $db->Execute('delete from atSelectOptionsSelected where avID = ?', array($this->getAttributeValueID()));
        if (count($options) > 0) {
            foreach ($options as $opt) {
                $db->Execute('insert into atSelectOptionsSelected (avID, atSelectOptionID) values (?, ?)',
                    array($this->getAttributeValueID(), $opt->getSelectAttributeOptionID()));
                if ($this->akSelectAllowMultipleValues == false) {
                    break;
                }
            }
        }
    }

    public function getDisplayValue()
    {
        $list = $this->getSelectedOptions();
        $html = '';
        foreach ($list as $l) {
            $html .= $l->getSelectAttributeOptionDisplayValue() . '<br/>';
        }

        return $html;
    }

    public function getDisplaySanitizedValue()
    {
        return $this->getDisplayValue();
    }

    public function validateValue()
    {
        return is_object($value = $this->getValue()) && ((string)$value != '');
    }

    public function validateForm($p)
    {
        $this->load();
        $options = $this->request('atSelectOptionValue');
        return $options != '';
    }

    public function searchForm($list)
    {
        $options = $this->request('atSelectOptionID');
        $db = Database::get();
        $tbl = $this->attributeKey->getIndexedSearchTable();
        if (!is_array($options)) {
            return $list;
        }
        $optionQuery = array();
        foreach ($options as $id) {
            if ($id > 0) {
                $opt = Option::getByID($id);
                if (is_object($opt)) {
                    $optionQuery[] = $opt->getSelectAttributeOptionValue(false);
                }
            }
        }
        if (count($optionQuery) == 0) {
            return false;
        }

        $i = 0;
        $multiString = '';
        foreach ($optionQuery as $val) {
            $val = $db->quote('%||' . $val . '||%');
            $multiString .= 'REPLACE(ak_' . $this->attributeKey->getAttributeKeyHandle() . ', "\n", "||") like ' . $val . ' ';
            if (($i + 1) < count($optionQuery)) {
                $multiString .= 'OR ';
            }
            $i++;
        }
        $list->filter(false, '(' . $multiString . ')');

        return $list;
    }

    public function getValue()
    {
        $list = $this->getSelectedOptions();

        return $list;
    }

    public function getSearchIndexValue()
    {
        $str = "\n";
        $list = $this->getSelectedOptions();
        foreach ($list as $l) {
            $l = (is_object($l) && method_exists($l, '__toString')) ? $l->__toString() : $l;
            $str .= $l . "\n";
        }
        // remove line break for empty list
        if ($str == "\n") {
            return '';
        }

        return $str;
    }

    public function getSelectedOptions()
    {
        if (!isset($this->akSelectOptionDisplayOrder)) {
            $this->load();
        }
        $db = Database::get();
        $sortByDisplayName = false;
        switch ($this->akSelectOptionDisplayOrder) {
            case 'popularity_desc':
                $options = $db->GetAll("select ID, value, displayOrder, (select count(s2.atSelectOptionID) from atSelectOptionsSelected s2 where s2.atSelectOptionID = ID) as total from atSelectOptionsSelected inner join atSelectOptions on atSelectOptionsSelected.atSelectOptionID = atSelectOptions.ID where avID = ? order by total desc, value asc",
                    array($this->getAttributeValueID()));
                break;
            case 'alpha_asc':
                $options = $db->GetAll("select ID, value, displayOrder from atSelectOptionsSelected inner join atSelectOptions on atSelectOptionsSelected.atSelectOptionID = atSelectOptions.ID where avID = ?",
                    array($this->getAttributeValueID()));
                $sortByDisplayName = true;
                break;
            default:
                $options = $db->GetAll("select ID, value, displayOrder from atSelectOptionsSelected inner join atSelectOptions on atSelectOptionsSelected.atSelectOptionID = atSelectOptions.ID where avID = ? order by displayOrder asc",
                    array($this->getAttributeValueID()));
                break;
        }
        $db = Database::get();
        $list = new OptionList();
        foreach ($options as $row) {
            $opt = new Option($row['ID'], $row['value'], $row['displayOrder']);
            $list->add($opt);
        }
        if ($sortByDisplayName) {
            $list->sortByDisplayName();
        }

        return $list;
    }

    /**
     * Used by select2. Automatically takes a value request and converts it into tag/text key value pairs.
     * New options are just text/tag, whereas existing ones are SelectAttributeOption:ID/text
     */
    public function action_load_autocomplete_selected_value()
    {
        $r = \Request::getInstance();
        $value = $r->query->get('value');
        $values = explode(',', $value);
        $response = array();
        foreach ($values as $value) {
            $value = trim($value);
            $o = new \stdClass;
            if (strpos($value, 'SelectAttributeOption:') === 0) {
                $optionID = substr($value, 22);
                $option = Option::getByID($optionID);
                if (is_object($option)) {
                    $o->id = $value;
                    $o->text = $option->getSelectAttributeOptionValue();
                }
            } else {
                $o->id = $value;
                $o->text = $value;
            }

            $response[] = $o;
        }

        print json_encode($response);
        \Core::shutdown();
    }

    public function action_load_autocomplete_values()
    {
        $this->load();
        $values = array();
        // now, if the current instance of the attribute key allows us to do autocomplete, we return all the values
        if ($this->akSelectAllowOtherValues) {
            $options = $this->getOptions($_GET['q'] . '%');
            foreach ($options as $opt) {
                $o = new \stdClass;
                $o->id = 'SelectAttributeOption:' . $opt->getSelectAttributeOptionID();
                $o->text = $opt->getSelectAttributeOptionValue(false);
                $values[] = $o;
            }
        }
        print json_encode($values);
    }

    public function getOptionUsageArray($parentPage = false, $limit = 9999)
    {
        $db = Database::get();
        $q = "select atSelectOptions.value, atSelectOptionID, count(atSelectOptionID) as total from Pages inner join CollectionVersions on (Pages.cID = CollectionVersions.cID and CollectionVersions.cvIsApproved = 1) inner join CollectionAttributeValues on (CollectionVersions.cID = CollectionAttributeValues.cID and CollectionVersions.cvID = CollectionAttributeValues.cvID) inner join atSelectOptionsSelected on (atSelectOptionsSelected.avID = CollectionAttributeValues.avID) inner join atSelectOptions on atSelectOptionsSelected.atSelectOptionID = atSelectOptions.ID where Pages.cIsActive = 1 and CollectionAttributeValues.akID = ? ";
        $v = array($this->attributeKey->getAttributeKeyID());
        if (is_object($parentPage)) {
            $v[] = $parentPage->getCollectionID();
            $q .= "and cParentID = ?";
        }
        $q .= " group by atSelectOptionID order by total desc limit " . $limit;
        $r = $db->Execute($q, $v);
        $list = new OptionList();
        $i = 0;
        while ($row = $r->FetchRow()) {
            $opt = new Option($row['atSelectOptionID'], $row['value'], $i, $row['total']);
            $list->add($opt);
            $i++;
        }

        return $list;
    }

    public function filterByAttribute(AttributedItemList $list, $value, $comparison = '=')
    {
        if ($value instanceof Option) {
            $option = $value;
        } else {
            $option = Option::getByValue($value);
        }
        if (is_object($option)) {
            $column = 'ak_' . $this->attributeKey->getAttributeKeyHandle();
            $qb = $list->getQueryObject();
            $qb->andWhere(
                $comparison === '!=' || $comparison === '<>'
                    ? $qb->expr()->notLike($column, ':optionValue_' . $this->attributeKey->getAttributeKeyID())
                    : $qb->expr()->like($column, ':optionValue_' . $this->attributeKey->getAttributeKeyID())
            );
	        $qb->setParameter('optionValue_' . $this->attributeKey->getAttributeKeyID(), "%\n" . $option->getSelectAttributeOptionValue(false) . "\n%");
        }
    }

    /**
     * Returns a list of available options optionally filtered by an sql $like statement ex: startswith%.
     *
     * @param string $like
     *
     * @return SelectAttributeTypeOptionList
     */
    public function getOptions($like = null)
    {
        if (!isset($this->akSelectOptionDisplayOrder)) {
            $this->load();
        }
        $db = Database::get();
        switch ($this->akSelectOptionDisplayOrder) {
            case 'popularity_desc':
                if (isset($like) && strlen($like)) {
                    $r = $db->Execute('select ID, value, displayOrder, count(atSelectOptionsSelected.atSelectOptionID) as total
						from atSelectOptions left join atSelectOptionsSelected on (atSelectOptions.ID = atSelectOptionsSelected.atSelectOptionID)
						where akID = ? AND atSelectOptions.value LIKE ? group by ID order by total desc, value asc',
                        array($this->attributeKey->getAttributeKeyID(), $like));
                } else {
                    $r = $db->Execute('select ID, value, displayOrder, count(atSelectOptionsSelected.atSelectOptionID) as total
						from atSelectOptions left join atSelectOptionsSelected on (atSelectOptions.ID = atSelectOptionsSelected.atSelectOptionID)
						where akID = ? group by ID order by total desc, value asc',
                        array($this->attributeKey->getAttributeKeyID()));
                }
                break;
            case 'alpha_asc':
                if (isset($like) && strlen($like)) {
                    $r = $db->Execute('select ID, value, displayOrder from atSelectOptions where akID = ? AND atSelectOptions.value LIKE ? order by value asc',
                        array($this->attributeKey->getAttributeKeyID(), $like));
                } else {
                    $r = $db->Execute('select ID, value, displayOrder from atSelectOptions where akID = ? order by value asc',
                        array($this->attributeKey->getAttributeKeyID()));
                }
                break;
            default:
                if (isset($like) && strlen($like)) {
                    $r = $db->Execute('select ID, value, displayOrder from atSelectOptions where akID = ? AND atSelectOptions.value LIKE ? order by displayOrder asc',
                        array($this->attributeKey->getAttributeKeyID(), $like));
                } else {
                    $r = $db->Execute('select ID, value, displayOrder from atSelectOptions where akID = ? order by displayOrder asc',
                        array($this->attributeKey->getAttributeKeyID()));
                }
                break;
        }
        $options = new OptionList();
        while ($row = $r->FetchRow()) {
            $opt = new Option($row['ID'], $row['value'], $row['displayOrder']);
            $options->add($opt);
        }

        return $options;
    }

    public function saveKey($data)
    {
        $ak = $this->getAttributeKey();

        $db = Database::get();

        $initialOptionSet = $this->getOptions();
        $selectedPostValues = $this->getSelectValuesFromPost();

        if (isset($data['akSelectAllowMultipleValues']) && ($data['akSelectAllowMultipleValues'] == 1)) {
            $akSelectAllowMultipleValues = 1;
        } else {
            $akSelectAllowMultipleValues = 0;
        }
        if (isset($data['akSelectAllowOtherValues']) && ($data['akSelectAllowOtherValues'] == 1)) {
            $akSelectAllowOtherValues = 1;
        } else {
            $akSelectAllowOtherValues = 0;
        }
        if (isset($data['akSelectOptionDisplayOrder']) && in_array($data['akSelectOptionDisplayOrder'],
                array('display_asc', 'alpha_asc', 'popularity_desc'))
        ) {
            $akSelectOptionDisplayOrder = $data['akSelectOptionDisplayOrder'];
        } else {
            $akSelectOptionDisplayOrder = 'display_asc';
        }

        // now we have a collection attribute key object above.
        $db->Replace('atSelectSettings', array(
            'akID' => $ak->getAttributeKeyID(),
            'akSelectAllowMultipleValues' => $akSelectAllowMultipleValues,
            'akSelectAllowOtherValues' => $akSelectAllowOtherValues,
            'akSelectOptionDisplayOrder' => $akSelectOptionDisplayOrder,
        ), array('akID'), true);

        // Now we add the options
        $newOptionSet = new OptionList();
        $displayOrder = 0;
        foreach ($selectedPostValues as $option) {
            $opt = $option->saveOrCreate($ak);
            if ($akSelectOptionDisplayOrder == 'display_asc') {
                $opt->setDisplayOrder($displayOrder);
            }
            $newOptionSet->add($opt);
            $displayOrder++;
        }

        // Now we remove all options that appear in the
        // old values list but not in the new
        foreach ($initialOptionSet as $iopt) {
            if (!$newOptionSet->contains($iopt)) {
                $iopt->delete();
            }
        }
    }

    /**
     * Convenience methods to retrieve a select attribute key's settings.
     */
    public function getAllowMultipleValues()
    {
        if (is_null($this->akSelectAllowMultipleValues)) {
            $this->load();
        }

        return $this->akSelectAllowMultipleValues;
    }

    public function getAllowOtherValues()
    {
        if (is_null($this->akSelectAllowOtherValues)) {
            $this->load();
        }

        return $this->akSelectAllowOtherValues;
    }

    public function getOptionDisplayOrder()
    {
        if (is_null($this->akSelectOptionDisplayOrder)) {
            $this->load();
        }

        return $this->akSelectOptionDisplayOrder;
    }
}
