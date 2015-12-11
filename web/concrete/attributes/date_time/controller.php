<?php
namespace Concrete\Attribute\DateTime;

use Loader;
use Core;
use \Concrete\Core\Attribute\Controller as AttributeTypeController;

class Controller extends AttributeTypeController
{
    public $helpers = array('form');

    protected $searchIndexFieldDefinition = array('type' => 'datetime', 'options' => array('notnull' => false));

    public function saveKey($data)
    {
        $akDateDisplayMode = $data['akDateDisplayMode'];
        if (!$akDateDisplayMode) {
            $akDateDisplayMode = 'date_time';
        }
        $this->setDisplayMode($akDateDisplayMode);
    }

    public function setDisplayMode($akDateDisplayMode)
    {
        $db = Loader::db();
        $ak = $this->getAttributeKey();
        $db->Replace('atDateTimeSettings', array(
            'akID' => $ak->getAttributeKeyID(),
            'akDateDisplayMode' => $akDateDisplayMode
        ), array('akID'), true);
    }

    public function type_form()
    {
        $this->load();
    }

    public function getDisplayValue()
    {
        $this->load();
        $v = $this->getValue();
        if(empty($v)) {
            return '';
        }
        $dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
        if ($this->akDateDisplayMode == 'date') {
            // Don't use user's timezone to avoid showing wrong dates
            return $dh->formatDate($v, false, 'system');
        }
        else {
            return $dh->formatDateTime($v);
        }
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
        $dt = Loader::helper('form/date_time');
        $caValue = $this->getValue();
        $html = Loader::helper('html');
        switch ($this->akDateDisplayMode) {
            case 'text':
                $form = Loader::helper('form');
                print $form->text($this->field('value'), $this->getDisplayValue());
                break;
            case 'date':
                $this->requireAsset('jquery/ui');
                print $dt->date($this->field('value'), $caValue == null ? '' : $caValue);
                break;
            default:
                $this->requireAsset('jquery/ui');
                print $dt->datetime($this->field('value'), $caValue == null ? '' : $caValue);
                break;
        }
    }

    public function exportKey($akey)
    {
        $this->load();
        $type = $akey->addChild('type');
        $type->addAttribute('mode', $this->akDateDisplayMode);

        return $akey;
    }

    public function importKey($akey)
    {
        if (isset($akey->type)) {
            $data['akDateDisplayMode'] = $akey->type['mode'];
            $this->saveKey($data);
        }
    }

    public function validateValue()
    {
        $v = $this->getValue();
        return $v != false;
    }

    public function validateForm($data)
    {
        if (!isset($this->akDateDisplayMode)) {
            $this->load();
        }
        switch ($this->akDateDisplayMode) {
            case 'date_time':
                if (empty($data['value_dt']) || (!is_numeric($data['value_h'])) || (!is_numeric($data['value_m']))) {
                    return false;
                }
                $dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
                switch ($dh->getTimeFormat()) {
                    case 12:
                        if (empty($data['value_a'])) {
                            return false;
                        }
                        break;
                }

                return true;
            default:
                return $data['value'] != '';
        }
    }

    public function getValue()
    {
        $db = Loader::db();
        $value = $db->GetOne("select value from atDateTime where avID = ?", array($this->getAttributeValueID()));

        return $value;
    }

    public function search()
    {
        $dt = Loader::helper('form/date_time');
        $html = $dt->date($this->field('from'), $this->request('from'), true);
        $html .= ' ' . t('to') . ' ';
        $html .= $dt->date($this->field('to'), $this->request('to'), true);
        print $html;
    }

    public function saveValue($value)
    {
        if ($value != '') {
            $value = date('Y-m-d H:i:s', strtotime($value));
        } else {
            $value = null;
        }

        $db = Loader::db();
        $db->Replace('atDateTime', array('avID' => $this->getAttributeValueID(), 'value' => $value), 'avID', true);
    }

    public function duplicateKey($newAK)
    {
        $this->load();
        $db = Loader::db();
        $db->Execute('insert into atDateTimeSettings (akID, akDateDisplayMode) values (?, ?)', array($newAK->getAttributeKeyID(), $this->akDateDisplayMode));
    }

    public function saveForm($data)
    {
        $this->load();
        $dt = Loader::helper('form/date_time');
        switch ($this->akDateDisplayMode) {
            case 'text':
                $this->saveValue($data['value']);
                break;
            case 'date':
            case 'date_time':
                $value = $dt->translate('value', $data);
                $this->saveValue($value);
                break;
        }
    }

    protected function load()
    {
        $ak = $this->getAttributeKey();
        if (!is_object($ak)) {
            return false;
        }

        $db = Loader::db();
        $row = $db->GetRow('select akDateDisplayMode from atDateTimeSettings where akID = ?', $ak->getAttributeKeyID());
        $this->akDateDisplayMode = $row['akDateDisplayMode'];

        $this->set('akDateDisplayMode', $this->akDateDisplayMode);
    }

    public function deleteKey()
    {
        $db = Loader::db();
        $arr = $this->attributeKey->getAttributeValueIDList();
        foreach ($arr as $id) {
            $db->Execute('delete from atDateTime where avID = ?', array($id));
        }
    }
    public function deleteValue()
    {
        $db = Loader::db();
        $db->Execute('delete from atDateTime where avID = ?', array($this->getAttributeValueID()));
    }

}
