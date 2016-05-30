<?php
namespace Concrete\Attribute\Textarea;

use Concrete\Core\Attribute\DefaultController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Entity\Attribute\Key\Type\TextareaType;
use Core;
use Database;
use Concrete\Core\Entity\Attribute\Value\Value\TextareaValue;

class Controller extends DefaultController
{
    protected $searchIndexFieldDefinition = array('type' => 'text', 'options' => array('default' => null, 'notnull' => false));

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('font');
    }

    protected $akTextareaDisplayMode;
    protected $akTextareaDisplayModeCustomOptions;
    public $helpers = array('form');

    public function saveKey($data)
    {
        $type = $this->getAttributeKeyType();
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

    public function getDisplaySanitizedValue()
    {
        $this->load();
        if ($this->akTextareaDisplayMode == 'text') {
            return parent::getDisplaySanitizedValue();
        }

        return htmLawed(parent::getValue(), array('safe' => 1, 'deny_attribute' => 'style'));
    }

    public function form($additionalClass = false)
    {
        $this->load();
        $this->requireAsset('jquery/ui');

        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->getAttributeValue()->getValue();
        }
        // switch display type here
        if ($this->akTextareaDisplayMode == 'text' || $this->akTextareaDisplayMode == '') {
            echo Core::make('helper/form')->textarea($this->field('value'), $value, array('class' => $additionalClass, 'rows' => 5));
        } else {
            echo Core::make('editor')->outputStandardEditor($this->field('value'), $value);
        }
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

        $type = $ak->getAttributeKeyType();
        /*
         * @var $type TextareaType
         */

        $this->akTextareaDisplayMode = $type->getMode();
        $this->set('akTextareaDisplayMode', $type->getMode());
    }

    public function exportKey($akey)
    {
        $this->load();
        $akey->addChild('type')->addAttribute('mode', $this->akTextareaDisplayMode);

        return $akey;
    }

    public function createAttributeValue($value)
    {
        $av = new TextareaValue();
        $av->setValue($value);

        return $av;
    }

    public function importKey(\SimpleXMLElement $akey)
    {
        $type = $this->getAttributeKeyType();
        if (isset($akey->type)) {
            $data['akTextareaDisplayMode'] = $akey->type['mode'];
            $type->setMode((string) $akey->type['mode']);
        }

        return $type;
    }

    public function createAttributeKeyType()
    {
        return new TextareaType();
    }
}
