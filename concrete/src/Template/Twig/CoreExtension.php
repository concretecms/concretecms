<?php

namespace Concrete\Core\Template\Twig;

use Concrete\Core\Area\Area;
use Concrete\Core\Area\GlobalArea;
use Concrete\Core\File\File;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\View\View;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CoreExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('preg_replace', function ($subject, ...$args) {
                if ($subject instanceof Markup) {
                    $subject = (string) $subject;
                }

                array_splice($args, 2, 0, $subject);
                return preg_replace(...$args);
            })
        ];
    }

    public function getFunctions()
    {
        $functions = array_merge(
            $this->getBasicFunctions(),
            $this->getFileFunctions(),
            $this->getPageFunctions(),
        );

        return $functions;
    }

    private function getBasicFunctions(): array
    {
        return array_map(function ($function) {
            return new TwigFunction(...$function);
        }, [
            ['t', 't'],
            ['app', 'app'],
            ['url', [Url::class, 'to']],
            ['activeLocale', function (): string {
                return Localization::activeLocale();
            }],
            ['activeLanguage', function (): string {
                return Localization::activeLanguage();
            }],
            ['Area', function (string $handle, ?Page $c = null): FluentArea {
                return new FluentArea(new Area($handle), $c);
            }],
            ['GlobalArea', function (string $handle, ?Page $c = null): FluentArea {
                return new FluentArea(new GlobalArea($handle), $c);
            }],
            ['Stack', function (string $name): FluentArea {
                return new FluentArea(Stack::getByName($name));
            }],
            ['element', function (...$args): string {
                ob_start();
                View::element(...$args);
                return ob_get_clean();
            }]
        ]);
    }

    private function getFileFunctions(): array
    {
        return [
            new TwigFunction('fileGetByID', [File::class, 'getByID']),
        ];
    }

    private function getPageFunctions(): array
    {
        return [
            new TwigFunction('getCurrentPage', [Page::class, 'getCurrentPage']),
        ];
    }
}