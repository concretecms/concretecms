<?php
namespace Concrete\Core\Sharing\SocialNetwork;

use Concrete\Core\Entity\Site\Site;
use Database;
use Doctrine\ORM\Mapping as ORM;

class Link
{
    public static function getList(Site $site = null)
    {
        $em = \ORM::entityManager();
        $site = is_object($site) ? $site : \Core::make('site')->getSite();
        return $em->getRepository('\Concrete\Core\Entity\Sharing\SocialNetwork\Link')->findBy(array('site' => $site), array('ssHandle' => 'asc'));
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

    public static function getByServiceHandle($ssHandle, Site $site = null)
    {
        $em = \ORM::entityManager();
        $site = is_object($site) ? $site : \Core::make('site')->getSite();
        return $em->getRepository('\Concrete\Core\Entity\Sharing\SocialNetwork\Link')->findOneBy(
            array('ssHandle' => $ssHandle, 'site' => $site)
        );
    }
}
