<?php

namespace Concrete\Core\File\Image\Thumbnail\Type;

use Concrete\Core\Entity\File\Image\Thumbnail\Type\Type as ThumbnailType;
use Concrete\Core\Support\Facade\Application;

class Type
{
    /**
     * Thumbnail sizing mode: proportional.
     *
     * @var string
     */
    const RESIZE_PROPORTIONAL = ThumbnailType::RESIZE_PROPORTIONAL;

    /**
     * Thumbnail sizing mode: exact dimensions.
     *
     * @var string
     */
    const RESIZE_EXACT = ThumbnailType::RESIZE_EXACT;

    /**
     * Default thumbnail sizing mode.
     *
     * @var string
     */
    const RESIZE_DEFAULT = ThumbnailType::RESIZE_DEFAULT;

    /**
     * Get the list of all the available thumbnail types.
     *
     * @return \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type[]
     */
    public static function getList()
    {
        $em = \ORM::entityManager();

        return $em->getRepository('\Concrete\Core\Entity\File\Image\Thumbnail\Type\Type')->findBy([], ['ftTypeWidth' => 'asc']);
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

        $types = static::getList();
        $versions = [];

        foreach ($types as $type) {
            $versions[] = $type->getBaseVersion();
            if ($config->get('concrete.file_manager.images.create_high_dpi_thumbnails')) {
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
            $linkNode = $child->addChild('thumbnailtype');
            $linkNode->addAttribute('name', $link->getName());
            $linkNode->addAttribute('handle', $link->getHandle());
            $linkNode->addAttribute('width', $link->getWidth());
            if ($link->getHeight()) {
                $linkNode->addAttribute('height', $link->getHeight());
            }
            if ($link->isRequired()) {
                $linkNode->addAttribute('required', $link->isRequired());
            }
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
        $em = \ORM::entityManager();
        $r = $em->find('\Concrete\Core\Entity\File\Image\Thumbnail\Type\Type', $id);

        return $r;
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
        // ugh doctrine doesn't cache when searching by ftTypeHandle
        $cache = \Core::make('cache/request');
        $item = $cache->getItem('file/image/thumbnail/' . $ftTypeHandle);
        if (!$item->isMiss()) {
            return $item->get();
        }

        $em = \ORM::entityManager();
        $r = $em->getRepository('\Concrete\Core\Entity\File\Image\Thumbnail\Type\Type')
            ->findOneBy(['ftTypeHandle' => $ftTypeHandle]);

        $cache->save($item->set($r));

        return $r;
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
