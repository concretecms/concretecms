<?php
namespace Concrete\Core\Page;

use Concrete\Core\Backup\ContentExporter;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Html\Object\HeadLink;
use Concrete\Core\Http\Request;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Database;
use Doctrine\ORM\Mapping as ORM;

class Feed
{

    public static function getList()
    {
        $em = \ORM::entityManager();
        return $em->getRepository('\Concrete\Core\Entity\Page\Feed')->findBy(array(), array('pfTitle' => 'asc'));
    }

    public static function exportList(\SimpleXMLElement $node)
    {
        $child = $node->addChild('pagefeeds');
        $list = static::getList();
        foreach ($list as $feed) {
            $feedNode = $child->addChild('feed');
            if ($feed->getParentID()) {
                $feedNode->addChild('parent', ContentExporter::replacePageWithPlaceHolder($feed->getParentID()));
            }
            $feedNode->addChild('title', $feed->getTitle());
            $feedNode->addChild('description', $feed->getDescription());
            $feedNode->addChild('handle', $feed->getHandle());
            if ($feed->getIncludeAllDescendents()) {
                $feedNode->addChild('descendents', 1);
            }
            if ($feed->getDisplayAliases()) {
                $feedNode->addChild('aliases', 1);
            }
            if ($feed->getDisplayFeaturedOnly()) {
                $feedNode->addChild('featured', 1);
            }
            if ($feed->getPageTypeID()) {
                $feedNode->addChild('pagetype', ContentExporter::replacePageTypeWithPlaceHolder($feed->getPageTypeID()));
            }
            if ($feed->getTypeOfContentToDisplay() == 'S') {
                $type = $feedNode->addChild('contenttype');
                $type->addAttribute('type', 'description');
            } else {
                $area = $feedNode->addChild('contenttype');
                $area->addAttribute('type', 'area');
                $area->addAttribute('handle', $feed->getAreaHandleToDisplay());
            }
        }
    }

    public static function getByID($id)
    {
        $em = \ORM::entityManager();
        $r = $em->find('\Concrete\Core\Entity\Page\Feed', $id);

        return $r;
    }

    /**
     * Get a PageFeed by its handle
     * @param $pfHandle
     * @return self|null
     */
    public static function getByHandle($pfHandle)
    {
        $em = \ORM::entityManager();
        return $em->getRepository('\Concrete\Core\Entity\Page\Feed')->findOneBy(
            array('pfHandle' => $pfHandle)
        );
    }

}
