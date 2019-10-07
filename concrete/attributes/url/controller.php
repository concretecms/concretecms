<?php
namespace Concrete\Attribute\Url;

use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Attribute\DefaultController;
use Concrete\Core\Entity\Attribute\Key\Settings\UrlSettings;

class Controller extends DefaultController
{
    protected $akUrlPlaceholder;
    public $helpers = ['form'];

    public function saveKey($data)
    {
        $type = $this->getAttributeKeySettings();
        $data += [
            'akUrlPlaceholder' => null,
        ];

        $akUrlPlaceholder = $data['akUrlPlaceholder'];
        $type->setPlaceholder($akUrlPlaceholder);

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

        $akUrlPlaceholder = '';
        if (isset($this->akUrlPlaceholder)) {
            $akUrlPlaceholder = $this->akUrlPlaceholder;
        }
        $this->set('akUrlPlaceholder', $akUrlPlaceholder);
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
         * @var $type UrlSettings
         */
        $this->akUrlPlaceholder = $type->getPlaceholder();
        $this->set('akUrlPlaceholder', $type->getPlaceholder());
    }

    public function exportKey($akey)
    {
        $this->load();
        $akey->addChild('type')->addAttribute('placeholder', $this->akUrlPlaceholder);

        return $akey;
    }

    public function importKey(\SimpleXMLElement $akey)
    {
        $type = $this->getAttributeKeySettings();
        if (isset($akey->type)) {
            $data['akUrlPlaceholder'] = $akey->type['placeholder'];
            $type->setPlaceholder((string) $akey->type['placeholder']);
        }

        return $type;
    }

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('link');
    }

    public function getAttributeKeySettingsClass()
    {
        return UrlSettings::class;
    }
}
