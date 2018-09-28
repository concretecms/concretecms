<?php
namespace Concrete\Core\Localization\Translator;

/**
 * Translator adapters wrap the functionality of a specific translator class
 * into a format that can be used within the concrete5 context.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
interface TranslatorAdapterInterface
{
    /**
     * Returns an instance of a translator object.
     *
     * @return mixed The translator object
     */
    public function getTranslator();

    /**
     * Get the locale from the translator object.
     *
     * @return string
     */
    public function getLocale();

    /**
     * Sets the locale to the translator object.
     *
     * @param string $locale
     */
    public function setLocale($locale);

    /**
     * Translate the given text. Returns the translated form of the text.
     *
     * @param string $text
     *
     * @return string
     */
    public function translate($text);

    /**
     * Translate the text either into the singular or the plural format
     * depending on the given number. Returns the correct format of the text
     * in its translated form.
     *
     * @param string $singular
     * @param string $plural
     * @param int $number
     *
     * @return int
     */
    public function translatePlural($singular, $plural, $number);

    /**
     * Translate the given text with the given context. Returns the translated
     * form of the text.
     *
     * @param string $context
     * @param string $text
     *
     * @return string
     */
    public function translateContext($context, $text);
}
