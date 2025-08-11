<?php

namespace Concrete\Core\File\Image\Thumbnail\Type;

use Concrete\Core\Cache\Level\RequestCache;
use Concrete\Core\Entity\File\Image\Thumbnail\Type\Type as ThumbnailTypeEntity;
use Concrete\Core\File\Set\Set as FileSet;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;

class Type
{
    /**
     * Thumbnail sizing mode: proportional.
     *
     * @var string
     */
    const RESIZE_PROPORTIONAL = ThumbnailTypeEntity::RESIZE_PROPORTIONAL;

    /**
     * Thumbnail sizing mode: exact dimensions.
     *
     * @var string
     */
    const RESIZE_EXACT = ThumbnailTypeEntity::RESIZE_EXACT;

    /**
     * Default thumbnail sizing mode.
     *
     * @var string
     */
    const RESIZE_DEFAULT = ThumbnailTypeEntity::RESIZE_DEFAULT;

    /**
     * Get the list of all the available thumbnail types.
     *
     * @return \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type[]
     */
    public static function getList()
    {
        $app = Application::getFacadeApplication();

        /** @var RequestCache $cache */
        $cache = $app->make('cache/request');
        $item = $cache->getItem('file/image/thumbnail/type/list');
        if ($item->isHit()) {
            return $item->get();
        }

        $em = $app->make(EntityManagerInterface::class);
        $list = $em->getRepository(ThumbnailTypeEntity::class)->findBy([], ['ftTypeWidth' => 'asc']);

        if ($item->isMiss()) {
            $cache->save($item->set($list));
        }

        return $list;
    }

    /**
     * Get the list of all the available thumbnail type versions.
     *
     * @return \Concrete\Core\File\Image\Thumbnail\Type\Version[]
     */
    public static function getVersionList()
    {
        $app = Application::getFacadeApplication();
        $config = $app->make('config');
        $createHightDPIVersions = (bool) $config->get('concrete.file_manager.images.create_high_dpi_thumbnails');

        $types = static::getList();
        $versions = [];

        foreach ($types as $type) {
            $versions[] = $type->getBaseVersion();
            if ($createHightDPIVersions) {
                $versions[] = $type->getDoubledVersion();
            }
        }

        return $versions;
    }

    /**
     * Export the list of all the thumbnail types.
     *
     * @param \SimpleXMLElement $node the parent node to append the thumbnailtypes XML node to
     */
    public static function exportList($node)
    {
        $child = $node->addChild('thumbnailtypes');
        $list = static::getList();
        foreach ($list as $link) {
            $link->export($child);
        }
    }

    /**
     * Get a thumbnail type given its id.
     *
     * @param int $id
     *
     * @return \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type|null
     */
    public static function getByID($id)
    {
        if ($id) {
            $app = Application::getFacadeApplication();
            $em = $app->make(EntityManagerInterface::class);
            $result = $em->find(ThumbnailTypeEntity::class, $id);
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * Get a thumbnail type given its handle.
     *
     * @param string $ftTypeHandle
     *
     * @return \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type|null
     */
    public static function getByHandle($ftTypeHandle)
    {
        $ftTypeHandle = (string) $ftTypeHandle;
        $app = Application::getFacadeApplication();
        $cache = $app->make('cache/request');
        $item = $cache->getItem('file/image/thumbnail/' . $ftTypeHandle);
        if ($item->isMiss()) {
            $em = $app->make(EntityManagerInterface::class);
            $repo = $em->getRepository(ThumbnailTypeEntity::class);
            $result = $repo->findOneBy(['ftTypeHandle' => $ftTypeHandle]);
            $cache->save($item->set($result));
        } else {
            $result = $item->get();
        }

        return $result;
    }

    /**
     * Get all the available thumbnail sizing options.
     *
     * @return string[] The list of all the Type::RESIZE_... constants.
     */
    public static function getSizingOptions()
    {
        return [
            self::RESIZE_PROPORTIONAL,
            self::RESIZE_EXACT,
            self::RESIZE_DEFAULT,
        ];
    }
}
