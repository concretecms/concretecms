<?php
namespace Concrete\Attribute\DateTime;

use Concrete\Core\Attribute\Controller as AttributeTypeController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Attribute\SimpleTextExportableAttributeInterface;
use Concrete\Core\Entity\Attribute\Key\Settings\DateTimeSettings;
use Concrete\Core\Entity\Attribute\Value\Value\DateTimeValue;
use Concrete\Core\Error\ErrorList\Error\Error;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\ErrorList\Field\AttributeField;
use DateTime;
use Exception;

class Controller extends AttributeTypeController implements SimpleTextExportableAttributeInterface
{
    public $helpers = ['form', 'date', 'form/date_time'];

    protected $searchIndexFieldDefinition = ['type' => 'datetime', 'options' => ['notnull' => false]];

    protected $akUseNowIfEmpty = null;
    protected $akDateDisplayMode = null;
    protected $akTextCustomFormat = null;
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
        $type->setTextCustomFormat(isset($data['akTextCustomFormat']) ? $data['akTextCustomFormat'] : '');
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

        return (null === $datetime) ? null : $datetime->format('Y-m-d H:i:s');
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
        if (null === $datetime && $this->akUseNowIfEmpty) {
            $datetime = new DateTime();
        }
        $this->set('value', $datetime);
        $this->set('displayMode', $this->akDateDisplayMode);
        $this->set('textCustomFormat', $this->akTextCustomFormat);
        $this->set('timeResolution', $this->akTimeResolution);
    }

    public function exportKey($akey)
    {
        $this->load();
        $type = $akey->addChild('type');
        $type->addAttribute('use-now-if-empty', $this->akUseNowIfEmpty ? 1 : 0);
        $type->addAttribute('mode', $this->akDateDisplayMode);
        $type->addAttribute('text-custom-format', $this->akTextCustomFormat);
        $type->addAttribute('time-resolution', $this->akTimeResolution);

        return $akey;
    }

    public function importKey(\SimpleXMLElement $akey)
    {
        $type = $this->getAttributeKeySettings();
        if (isset($akey->type)) {
            $type->setUseNowIfEmpty($akey->type['use-now-if-empty']);
            $type->setMode($akey->type['mode']);
            $type->setTextCustomFormat(isset($akey->type['text-custom-format']) ? $akey->type['text-custom-format'] : '');
            $type->setTimeResolution($akey->type['time-resolution']);
        }

        return $type;
    }

    public function validateValue()
    {
        $v = $this->getAttributeValue()->getValue();

        return false != $v;
    }

    public function validateForm($data)
    {
        $required = $this->getAttributeKey()->getAkIsRequired();
        if (!$required) {
            return true;
        } else {
            $dataPosted = $this->post();
            $original_value = array_key_exists('value', $dataPosted) ? $dataPosted['value'] : false;
            $value = $data['value']->getValue();
            if (!$original_value) {
                return new Error(t($this->getAttributeKey()->getAttributeKeyName() . ' requires a valid date time no date given'),
                    new
                AttributeField($this->getAttributeKey()));
            }
            if (null === $this->akDateDisplayMode) {
                $this->load();
            }
            switch ($this->akDateDisplayMode) {
                case 'date_time':
                case 'date':
                    if (!$value instanceof DateTime) {
                        return new Error(t($this->getAttributeKey()->getAttributeKeyName() . ' requires a valid date time'), new
                        AttributeField($this->getAttributeKey()));
                    }
                    break;
                case 'date_text':
                case 'text':
                $dh = $this->app->make('helper/date');
                $format = $this->getAttributeKeySettings()->getTextCustomFormat();
                if (!$format) {
                    // get default format we want to implement
                    if ('date_text' === $this->akDateDisplayMode) {
                        $format = $dh->getPHPDatePattern();
                    } else {
                        $format = $dh->getPHPDateTimePattern();
                    }
                }

                $parsed = false;
                if ($original_value && $format) {
                    $parsed = DateTime::createFromFormat(
                        $format,
                        $original_value,
                        $dh->getTimezone('user')
                    );
                }
                if (!$parsed && $format) {
                    $now = (new DateTime())->format('Y-m-d H:i:s');
                    $formattedDateExample = DateTime::createFromFormat('Y-m-d H:i:s', $now);
                    $formattedDateExample = $formattedDateExample->format($format);

                    return new Error(t($this->getAttributeKey()->getAttributeKeyName() . ' should match the following format e.g. ' .
                        $formattedDateExample, new AttributeField($this->getAttributeKey())));
                }

                break;
            }
        }

        return true;
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
            case 'date_text':
                if (isset($data['value']) && is_string($data['value']) && '' !== $data['value']) {
                    $dh = $this->app->make('helper/date');
                    if ('' !== $this->akTextCustomFormat) {
                        $format = $this->akTextCustomFormat;
                    } elseif ('date_text' === $this->akDateDisplayMode) {
                        $format = $dh->getPHPDatePattern();
                    } else {
                        $format = $dh->getPHPDateTimePattern();
                    }

                    try {
                        $parsed = DateTime::createFromFormat(
                            $format,
                            $data['value'],
                            $dh->getTimezone('user')
                        );

                        if ($parsed) {
                            if ('date_text' !== $this->akDateDisplayMode) {
                                $parsed->setTimezone($dh->getTimezone('system'));
                            }
                            $datetime = $parsed;
                            $datetime->original_value = $data['value'];
                            $datetime->date_format = $format;
                        }
                    } catch (Exception $x) {
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

    public function getAttributeKeySettingsClass()
    {
        return DateTimeSettings::class;
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
        if (null !== $datetime) {
            if (null === $this->akDateDisplayMode) {
                $this->load();
            }
            $dh = $this->app->make('helper/date');
            /* @var \Concrete\Core\Localization\Service\Date $dh */
            switch ($this->akDateDisplayMode) {
                case 'date':
                    $result = $dh->formatDate($datetime, 'short', $datetime->getTimezone());
                    break;
                case 'date_text':
                    if ('' === $this->akTextCustomFormat) {
                        $result = $dh->formatDate($datetime, 'short', $datetime->getTimezone());
                    } else {
                        $result = $dh->formatCustom($this->akTextCustomFormat, $datetime, $datetime->getTimezone());
                    }
                    break;
                case 'text':
                    if ('' === $this->akTextCustomFormat) {
                        $result = $dh->formatDateTime($datetime);
                    } else {
                        $result = $dh->formatCustom($this->akTextCustomFormat, $datetime);
                    }
                    break;
                case 'date_time':
                default:
                    $result = $dh->formatDateTime($datetime);
                    break;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\SimpleTextExportableAttributeInterface::getAttributeValueTextRepresentation()
     */
    public function getAttributeValueTextRepresentation()
    {
        $dateTime = $this->getDateTime();
        if (null === $dateTime) {
            $result = '';
        } else {
            if (!isset($this->akDateDisplayMode)) {
                $this->load();
            }
            switch ($this->akDateDisplayMode) {
                case 'date':
                case 'date_text':
                    $result = $dateTime->format('Y-m-d');
                    break;
                case 'date_time':
                case 'text':
                default:
                    // Let's convert the date/time from the system timezone to the website default timezone
                    $toTimezone = $this->app->make('date')->getTimezone('app');
                    $dateTime = clone $dateTime;
                    $dateTime->setTimezone($toTimezone);
                    $result = $dateTime->format('Y-m-d H:i:s');
                    break;
            }
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
        if ('' === $textRepresentation) {
            if (null !== $value) {
                $value->setValue(null);
            }
        } else {
            if (!isset($this->akDateDisplayMode)) {
                $this->load();
            }
            switch ($this->akDateDisplayMode) {
                case 'date':
                case 'date_text':
                    $dateTime = @DateTime::createFromFormat('Y-m-d', $textRepresentation);
                    break;
                case 'date_time':
                case 'text':
                default:
                    $dateTime = @DateTime::createFromFormat('Y-m-d H:i:s', $textRepresentation);
                    if ($dateTime) {
                        $toTimezone = $this->app->make('date')->getTimezone('system');
                        $dateTime->setTimezone($toTimezone);
                    }
                    break;
            }
            if (!$dateTime) {
                $warnings->add(t('"%1$s" is not a valid date value for the attribute with handle %2$s', $textRepresentation, $this->attributeKey->getAttributeKeyHandle()));
            } else {
                if (null === $value) {
                    $value = $this->createAttributeValue($dateTime);
                } else {
                    $value->setValue($dateTime);
                }
            }
        }

        return $value;
    }

    protected function load()
    {
        $ak = $this->getAttributeKey();
        if (!is_object($ak)) {
            return false;
        }

        $type = $ak->getAttributeKeySettings();
        /* @var DateTimeType $type */
        $this->akUseNowIfEmpty = $type->getUseNowIfEmpty();
        $this->set('akUseNowIfEmpty', $this->akUseNowIfEmpty);
        $this->akDateDisplayMode = (string) $type->getMode();
        $this->set('akDateDisplayMode', $this->akDateDisplayMode);
        $this->akTextCustomFormat = $type->getTextCustomFormat();
        $this->set('akTextCustomFormat', $this->akTextCustomFormat);
        $this->akTimeResolution = $type->getTimeResolution();
        $this->set('akTimeResolution', $this->akTimeResolution);
        $this->set('akIsRequired', $this->getAttributeKey()->getAkIsRequired());
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
            if (null !== $valueObject) {
                $dateTime = $valueObject->getValue();
                if ($dateTime instanceof DateTime) {
                    $result = $dateTime;
                }
            }
        }

        return $result;
    }
}
