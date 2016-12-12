<?php
namespace Concrete\Controller\Dialog\Marketplace;

use Concrete\Controller\Backend\UserInterface\MarketplaceItem;
use Concrete\Core\Package\Package;

class Download extends MarketplaceItem
{

    protected $viewPath = '/dialogs/marketplace/download';

    public function view()
    {
        $error = \Core::make('helper/validation/error');
        $r = $this->item->download();
        if ($r != false) {
            if (is_array($r)) {
                $errors = Package::mapError($r);
                foreach($errors as $e) {
                    $error->add($e);
                }
            } else {
                $error->add($r);
            }
        }

        if (!$error->has()) {
            $tests = Package::testForInstall($this->item->getHandle());
            if (is_array($tests)) {
                $results = Package::mapError($tests);
                foreach($results as $te) {
                    $error->add($te);
                }
            } else {
                $p = Package::getClass($this->item->getHandle());
                try {
                    $p->install();
                } catch(\Exception $e) {
                    $error->add($e->getMessage());
                }
            }
        }

        $this->set('error', $error);
        $this->set('mri', $this->item);
    }

}
