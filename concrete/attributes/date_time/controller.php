<?php
namespace Concrete\Attribute\DateTime;

use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Entity\Attribute\Key\Settings\DateTimeSettings;
use Concrete\Core\Entity\Attribute\Value\Value\DateTimeValue;
use Concrete\Core\Attribute\Controller as AttributeTypeController;
use DateTime;
use Exception;

class Controller extends AttributeTypeController
{
    public $helpers = ['form', 'date','form/date_time'];

    protected $searchIndexFieldDefinition = ['type' => 'datetime', 'options' => ['notnull' => false]];

    protected $akUseNowIfEmpty = null;
    protected $akDateDisplayMode = null;
    protected $akTimeResolution = null;

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('clock-o');
    }

    public function saveKey($data)
    {
        if (!is_array($data)) {
            $data = [];
        }
        $data += [
            'akUseNowIfEmpty' => false,
        ];
        $type = $this->getAttributeKeySettings();
        $type->setUseNowIfEmpty($data['akUseNowIfEmpty']);
        $type->setMode($data['akDateDisplayMode']);
        if (isset($data['akTimeResolution'])) {
            $type->setTimeResolution($data['akTimeResolution']);
        }

        return $type;
    }

    public function type_form()
    {
        $this->load();
    }

    public function getSearchIndexValue()
    {
        $datetime = $this->getDateTime();

        return ($datetime === null) ? null : $datetime->format('Y-m-d H:i:s');
    }

    public function searchForm($list)
    {
        $dateFrom = $this->request('from');
        $dateTo = $this->request('to');
        if ($dateFrom) {
            $dateFrom = date('Y-m-d', strtotime($dateFrom));
            $list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), $dateFrom, '>=');
        }
        if ($dateTo) {
            $dateTo = date('Y-m-d', strtotime($dateTo));
            $list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), $dateTo, '<=');
        }

        return $list;
    }

    public function form()
    {
        $this->load();
        $datetime = $this->getDateTime();
        if ($datetime === null && $this->akUseNowIfEmpty) {
            $datetime = new DateTime();
        }
        $this->set('value', $datetime);
        $this->set('displayMode',$this->akDateDisplayMode );
        $this->set('timeResolution', $this->akTimeResolution);

    }

    public function exportKey($akey)
    {
        $this->load();
        $type = $akey->addChild('type');
        $type->addAttribute('use-now-if-empty', $this->akUseNowIfEmpty ? 1 : 0);
        $type->addAttribute('mode', $this->akDateDisplayMode);
        $type->addAttribute('time-resolution', $this->akTimeResolution);

        return $akey;
    }

    public function importKey(\SimpleXMLElement $akey)
    {
        $type = $this->getAttributeKeySettings();
        if (isset($akey->type)) {
            $type->setUseNowIfEmpty($akey->type['use-now-if-empty']);
            $type->setMode($akey->type['mode']);
            $type->setTimeResolution($akey->type['time-resolution']);
        }

        return $type;
    }

    public function validateValue()
    {
        $v = $this->getAttributeValue()->getValue();

        return $v != false;
    }

    public function validateForm($data)
    {
        if ($this->akDateDisplayMode === null) {
            $this->load();
        }
        switch ($this->akDateDisplayMode) {
            case 'date_time':
                if (empty($data['value_dt']) || (!is_numeric($data['value_h'])) || (!is_numeric($data['value_m']))) {
                    return false;
                }
                $dh = $this->app->make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
                switch ($dh->getTimeFormat()) {
                    case 12:
                        if (empty($data['value_a'])) {
                            return false;
                        }
                        break;
                }

                return true;
            default:
                return $data['value'] != '';
        }
    }

    public function search()
    {
        $dt = $this->app->make('helper/form/date_time');
        $html = $dt->date($this->field('from'), $this->request('from'), true);
        $html .= ' ' . t('to') . ' ';
        $html .= $dt->date($this->field('to'), $this->request('to'), true);
        echo $html;
    }

    public function getAttributeValueClass()
    {
        return DateTimeValue::class;
    }

    public function exportValue(\SimpleXMLElement $akv)
    {
        $value = null;
        if (isset($this->attributeValue)) {
            $object = $this->attributeValue->getValueObject();
            if ($object) {
                $datetime = $object->getValue();
                if ($datetime) {
                    $value = $datetime->format('Y-m-d H:i:s');
                }

            }
        }
        $akv->addChild('value', $value);
    }

    public function createAttributeValue($value)
    {
        if ($value) {
            if (!($value instanceof DateTime)) {
                $timestamp = strtotime($value);
                $value = new DateTime(date('Y-m-d H:i:s', $timestamp));
            }
        } else {
            $value = null;
        }

        $av = new DateTimeValue();
        $av->setValue($value);

        return $av;
    }

    public function createAttributeValueFromRequest()
    {
        $this->load();
        $data = $this->post();
        $datetime = null;
        switch ($this->akDateDisplayMode) {
            case 'text':
                if (isset($data['value']) && is_string($data['value']) && $data['value'] !== '') {
                    $dh = $this->app->make('helper/date');
                    try {
                        $datetime = DateTime::createFromFormat(
                            $dh->getPHPDateTimePattern(),
                            $data['value'],
                            $dh->getTimezone('user')
                        );
                    } catch (Exception $x) {
                    }
                    if ($datetime !== null) {
                        $datetime->setTimezone($dh->getTimezone('system'));
                    }
                }
                break;
            case 'date':
            case 'date_time':
            default:
                $dt = $this->app->make('helper/form/date_time');
                /* @var \Concrete\Core\Form\Service\Widget\DateTime $dt */
                $datetime = $dt->translate('value', $data, true);
                break;
        }

        return $this->createAttributeValue($datetime);
    }

    protected function load()
    {
        $ak = $this->getAttributeKey();
        if (!is_object($ak)) {
            return false;
        }

        $type = $ak->getAttributeKeySettings();
        /*
         * @var $type DateTimeType
         */

        $this->akUseNowIfEmpty = $type->getUseNowIfEmpty();
        $this->set('akUseNowIfEmpty', $this->akUseNowIfEmpty);
        $this->akDateDisplayMode = (string) $type->getMode();
        $this->set('akDateDisplayMode', $this->akDateDisplayMode);
        $this->akTimeResolution = $type->getTimeResolution();
        $this->set('akTimeResolution', $this->akTimeResolution);
    }

    public function getAttributeKeySettingsClass()
    {
        return DateTimeSettings::class;
    }

    /**
     * Retrieve the date/time value.
     *
     * @return DateTime|null
     */
    protected function getDateTime()
    {
        $result = null;
        if ($this->attributeValue) {
            $valueObject = $this->getAttributeValue();
            if ($valueObject !== null) {
                $dateTime = $valueObject->getValue();
                if ($dateTime instanceof DateTime) {
                    $result = $dateTime;
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see AttributeTypeController::getDisplayValue()
     */
    public function getDisplayValue()
    {
        $result = '';
        $datetime = $this->getDateTime();
        if ($datetime !== null) {
            if ($this->akDateDisplayMode === null) {
                $this->load();
            }
            $dh = $this->app->make('helper/date');
            /* @var \Concrete\Core\Localization\Service\Date $dh */
            switch ($this->akDateDisplayMode) {
                case 'date':
                    $result = $dh->formatDate($datetime, 'short', $datetime->getTimezone());
                    break;
                case 'text':
                case 'date_time':
                default:
                    $result = $dh->formatDateTime($datetime);
                    break;
            }
        }

        return $result;
    }
}
