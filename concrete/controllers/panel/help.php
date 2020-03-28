<?php

namespace Concrete\Controller\Panel;

use Concrete\Controller\Backend\UserInterface;

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
        $this->set('showIntro', true);
        $this->set('config', $this->app->make('config'));
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
}
