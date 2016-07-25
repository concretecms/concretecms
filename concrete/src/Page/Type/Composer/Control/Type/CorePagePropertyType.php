<?php
namespace Concrete\Core\Page\Type\Composer\Control\Type;

use Loader;
use Core;

class CorePagePropertyType extends Type
{
    protected $corePageProperties = array(
        'name',
        'url_slug',
        'date_time',
        'description',
        'user',
        'page_template',
        'publish_target',
    );

    public function getPageTypeComposerControlObjects()
    {
        $objects = array();
        foreach ($this->corePageProperties as $propertyHandle) {
            $objects[] = $this->getPageTypeComposerControlByIdentifier($propertyHandle);
        }

        return $objects;
    }

    public function getPageTypeComposerControlByIdentifier($identifier)
    {
        $class = '\\Concrete\\Core\\Page\\Type\\Composer\\Control\\CorePageProperty\\' . Loader::helper('text')->camelcase($identifier) . 'CorePageProperty';
        $object = Core::make($class);

        return $object;
    }

    public function configureFromImportHandle($handle)
    {
        return static::getPageTypeComposerControlByIdentifier($handle);
    }
}
