<?php
namespace Concrete\Core\Page\Type\Composer;

use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type;

interface FormElementControllerInterface
{
    function setPageTypeObject(Type $type);
    function setPageObject(Page $page);
    function setTargetPageObject(Page $targetPage);
}