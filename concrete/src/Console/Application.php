<?php

namespace Concrete\Core\Console;

use Concrete\Core\Application\Application as CMSApplication;
use Symfony\Component\Console\Application as SymfonyApplication;

class Application extends SymfonyApplication
{
    /**
     * @var CMSApplication
     */
    protected $app;

    public function __construct(CMSApplication $app)
    {
        $this->app = $app;
        parent::__construct('concrete', $this->app->make('config')->get('concrete.version'));
    }
    
    /**
     * @deprecated
     */
    public function getConcrete5()
    {
        return $this->getConcrete();
    }

    /**
     * Get the concrete5 application instance
     * @return \Concrete\Core\Application\Application
     */
    public function getConcrete()
    {
        return $this->app;
    }
}
