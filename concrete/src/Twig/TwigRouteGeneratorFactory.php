<?php

namespace Concrete\Core\Twig;

use Pagerfanta\RouteGenerator\RouteGeneratorFactoryInterface;
use Pagerfanta\RouteGenerator\RouteGeneratorInterface;

class TwigRouteGeneratorFactory implements RouteGeneratorFactoryInterface
{
    public function create(array $options = []): RouteGeneratorInterface
    {
        return new TwigRouteGenerator($options);
    }
}
