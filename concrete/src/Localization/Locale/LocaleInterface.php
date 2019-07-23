<?php

namespace Concrete\Core\Localization\Locale;

interface LocaleInterface
{
    /**
     * Get the locale record identifier.
     *
     * @return int|null
     */
    public function getLocaleID();

    /**
     * Get the language code.
     *
     * @return string
     *
     * @example <code>en</code>
     */
    public function getLanguage();

    /**
     * Get the Country/territory code.
     *
     * @return string
     *
     * @example <code>US</code>
     */
    public function getCountry();

    /**
     * Get the full code of this locale.
     *
     * @return string
     *
     * @example <code>en_US</code>
     */
    public function getLocale();

    /**
     * Get the number of plural rules used in this locale.
     *
     * @return int
     */
    public function getNumPlurals();

    /**
     * Get the plural rule names with examples (using CLDR format).
     *
     * @return array array keys are the CLDR identifiers or the plural rules (zero, one, two, few, many, other), array values are example values
     *
     * @example <pre><code>[
     *     'one' => '1',
     *     'other' => '0, 2~16, 100, 1000, 10000, 100000, 1000000, …',
     * ]</code></pre>
     */
    public function getPluralCases();

    /**
     * Get the plural rules definition of this locale (using gettext format).
     *
     * @return string
     *
     * @example <code>(n != 1)</code>
     */
    public function getPluralRule();

    /**
     * Set the number of plural rules used in this locale.
     *
     * @param int $msNumPlurals
     * @param mixed $numPlurals
     */
    public function setNumPlurals($numPlurals);

    /**
     * Set the plural rule names with examples (using CLDR format).
     *
     * @param string $msPluralCases
     * @param mixed $numCases
     *
     * @example <code>one@1\nother@0, 2~16, 100, 1000, 10000, 100000, 1000000, …</code>
     */
    public function setPluralCases($numCases);

    /**
     * Set the plural rules definition of this locale (using gettext format).
     *
     * @param string $msPluralRule
     * @param mixed $pluralRule
     *
     * @example <code>(n != 1)</code>
     */
    public function setPluralRule($pluralRule);

    /**
     * Get the display name of this locale.
     *
     * @param string|null $locale The locale to be used to localize the locale name
     *
     * @return string
     *
     * @example <code>getLanguage('en_US')</code> returns the display name of this locale in American English
     * @example <code>getLanguage('it_IT')</code> returns the display name of this locale in Italian
     * @example <code>getLanguage()</code> returns the display name of this locale in the currently active locale
     */
    public function getLanguageText($locale = null);
}
