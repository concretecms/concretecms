<?php
namespace Concrete\Controller\Dialog\Page;

class Design extends \Concrete\Controller\Panel\Page\Design
{
    protected $viewPath = '/dialogs/page/design';

    public function canAccess()
    {
        return $this->permissions->canEditPageType() || parent::canAccess();
    }
}
