<?php

namespace Concrete\Attribute\Select;

use Concrete\Core\Attribute\Controller as AttributeTypeController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Attribute\SimpleTextExportableAttributeInterface;
use Concrete\Core\Entity\Attribute\Key\Settings\SelectSettings;
use Concrete\Core\Entity\Attribute\Value\Value\SelectValue;
use Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption;
use Concrete\Core\Entity\Attribute\Value\Value\SelectValueOptionList;
use Concrete\Core\Entity\Attribute\Value\Value\SelectValueUsedOption;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Search\ItemList\Database\AttributedItemList;
use Core;
use Database;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;

class Controller extends AttributeTypeController implements SimpleTextExportableAttributeInterface
{
    protected $searchIndexFieldDefinition = [
        'type' => 'text',
        'options' => ['default' => null, 'notnull' => false],
    ];
    private $akSelectAllowMultipleValues;
    private $akSelectAllowOtherValues;
    private $akHideNoneOption;
    private $akSelectOptionDisplayOrder;
    private $akDisplayMultipleValuesOnSelect;

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('list-alt');
    }

    public function getAttributeValueClass()
    {
        return SelectValue::class;
    }

    public function type_form()
    {
        $this->set('form', Core::make('helper/form'));
        $this->load();

        if ($this->request->getMethod() == 'POST') {
            $akSelectValues = $this->getSelectValuesFromPost();
            $this->set('akSelectValues', $akSelectValues);
        } elseif (isset($this->attributeKey)) {
            $options = $this->getOptions();
            $this->set('akSelectValues', $options);
        } else {
            $this->set('akSelectValues', []);
        }
    }

    public function exportKey($akey)
    {
        $this->load();

        $type = $akey->addChild('type');
        $type->addAttribute('allow-multiple-values', $this->akSelectAllowMultipleValues);
        $type->addAttribute('display-order', $this->akSelectOptionDisplayOrder);
        $type->addAttribute('display-multiple-values', $this->akDisplayMultipleValuesOnSelect);
        $type->addAttribute('allow-other-values', $this->akSelectAllowOtherValues);
        $type->addAttribute('hide-none-option', $this->akHideNoneOption);
        $options = $this->getOptions();
        $node = $type->addChild('options');
        foreach ($options as $option) {
            $opt = $node->addChild('option');
            $opt->addAttribute('value', $option->getSelectAttributeOptionValue());
            $opt->addAttribute('is-end-user-added', $option->isEndUserAdded());
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
        /**
         * @var SelectSettings
         */
        $type = $this->getAttributeKey()->getAttributeKeySettings();
        $type->setAllowMultipleValues($allow);
        $this->entityManager->persist($type);
        $this->entityManager->flush();
    }

    public function setAllowOtherValues($allow)
    {
        /**
         * @var SelectSettings
         */
        $type = $this->getAttributeKey()->getAttributeKeySettings();
        $type->setAllowOtherValues($allow);
        $this->entityManager->persist($type);
        $this->entityManager->flush();
    }

    public function setHideNoneOption($allow)
    {
        /**
         * @var SelectSettings
         */
        $type = $this->getAttributeKey()->getAttributeKeySettings();
        $type->setHideNoneOption($allow);
        $this->entityManager->persist($type);
        $this->entityManager->flush();
    }

    public function setOptionDisplayOrder($order)
    {
        /**
         * @var SelectSettings
         */
        $type = $this->getAttributeKey()->getAttributeKeySettings();
        $type->setDisplayOrder($order);
        $this->entityManager->persist($type);
        $this->entityManager->flush();
    }

    public function setOptions($options)
    {
        /**
         * @var SelectSettings
         */
        $type = $this->getAttributeKey()->getAttributeKeySettings();
        $list = new SelectValueOptionList();
        $list->setOptions($options);
        $type->setOptionList($list);
        $this->entityManager->persist($type);
        $this->entityManager->flush();
    }

    public function importKey(\SimpleXMLElement $akey)
    {
        /**
         * @var SelectSettings
         */
        $type = $this->getAttributeKeySettings();
        if (isset($akey->type)) {
            $akSelectAllowMultipleValues = $akey->type['allow-multiple-values'];
            $akDisplayMultipleValuesOnSelect = $akey->type['display-multiple-values'];
            $akSelectOptionDisplayOrder = $akey->type['display-order'];
            $akSelectAllowOtherValues = $akey->type['allow-other-values'];
            $akHideNoneOption = $akey->type['hide-none-option'];

            $type->setAllowMultipleValues(((string) $akSelectAllowMultipleValues) == '1' ? true : false);
            $type->setDisplayOrder($akSelectOptionDisplayOrder);
            $type->setAllowOtherValues(((string) $akSelectAllowOtherValues) == '1' ? true : false);
            $type->setDisplayMultipleValuesOnSelect(((string) $akDisplayMultipleValuesOnSelect) == '1' ? true : false);
            $type->setHideNoneOption(((string) $akHideNoneOption) == '1' ? true : false);

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

    public function form()
    {
        $this->load();
        $selectedOptions = [];
        $selectedOptionIDs = [];
        if ($this->akSelectAllowOtherValues) {
            // This is the fancy auto complete, which uses an irritating way of handling
            // IDs so that we can discern whether something is an existing selected option
            // vs just a number that happens to match that option's ID.
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $options = $this->loadSelectedTagValueFromPost($this->request('atSelectOptionValue'));
                foreach ($options as $opt) {
                    $selectedOptions[] = ['id' => $opt->id, 'text' => $opt->text];
                    $selectedOptionIDs[] = $opt->id;
                }
            } else {
                $options = $this->getSelectedOptions();
                foreach ($options as $opt) {
                    $selectedOptions[] = ['id' => 'SelectAttributeOption:' . $opt->getSelectAttributeOptionID(), 'text' => $opt->getSelectAttributeOptionValue()];
                    $selectedOptionIDs[] = 'SelectAttributeOption:' . $opt->getSelectAttributeOptionID();
                }
            }
        } else {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $options = $this->loadSelectedTagValueFromPost($this->request('atSelectOptionValue'));
                foreach ($options as $opt) {
                    $selectedOptionIDs[] = $opt->id;
                }
            } else {
                $options = $this->getSelectedOptions();
                foreach ($options as $opt) {
                    $selectedOptionIDs[] = $opt->getSelectAttributeOptionID();
                }
            }
        }
        $this->set('selectedOptionIDs', $selectedOptionIDs);
        $this->set('selectedOptions', $selectedOptions);
        $this->requireAsset('selectize');
    }

    public function search()
    {
        $this->load();
        $selectedOptions = $this->request('atSelectOptionID');
        if (!is_array($selectedOptions)) {
            $selectedOptions = [];
        }
        $this->set('selectedOptions', $selectedOptions);
    }

    public function createAttributeValueFromRequest()
    {
        $data = $this->post();
        $this->load();

        $akSelectAllowMultipleValues = $this->akSelectAllowMultipleValues;
        $akSelectAllowOtherValues = $this->akSelectAllowOtherValues;
        $keyType = $this->attributeKey->getAttributeKeySettings();
        $optionList = $keyType->getOptionList();
        if (!$akSelectAllowMultipleValues && !$akSelectAllowOtherValues) {
            // select list. Only one option possible. No new options.
            $option = $this->getOptionByID($data['atSelectOptionValue']);
            if (is_object($option)) {
                return $this->createAttributeValue($option);
            } else {
                return $this->createAttributeValue(null);
            }
        } else {
            if ($akSelectAllowMultipleValues && !$akSelectAllowOtherValues) {
                // checkbox list.  No new options.
                $options = [];
                if (is_array($data['atSelectOptionValue'])) {
                    foreach ($data['atSelectOptionValue'] as $optionID) {
                        $option = $this->getOptionByID($optionID);
                        if (is_object($option)) {
                            $options[] = $option;
                        }
                    }
                }

                return $this->createAttributeValue($options);
            } else {
                if (!$akSelectAllowMultipleValues && $akSelectAllowOtherValues) {
                    // The post comes through in the select2 format. Either a SelectAttributeOption:ID item
                    // or a new item.
                    $option = false;
                    if ($data['atSelectOptionValue']) {
                        if (preg_match('/SelectAttributeOption\:(.+)/i',
                            $data['atSelectOptionValue'], $matches)) {
                            $option = $this->getOptionByID($matches[1]);
                        } else {
                            $option = $this->getOptionByValue(trim($data['atSelectOptionValue']), $this->attributeKey);
                            if (!is_object($option)) {
                                $displayOrder = 0;
                                if ($optionList) {
                                    $displayOrder = count($optionList->getOptions());
                                }
                                $option = new SelectValueOption();
                                $option->setOptionList($optionList);
                                $option->setIsEndUserAdded(true);
                                $option->setDisplayOrder($displayOrder);
                                $option->setSelectAttributeOptionValue(trim($data['atSelectOptionValue']));
                            }
                        }
                    }
                    if (is_object($option)) {
                        return $this->createAttributeValue($option);
                    } else {
                        return $this->createAttributeValue(null);
                    }
                } else {
                    if ($akSelectAllowMultipleValues && $akSelectAllowOtherValues) {
                        // The post comes through in the select2 format. A comma-separated
                        // list of SelectAttributeOption:ID items and new items.
                        $options = [];
                        if ($data['atSelectOptionValue']) {
                            foreach (explode(',', $data['atSelectOptionValue']) as $value) {
                                if (preg_match('/SelectAttributeOption\:(.+)/i', $value, $matches)) {
                                    $option = $this->getOptionByID($matches[1]);
                                } else {
                                    $option = $this->getOptionByValue(trim($value), $this->attributeKey);
                                    if (!is_object($option)) {
                                        $displayOrder = 0;
                                        if ($optionList) {
                                            $displayOrder = count($optionList->getOptions());
                                        }
                                        $option = new SelectValueOption();
                                        $option->setOptionList($optionList);
                                        $option->setDisplayOrder($displayOrder);
                                        $option->setSelectAttributeOptionValue(trim($value));
                                        $option->setIsEndUserAdded(true);
                                    }
                                }

                                if (is_object($option)) {
                                    $options[] = $option;
                                }
                            }
                        }

                        if (count($options)) {
                            return $this->createAttributeValue($options);
                        } else {
                            return $this->createAttributeValue(null);
                        }
                    }
                }
            }
        }
    }

    public function importValue(\SimpleXMLElement $akv)
    {
        if (isset($akv->value)) {
            $vals = [];
            foreach ($akv->value->children() as $ch) {
                $vals[] = (string) $ch;
            }

            return $this->createAttributeValue($vals);
        }
    }

    public function getOptionByID($id)
    {
        $orm = $this->entityManager;
        $repository = $orm->getRepository('\Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption');
        $option = $repository->findOneBy([
            'avSelectOptionID' => $id,
        ]);

        return $option;
    }

    public function getOptionByValue($value, $attributeKey = false)
    {
        $orm = \Database::connection()->getEntityManager();
        $repository = $orm->getRepository('\Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption');
        if ($attributeKey) {
            $existingList = $attributeKey->getAttributeKeySettings()->getOptionList();
        }
        if (isset($existingList) && is_object($existingList)) {
            $option = $repository->findOneBy([
                'list' => $existingList,
                'value' => $value,
            ]);
        } else {
            $option = $repository->findOneByValue($value);
        }

        return $option;
    }

    /**
     * Sets select options for a particular attribute
     * If the $value == string, then 1 item is selected
     * if array, then multiple, but only if the attribute in question is a select multiple
     * Note, items CANNOT be added to the pool (even if the attribute allows it) through this process.
     * Items should now be added to the database if they don't exist already & if the allow checkbox is
     * checked under the attribute settings
     * Code from this bug - http://www.concrete5.org/index.php?cID=595692.
     *
     * @param mixed $value
     */
    public function createAttributeValue($value)
    {
        $this->load();

        $options = [];

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

    public function validateValue()
    {
        return is_object($value = $this->getAttributeValue()->getValue()) && ((string) $value != '');
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
        $db = $this->entityManager->getConnection();
        if (!is_array($options)) {
            return $list;
        }
        $optionQuery = [];
        foreach ($options as $id) {
            if ($id > 0) {
                $opt = $this->getOptionByID($id);
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
        if ($this->attributeValue && $this->attributeValue->getValue()) {
            $list = $this->attributeValue->getValue()->getSelectedOptions();
            foreach ($list as $l) {
                $str .= $l . "\n";
            }
        }
        // remove line break for empty list
        if ($str == "\n") {
            return '';
        }

        return $str;
    }

    public function getSelectedOptions()
    {
        if ($this->attributeValue && $this->attributeValue->getValue()) {
            return $this->attributeValue->getValue()->getSelectedOptions();
        }

        return [];
    }

    public function action_load_autocomplete_values()
    {
        $this->load();
        $values = [];
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
        $q = 'select atSelectOptions.value, atSelectOptions.avSelectOptionID, count(atSelectOptions.avSelectOptionID) as total from Pages inner join CollectionVersions on (Pages.cID = CollectionVersions.cID and CollectionVersions.cvIsApproved = 1) inner join CollectionAttributeValues on (CollectionVersions.cID = CollectionAttributeValues.cID and CollectionVersions.cvID = CollectionAttributeValues.cvID) inner join atSelectOptionsSelected on (atSelectOptionsSelected.avID = CollectionAttributeValues.avID) inner join atSelectOptions on atSelectOptionsSelected.avSelectOptionID = atSelectOptions.avSelectOptionID where Pages.cIsActive = 1 and CollectionAttributeValues.akID = ? ';
        $v = [$this->attributeKey->getAttributeKeyID()];
        if (is_object($parentPage)) {
            $v[] = $parentPage->getCollectionID();
            $q .= 'and cParentID = ?';
        }
        $q .= ' group by avSelectOptionID order by total desc limit ' . $limit;
        $r = $db->Execute($q, $v);
        $options = new ArrayCollection();
        while ($row = $r->FetchRow()) {
            $opt = new SelectValueUsedOption();
            $opt->setSelectAttributeOptionValue($row['value']);
            $opt->setSelectAttributeOptionID($row['avSelectOptionID']);
            $opt->setSelectAttributeOptionUsageCount($row['total']);
            $options->add($opt);
        }

        return $options;
    }

    public function filterByAttribute(AttributedItemList $list, $value, $comparison = '=')
    {
        $em = \Database::connection()->getEntityManager();
        if ($value instanceof SelectValueOption) {
            $option = $value;
        } else {
            $option = $em->getRepository('\Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption')
                ->findOneByValue($value);
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
     * @param null|mixed $keywords
     */
    public function getOptions($keywords = null)
    {
        if (!isset($this->attributeKey)) {
            return [];
        }

        if (!is_object($this->attributeKey->getAttributeKeySettings())) {
            return [];
        }

        if (!isset($this->akSelectOptionDisplayOrder)) {
            $this->load();
        }

        $type = $this->attributeKey->getAttributeKeySettings();

        $em = \Database::get()->getEntityManager();
        $r = $em->getRepository('\Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption');
        $builder = $r->createQueryBuilder('v');
        $builder->where('v.list = :list');
        $builder->andWhere('v.isDeleted = false');
        if ($keywords) {
            $builder->andWhere($builder->expr()->like('v.value', ':value'));
            $builder->setParameter('value', $keywords . '%');
        }
        $builder->setParameter('list', $type->getOptionList());
        $options = [];
        switch ($this->akSelectOptionDisplayOrder) {
            case 'popularity_desc':
                $builder->addSelect('count(va.generic_value) as total');
                $builder->leftJoin('v.values', 'va');
                $builder->groupBy('v.avSelectOptionID');
                $builder->orderBy('total', 'desc');
                $r = $builder->getQuery()->getResult();
                foreach ($r as $option) {
                    $options[] = $option[0];
                }
                break;
            case 'alpha_asc':
                $builder->orderBy('v.value', 'asc');
                $options = $builder->getQuery()->getResult();
                break;
            default:
                $builder->orderBy('v.displayOrder', 'asc');
                $options = $builder->getQuery()->getResult();
                break;
        }

        return $options;
    }

    public function saveKey($data)
    {
        /**
         * @var SelectSettings
         */
        $type = $this->getAttributeKeySettings();
        $optionList = $type->getOptionList();

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
        if (isset($data['akDisplayMultipleValuesOnSelect']) && ($data['akDisplayMultipleValuesOnSelect'] == 1)) {
            $akDisplayMultipleValuesOnSelect = 1;
        } else {
            $akDisplayMultipleValuesOnSelect = 0;
        }

        if (isset($data['akSelectOptionDisplayOrder']) && in_array($data['akSelectOptionDisplayOrder'],
                ['display_asc', 'alpha_asc', 'popularity_desc'])
        ) {
            $akSelectOptionDisplayOrder = $data['akSelectOptionDisplayOrder'];
        } else {
            $akSelectOptionDisplayOrder = 'display_asc';
        }

        if (isset($data['akHideNoneOption']) && ($data['akHideNoneOption'] == 1)) {
            $akHideNoneOption = 1;
        } else {
            $akHideNoneOption = 0;
        }

        $type->setAllowMultipleValues((bool) $akSelectAllowMultipleValues);
        $type->setDisplayOrder($akSelectOptionDisplayOrder);
        $type->setAllowOtherValues((bool) $akSelectAllowOtherValues);
        $type->setDisplayMultipleValuesOnSelect((bool) $akDisplayMultipleValuesOnSelect);
        $type->setHideNoneOption((bool) $akHideNoneOption);

        $selectedPostValues = $this->getSelectValuesFromPost();

        // handle removing existing options that aren't in the post.
        foreach ($optionList->getOptions() as $option) {
            if (!in_array($option, $selectedPostValues)) {
                $option->setIsOptionDeleted(true);
            }
        }

        // handle adding the options.
        foreach ($selectedPostValues as $option) {
            if (!$option->getSelectAttributeOptionID()) { // this is a new option.
                $option->setOptionList($optionList);
                $optionList->getOptions()->add($option);
            }
        }

        $type->setOptionList($optionList);

        return $type;
    }

    /**
     * Convenience methods to retrieve a select attribute key's settings.
     */
    public function getAllowMultipleValues()
    {
        if ($this->akSelectAllowMultipleValues === null) {
            $this->load();
        }

        return $this->akSelectAllowMultipleValues;
    }

    public function getAllowOtherValues()
    {
        if ($this->akSelectAllowOtherValues === null) {
            $this->load();
        }

        return $this->akSelectAllowOtherValues;
    }

    public function getOptionDisplayOrder()
    {
        if ($this->akSelectOptionDisplayOrder === null) {
            $this->load();
        }

        return $this->akSelectOptionDisplayOrder;
    }

    public function getAttributeKeySettingsClass()
    {
        return SelectSettings::class;
    }

    public function getControlID()
    {
        return $this->field('atSelectOptionValue');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\SimpleTextExportableAttributeInterface::getAttributeValueTextRepresentation()
     */
    public function getAttributeValueTextRepresentation()
    {
        $value = $this->getAttributeValueObject();
        if ($value === null) {
            $result = '';
        } else {
            $options = [];
            foreach ($value->getSelectedOptions() as $selectedOption) {
                /* @var SelectValueOption $selectedOption */
                $options[] = $selectedOption->getSelectAttributeOptionValue();
            }
            $result = implode("\n", $options);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\SimpleTextExportableAttributeInterface::updateAttributeValueFromTextRepresentation()
     */
    public function updateAttributeValueFromTextRepresentation($textRepresentation, ErrorList $warnings)
    {
        $value = $this->getAttributeValueObject();
        $optionTexts = explode("\n", $textRepresentation);
        $optionTexts = array_filter(
            array_map('trim', $optionTexts),
            function ($optionText) {
                return $optionText !== '';
            }
        );
        $numOptions = count($optionTexts);
        if ($numOptions === 0) {
            if ($value !== null) {
                $value->getSelectedOptions()->clear();
            }
        } else {
            $settings = $this->getAttributeKeySettings();
            /* @var SelectSettings $settings */
            if ($numOptions > 1 && !$settings->getAllowMultipleValues()) {
                $warnings->add(t('The Select Attribute with handle "%s" does not support multiple options: only the first one will be imported', $this->attributeKey->getAttributeKeyHandle()));
                $firstOptionText = array_shift($optionTexts);
                $optionTexts = [$firstOptionText];
                $numOptions = 1;
            }
            $optionList = $settings->getOptionList();
            $firstOption = true;
            foreach ($optionTexts as $optionText) {
                $selectedOption = null;
                $deletedOptions = [];
                foreach ($optionList->getOptions() as $option) {
                    if ($option->isOptionDeleted()) {
                        $deletedOptions[$option->getSelectAttributeOptionValue()] = $option;
                    } elseif ($option->getSelectAttributeOptionValue() === $optionText) {
                        $selectedOption = $option;
                        break;
                    }
                }
                if ($selectedOption === null) {
                    if ($settings->getAllowOtherValues()) {
                        $displayOrder = 0;
                        if ($optionList) {
                            $displayOrder = count($optionList->getOptions());
                        }
                        $selectedOption = new SelectValueOption();
                        $selectedOption->setOptionList($optionList);
                        $selectedOption->setIsEndUserAdded(true);
                        $selectedOption->setDisplayOrder($displayOrder);
                        $selectedOption->setSelectAttributeOptionValue($optionText);
                        $optionList->getOptions()->add($selectedOption);
                    } elseif (isset($deletedOptions[$optionText])) {
                        $selectedOption = $deletedOptions[$optionText];
                        $warnings->add(t('"%1$s" matches a deleted option the Select Attribute with handle "%2$s"', $optionText, $this->attributeKey->getAttributeKeyHandle()));
                    } else {
                        $warnings->add(t('"%1$s" is not a valid option for the Select Attribute with handle "%2$s"', $optionText, $this->attributeKey->getAttributeKeyHandle()));
                    }
                }
                if ($selectedOption !== null) {
                    if ($value === null) {
                        $value = new SelectValue();
                    }
                    if ($firstOption) {
                        $firstOption = false;
                        $value->getSelectedOptions()->clear();
                    }
                    $value->getSelectedOptions()->add($selectedOption);
                }
            }
        }

        return $value;
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

        /**
         * @var SelectSettings
         */
        $type = $ak->getAttributeKeySettings();
        if (is_object($type)) {
            $this->akSelectAllowMultipleValues = $type->getAllowMultipleValues();
            $this->akSelectAllowOtherValues = $type->getAllowOtherValues();
            $this->akSelectOptionDisplayOrder = $type->getDisplayOrder();
            $this->akDisplayMultipleValuesOnSelect = $type->getDisplayMultipleValuesOnSelect();
            $this->akHideNoneOption = $type->getHideNoneOption();

            $this->set('akDisplayMultipleValuesOnSelect', $this->akDisplayMultipleValuesOnSelect);
            $this->set('akSelectAllowMultipleValues', $this->akSelectAllowMultipleValues);
            $this->set('akSelectAllowOtherValues', $this->akSelectAllowOtherValues);
            $this->set('akSelectOptionDisplayOrder', $this->akSelectOptionDisplayOrder);
            $this->set('akHideNoneOption', $this->akHideNoneOption);
        }
    }

    /**
     * Used by selectize. Automatically takes a value request and converts it into tag/text key value pairs.
     * New options are just text/tag, whereas existing ones are SelectAttributeOption:ID/text.
     *
     * @param mixed $value
     */
    protected function loadSelectedTagValueFromPost($value)
    {
        $em = \Database::get()->getEntityManager();
        $r = $em->getRepository('\Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption');
        $type = $this->attributeKey->getAttributeKeySettings();

        if (!is_array($value)) {
            $values = explode(',', $value);
        } else {
            $values = $value;
        }
        
        $response = [];
        foreach ($values as $value) {
            $value = trim($value);
            $o = new \stdClass();
            if (strpos($value, 'SelectAttributeOption:') === 0) {
                $optionID = substr($value, 22);
                $option = $r->findOneBy(['list' => $type->getOptionList(), 'avSelectOptionID' => $optionID]);
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

        return $response;
    }

    private function getSelectValuesFromPost()
    {
        $displayOrder = 0;
        $options = [];
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
                $opt = $this->getOptionByID($id);
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
}
