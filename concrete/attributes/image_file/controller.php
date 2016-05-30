<?php
namespace Concrete\Attribute\ImageFile;

use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Entity\Attribute\Key\Type\ImageFileType;
use Concrete\Core\Entity\Attribute\Value\Value\ImageFileValue;
use Concrete\Core\Error\ErrorList\Error\Error;
use Concrete\Core\Error\ErrorList\Error\FieldNotPresentError;
use Concrete\Core\Error\ErrorList\Field\AttributeField;
use Concrete\Core\File\Importer;
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

    public function saveKey($data)
    {
        /**
         * @var $type ImageFileType
         */
        $type = $this->getAttributeKeyType();
        $data += array(
            'mode' => null,
        );
        $mode = $data['mode'];
        if ($mode == ImageFileType::TYPE_HTML_INPUT) {
            $type->setModeToHtmlInput();
        } else {
            $type->setModeToFileManager();
        }
        return $type;
    }

    public function type_form()
    {
        $this->set('form', \Core::make('helper/form'));
        $this->set('mode', $this->getAttributeKeyType()->getMode());
    }


    public function exportKey($akey)
    {
        /**
         * @var $type ImageFileType
         */
        $type = $this->getAttributeKeyType();
        if ($type->isModeHtmlInput()) {
            $mode = 'html_input';
        } else {
            $mode = 'file_manager';
        }
        $akey->addChild('type')->addAttribute('mode', $mode);
        return $akey;
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
        $this->set('mode', $this->getAttributeKeyType()->getMode());
        $this->set('file', $bf);

    }

    public function importKey(\SimpleXMLElement $akey)
    {
        $type = $this->getAttributeKeyType();
        /**
         * @var $type ImageFileType
         */
        if (isset($akey->type)) {
            $mode = (string) $akey->type['mode'];
            if ($mode == 'html_input') {
                $type->setModeToHtmlInput();
            }
        }

        return $type;
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
                    return $this->createAttributeValue($f);
                }
            }
        }
    }

    // run when we call setAttribute(), instead of saving through the UI
    public function createAttributeValue($obj)
    {
        if ($obj && !is_object($obj)) {
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
        if ($this->getAttributeKeyType()->isModeFileManager()) {
            if (Core::make('helper/validation/numbers')->integer($data['value'])) {
                $f = File::getByID($data['value']);
                if (is_object($f) && !$f->isError()) {
                    return true;
                } else {
                    return new Error(t('You must specify a valid file for %s', $this->getAttributeKey()->getAttributeKeyDisplayName()),
                        new AttributeField($this->getAttributeKey())
                    );
                }
            } else {
                return new FieldNotPresentError(new AttributeField($this->getAttributeKey()));
            }
        }
        if ($this->getAttributeKeyType()->isModeHtmlInput()) {
            $tmp_name = $_FILES['akID']['tmp_name'][$this->attributeKey->getAttributeKeyID()]['value'];
            $name = $_FILES['akID']['name'][$this->attributeKey->getAttributeKeyID()]['value'];
            if (!empty($tmp_name) && is_uploaded_file($tmp_name)) {
                $fh = \Core::make('helper/validation/file');
                if (!$fh->file($tmp_name)) {
                    return new Error(t('You have not uploaded a valid file.'),
                        new AttributeField($this->getAttributeKey())
                    );
                }

                if (!$fh->extension($name)) {
                    return new Error(t('Invalid file extension.'),
                        new AttributeField($this->getAttributeKey())
                    );
                }

                return true;
            } else {
                return new FieldNotPresentError(new AttributeField($this->getAttributeKey()));
            }
        }
    }

    public function createAttributeValueFromRequest()
    {
        $data = $this->post();
        if ($this->getAttributeKeyType()->isModeFileManager()) {
            if ($data['value'] > 0) {
                $f = File::getByID($data['value']);
                return $this->createAttributeValue($f);
            }
        }
        if ($this->getAttributeKeyType()->isModeHtmlInput()) {
            // import the file.
            $tmp_name = $_FILES['akID']['tmp_name'][$this->attributeKey->getAttributeKeyID()]['value'];
            $name = $_FILES['akID']['name'][$this->attributeKey->getAttributeKeyID()]['value'];
            if (!empty($tmp_name) && is_uploaded_file($tmp_name)) {
                $importer = new Importer();
                $f = $importer->import($tmp_name, $name);
                if (is_object($f)) {
                    return $this->createAttributeValue($f->getFile());
                }
            }
        }
    }

    public function createAttributeKeyType()
    {
        return new ImageFileType();
    }
}
