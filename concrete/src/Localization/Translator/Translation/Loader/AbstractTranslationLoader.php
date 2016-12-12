<?php

namespace Concrete\Core\Localization\Translator\Translation\Loader;

use Concrete\Core\Application\Application;
use Concrete\Core\Localization\Translator\Translation\TranslationLoaderInterface;
use Concrete\Core\Localization\Translator\TranslatorAdapterInterface;

/**
 * Abstract translation loader provides general methods needed in most
 * translation loader implementations.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
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
