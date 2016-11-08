<?php
namespace Concrete\Attribute\Text;

use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Attribute\DefaultController;
use Concrete\Core\Entity\Attribute\Key\Settings\TextSettings;
use Core;
use Database;
use Concrete\Core\Entity\Attribute\Value\Value\TextValue;
class Controller extends DefaultController
{
    protected $searchIndexFieldDefinition = array('type' => 'text', 'options' => array('default' => null, 'notnull' => false));

    protected $akTextPlaceholder;
    public $helpers = array('form');

    public function saveKey($data)
    {
        $type = $this->getAttributeKeySettings();
        $data += array(
            'akTextPlaceholder' => null,
        );
        $akTextPlaceholder = $data['akTextPlaceholder'];

        $type->setPlaceholder($akTextPlaceholder);

        return $type;
    }

    public function getDisplayValue()
    {
        return h($this->getValue());
    }

    public function form()
    {
        $this->load();
        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->app->make('helper/text')->entities($this->getAttributeValue()->getValue());
        }
        echo $this->app->make('helper/form')->text($this->field('value'), $value, array( 'placeholder' => $this->akTextPlaceholder));
    }

    public function composer()
    {
        $this->load();
        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->app->make('helper/text')->entities($this->getAttributeValue()->getValue());
        }
        echo $this->app->make('helper/form')->text($this->field('value'), $value, array('class' => 'span5', 'placeholder' => $this->akTextPlaceholder));
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
         * @var $type TextSettings
         */
        $this->akTextPlaceholder = $type->getPlaceholder();
        $this->set('akTextPlaceholder', $type->getPlaceholder());
    }

    public function exportKey($akey)
    {
        $this->load();
        $akey->addChild('type')->addAttribute('placeholder', $this->akTextPlaceholder);

        return $akey;
    }

    public function createAttributeValue($value)
    {
        $av = new TextValue();
        $av->setValue($value);

        return $av;
    }

    public function importKey(\SimpleXMLElement $akey)
    {
        $type = $this->getAttributeKeySettings();
        if (isset($akey->type)) {
            $data['akTextPlaceholder'] = $akey->type['placeholder'];
            $type->setPlaceholder((string) $akey->type['placeholder']);
        }

        return $type;
    }

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('file-text');
    }
}
