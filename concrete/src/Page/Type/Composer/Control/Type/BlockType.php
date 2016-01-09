<?php
namespace Concrete\Core\Page\Type\Composer\Control\Type;

use Loader;
use \Concrete\Core\Foundation\Object;
use BlockType as ConcreteBlockType;
use BlockTypeList;
use Environment;
use \Concrete\Core\Page\Type\Composer\Control\BlockControl;

class BlockType extends Type
{

    public function getPageTypeComposerControlObjects()
    {
        $objects = array();
        $btl = new BlockTypeList();
        $blockTypes = $btl->get();
        $ci = Loader::helper('concrete/urls');

        $env = Environment::get();

        foreach ($blockTypes as $bt) {
            $cmf = $env->getRecord(DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . '/' . FILENAME_BLOCK_COMPOSER,
                $bt->getPackageHandle());
            if ($cmf->exists() || count($bt->getBlockTypeComposerTemplates()) > 0) {
                $bx = new BlockControl();
                $bx->setBlockTypeID($bt->getBlockTypeID());
                $bx->setPageTypeComposerControlIconSRC($ci->getBlockTypeIconURL($bt));
                $bx->setPageTypeComposerControlName(t($bt->getBlockTypeName()));
                $objects[] = $bx;
            }
        }
        return $objects;
    }

    public function getPageTypeComposerControlByIdentifier($identifier)
    {
        $bt = ConcreteBlockType::getByID($identifier);
        $ci = Loader::helper('concrete/urls');
        $bx = new BlockControl();
        $bx->setBlockTypeID($bt->getBlockTypeID());
        $bx->setPageTypeComposerControlIconSRC($ci->getBlockTypeIconURL($bt));
        $bx->setPageTypeComposerControlName(t($bt->getBlockTypeName()));
        return $bx;
    }

    public function controlTypeSupportsOutputControl()
    {
        return true;
    }

    public function configureFromImportHandle($handle)
    {
        $bt = ConcreteBlockType::getByHandle($handle);
        return static::getPageTypeComposerControlByIdentifier($bt->getBlockTypeID());
    }


}
