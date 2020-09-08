<?php

namespace Concrete\Attribute\Textarea;

use Concrete\Core\Attribute\DefaultController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Attribute\XEditableConfigurableAttributeInterface;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Entity\Attribute\Key\Settings\TextareaSettings;
use Concrete\Core\Entity\Attribute\Value\Value\TextValue;
use Core;

class Controller extends DefaultController implements XEditableConfigurableAttributeInterface
{
    public $helpers = ['form'];

    protected $akTextareaDisplayMode;
    protected $akTextareaDisplayModeCustomOptions;
    protected $akTextPlaceholder;

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('font');
    }

    public function saveKey($data)
    {
        $type = $this->getAttributeKeySettings();
        $data += [
            'akTextareaDisplayMode' => null,
            'akTextPlaceholder' => null,
        ];
        $akTextareaDisplayMode = $data['akTextareaDisplayMode'];
        if (!$akTextareaDisplayMode) {
            $akTextareaDisplayMode = 'text';
        }
        $options = [];
        if ($akTextareaDisplayMode == 'rich_text_custom') {
            $options = $data['akTextareaDisplayModeCustomOptions'];
        }
        $akTextPlaceholder = $data['akTextPlaceholder'];

        $type->setMode($akTextareaDisplayMode);
        $type->setPlaceholder($akTextPlaceholder);

        return $type;
    }

    public function getValue()
    {
        $this->load();
        if ($this->akTextareaDisplayMode == 'text') {
            $value = $this->getAttributeValue()->getValueObject();

            return (string) $value;
        }

        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->getAttributeValue()->getValueObject();

            if ($value) {
                $this->load();
                $value = (string) $value;
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
            return htmLawed($value, ['safe' => 1]);
        }

        return nl2br(h($value));
    }

    public function form()
    {
        $this->load();
        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->getAttributeValue()->getValueObject();

            if ($value) {
                if ($this->akTextareaDisplayMode == 'rich_text') {
                    $value = LinkAbstractor::translateFromEditMode($value);
                }
            }
        }
        $this->set('akTextareaDisplayMode', $this->akTextareaDisplayMode);
        $this->set('value', $value);
        $akTextPlaceholder = '';
        if (isset($this->akTextPlaceholder)) {
            $akTextPlaceholder = $this->akTextPlaceholder;
        }
        $this->set('akTextPlaceholder', $akTextPlaceholder);
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
        $this->set('akTextareaDisplayModeCustomOptions', []);
        $this->load();
    }

    public function getAttributeValueClass()
    {
        return TextValue::class;
    }

    public function exportKey($akey)
    {
        $this->load();
        $akey->addChild('type')->addAttribute('mode', $this->akTextareaDisplayMode);
        $akey->addChild('type')->addAttribute('placeholder', $this->akTextPlaceholder);

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
            $data['akTextPlaceholder'] = $akey->type['placeholder'];
            $type->setPlaceholder((string) $akey->type['placeholder']);
        }

        return $type;
    }

    public function getAttributeKeySettingsClass()
    {
        return TextareaSettings::class;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\XEditableConfigurableAttributeInterface::getXEditableOptions()
     */
    public function getXEditableOptions()
    {
        $this->load();
        if ($this->akTextareaDisplayMode === 'rich_text') {
            return [
                'editableMode' => 'inline',
                'onblur' => 'ignore',
                'showbuttons' => 'bottom',
            ];
        }

        return [];
    }

    protected function load()
    {
        $ak = $this->getAttributeKey();
        if (!is_object($ak)) {
            return false;
        }

        $type = $ak->getAttributeKeySettings();
        /**
         * @var TextareaSettings
         */
        $this->akTextareaDisplayMode = $type->getMode();
        $this->set('akTextareaDisplayMode', $type->getMode());
        $this->akTextPlaceholder = $type->getPlaceholder();
        $this->set('akTextPlaceholder', $type->getPlaceholder());
    }
}
