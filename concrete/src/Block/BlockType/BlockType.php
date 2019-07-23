<?php

namespace Concrete\Core\Block\BlockType;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Block\BlockType\BlockType as BlockTypeEntity;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;

class BlockType
{
    /**
     * Get a BlockType given its handle.
     *
     * @param string $btHandle
     *
     * @return \Concrete\Core\Entity\Block\BlockType\BlockType|null
     */
    public static function getByHandle($btHandle)
    {
        $result = null;
        $btHandle = (string) $btHandle;
        if ($btHandle !== '') {
            $app = Application::getFacadeApplication();
            $em = $app->make(EntityManagerInterface::class);
            $repo = $em->getRepository(BlockTypeEntity::class);
            $result = $repo->findOneBy(['btHandle' => $btHandle]);
            if ($result !== null) {
                $result->loadController();
            }
        }

        return $result;
    }

    /**
     * Get a BlockType given its ID.
     *
     * @param int $btID
     *
     * @return \Concrete\Core\Entity\Block\BlockType\BlockType|null
     */
    public static function getByID($btID)
    {
        $result = null;
        $btID = (int) $btID;
        if ($btID !== 0) {
            $app = Application::getFacadeApplication();
            $em = $app->make(EntityManagerInterface::class);
            $result = $em->find(BlockTypeEntity::class, $btID);
            if ($result !== null) {
                $result->loadController();
            }
        }

        return $result;
    }

    /**
     * Install a BlockType that is passed via a btHandle string. The core or override directories are parsed.
     *
     * @param string $btHandle The handle of the block type
     * @param \Concrete\Core\Entity\Package|\Concrete\Core\Package\Package|string|false $pkg The package owning the block type (or its handle)
     *
     * @return \Concrete\Core\Entity\Block\BlockType\BlockType
     */
    public static function installBlockType($btHandle, $pkg = false)
    {
        $app = Application::getFacadeApplication();
        $em = $app->make(EntityManagerInterface::class);
        $pkgHandle = (string) (is_object($pkg) ? $pkg->getPackageHandle() : $pkg);
        $class = static::getBlockTypeMappedClass($btHandle, $pkgHandle);
        $bta = $app->build($class);

        $locator = $app->make(FileLocator::class);
        if ($pkgHandle !== '') {
            $locator->addLocation(new FileLocator\PackageLocation($pkgHandle));
        }
        $path = dirname($locator->getRecord(DIRNAME_BLOCKS . '/' . $btHandle . '/' . FILENAME_BLOCK_DB)->getFile());

        //Attempt to run the subclass methods (install schema from db.xml, etc.)
        $bta->install($path);

        // Prevent the database records being stored in wrong language
        $loc = $app->make(Localization::class);
        $loc->pushActiveContext(Localization::CONTEXT_SYSTEM);
        try {
            //Install the block
            $bt = new BlockTypeEntity();
            $bt->loadFromController($bta);
            if (is_object($pkg)) {
                $bt->setPackageID($pkg->getPackageID());
            }
            $bt->setBlockTypeHandle($btHandle);
        } finally {
            $loc->popActiveContext();
        }

        $em->persist($bt);
        $em->flush();

        if ($bta->getBlockTypeDefaultSet()) {
            $set = Set::getByHandle($bta->getBlockTypeDefaultSet());
            if ($set !== null) {
                $set->addBlockType($bt);
            }
        }

        return $bt;
    }

    /**
     * Return the class file that this BlockType uses.
     *
     * @param string $btHandle The handle of the block type
     * @param string|false $pkgHandle The handle of the package owning the block type
     *
     * @return string|null
     */
    public static function getBlockTypeMappedClass($btHandle, $pkgHandle = false)
    {
        $app = Application::getFacadeApplication();
        $txt = $app->make('helper/text');

        $pkgHandle = (string) $pkgHandle;
        $locator = $app->make(FileLocator::class);
        if ($pkgHandle !== '') {
            $locator->addLocation(new FileLocator\PackageLocation($pkgHandle));
        }
        $r = $locator->getRecord(DIRNAME_BLOCKS . '/' . $btHandle . '/' . FILENAME_CONTROLLER);
        $overriddenPackageHandle = (string) $r->getPackageHandle();
        if ($overriddenPackageHandle !== '') {
            $pkgHandle = $overriddenPackageHandle;
        }

        $prefix = $r->isOverride() ? true : $pkgHandle;
        $class = core_class('Block\\' . $txt->camelcase($btHandle) . '\\Controller', $prefix);

        return class_exists($class) ? $class : null;
    }

    /**
     * Clears output and record caches.
     */
    public static function clearCache()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $sm = $db->getSchemaManager();
        $tableNames = array_map('strtolower', $sm->listTableNames());
        if (in_array('config', $tableNames, true)) {
            $platform = $db->getDatabasePlatform();
            foreach ($sm->listTableColumns('Blocks') as $tableColumn) {
                if (strcasecmp($tableColumn->getName(), 'btCachedBlockRecord') === 0) {
                    $db->query('update Blocks set btCachedBlockRecord = null');
                    break;
                }
            }
            if (in_array('collectionversionblocksoutputcache', $tableNames, true)) {
                $db->exec($platform->getTruncateTableSQL('CollectionVersionBlocksOutputCache'));
            }
        }
    }

    /**
     * @deprecated use the installBlockType method
     *
     * @param mixed $btHandle
     * @param mixed $pkg
     *
     * @return \Concrete\Core\Entity\Block\BlockType\BlockType
     */
    public static function installBlockTypeFromPackage($btHandle, $pkg)
    {
        return static::installBlockType($btHandle, $pkg);
    }
}
