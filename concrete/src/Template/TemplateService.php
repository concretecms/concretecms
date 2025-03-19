<?php

namespace Concrete\Core\Template;

class TemplateService
{
    /**
     * @var TwigFactory
     */
    private $twigFactory;

    public function __construct(TwigFactory $twigFactory)
    {
        $this->twigFactory = $twigFactory;
    }

    public function renderTemplate(string $path, array $data = [], $bindTo = null): string
    {
        if (str_ends_with($path, '.php')) {
            return $this->renderPhpTemplate($path, $data, $bindTo);
        }
        if (str_ends_with($path, '.html.twig')) {
            return $this->renderTwigTemplate($path, $data);
        }

        throw new \InvalidArgumentException(t('Invalid template path.'));
    }

    protected function renderPhpTemplate(string $path, array $data = [], ?object $bindTo = null): string
    {
        $include = function ($_ccm_path, $data) {
            unset($data['_ccm_path']);
            extract($data);
            include $_ccm_path;
        };

        if ($bindTo) {
            $include = $include->bindTo($bindTo);
        }

        ob_start();
        $include($path, $data);
        return ob_get_clean();
    }

    private function renderTwigTemplate(string $path, array $data): string
    {
        return $this->twigFactory
            ->create(dirname($path))
            ->render(basename($path), $data);
    }
}