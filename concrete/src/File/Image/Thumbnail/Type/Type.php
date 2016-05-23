<?php
namespace Concrete\Core\File\Image\Thumbnail\Type;

use Database;
use Doctrine\ORM\Mapping as ORM;

class Type
{

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
        $item = \Core::make('cache/request')->getItem('file/image/thumbnail/' . $ftTypeHandle);
        if (!$item->isMiss()) {
            return $item->get();
        }

        $em = \ORM::entityManager();
        $r = $em->getRepository('\Concrete\Core\Entity\File\Image\Thumbnail\Type\Type')
            ->findOneBy(array('ftTypeHandle' => $ftTypeHandle));

        $item->set($r);

        return $r;
    }

}
