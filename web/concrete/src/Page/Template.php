<?php
namespace Concrete\Core\Page;

use PageType;
use Concrete\Core\Support\Facade\Database;
use \Concrete\Core\Package\PackageList;
use Core;

/**
 * @Entity
 * @Table(name="PageTemplates")
 */
class Template
{
    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $pTemplateID;

    /** @Column(type="string") **/
    protected $pTemplateHandle;

    /** @Column(type="string") **/
    protected $pTemplateIcon = FILENAME_PAGE_TEMPLATE_DEFAULT_ICON;

    /** @Column(type="string") **/
    protected $pTemplateName;

    /** @Column(type="boolean") **/
    protected $pTemplateIsInternal = false;

    /** @Column(type="integer") **/
    protected $pkgID = 0;

    public static function exportList($xml)
    {
        $nxml = $xml->addChild('pagetemplates');
        $list = static::getList();
        foreach ($list as $pt) {
            $type = $nxml->addChild('pagetemplate');
            $type->addAttribute('icon', $pt->getPageTemplateIcon());
            $type->addAttribute('name', Core::make('helper/text')->entities($pt->getPageTemplateName()));
            $type->addAttribute('handle', $pt->getPageTemplateHandle());
            $type->addAttribute('package', $pt->getPackageHandle());
            $type->addAttribute('internal', $pt->isPageTemplateInternal());
        }
    }

    public function getPageTemplateID()
    {
        return $this->pTemplateID;
    }

    public function getPageTemplateName()
    {
        return $this->pTemplateName;
    }

    public function getPageTemplateHandle()
    {
        return $this->pTemplateHandle;
    }

    public function isPageTemplateInternal()
    {
        return $this->pTemplateIsInternal;
    }

    public function getPageTemplateIcon()
    {
        return $this->pTemplateIcon;
    }

    public function getPackageID()
    {
        return $this->pkgID;
    }

    public function getPackageHandle()
    {
        return PackageList::getHandle($this->pkgID);
    }

    /** Returns the display name for this page template (localized and escaped accordingly to $format)
     * @param string $format = 'html'
     *   Escape the result in html format (if $format is 'html').
     *   If $format is 'text' or any other value, the display name won't be escaped.
     * @return string
     */
    public function getPageTemplateDisplayName($format = 'html')
    {
        $value = tc('PageTemplateName', $this->getPageTemplateName());
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    public static function getByHandle($pTemplateHandle)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        return $em->getRepository('\Concrete\Core\Page\Template')
            ->findOneBy(
                array('pTemplateHandle' => $pTemplateHandle)
            );
    }

    public static function getByID($pTemplateID)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        return $em->find('\Concrete\Core\Page\Template', $pTemplateID);
    }

    public function delete()
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $em->remove($this);
        $em->flush();
    }

    protected static function sort($list)
    {
        usort(
            $list,
            function ($a, $b) {
                return strcasecmp($a->getPageTemplateDisplayName('text'), $b->getPageTemplateDisplayName('text'));
            }
        );
        return $list;
    }

    public static function getListByPackage($pkg)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $list = $em->getRepository('\Concrete\Core\Page\Template')
            ->findBy(
                array('pkgID' => $pkg->getPackageID())
            );
        $list = self::sort($list);
        return $list;
    }

    public static function getList($includeInternal = false)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $args = array('pTemplateIsInternal' => $includeInternal);
        $list = $em->getRepository('\Concrete\Core\Page\Template')->findBy(
            $args, array('pTemplateID' => 'asc')
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

        $template = new self();
        $template->pTemplateHandle = $pTemplateHandle;
        $template->pTemplateName = $pTemplateName;
        $template->pTemplateIcon = $pTemplateIcon;
        $template->pkgID = $pkgID;
        $template->pTemplateIsInternal = (bool) $pTemplateIsInternal;

        $em = Database::get()->getEntityManager();
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

    public function update($pTemplateHandle, $pTemplateName, $pTemplateIcon = FILENAME_PAGE_TEMPLATE_DEFAULT_ICON)
    {
        $this->pTemplateHandle = $pTemplateHandle;
        $this->pTemplateName = $pTemplateName;
        $this->pTemplateIcon = $pTemplateIcon;

        $em = Database::get()->getEntityManager();
        $em->persist($this);
        $em->flush();
    }

    public function getIcons()
    {
        $f = Core::make('helper/file');
        return $f->getDirectoryContents(DIR_FILES_PAGE_TEMPLATE_ICONS);
    }

    public function getPageTemplateIconImage()
    {
        $src = REL_DIR_FILES_PAGE_TEMPLATE_ICONS . '/' . $this->pTemplateIcon;
        $iconImg = '<img src="' . $src . '" height="' . \Config::get('concrete.icons.page_template.height') . '" width="' . \Config::get('concrete.icons.page_template.width') . '" alt="' . $this->getPageTemplateDisplayName(
            ) . '" title="' . $this->getPageTemplateDisplayName() . '" />';
        return $iconImg;
    }
}
