<?php
namespace Concrete\Core\Page;

use PageType;
use Core;

class Template
{
    public static function exportList($xml)
    {
        $nxml = $xml->addChild('pagetemplates');
        $list = static::getList();
        foreach ($list as $pt) {
            $pt->export($nxml);
        }
    }

    public static function getByHandle($pTemplateHandle)
    {
        $em = \ORM::entityManager();

        return $em->getRepository('\Concrete\Core\Entity\Page\Template')
            ->findOneBy(
                ['pTemplateHandle' => $pTemplateHandle]
            );
    }

    public static function getByID($pTemplateID)
    {
        if ($pTemplateID) {
            $em = \ORM::entityManager();

            return $em->find('\Concrete\Core\Entity\Page\Template', $pTemplateID);
        }
    }

    protected static function sort($list)
    {
        $wrapperList = [];
        foreach ($list as $item) {
            $wrapperList[] = ['item' => $item, 'text' => $item->getPageTemplateDisplayName('text')];
        }
        usort(
            $wrapperList,
            function ($a, $b) {
                return strcasecmp($a['text'], $b['text']);
            }
        );
        $result = [];
        foreach ($wrapperList as $array) {
            $result[] = $array['item'];
        }

        return $result;
    }

    public static function getListByPackage($pkg)
    {
        $em = \ORM::entityManager();
        $list = $em->getRepository('\Concrete\Core\Entity\Page\Template')
            ->findBy(
                ['pkgID' => $pkg->getPackageID()]
            );
        $list = self::sort($list);

        return $list;
    }

    public static function getList($includeInternal = false)
    {
        $em = \ORM::entityManager();
        $args = ['pTemplateIsInternal' => $includeInternal];
        $list = $em->getRepository('\Concrete\Core\Entity\Page\Template')->findBy(
            $args, ['pTemplateID' => 'asc']
        );
        $list = self::sort($list);

        return $list;
    }

    public static function add(
        $pTemplateHandle,
        $pTemplateName,
        $pTemplateIcon = FILENAME_PAGE_TEMPLATE_DEFAULT_ICON,
        $pkg = null,
        $pTemplateIsInternal = false
    ) {
        $pkgID = (!is_object($pkg)) ? 0 : $pkg->getPackageID();

        $template = new \Concrete\Core\Entity\Page\Template();
        $template->pTemplateHandle = $pTemplateHandle;
        $template->pTemplateName = $pTemplateName;
        $template->pTemplateIcon = $pTemplateIcon;
        $template->pkgID = $pkgID;
        $template->pTemplateIsInternal = (bool) $pTemplateIsInternal;

        $em = \ORM::entityManager();
        $em->persist($template);
        $em->flush();

        // now that we have added a template, we need to find any page types that can use this template (any page types that allow ALL page templates or all + a template not of this kind)
        // and we need to update them to have a reference to this template defaults
        $ptlist = PageType::getList();
        foreach ($ptlist as $pt) {
            $pt->rescanPageTypeComposerOutputControlObjects();
        }

        return $template;
    }

    public function getIcons()
    {
        $f = Core::make('helper/file');

        return $f->getDirectoryContents(DIR_FILES_PAGE_TEMPLATE_ICONS);
    }
}
