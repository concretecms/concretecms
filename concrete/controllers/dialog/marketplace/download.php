<?php
namespace Concrete\Controller\Dialog\Marketplace;

use Concrete\Controller\Backend\UserInterface\MarketplaceItem;
use Concrete\Core\Package\Package;
use Concrete\Core\Support\Facade\Package as PackageService;

class Download extends MarketplaceItem
{
    protected $viewPath = '/dialogs/marketplace/download';

    public function view()
    {
        $error = \Core::make('helper/validation/error');
        $r = $this->item->download();
        if ($r != false) {
            $error->add($r);
        }

        if (!$error->has()) {
            $p = PackageService::getClass($this->item->getHandle());
            if (is_object($p)) {
                $tests = $p->testForInstall();
                if (is_object($tests)) {
                    $error->add($tests);
                } else {
                    try {
                        PackageService::install($p, []);
                    } catch (\Exception $e) {
                        $error->add($e->getMessage());
                    }
                }
            }
        }
        $this->set('error', $error);
        $this->set('mri', $this->item);
    }
}
