<?php
namespace Concrete\Controller\Dialog\Help;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Application\UserInterface\Welcome\Modal\Modal;
use Concrete\Core\Application\UserInterface\Welcome\Type\IntroductionType;
use Concrete\Core\User\User;

class Help extends UserInterface
{
    protected $viewPath = '/dialogs/help/help';

    public function view()
    {
        $modal = new Modal($this->app->make(IntroductionType::class));
        $this->set('modal', $modal);
    }

    public function canAccess()
    {
        $token = $this->app->make('token');
        return $token->validate('view_help', $this->request->request->get('ccm_token'));
    }
}
