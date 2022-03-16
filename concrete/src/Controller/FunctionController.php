<?php

declare(strict_types=1);

namespace Concrete\Core\Controller;

class FunctionController extends AbstractController
{
    /**
     * @var string
     */
    private $functionName;

    public function __construct(string $functionName)
    {
        parent::__construct();
        $this->functionName = $functionName;
    }

    public function getFunctionName(): string
    {
        return $this->functionName;
    }

    public function __invoke()
    {
        $functionName = $this->getFunctionName();
        return $functionName();
    }
}
