<?php
namespace Concrete\Controller\Dialog\Help;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Application\UserInterface\Welcome\Modal\IntroductionModal;
use Concrete\Core\User\User;

class Help extends UserInterface
{
    protected $viewPath = '/dialogs/help/help';

    public function view()
    {
        $modal = $this->app->make(IntroductionModal::class);
        $this->set('modal', $modal);
    }

    public function canAccess()
    {
        $token = $this->app->make('token');
        return $token->validate('view_help', $this->request->request->get('ccm_token'));
    }
}
