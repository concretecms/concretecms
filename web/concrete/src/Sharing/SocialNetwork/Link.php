<?php
namespace Concrete\Core\Sharing\SocialNetwork;

use Database;
use Doctrine\ORM\Mapping as ORM;

class Link
{
    public static function getList()
    {
        $em = \ORM::entityManager();
        return $em->getRepository('\Concrete\Core\Entity\Sharing\SocialNetwork\Link')->findBy(array(), array('ssHandle' => 'asc'));
    }

    public static function exportList($node)
    {
        $child = $node->addChild('sociallinks');
        $list = static::getList();
        foreach ($list as $link) {
            $linkNode = $child->addChild('link');
            $linkNode->addAttribute('service', $link->getServiceObject()->getHandle());
            $linkNode->addAttribute('url', $link->getURL());
        }
    }

    public static function getByID($id)
    {
        $em = \ORM::entityManager();
        $r = $em->find('\Concrete\Core\Entity\Sharing\SocialNetwork\Link', $id);

        return $r;
    }

    public static function getByServiceHandle($ssHandle)
    {
        $em = \ORM::entityManager();
        return $em->getRepository('\Concrete\Core\Entity\Sharing\SocialNetwork\Link')->findOneBy(
            array('ssHandle' => $ssHandle)
        );
    }
}
