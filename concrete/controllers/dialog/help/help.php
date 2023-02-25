<?php
namespace Concrete\Controller\Dialog\Help;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Application\UserInterface\Welcome\Modal\Modal;
use Concrete\Core\Application\UserInterface\Welcome\Type\IntroductionItemFactory;
use Concrete\Core\Application\UserInterface\Welcome\Type\IntroductionType;
use Concrete\Core\User\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class Help extends UserInterface
{
    protected $viewPath = '/dialogs/help/help';

    public function view()
    {
        $modal = new Modal($this->app->make(IntroductionType::class));
        $this->set('modal', $modal);
    }

    /**
     * This method is used by the help component directly. We have to have the help
     * component query the backend for its information because in the initial introduction
     * flow we render the items in the introductiontype at the same time as we grab
     * the original survey, so we don't know what the answers are going to be.
     */
    public function getItems(): JsonResponse
    {
        $introductionItemFactory = $this->app->make(IntroductionItemFactory::class);
        return new JsonResponse($introductionItemFactory->getItems());
    }

    public function canAccess()
    {
        $token = $this->app->make('token');
        return $token->validate('view_help', $this->request->request->get('ccm_token'));
    }
}
