<?php
namespace Concrete\Controller\Dialog\Help;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Announcement\Item\Factory\WelcomeItemFactory;
use Symfony\Component\HttpFoundation\JsonResponse;

class Help extends UserInterface
{
    protected $viewPath = '/dialogs/help/help';

    public function view()
    {
        $welcomeItemFactory = $this->app->make(WelcomeItemFactory::class);
        $this->set('items', $welcomeItemFactory->getItems());
    }

    /**
     * This method is used by the help component directly. We have to have the help
     * component query the backend for its information because in the initial introduction
     * flow we render the items in the welcome type at the same time as we grab
     * the original survey, so we don't know what the answers are going to be.
     */
    public function getItems(): JsonResponse
    {
        $welcomeItemFactory = $this->app->make(WelcomeItemFactory::class);
        return new JsonResponse($welcomeItemFactory->getItems());
    }

    public function canAccess()
    {
        $token = $this->app->make('token');
        return $token->validate('view_help', $this->request->request->get('ccm_token'));
    }
}
