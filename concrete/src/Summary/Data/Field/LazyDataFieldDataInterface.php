<?php
namespace Concrete\Core\Summary\Data\Field;

use Concrete\Core\Summary\Category\CategoryMemberInterface;

interface LazyDataFieldDataInterface extends DataFieldDataInterface
{

    public function loadDataFieldDataFromCategoryMember(CategoryMemberInterface $categoryMember) : DataFieldDataInterface;

}
