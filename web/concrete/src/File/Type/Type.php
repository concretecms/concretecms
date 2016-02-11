<?php
namespace Concrete\Core\File\Type;

use Loader;
use \Concrete\Core\Package\PackageList;
use Core;

class Type
{

    // File Type Constants
    const T_IMAGE = 1;          //!< @javascript-exported
    const T_VIDEO = 2;          //!< @javascript-exported
    const T_TEXT = 3;           //!< @javascript-exported
    const T_AUDIO = 4;          //!< @javascript-exported
    const T_DOCUMENT = 5;       //!< @javascript-exported
    const T_APPLICATION = 6;    //!< @javascript-exported
    const T_UNKNOWN = 99;       //!< @javascript-exported

    public $pkgHandle = false;

    public function __construct()
    {
        $this->type = static::T_UNKNOWN;
        $this->name = $this->mapGenericTypeText($this->type);
    }

    public function getPackageHandle()
    {
        return $this->pkgHandle;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getExtension()
    {
        return $this->extension;
    }

    public function getCustomImporter()
    {
        return $this->customImporter;
    }

    public function getGenericType()
    {
        return $this->type;
    }

    public function getView()
    {
        return $this->view;
    }

    public function getEditor()
    {
        return $this->editor;
    }

    protected function mapGenericTypeText($type)
    {
        switch ($type) {
            case static::T_IMAGE:
                return t('Image');
                break;
            case static::T_VIDEO:
                return t('Video');
                break;
            case static::T_TEXT:
                return t('Text');
                break;
            case static::T_AUDIO:
                return t('Audio');
                break;
            case static::T_DOCUMENT:
                return t('Document');
                break;
            case static::T_APPLICATION:
                return t('Application');
                break;
            case static::T_UNKNOWN:
                return t('File');
                break;

        }
    }

    public static function getGenericTypeText($type)
    {
        if ($type > 0) {
            return static::mapGenericTypeText($type);
        } else {
            if (!empty($this->type)) {
                return static::mapGenericTypeText($this->type);
            }
        }
    }

    public function getCustomInspector()
    {
        $name = camelcase($this->getCustomImporter()) . 'Inspector';
        $class = overrideable_core_class('Core\\File\\Type\\Inspector\\' . $name, 'File/Type/Inspector/' . $name . '.php', $this->getPackageHandle());
        $cl = Core::make($class);
        return $cl;
    }

    public static function getUsedExtensionList()
    {
        $db = Loader::db();
        $stm = $db->query('select distinct fvExtension from FileVersions where fvIsApproved = 1 and fvExtension <> ""');
        $extensions = array();
        while ($row = $stm->fetch()) {
            $extensions[] = $row['fvExtension'];
        }
        return $extensions;
    }

    public static function getUsedTypeList()
    {
        $db = Loader::db();
        $stm = $db->query('select distinct fvType from FileVersions where fvIsApproved = 1 and fvType <> 0');
        $types = array();
        while ($row = $stm->fetch()) {
            $types[] = $row['fvType'];
        }
        return $types;
    }

    public static function getTypeList()
    {
        return array(
            static::T_DOCUMENT,
            static::T_IMAGE,
            static::T_VIDEO,
            static::T_AUDIO,
            static::T_TEXT,
            static::T_APPLICATION,
            static::T_UNKNOWN
        );
    }

    /**
     * Returns a thumbnail for this type of file
     */
    public function getThumbnail($fullImageTag = true)
    {
        $type = \Concrete\Core\File\Image\Thumbnail\Type\Type::getByHandle(\Config::get('concrete.icons.file_manager_listing.handle'));
        if (file_exists(DIR_AL_ICONS . '/' . $this->extension . '.png')) {
            $url = REL_DIR_AL_ICONS . '/' . $this->extension . '.png';
        } else {
            $url = AL_ICON_DEFAULT;
        }
        if ($fullImageTag == true) {
            return sprintf('<img src="%s" width="%s" height="%s" class="img-responsive ccm-generic-thumbnail">', $url, $type->getWidth(), $type->getHeight());
        } else {
            return $url;
        }
    }


}
