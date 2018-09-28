<?php

namespace Concrete\Core\Entity;

use Concrete\Core\Export\Item\Locale;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Punic\Language as PunicLanguage;
use Throwable;

trait LocaleTrait
{
    /**
     * The language code.
     *
     * @ORM\Column(type="string", length=32)
     *
     * @var string
     *
     * @example <code>en</code>
     */
    protected $msLanguage;

    /**
     * The Country/territory code.
     *
     * @ORM\Column(type="string", length=32)
     *
     * @var string
     *
     * @example <code>US</code>
     */
    protected $msCountry;

    /**
     * The number of plural rules used in this locale.
     *
     * @ORM\Column(type="integer", length=10)
     *
     * @var int
     */
    protected $msNumPlurals = 2;

    /**
     * The plural rules definition of this locale (using gettext format).
     *
     * @ORM\Column(type="string", length=400)
     *
     * @var string
     *
     * @example <code>(n != 1)</code>
     */
    protected $msPluralRule = '(n != 1)';

    /**
     * The plural rule names with examples (using CLDR format).
     *
     * @ORM\Column(type="string", length=1000)
     *
     * @var string
     *
     * @example <code>one@1\nother@0, 2~16, 100, 1000, 10000, 100000, 1000000, …</code>
     */
    protected $msPluralCases = "one@1\nother@0, 2~16, 100, 1000, 10000, 100000, 1000000, …";

    /**
     * Get the language code.
     *
     * @return string
     *
     * @example <code>en</code>
     */
    public function getLanguage()
    {
        return $this->msLanguage;
    }

    /**
     * Set the language code.
     *
     * @param string $msLanguage
     *
     * @example <code>en</code>
     */
    public function setLanguage($msLanguage)
    {
        $this->msLanguage = $msLanguage;
    }

    /**
     * Get the Country/territory code.
     *
     * @return string
     *
     * @example <code>US</code>
     */
    public function getCountry()
    {
        return $this->msCountry;
    }

    /**
     * Set the Country/territory code.
     *
     * @param string $msCountry
     *
     * @example <code>US</code>
     */
    public function setCountry($msCountry)
    {
        $this->msCountry = $msCountry;
    }

    /**
     * Get the number of plural rules used in this locale.
     *
     * @return int
     */
    public function getNumPlurals()
    {
        return $this->msNumPlurals;
    }

    /**
     * Set the number of plural rules used in this locale.
     *
     * @param int $msNumPlurals
     */
    public function setNumPlurals($msNumPlurals)
    {
        $this->msNumPlurals = $msNumPlurals;
    }

    /**
     * Get the plural rules definition of this locale (using gettext format).
     *
     * @return string
     *
     * @example <code>(n != 1)</code>
     */
    public function getPluralRule()
    {
        return $this->msPluralRule;
    }

    /**
     * Set the plural rules definition of this locale (using gettext format).
     *
     * @param string $msPluralRule
     *
     * @example <code>(n != 1)</code>
     */
    public function setPluralRule($msPluralRule)
    {
        $this->msPluralRule = $msPluralRule;
    }

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
    public function getPluralCases()
    {
        $msPluralCases = [];
        foreach (explode("\n", $this->msPluralCases) as $line) {
            list($key, $examples) = explode('@', $line);
            $msPluralCases[$key] = $examples;
        }

        return $msPluralCases;
    }

    /**
     * Set the plural rule names with examples (using CLDR format).
     *
     * @param string $msPluralCases
     *
     * @example <code>one@1\nother@0, 2~16, 100, 1000, 10000, 100000, 1000000, …</code>
     */
    public function setPluralCases($msPluralCases)
    {
        $this->msPluralCases = $msPluralCases;
    }

    /**
     * Get the full code of this locale.
     *
     * @return string
     *
     * @example <code>en_US</code>
     */
    public function getLocale()
    {
        return sprintf('%s_%s', $this->getLanguage(), $this->getCountry());
    }

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
    public function getLanguageText($locale = null)
    {
        try {
            $text = PunicLanguage::getName($this->getLanguage(), $locale ?: '');
        } catch (Exception $e) {
            $text = $this->getLanguage();
        } catch (Throwable $e) {
            $text = $this->getLanguage();
        }

        return $text;
    }

    /**
     * Get the object to be used to export this item to aSimpleXMLElement.
     *
     * @return \Concrete\Core\Export\Item\Locale
     */
    public function getExporter()
    {
        return new Locale();
    }
}
