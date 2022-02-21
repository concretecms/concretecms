<?php
namespace Concrete\Controller\Panel\Detail\Page;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Utility\Service\Validation\Numbers;

class Versions extends BackendInterfacePageController
{
    protected $viewPath = '/panels/details/page/versions';

    public function canAccess()
    {
        return $this->permissions->canViewPageVersions();
    }

    public function view()
    {
        $this->set('ih', $this->app->make('helper/concrete/ui'));
        /** @var Numbers $val */
        $val = $this->app->make('helper/validation/numbers');
        $this->set('cID', $this->page->getCollectionID());

        $versions = [];
        if ($this->request->query->has('cvID')) {
            $cvIDs = $this->request->query->get('cvID');
            if (is_array($cvIDs)) {
                foreach ($cvIDs as $index => $cvID) {
                    if ($val->integer($cvID)) {
                        $versions[] = $cvID;
                    }
                }
            }
        }
        if (empty($versions)) {
            throw new UserMessageException(t('Invalid Request.'));
        }
        $this->set('versions', $versions);
    }
}
