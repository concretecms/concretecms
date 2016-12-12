<?php
namespace Concrete\Core\Entity\Page;

use PageType;
use Concrete\Core\Support\Facade\Database;
use Concrete\Core\Package\PackageList;
use Core;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="PageTemplates")
 */
class Template
{
    /** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue **/
    public $pTemplateID;

    /** @ORM\Column(type="string") **/
    public $pTemplateHandle;

    /** @ORM\Column(type="string") **/
    public $pTemplateIcon = FILENAME_PAGE_TEMPLATE_DEFAULT_ICON;

    /** @ORM\Column(type="string") **/
    public $pTemplateName;

    /** @ORM\Column(type="boolean") **/
    public $pTemplateIsInternal = false;

    /** @ORM\Column(type="integer") **/
    public $pkgID = 0;

    public function export($node)
    {
        $type = $node->addChild('pagetemplate');
        $type->addAttribute('icon', $this->getPageTemplateIcon());
        $type->addAttribute('name', Core::make('helper/text')->entities($this->getPageTemplateName()));
        $type->addAttribute('handle', $this->getPageTemplateHandle());
        $type->addAttribute('package', $this->getPackageHandle());
        $type->addAttribute('internal', $this->isPageTemplateInternal());
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
     *
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

    public function delete()
    {
        $em = \ORM::entityManager('core');
        $em->remove($this);
        $em->flush();
    }

    public function update($pTemplateHandle, $pTemplateName, $pTemplateIcon = FILENAME_PAGE_TEMPLATE_DEFAULT_ICON)
    {
        $this->pTemplateHandle = $pTemplateHandle;
        $this->pTemplateName = $pTemplateName;
        $this->pTemplateIcon = $pTemplateIcon;

        $em = \ORM::entityManager();
        $em->persist($this);
        $em->flush();
    }

    public function getPageTemplateIconImage()
    {
        $src = REL_DIR_FILES_PAGE_TEMPLATE_ICONS . '/' . $this->pTemplateIcon;
        $iconImg = '<img src="' . $src . '" height="' . \Config::get('concrete.icons.page_template.height') . '" width="' . \Config::get('concrete.icons.page_template.width') . '" alt="' . $this->getPageTemplateDisplayName(
            ) . '" title="' . $this->getPageTemplateDisplayName() . '" />';

        return $iconImg;
    }
}
