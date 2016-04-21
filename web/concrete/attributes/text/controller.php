<?php
namespace Concrete\Attribute\Text;
use Core;
use Database;
use \Concrete\Core\Foundation\Object;
use \Concrete\Core\Attribute\DefaultController;
class Controller extends DefaultController  {

    protected $searchIndexFieldDefinition = array('type' => 'text', 'options' => array('default' => null, 'notnull' => false));

    public $helpers = array('form');

    public function saveKey($data)
    {
        $data += array(
            'akTextPlaceholder' => null,
        );
        $akTextPlaceholder = $data['akTextPlaceholder'];

        $this->setDisplayMode($akTextPlaceholder);
    }

    public function form() {
        $this->load();
        if (is_object($this->attributeValue)) {
            $value = Core::make('helper/text')->entities($this->getAttributeValue()->getValue());
        }
        print Core::make('helper/form')->text($this->field('value'), $value, array( 'placeholder' => $this->akTextPlaceholder));
    }

    public function composer() {
        if (is_object($this->attributeValue)) {
            $value = Core::make('helper/text')->entities($this->getAttributeValue()->getValue());
        }
        print Core::make('helper/form')->text($this->field('value'), $value, array('class' => 'span5'));
    }

    public function setDisplayMode($akTextPlaceholder)
    {
        $db = Database::connection();
        $ak = $this->getAttributeKey();

        $db->Replace('atTextSettings', array(
            'akID' => $ak->getAttributeKeyID(),
            'akTextPlaceholder' => $akTextPlaceholder
        ), array('akID'), true);
    }

    // should have to delete the at thing
    public function deleteKey()
    {
        $db = Database::connection();
        $arr = $this->attributeKey->getAttributeValueIDList();
        foreach ($arr as $id) {
            $db->Execute('delete from atDefault where avID = ?', array($id));
        }

        $db->Execute('delete from atTextSettings where akID = ?', array($this->attributeKey->getAttributeKeyID()));
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

        $db = Database::connection();
        $row = $db->GetRow('select akTextPlaceholder from atTextSettings where akID = ?', array($ak->getAttributeKeyID()));
        $this->akTextPlaceholder = $row['akTextPlaceholder'];

        $this->set('akTextPlaceholder', $this->akTextPlaceholder);
    }

    public function exportKey($akey)
    {
        $this->load();
        $akey->addChild('type')->addAttribute('mode', $this->akTextPlaceholder);

        return $akey;
    }

    public function importKey($akey)
    {
        if (isset($akey->type)) {
            $data['akTextPlaceholder'] = $akey->type['mode'];
            $this->saveKey($data);
        }
    }

    public function duplicateKey($newAK)
    {
        $this->load();
        $db = Database::connection();
        $db->Replace('atTextSettings', array(
            'akID' => $newAK->getAttributeKeyID(),
            'akTextPlaceholder' => $this->akTextPlaceholder,
        ), array('akID'), true);
    }
}