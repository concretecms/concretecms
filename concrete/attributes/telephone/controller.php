<?php
namespace Concrete\Attribute\Telephone;

use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Entity\Attribute\Key\Settings\TelephoneSettings;
use Concrete\Core\Attribute\DefaultController;

class Controller extends DefaultController
{
    protected $akTelephonePlaceholder;
    public $helpers = ['form'];

    public function saveKey($data)
    {
        $type = $this->getAttributeKeySettings();
        $data += [
            'akTelephonePlaceholder' => null,
        ];

        $akTelephonePlaceholder = $data['akTelephonePlaceholder'];
        $type->setPlaceholder($akTelephonePlaceholder);

        return $type;
    }

    public function form()
    {
        $this->load();
        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->app->make('helper/text')->entities($this->getAttributeValue()->getValue());
        }
        $this->set('value', $value);

        $akTelephonePlaceholder = '';
        if (isset($this->akTelephonePlaceholder)) {
            $akTelephonePlaceholder = $this->akTelephonePlaceholder;
        }
        $this->set('akTelephonePlaceholder', $akTelephonePlaceholder);
    }

    public function type_form()
    {
        $this->load();
    }

    protected function load()
    {
        $ak = $this->getAttributeKey();
        if (!is_object($ak)) {
            return false;
        }

        $type = $ak->getAttributeKeySettings();
        /**
         * @var $type TelephoneSettings
         */
        $this->akTelephonePlaceholder = $type->getPlaceholder();
        $this->set('akTelephonePlaceholder', $type->getPlaceholder());
    }

    public function composer()
    {
        $this->form();
    }

    public function exportKey($akey)
    {
        $this->load();
        $akey->addChild('type')->addAttribute('placeholder', $this->akTelephonePlaceholder);

        return $akey;
    }

    public function importKey(\SimpleXMLElement $akey)
    {
        $type = $this->getAttributeKeySettings();
        if (isset($akey->type)) {
            $data['akTelephonePlaceholder'] = $akey->type['placeholder'];
            $type->setPlaceholder((string) $akey->type['placeholder']);
        }

        return $type;
    }

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('phone');
    }

    public function getAttributeKeySettingsClass()
    {
        return TelephoneSettings::class;
    }
}
