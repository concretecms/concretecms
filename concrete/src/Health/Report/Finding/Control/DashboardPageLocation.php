<?php
namespace Concrete\Core\Health\Report\Finding\Control;

use Concrete\Core\Health\Report\Finding\Control\Traits\SimpleSerializableAndDenormalizableClassTrait;
use Concrete\Core\Page\Page;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

abstract class DashboardPageLocation implements DashboardPageLocationInterface
{

    use SimpleSerializableAndDenormalizableClassTrait;

    public function getUrl(): string
    {
        $page = Page::getByPath($this->getPagePath());
        return (string) $page->getCollectionLink();
    }

}
