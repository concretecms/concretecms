<?php

declare(strict_types=1);

namespace Concrete\Controller\Panel;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Application\Service\UserInterface\Help\HelpPanelMessageFormatter;
use Concrete\Core\Application\Service\UserInterface\Help\Message;
use Concrete\Core\Page\Page;

class Help extends UserInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/panels/help';

    public function view()
    {
        $this->set('config', $this->app->make('config'));
        $this->set('page', $this->getPage());
        $this->set('message', $this->getHelpMessage());
        $this->set('messageFormatter', $this->app->make(HelpPanelMessageFormatter::class));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    protected function canAccess()
    {
        return true;
    }

    protected function getHelpMessage(): ?Message
    {
        $helpMessageIdentifier = $this->getHelpMessageIdentifier();

        return $helpMessageIdentifier === '' ? null : $this->app->make('help/dashboard')->getMessage($helpMessageIdentifier);
    }

    protected function getHelpMessageIdentifier(): string
    {
        $page = $this->getPage();

        return $page === null ? '' : (string) $page->getCollectionPath();
    }
    
    protected function getPage(): ?Page
    {
        $cID = $this->request->query->get('cID');
        if (empty($cID) || is_array($cID)) {
            return null;
        }
        $page = Page::getByID((int) $cID);

        return $page && !$page->isError() ? $page : null;
    }

}
