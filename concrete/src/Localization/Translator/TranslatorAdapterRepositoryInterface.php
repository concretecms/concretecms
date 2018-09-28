<?php
namespace Concrete\Core\Localization\Translator;

/**
 * Translator adapter repository can store any number of translator adapters.
 * Used for storing multiple translator adapter instances and fetching them
 * for different purposes.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
interface TranslatorAdapterRepositoryInterface
{
    /**
     * Registers the translator for the given handle and locale to be the
     * object provided as the third parameter. The passed adapter needs to
     * implement the TranslatorAdapterInterface.
     *
     * @param string $handle
     * @param string $locale
     * @param TranslatorAdapter $translator
     */
    public function registerTranslatorAdapter($handle, $locale, TranslatorAdapterInterface $translatorAdapter);

    /**
     * Checks whether a translator has been defined for the given handle and
     * locale.
     *
     * @param string $handle
     * @param string $locale
     */
    public function hasTranslatorAdapter($handle, $locale);

    /**
     * Gets the translator for the given handle and locale. This will also
     * initialize the translator object for the given handle if it has not been
     * fetched previously through this method.
     *
     * @param string $handle
     * @param string $locale
     *
     * @return mixed The translator object
     */
    public function getTranslatorAdapter($handle, $locale);

    /**
     * Removes the translator for the given handle and locale.
     *
     * @param string $handle
     * @param string $locale
     */
    public function removeTranslatorAdapter($handle, $locale);

    /**
     * Removes all the translators for the given handle, no matter what their
     * locale is.
     *
     * @param $handle
     */
    public function removeTranslatorAdaptersWithHandle($handle);
}
