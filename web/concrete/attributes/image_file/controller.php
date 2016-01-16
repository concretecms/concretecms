<?php
namespace Concrete\Attribute\ImageFile;

use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Entity\Attribute\Key\Type\ImageFileType;
use Concrete\Core\Entity\Attribute\Value\Value\ImageFileValue;
use Core;
use File;
use Concrete\Core\Backup\ContentExporter;
use Concrete\Core\Attribute\Controller as AttributeTypeController;

class Controller extends AttributeTypeController
{
    protected $searchIndexFieldDefinition = array('type' => 'integer', 'options' => array('default' => 0, 'notnull' => false));

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('download');
    }

    public function getDisplayValue()
    {
        $f = $this->getValue();
        if (is_object($f)) {
            return '<a href="' . $f->getDownloadURL() . '">' . $f->getTitle() . '</a>';
        }
    }

    public function getDisplaySanitizedValue()
    {
        return $this->getDisplayValue();
    }

    public function exportValue(\SimpleXMLElement $akn)
    {
        $av = $akn->addChild('value');
        $fo = $this->getValue();
        if (is_object($fo)) {
            $av->addChild('fID', ContentExporter::replaceFileWithPlaceHolder($fo->getFileID()));
        } else {
            $av->addChild('fID', 0);
        }
    }

    public function searchForm($list)
    {
        $fileID = $this->request('value');
        $list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), $fileID);

        return $list;
    }

    public function getSearchIndexValue()
    {
        return $this->attributeValue->getFileID();
    }

    public function search()
    {
        // search by file causes too many problems
        //$al = Core::make('helper/concrete/asset_library');
        //print $al->file('ccm-file-akID-' . $this->attributeKey->getAttributeKeyID(), $this->field('value'), t('Choose File'), $bf);
    }

    public function form()
    {
        $bf = false;
        if (is_object($this->attributeValue)) {
            $bf = $this->getValue();
        }
        $al = Core::make('helper/concrete/asset_library');
        $form = '<div class="ccm-attribute ccm-attribute-image-file">';
        $form .= $al->file('ccm-file-akID-' . $this->attributeKey->getAttributeKeyID(), $this->field('value'), t('Choose File'), $bf);
        $form .= '</div>';
        echo $form;
    }

    public function importValue(\SimpleXMLElement $akv)
    {
        if (isset($akv->value->fID)) {
            $fIDVal = (string) $akv->value->fID;
            $inspector = \Core::make('import/value_inspector');
            $result = $inspector->inspect($fIDVal);
            $fID = $result->getReplacedValue();
            if ($fID) {
                $f = File::getByID($fID);
                if (is_object($f)) {
                    return $this->saveValue($f);
                }
            }
        }
    }

    // run when we call setAttribute(), instead of saving through the UI
    public function saveValue($obj)
    {
        if (!is_object($obj)) {
            $obj = File::getByID($obj);
        }

        $value = new ImageFileValue();
        $value->setFileObject($obj);

        return $value;
    }

    public function validateValue()
    {
        $f = $this->getValue();
        if (!is_object($f)) {
            $e = Core::make('helper/validation/error');
            $e->add(t('You must specify a valid file for %s', $this->attributeKey->getAttributeKeyDisplayName()));
        }

        return $e;
    }

    public function validateForm($data)
    {
        if (Core::make('helper/validation/numbers')->integer($data['value'])) {
            $f = File::getByID($data['value']);
            if (is_object($f) && !$f->isError()) {
                return true;
            }
        }
        $e = Core::make('helper/validation/error');
        $e->add(t('You must specify a valid file for %s', $this->attributeKey->getAttributeKeyDisplayName()));

        return $e;
    }

    public function saveForm($data)
    {
        if ($data['value'] > 0) {
            $f = File::getByID($data['value']);
            return $this->saveValue($f);
        }
    }

    public function importKey($akey)
    {
        $type = new ImageFileType();

        return $type;
    }

    public function saveKey($data)
    {
        $type = new ImageFileType();

        return $type;
    }

    public function createAttributeKeyType()
    {
        return new ImageFileType();
    }
}
