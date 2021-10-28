<?php

namespace Concrete\Controller\SinglePage;

use Concrete\Core\Page\Controller\AccountPageController;

class Account extends AccountPageController
{
    public function view()
    {
        return $this->buildRedirectToFirstAccessibleChildPage();
    }
}
