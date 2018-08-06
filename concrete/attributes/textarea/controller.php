<?php
namespace Concrete\Attribute\Textarea;

use Concrete\Core\Attribute\DefaultController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Entity\Attribute\Key\Settings\TextareaSettings;
use Concrete\Core\Entity\Attribute\Value\Value\TextValue;
use Core;
use Database;

class Controller extends DefaultController
{

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('font');
    }

    protected $akTextareaDisplayMode;
    protected $akTextareaDisplayModeCustomOptions;
    public $helpers = array('form');

    public function saveKey($data)
    {
        $type = $this->getAttributeKeySettings();
        $data += array(
            'akTextareaDisplayMode' => null,
        );
        $akTextareaDisplayMode = $data['akTextareaDisplayMode'];
        if (!$akTextareaDisplayMode) {
            $akTextareaDisplayMode = 'text';
        }
        $options = array();
        if ($akTextareaDisplayMode == 'rich_text_custom') {
            $options = $data['akTextareaDisplayModeCustomOptions'];
        }

        $type->setMode($akTextareaDisplayMode);

        return $type;
    }

    public function getValue()
    {
        $this->load();
        if ($this->akTextareaDisplayMode == 'text') {
            $value = $this->getAttributeValue()->getValueObject();
            return (string)$value;
        }

        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->getAttributeValue()->getValueObject();

            if ($value) {
                $this->load();
                if ($this->akTextareaDisplayMode == 'rich_text') {
                    $value = LinkAbstractor::translateFrom($value);
                }
            }
        }
        return $value;
    }

    public function getDisplayValue()
    {
        $value = $this->getValue();
        if ($this->akTextareaDisplayMode == 'rich_text') {
            return htmLawed($value, array('safe' => 1));
        }

        return htmLawed($value, array('safe' => 1, 'deny_attribute' => 'style'));
    }



    public function form()
    {
        $this->load();
        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->getAttributeValue()->getValue();

            if ($value) {
                if ($this->akTextareaDisplayMode == 'rich_text') {
                    $value = LinkAbstractor::translateFromEditMode($value);
                }
            }
        }
        $this->set('akTextareaDisplayMode', $this->akTextareaDisplayMode);
        $this->set('value', $value);
    }

    public function composer()
    {
        $this->form();
    }

    public function searchForm($list)
    {
        $list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), '%' . $this->request('value') . '%', 'like');

        return $list;
    }

    public function search()
    {
        $f = Core::make('helper/form');
        echo $f->text($this->field('value'), $this->request('value'));
    }

    public function type_form()
    {
        $this->set('akTextareaDisplayModeCustomOptions', array());
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
         * @var $type TextareaSettings
         */

        $this->akTextareaDisplayMode = $type->getMode();
        $this->set('akTextareaDisplayMode', $type->getMode());
    }

    public function getAttributeValueClass()
    {
        return TextValue::class;
    }

    public function exportKey($akey)
    {
        $this->load();
        $akey->addChild('type')->addAttribute('mode', $this->akTextareaDisplayMode);

        return $akey;
    }

    public function createAttributeValue($value)
    {
        $this->load();
        if ($this->akTextareaDisplayMode == 'rich_text') {
            $value = LinkAbstractor::translateTo($value);
        }

        $av = new TextValue();
        $av->setValue($value);

        return $av;
    }

    public function importKey(\SimpleXMLElement $akey)
    {
        $type = $this->getAttributeKeySettings();
        if (isset($akey->type)) {
            $data['akTextareaDisplayMode'] = $akey->type['mode'];
            $type->setMode((string) $akey->type['mode']);
        }

        return $type;
    }

    public function getAttributeKeySettingsClass()
    {
        return TextareaSettings::class;
    }

}
