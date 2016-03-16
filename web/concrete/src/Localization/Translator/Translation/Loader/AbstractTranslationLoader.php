<?php

namespace Concrete\Core\Localization\Translator\Translation\Loader;

use Concrete\Core\Application\Application;
use Concrete\Core\Localization\Translator\Translation\TranslationLoaderInterface;
use Concrete\Core\Localization\Translator\TranslatorAdapterInterface;

abstract class AbstractTranslationLoader implements TranslationLoaderInterface
{

    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritDoc}
     */
    abstract public function loadTranslations(TranslatorAdapterInterface $translator);

}