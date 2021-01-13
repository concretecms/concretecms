<?php

namespace Concrete\Attribute\Duration;

use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Controller as AttributeTypeController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Entity\Attribute\Key\Settings\DurationSettings;
use Concrete\Core\Entity\Attribute\Value\Value\DurationValue;
use Concrete\Core\Utility\Service\Text;
use /** @noinspection PhpComposerExtensionStubsInspection */
    SimpleXMLElement;

class Controller extends AttributeTypeController
{
    protected $unitTypes = [];
    protected $unitType = 'seconds';

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('clock');
    }

    public function getAttributeKeySettingsClass()
    {
        return DurationSettings::class;
    }

    public function getAttributeValueClass()
    {
        return DurationValue::class;
    }

    /**
     * @param int $value
     * @return bool|AttributeValueInterface|DurationValue
     */
    public function createAttributeValue($value)
    {
        $av = new DurationValue();
        $av->setValue((int)$value);
        return $av;
    }

    public function getSearchIndexValue()
    {
        return $this->getSeconds();
    }

    public function getPlaintextValue()
    {
        $this->load();

        return sprintf("%s %s", $this->getAttributeValue()->getValueObject(), $this->unitTypes[$this->unitType]);
    }

    public function getDisplayValue()
    {
        /** @var Text $textService */
        $textService = $this->app->make(Text::class);
        return $textService->entities($this->getPlaintextValue());
    }

    public function createAttributeValueFromRequest()
    {
        return $this->createAttributeValue($this->post("value"));
    }

    /**
     * @return int
     */
    public function getSeconds()
    {
        $this->load();

        $value = $this->getAttributeValue()->getValueObject();

        if ($value instanceof DurationValue) {
            $unitTypeFactors = [
                'seconds' => 1,
                'minutes' => 60,
                'hours' => 3600,
                'days' => 86400,
                'weeks' => 604800,
                'months' => 2628000,
                'years' => 31556926
            ];

            $unitTypeFactor = $unitTypeFactors[$this->unitType];

            $seconds = ($value->getValue() * $unitTypeFactor);

            return $seconds;
        }

        return 0;
    }

    protected function load()
    {
        $unitTypes = [
            'seconds' => t("Seconds"),
            'minutes' => t("Minutes"),
            'hours' => t("Hours"),
            'days' => t("Days"),
            'weeks' => t("Weeks"),
            'months' => t("Months"),
            'years' => t("Years")
        ];

        $this->unitTypes = $unitTypes;

        $ak = $this->getAttributeKey();

        if (is_object($ak)) {
            /** @var DurationSettings $type */
            $type = $ak->getAttributeKeySettings();

            $this->unitType = $type->getUnitType();
        }

        if (!in_array($this->unitType, array_keys($unitTypes))) {
            $this->unitType = "seconds";
        }

        $this->set('unitType', $this->unitType);
        $this->set("unitTypes", $this->unitTypes);
    }

    public function exportKey($akey)
    {
        $this->load();
        $akey->addChild('type')->addAttribute('unitType', $this->unitType);

        return $akey;
    }

    public function importKey(/** @noinspection PhpComposerExtensionStubsInspection */ SimpleXMLElement $akey)
    {
        /** @var DurationSettings $type */
        $type = $this->getAttributeKeySettings();
        if (isset($akey->type)) {
            $data['unitType'] = $akey->type['unitType'];
            $type->setUnitType((string)$akey->type['unitType']);
        }

        return $type;
    }

    public function saveKey($data)
    {
        /** @var DurationSettings $type */
        $type = $this->getAttributeKeySettings();

        $data += [
            'unitType' => null
        ];

        $type->setUnitType($data['unitType']);

        return $type;
    }

    public function type_form()
    {
        $this->load();
    }

    public function form()
    {
        $this->load();

        $value = null;

        if (is_object($this->attributeValue)) {
            $value = (int)$this->getAttributeValue()->getValue();
        }

        $this->set('value', $value);
    }
}
