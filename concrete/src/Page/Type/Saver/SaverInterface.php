<?php
namespace Concrete\Core\Page\Type\Saver;

use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type;

interface SaverInterface
{
    public function saveForm(Page $page);
    public function setPageTypeObject(Type $type);
}
