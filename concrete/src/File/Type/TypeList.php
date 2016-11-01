<?php
namespace Concrete\Core\File\Type;

use Concrete\Core\File\Type\Type as FileType;
use Concrete\Core\Support\Facade\Application;
use stdClass;

/**
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2009 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
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
            $instance = new $v();
        }

        return $instance;
    }

    /**
     * @var FileType[]
     */
    protected $types = [];

    /**
     * @var stdClass[]
     */
    protected $importerAttributes = [];

    /**
     * Register a file type.
     *
     * @param string $extension Comma-separated list of file extensions (lower case and without leading dots)
     * @param string $name File type name
     * @param int $type Generic file type (one of the \Concrete\Core\File\Type\Type\Type::T_... constants)
     * @param string $customImporter The handle of the custom importer
     * @param string $inlineFileViewer The handle of the inline viewer
     * @param string $editor The hHandle of the editor
     * @param string $pkgHandle The handle of the owner package
     */
    public function define($extension, $name, $type, $customImporter = '', $inlineFileViewer = '', $editor = '', $pkgHandle = '')
    {
        $ext = explode(',', $extension);
        foreach ($ext as $e) {
            $this->types[strtolower($e)] = (new FileType())
                ->setName($name)
                ->setExtension($e)
                ->setCustomImporter($customImporter)
                ->setEditor($editor)
                ->setGenericType($type)
                ->setView($inlineFileViewer)
                ->setPackageHandle($pkgHandle);
        }
    }

    /**
     * Register multiple file types.
     *
     * @param array $types Keys are the type names, values are the other parameters accepted by the define() method
     */
    public function defineMultiple(array $types)
    {
        foreach ($types as $type_name => $type_settings) {
            array_splice($type_settings, 1, 0, $type_name);

            call_user_func_array([$this, 'define'], $type_settings);
        }
    }

    public function defineImporterAttribute($akHandle, $akName, $akType, $akIsEditable)
    {
        $obj = new stdClass();
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
            call_user_func_array([$this, 'defineImporterAttribute'], $attribute_settings);
        }
    }

    /**
     * @param string $akHandle
     *
     * @return stdClass|null
     */
    public static function getImporterAttribute($akHandle)
    {
        $ftl = static::getInstance();

        return isset($ftl->importerAttributes[$akHandle]) ? $ftl->importerAttributes[$akHandle] : null;
    }

    /**
     * Can take an extension or a filename
     * Returns any registered information we have for the particular file type, based on its registration.
     *
     * @return FileType
     */
    public static function getType($ext)
    {
        $ftl = static::getInstance();
        if (strpos($ext, '.') !== false) {
            // filename
            $app = Application::getFacadeApplication();
            $h = $app->make('helper/file');
            $ext = $h->getExtension($ext);
        }
        $ext = strtolower($ext);
        if (isset($ftl->types[$ext])) {
            return $ftl->types[$ext];
        } else {
            $ft = new FileType(); // generic
            return $ft;
        }
    }
}
