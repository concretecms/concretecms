<?php

namespace Concrete\Core\File\Type;

use Loader;

/**
 * @package Core
 * @subpackage Files
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2009 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * @package Core
 * @subpackage Files
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2009 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
use \Concrete\Core\File\Type\Type as FileType;

class TypeList
{

    /**
     * @return TypeList
     */
    public static function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $v = __CLASS__;
            $instance = new $v;
        }
        return $instance;
    }

    protected $types = array();
    protected $importerAttributes = array();

    public function define(
        $extension,
        $name,
        $type,
        $customImporter = false,
        $inlineFileViewer = false,
        $editor = false,
        $pkgHandle = false
    ) {
        $ext = explode(',', $extension);
        foreach ($ext as $e) {
            $ft = new FileType();
            $ft->name = $name;
            $ft->extension = $e;
            $ft->customImporter = $customImporter;
            $ft->editor = $editor;
            $ft->type = strtolower($type);
            $ft->view = $inlineFileViewer;
            $ft->pkgHandle = $pkgHandle;
            $this->types[$e] = $ft;
        }
    }

    public function defineMultiple(array $types)
    {
        foreach ($types as $type_name => $type_settings) {
            array_splice($type_settings, 1, 0, $type_name);

            call_user_func_array(array($this, 'define'), $type_settings);
        }
    }

    public function defineImporterAttribute($akHandle, $akName, $akType, $akIsEditable)
    {
        $obj = new \stdClass;
        $obj->akHandle = $akHandle;
        $obj->akName = $akName;
        $obj->akType = $akType;
        $obj->akIsEditable = $akIsEditable;
        $this->importerAttributes[$akHandle] = $obj;
    }

    public function defineImporterAttributeMultiple(array $importer_attributes)
    {
        foreach ($importer_attributes as $attribute_name => $attribute_settings) {
            array_unshift($attribute_settings, $attribute_name);
            call_user_func_array(array($this, 'defineImporterAttribute'), $attribute_settings);
        }
    }

    public function getImporterAttribute($akHandle)
    {
        $ftl = static::getInstance();
        return $ftl->importerAttributes[$akHandle];
    }

    /**
     * Can take an extension or a filename
     * Returns any registered information we have for the particular file type, based on its registration
     */
    public static function getType($ext)
    {
        $ftl = static::getInstance();
        if (strpos($ext, '.') !== false) {
            // filename
            $h = Loader::helper('file');
            $ext = $h->getExtension($ext);
        }
        $ext = strtolower($ext);
        if (is_object($ftl->types[$ext])) {
            return $ftl->types[$ext];
        } else {
            $ft = new FileType(); // generic
            return $ft;
        }
    }


}
