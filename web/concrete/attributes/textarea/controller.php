<?php

namespace Concrete\Attribute\Textarea;

use Concrete\Core\Attribute\DefaultController;
use Core;
use Database;

class Controller extends DefaultController
{
    protected $searchIndexFieldDefinition = array('type' => 'text', 'options' => array('default' => null, 'notnull' => false));

    public $helpers = array('form');

    public function saveKey($data)
    {
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
        $this->setDisplayMode($akTextareaDisplayMode, $options);
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

        if (is_object($this->attributeValue)) {
            $value = $this->getAttributeValue()->getValue();
        }
        // switch display type here
        if ($this->akTextareaDisplayMode == 'text' || $this->akTextareaDisplayMode == '') {
            print Core::make('helper/form')->textarea($this->field('value'), $value, array('class' => $additionalClass, 'rows' => 5));
        } else {
            print Core::make('editor')->outputStandardEditor($this->field('value'), $value);
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
        print $f->text($this->field('value'), $this->request('value'));
    }

    public function setDisplayMode($akTextareaDisplayMode, $akTextareaDisplayModeCustomOptions = array())
    {
        $db = Database::connection();
        $ak = $this->getAttributeKey();
        $akTextareaDisplayModeCustomOptionsValue = '';
        if (is_array($akTextareaDisplayModeCustomOptions) && count($akTextareaDisplayModeCustomOptions) > 0) {
            $akTextareaDisplayModeCustomOptionsValue = serialize($akTextareaDisplayModeCustomOptions);
        }
        $db->Replace('atTextareaSettings', array(
            'akID' => $ak->getAttributeKeyID(),
            'akTextareaDisplayMode' => $akTextareaDisplayMode,
            'akTextareaDisplayModeCustomOptions' => $akTextareaDisplayModeCustomOptionsValue,
        ), array('akID'), true);
    }

    /*
    public function saveForm($data)
    {
        $this->saveValue($data['value']);
    }
    */

    // should have to delete the at thing
    public function deleteKey()
    {
        $db = Database::connection();
        $arr = $this->attributeKey->getAttributeValueIDList();
        foreach ($arr as $id) {
            $db->Execute('delete from atDefault where avID = ?', array($id));
        }

        $db->Execute('delete from atTextareaSettings where akID = ?', array($this->attributeKey->getAttributeKeyID()));
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

        $db = Database::connection();
        $row = $db->GetRow('select akTextareaDisplayMode, akTextareaDisplayModeCustomOptions from atTextareaSettings where akID = ?', array($ak->getAttributeKeyID()));
        $this->akTextareaDisplayMode = $row['akTextareaDisplayMode'];
        $this->akTextareaDisplayModeCustomOptions = array();
        if ($row['akTextareaDisplayMode'] == 'rich_text_custom') {
            $this->akTextareaDisplayModeCustomOptions = unserialize($row['akTextareaDisplayModeCustomOptions']);
        }
        $this->set('akTextareaDisplayMode', $this->akTextareaDisplayMode);
        $this->set('akTextareaDisplayModeCustomOptions', $this->akTextareaDisplayModeCustomOptions);
    }

    public function exportKey($akey)
    {
        $this->load();
        $akey->addChild('type')->addAttribute('mode', $this->akTextareaDisplayMode);

        return $akey;
    }

    public function importKey($akey)
    {
        if (isset($akey->type)) {
            $data['akTextareaDisplayMode'] = $akey->type['mode'];
            $this->saveKey($data);
        }
    }

    public function duplicateKey($newAK)
    {
        $this->load();
        $db = Database::connection();
        $db->Replace('atTextareaSettings', array(
            'akID' => $newAK->getAttributeKeyID(),
            'akTextareaDisplayMode' => $this->akDateDisplayMode,
        ), array('akID'), true);
    }
}
