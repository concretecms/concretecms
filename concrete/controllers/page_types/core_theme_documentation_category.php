<?php
namespace Concrete\Controller\PageType;

use Concrete\Core\Page\Controller\PageTypeController;

class CoreThemeDocumentationCategory extends PageTypeController
{

    public function view()
    {
        return $this->buildRedirectToFirstAccessibleChildPage();
    }

}
