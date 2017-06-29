<?php
namespace Concrete\Core\File\Image\Thumbnail\Type;

use Database;
use Doctrine\ORM\Mapping as ORM;
use \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type as ThumbnailType;

class Type
{
    const RESIZE_PROPORTIONAL = ThumbnailType::RESIZE_PROPORTIONAL;
    const RESIZE_EXACT = ThumbnailType::RESIZE_EXACT;
    const RESIZE_DEFAULT = ThumbnailType::RESIZE_DEFAULT;

    /**
     * @return \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type[]
     */
    public static function getList()
    {
        $em = \ORM::entityManager();

        return $em->getRepository('\Concrete\Core\Entity\File\Image\Thumbnail\Type\Type')->findBy(array(), array('ftTypeWidth' => 'asc'));
    }

    /**
     * @return \Concrete\Core\File\Image\Thumbnail\Type\Version[]
     */
    public static function getVersionList()
    {
        $types = static::getList();
        $versions = array();
        foreach ($types as $type) {
            $versions[] = $type->getBaseVersion();
            $versions[] = $type->getDoubledVersion();
        }

        return $versions;
    }

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

    public static function getByID($id)
    {
        $em = \ORM::entityManager();
        $r = $em->find('\Concrete\Core\Entity\File\Image\Thumbnail\Type\Type', $id);

        return $r;
    }

    /**
     * @param $ftTypeHandle
     *
     * @return \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type
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
            ->findOneBy(array('ftTypeHandle' => $ftTypeHandle));

        $cache->save($item->set($r));

        return $r;
    }

    public static function getSizingOptions()
    {
        return [
            self::RESIZE_PROPORTIONAL,
            self::RESIZE_EXACT,
            self::RESIZE_DEFAULT
        ];
    }
}
