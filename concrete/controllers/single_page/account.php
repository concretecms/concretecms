<?php
namespace Concrete\Controller\SinglePage;

use Concrete\Core\Page\Desktop\DesktopList;
use Concrete\Core\Routing\Redirect;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Controller\AccountPageController;

class Account extends AccountPageController
{

    public function view()
    {
        $child = $this->getPageObject()->getFirstChild();
        return Redirect::to($child);
    }
}
