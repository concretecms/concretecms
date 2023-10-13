<?php

namespace Concrete\Core\Twig;

use Concrete\Core\Support\Facade\Application;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigExtensions extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('outputInlineEditorInitJSFunction', [$this, 'outputInlineEditorInitJSFunction']),
        ];
    }

    public function outputInlineEditorInitJSFunction(): string
    {
        $app = Application::getFacadeApplication();

        return $app->make('editor')->outputInlineEditorInitJSFunction();
    }
}
