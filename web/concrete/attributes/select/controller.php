<?php
namespace Concrete\Attribute\Select;

use Concrete\Core\Entity\Attribute\Key\Type\SelectType;
use Concrete\Core\Entity\Attribute\Value\Value\SelectValue;
use Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption;
use Concrete\Core\Entity\Attribute\Value\Value\SelectValueOptionList;
use Concrete\Core\Search\ItemList\Database\AttributedItemList;
use Core;
use Database;
use Concrete\Core\Attribute\Controller as AttributeTypeController;
use Symfony\Component\HttpFoundation\JsonResponse;

class Controller extends AttributeTypeController
{
    private $akSelectAllowMultipleValues;
    private $akSelectAllowOtherValues;
    private $akSelectOptionDisplayOrder;

    protected $searchIndexFieldDefinition = array(
        'type' => 'string',
        'options' => array('default' => null, 'notnull' => false),
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
        /*
         * @var \Concrete\Core\Entity\Attribute\Key\SelectKey
         */
        $ak = $this->getAttributeKey();
        if (!is_object($ak)) {
            return false;
        }

        /*
         * @var SelectType
         */
        $type = $ak->getAttributeKeyType();

        $this->akSelectAllowMultipleValues = $type->getAllowMultipleValues();
        $this->akSelectAllowOtherValues = $type->getAllowOtherValues();
        $this->akSelectOptionDisplayOrder = $type->getDisplayOrder();

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
                $this->akSelectAllowOtherValues,
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
                $av->addChild('option', (string) $l);
            }
        }
    }

    public function setAllowedMultipleValues($allow)
    {
        $db = Database::get();
        $this->akSelectAllowMultipleValues = $allow;
        $db->Replace('atSelectSettings', array(
            'akID' => $this->attributeKey->getAttributeKeyID(),
            'akSelectAllowMultipleValues' => intval($allow),
        ), array('akID'), true);
    }

    public function setAllowOtherValues($allow)
    {
        $db = Database::get();
        $this->akSelectAllowOtherValues = $allow;
        $db->Replace('atSelectSettings', array(
            'akID' => $this->attributeKey->getAttributeKeyID(),
            'akSelectAllowOtherValues' => intval($allow),
        ), array('akID'), true);
    }

    public function setOptionDisplayOrder($order)
    {
        $db = Database::get();
        $this->akSelectOptionDisplayOrder = $order;
        $db->Replace('atSelectSettings', array(
            'akID' => $this->attributeKey->getAttributeKeyID(),
            'akSelectOptionDisplayOrder' => $order,
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
        $type = new SelectType();
        if (isset($akey->type)) {
            $akSelectAllowMultipleValues = $akey->type['allow-multiple-values'];
            $akSelectOptionDisplayOrder = $akey->type['display-order'];
            $akSelectAllowOtherValues = $akey->type['allow-other-values'];
            $type->setAllowMultipleValues((bool) $akSelectAllowMultipleValues);
            $type->setDisplayOrder($akSelectOptionDisplayOrder);
            $type->setAllowOtherValues((bool) $akSelectAllowOtherValues);
            $list = new SelectValueOptionList();
            if (isset($akey->type->options)) {
                $displayOrder = 0;
                foreach ($akey->type->options->children() as $option) {
                    $opt = new SelectValueOption();
                    $opt->setSelectAttributeOptionValue((string) $option['value']);
                    $opt->setIsEndUserAdded((bool) $option['is-end-user-added']);
                    $opt->setOptionList($list);
                    $opt->setDisplayOrder($displayOrder);
                    $list->getOptions()->add($opt);
                    ++$displayOrder;
                }
            }
            $type->setOptionList($list);
        }

        return $type;
    }

    private function getSelectValuesFromPost()
    {
        $displayOrder = 0;
        $options = array();
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
                $opt = new SelectValueOption();
                $opt->setSelectAttributeOptionValue($value);
                $opt->setDisplayOrder($displayOrder);
            } elseif ($_POST['akSelectValueExistingOption_' . $id] == $id) {
                $opt = new SelectValueOption();
                $opt->setSelectAttributeOptionID($id);
                $opt->setSelectAttributeOptionValue($value);
                $opt->setDisplayOrder($displayOrder);
            }

            if (is_object($opt)) {
                $options[] = $opt;
                ++$displayOrder;
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

    public function importValue(\SimpleXMLElement $akv)
    {
        if (isset($akv->value)) {
            $vals = array();
            foreach ($akv->value->children() as $ch) {
                $vals[] = (string) $ch;
            }

            return $this->saveValue($vals);
        }
    }

    public function getOptionByValue($value, $attributeKey = false)
    {
        $orm = \Database::connection()->getEntityManager();
        $repository = $orm->getRepository('\Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption');
        if ($attributeKey) {
            $existingList = $attributeKey->getAttributeKeyType()->getOptionList();
        }
        if (isset($existingList) && is_object($existingList)) {
            $option = $repository->findOneBy(array(
                'list' => $existingList,
                'value' => $value,
            ));
        } else {
            $option = $repository->findOneByValue($value);
        }

        return $option;
    }

    // Sets select options for a particular attribute
    // If the $value == string, then 1 item is selected
    // if array, then multiple, but only if the attribute in question is a select multiple
    // Note, items CANNOT be added to the pool (even if the attribute allows it) through this process.
    // Items should now be added to the database if they don't exist already & if the allow checkbox is checked under the attribute settings
    // Code from this bug - http://www.concrete5.org/index.php?cID=595692
    public function saveValue($value)
    {
        $this->load();

        $options = array();

        if ($value != null) {
            if (is_array($value) && $this->akSelectAllowMultipleValues) {
                foreach ($value as $v) {
                    if ($v instanceof SelectValueOption) {
                        $option = $v;
                    } else {
                        // Retrieve the option by value. Only get those that are assigned to this attribute key.
                        $option = $this->getOptionByValue($v, $this->attributeKey);
                    }

                    if (!is_object($option) && $this->akSelectAllowOtherValues) {
                        $option = new SelectValueOption();
                        $option->setIsEndUserAdded(true);
                        $option->setSelectAttributeOptionValue($v);
                    }

                    if (is_object($option)) {
                        $options[] = $option;
                    }
                }
            } else {
                if (is_array($value)) {
                    $value = $value[0];
                }

                if ($value instanceof SelectValueOption) {
                    $option = $value;
                } else {
                    $option = $this->getOptionByValue($value, $this->attributeKey);
                }

                if (is_object($option)) {
                    $options[] = $option;
                }
            }
        }

        $av = new SelectValue();
        $av->setSelectedOptions($options);

        return $av;
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
        return is_object($value = $this->getValue()) && ((string) $value != '');
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
            ++$i;
        }
        $list->filter(false, '(' . $multiString . ')');

        return $list;
    }

    public function getSearchIndexValue()
    {
        $str = "\n";
        $list = $this->attributeValue->getValue()->getSelectedOptions();
        foreach ($list as $l) {
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
        if (is_object($this->attributeValue)) {
            return $this->attributeValue->getValue()->getSelectedOptions();
        }
        return array();
    }

    /**
     * Used by select2. Automatically takes a value request and converts it into tag/text key value pairs.
     * New options are just text/tag, whereas existing ones are SelectAttributeOption:ID/text.
     */
    public function action_load_autocomplete_selected_value()
    {
        $em = \Database::get()->getEntityManager();
        $r = $em->getRepository('\Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption');
        $type = $this->attributeKey->getAttributeKeyType();

        $value = $this->request->query->get('value');
        $values = explode(',', $value);
        $response = array();
        foreach ($values as $value) {
            $value = trim($value);
            $o = new \stdClass();
            if (strpos($value, 'SelectAttributeOption:') === 0) {
                $optionID = substr($value, 22);
                $option = $r->findOneBy(array('list' => $type->getOptionList(), 'avSelectOptionID' => $optionID));
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

        return new JsonResponse($response);
    }

    public function action_load_autocomplete_values()
    {
        $this->load();
        $values = array();
        // now, if the current instance of the attribute key allows us to do autocomplete, we return all the values
        if ($this->akSelectAllowOtherValues) {
            $options = $this->getOptions($_GET['q']);
            foreach ($options as $opt) {
                $o = new \stdClass();
                $o->id = 'SelectAttributeOption:' . $opt->getSelectAttributeOptionID();
                $o->text = $opt->getSelectAttributeOptionValue(false);
                $values[] = $o;
            }
        }
        return new JsonResponse($values);
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
            ++$i;
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
                $qb->expr()->like($column, ':optionValue_' . $this->attributeKey->getAttributeKeyID())
            );
            $qb->setParameter('optionValue_' . $this->attributeKey->getAttributeKeyID(), "%\n" . $option->getSelectAttributeOptionValue(false) . "\n%");
        }
    }

    /**
     * Returns a list of available options optionally filtered by an sql $like statement ex: startswith%.
     *
     * @param string $like
     */
    public function getOptions($keywords = null)
    {
        if (!isset($this->attributeKey)) {
            return array();
        }

        if (!isset($this->akSelectOptionDisplayOrder)) {
            $this->load();
        }

        $type = $this->attributeKey->getAttributeKeyType();

        $em = \Database::get()->getEntityManager();
        $r = $em->getRepository('\Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption');
        $builder = $r->createQueryBuilder('v');
        $builder->where('v.list = :list');
        if ($keywords) {
            $builder->andWhere($builder->expr()->like('v.value', ':value'));
            $builder->setParameter('value', $keywords  . '%');
        }
        switch ($this->akSelectOptionDisplayOrder) {
            case 'popularity_desc':
                /**
                 * @TODO make this work again - there is currently no field.
                 * @TODO It used to be done with a group by on the selected options table.
                 */
                $builder->orderBy('v.popularity', 'asc');
                break;
            case 'alpha_asc':
                $builder->orderBy('v.value', 'asc');
                break;
            default:
                $builder->orderBy('v.displayOrder', 'asc');
                break;
        }
        $builder->setParameter('list', $type->getOptionList());
        return $builder->getQuery()->getResult();
    }

    public function saveKey($data)
    {
        if ($this->attributeKey) {
            $type = $this->attributeKey->getAttributeKeyType();
        } else {
            $type = new SelectType();
        }

        $orm = \Database::connection()->getEntityManager();

        $newOptionSet = new SelectValueOptionList();

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

        $type->setAllowMultipleValues((bool) $akSelectAllowMultipleValues);
        $type->setDisplayOrder($akSelectOptionDisplayOrder);
        $type->setAllowOtherValues((bool) $akSelectAllowOtherValues);

        $initialOptionSet = $this->getOptions();
        $selectedPostValues = $this->getSelectValuesFromPost();

        // Now we add the options

        foreach ($selectedPostValues as $option) {
            /*
             * @var $option SelectValueOption
             */
            $option->setOptionList($newOptionSet);
            $newOptionSet->getOptions()->add($option);
        }

        // Now we remove all options that appear in the
        // old values list but not in the new
        foreach ($initialOptionSet as $iopt) {
            if (!$newOptionSet->contains($iopt)) {
                $orm->remove($iopt);
            }
        }

        $type->setOptionList($newOptionSet);

        return $type;
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

    public function createAttributeKeyType()
    {
        return new SelectType();
    }
}
