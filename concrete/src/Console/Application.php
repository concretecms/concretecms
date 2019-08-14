<?php

namespace Concrete\Core\Console;

use Concrete\Core\Application\Application as CMSApplication;
use Symfony\Component\Console\Application as SymfonyApplication;

/**
 * @since 5.7.5
 */
class Application extends SymfonyApplication
{
    /**
     * @var CMSApplication
     * @since 8.2.0
     */
    protected $app;

    public function __construct(CMSApplication $app)
    {
        $this->app = $app;
        parent::__construct('concrete5', $this->app->make('config')->get('concrete.version'));
    }

    /**
     * Get the concrete5 application instance
     * @return \Concrete\Core\Application\Application
     * @since 8.5.0
     */
    public function getConcrete5()
    {
        return $this->app;
    }
}
