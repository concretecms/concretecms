<?php
namespace Concrete\Core\Page\Type\PublishTarget\Configuration;

use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\PublishTarget\Type\Type;
use Loader;
use \Concrete\Core\Foundation\Object;
use \Concrete\Core\Page\Type\PublishTarget\Type\Type as PageTypePublishTargetType;

abstract class Configuration extends Object
{

    abstract public function canPublishPageTypeBeneathTarget(\Concrete\Core\Page\Type\Type $pagetype, Page $page);

    public function getPageTypePublishTargetTypeID()
    {
        return $this->ptPublishTargetTypeID;
    }

    public function getPageTypePublishTargetTypeHandle()
    {
        return $this->ptPublishTargetTypeHandle;
    }

    public function getPageTypePublishTargetTypeObject()
    {
        return Type::getByID($this->ptPublishTargetTypeID);
    }

    public function __construct(PageTypePublishTargetType $type)
    {
        $this->ptPublishTargetTypeID = $type->getPageTypePublishTargetTypeID();
        $this->ptPublishTargetTypeHandle = $type->getPageTypePublishTargetTypeHandle();
        $this->pkgHandle = $type->getPackageHandle();
    }

    public function export($cxml)
    {
        $target = $cxml->addChild('target');
        $target->addAttribute('handle', $this->getPageTypePublishTargetTypeHandle());
        $target->addAttribute('package', $this->pkgHandle);
        return $target;
    }

    public function getDefaultParentPageID()
    {
        return 0;
    }


    public function includeChooseTargetForm($pagetype = false, $target = false)
    {
        Loader::element(
            DIRNAME_PAGE_TYPES . '/' . DIRNAME_ELEMENTS_PAGE_TYPES_PUBLISH_TARGET_TYPES . '/' . DIRNAME_ELEMENTS_PAGE_TYPES_PUBLISH_TARGET_TYPES_FORM . '/' . $this->getPageTypePublishTargetTypeHandle(
            ),
            array('configuration' => $this, 'target' => $target, 'pagetype' => $pagetype),
            $this->pkgHandle
        );
    }

    public function getPageTypePublishTargetConfiguredTargetParentPageID()
    {
        return 0;
    }

}
