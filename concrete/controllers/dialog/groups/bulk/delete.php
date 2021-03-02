<?php /** @noinspection PhpUnused */

/** @noinspection PhpDeprecationInspection */

namespace Concrete\Controller\Dialog\Groups\Bulk;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\User\Group\Command\DeleteGroupCommand;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\User;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\SanitizeService;
use Exception;

class Delete extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/groups/bulk/delete';
    /** @var Group[] */
    protected $groups = [];
    protected $canEdit = false;

    public function view()
    {
        $this->set('groups', $this->groups);
    }

    public function submit()
    {
        $app = Facade::getFacadeApplication();
        $r = new EditResponse();

        if (!$this->validateAction()) {
            $r->setError(new Exception(t('Invalid Token')));
            $r->outputJSON();
            $this->app->shutdown();
        }

        /** @var User $u */
        $u = $this->app->make(User::class);

        if (!$u->isSuperUser()) {
            $r->setError(new Exception(t('You need to be a super user to delete groups.')));
            $r->outputJSON();
            $this->app->shutdown();
        }

        $count = 0;

        if (count($this->groups) > 0) {
            foreach ($this->groups as $group) {

                if ($app->executeCommand(new DeleteGroupCommand($group->getGroupID())) !== false) {
                    $count++;
                }
            }
        }

        $r->setMessage(t2('%s group deleted', '%s groups deleted', $count));
        $r->setTitle(t('Groups Deleted'));
        $r->setRedirectURL(Url::to('/dashboard/users/groups'));
        $r->outputJSON();
    }

    protected function canAccess()
    {
        $this->populateGroups();
        return $this->canEdit;
    }

    protected function populateGroups()
    {
        /** @var SanitizeService $sh */
        $sh = $this->app->make(SanitizeService::class);

        if (is_array($this->request('item'))) {
            $this->groups = [];
            foreach ($this->request('item') as $uID) {
                $group = Group::getByID($sh->sanitizeInt($uID));
                if ($group instanceof Group) {
                    $this->groups[] = $group;
                }
            }
        }

        $this->canEdit = true;

        return $this->canEdit;
    }
}
