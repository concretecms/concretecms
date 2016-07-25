<?php
namespace Concrete\Controller\Element\Attribute;

use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Attribute\Category;

class SiteStandardListHeader extends StandardListHeader
{
    public function getElement()
    {
        return 'attribute/site_standard_list_header';
    }

}
