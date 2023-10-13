<?php

namespace Concrete\Core\Twig;

use Concrete\Core\Cache\Level\ExpensiveCache;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Localization\Localization;
use Pagerfanta\Twig\Extension\PagerfantaExtension;
use Pagerfanta\Twig\Extension\PagerfantaRuntime;
use Pagerfanta\View\TwitterBootstrap5View;
use Pagerfanta\View\ViewFactory;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Translation\Loader\MoFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;

class TwigServiceProvider extends Provider
{
    public function register(): void
    {
        $this->app->singleton(TranslatorInterface::class, function () {
            $locale = Localization::activeLocale();
            $translator = new Translator($locale);
            $translator->addLoader('mo', new MoFileLoader());
            $translator->addResource('mo', DIR_LANGUAGES . '/site/' . $locale . '.mo', $locale);

            return $translator;
        });

        $this->app->singleton(Environment::class, function () {
            $loader = new FilesystemLoader([
                DIR_BASE,
            ]);

            /**
             * @var Repository $config
             */
            $config = $this->app->make('config');
            /** @var ExpensiveCache $expensiveCache */
            $expensiveCache = $this->app->make('cache/expensive');

            $twig = new Environment(
                $loader,
                [
                    'debug' => $config->get('concrete.debug.display_errors'),
                    'cache' => $expensiveCache->isEnabled() ? new TwigCache($expensiveCache) : false,
                ]
            );
            $additionalThemePaths = $config->get('app.twig_additional_theme_paths', []);
            $themes = array_merge([
                'form_div_layout.html.twig',
                'bootstrap_5_layout.html.twig',
                '@concrete/file_selector.html.twig',
                '@concrete/wysiwyg.html.twig',
                '@concrete/page_selector.html.twig',
            ], $additionalThemePaths);

            $formEngine = new TwigRendererEngine($themes, $twig);
            $twig->addExtension(new FormExtension());
            $twig->addExtension(new TwigExtensions());

            /**
             * @var TranslatorInterface $translatorInterface
             */
            $translatorInterface = $this->app->make(TranslatorInterface::class);
            $twig->addExtension(new TranslationExtension($translatorInterface));

            $twig->getLoader()->addPath(DIR_BASE . '/concrete/vendor/symfony/twig-bridge/Resources/views/Form');
            $twig->getLoader()->addPath(DIR_BASE . '/concrete/views/twig/form', 'concrete');
            $twig->getLoader()->addPath(DIR_BASE . '/application/views/twig/form', 'application');

            $twig->addExtension(new PagerfantaExtension());

            $viewFactory = new ViewFactory();
            $viewFactory->set('twitter_bootstrap5', new TwitterBootstrap5View());
            $pagerfantaRuntime = new PagerfantaRuntime('twitter_bootstrap5', $viewFactory, new TwigRouteGeneratorFactory());

            $twig->addRuntimeLoader(new FactoryRuntimeLoader([
                FormRenderer::class => function () use ($formEngine) {
                    return new FormRenderer($formEngine);
                },
                PagerfantaRuntime::class => function () use ($pagerfantaRuntime) {
                    return $pagerfantaRuntime;
                },
            ]));

            return $twig;
        });
    }
}
