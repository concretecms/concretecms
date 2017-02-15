<?php
namespace Concrete\Core\Block\BlockType;

use Concrete\Core\Foundation\Environment;
use Core;
use Database as DB;
use Localization;
use Concrete\Core\Support\Facade\Facade;

class BlockType
{

    /**
     * Retrieves a BlockType object based on its btHandle.
     *
     * @return BlockType
     */
    public static function getByHandle($btHandle)
    {
        $em = \ORM::entityManager();
        $bt = $em->getRepository('\Concrete\Core\Entity\Block\BlockType\BlockType')->findOneBy(array('btHandle' => $btHandle));
        if (is_object($bt)) {
            $bt->loadController();

            return $bt;
        }
    }

    /**
     * Retrieves a BlockType object based on its btID.
     *
     * @return BlockType
     */
    public static function getByID($btID)
    {
        $em = \ORM::entityManager();
        $bt = $em->getRepository('\Concrete\Core\Entity\Block\BlockType\BlockType')->find($btID);
        $bt->loadController();

        return $bt;
    }

    /**
     * @deprecated
     */
    public static function installBlockTypeFromPackage($btHandle, $pkg)
    {
        static::installBlockType($btHandle, $pkg);
    }

    /**
     * Installs a BlockType that is passed via a btHandle string. The core or override directories are parsed.
     */
    public static function installBlockType($btHandle, $pkg = false)
    {
        $env = Environment::get();
        $pkgHandle = false;
        if (is_object($pkg)) {
            $pkgHandle = $pkg->getPackageHandle();
        }
        $class = static::getBlockTypeMappedClass($btHandle, $pkgHandle);
        $app = Facade::getFacadeApplication();
        $bta = $app->build($class);
        $path = dirname($env->getPath(DIRNAME_BLOCKS . '/' . $btHandle . '/' . FILENAME_BLOCK_DB, $pkgHandle));

        //Attempt to run the subclass methods (install schema from db.xml, etc.)
        $r = $bta->install($path);

        // Prevent the database records being stored in wrong language
        $loc = Localization::getInstance();
        $loc->pushActiveContext(Localization::CONTEXT_SYSTEM);

        //Install the block
        $bt = new \Concrete\Core\Entity\Block\BlockType\BlockType();
        $bt->loadFromController($bta);
        if (is_object($pkg)) {
            $bt->setPackageID($pkg->getPackageID());
        }
        $bt->setBlockTypeHandle($btHandle);

        $loc->popActiveContext();

        $em = \ORM::entityManager();
        $em->persist($bt);
        $em->flush();

        if ($bta->getBlockTypeDefaultSet()) {
            $set = Set::getByHandle($bta->getBlockTypeDefaultSet());
            if (is_object($set)) {
                $set->addBlockType($bt);
            }
        }

        return $bt;
    }

    /**
     * Return the class file that this BlockType uses.
     *
     * @return string
     */
    public static function getBlockTypeMappedClass($btHandle, $pkgHandle = false)
    {
        $env = Environment::get();
        $txt = Core::make('helper/text');
        $r = $env->getRecord(DIRNAME_BLOCKS . '/' . $btHandle . '/' . FILENAME_CONTROLLER);

        // Replace $pkgHandle if overridden via environment
        $r->pkgHandle and $pkgHandle = $r->pkgHandle;

        $prefix = $r->override ? true : $pkgHandle;
        $class = core_class('Block\\' . $txt->camelcase($btHandle) . '\\Controller', $prefix);

        if (class_exists($class)) {
            return $class;
        }
    }

    /**
     * Clears output and record caches.
     */
    public static function clearCache()
    {
        $db = DB::get();
        $r = $db->MetaTables();

        if (in_array('config', array_map('strtolower', $r))) {
            if (in_array('btcachedblockrecord', array_map('strtolower', $db->MetaColumnNames('Blocks')))) {
                $db->Execute('update Blocks set btCachedBlockRecord = null');
            }
            if (in_array('collectionversionblocksoutputcache', array_map('strtolower', $r))) {
                $db->Execute('truncate table CollectionVersionBlocksOutputCache');
            }
        }
    }


}
