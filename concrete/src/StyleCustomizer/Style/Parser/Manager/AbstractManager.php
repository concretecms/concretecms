<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser\Manager;
use Concrete\Core\Application\Application;
use Concrete\Core\StyleCustomizer\Style\Parser\ParserInterface;

abstract class AbstractManager implements ManagerInterface
{

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getParserFromType(string $type): ParserInterface
    {
        $method = 'create'.camelcase($type).'Parser';
        if (method_exists($this, $method)) {
            return $this->$method();
        } else {
            throw new \InvalidArgumentException("Style parser [$type] not supported.");
        }
    }



}