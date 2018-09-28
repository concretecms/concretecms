<?php
namespace Concrete\Core\Localization\Translator\Adapter\Plain;

use Concrete\Core\Localization\Translator\TranslatorAdapterInterface;

/**
 * Translator adapter that wraps the plain translator to provide the
 * translator methods needed in concrete5.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class TranslatorAdapter implements TranslatorAdapterInterface
{
    /** @var string */
    protected $locale;

    /**
     * The plain translator does not have any translator object attached to it,
     * so null is returned instead.
     *
     * @return null
     */
    public function getTranslator()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Dummy translate method for the plain translator adapter.
     *
     * Returns the same string that is passed to this method formatted with
     * the sprintf format in case multiple arguments are passed.
     *
     * @param string $text
     *
     * @return string
     */
    public function translate($text)
    {
        return $this->formatString($text, array_slice(func_get_args(), 1));
    }

    /**
     * Dummy plural translate method for the plain translator.
     *
     * Does not do any actual translation but returns the correct format of the
     * two provided strings ($singular and $plural) based on the passed number
     * parameter ($number).
     *
     * If the $number is equal to 1, the singular format will be returned.
     *
     * If the $number is not equal to 1, the plural format will be returned.
     *
     * @param string $singular
     * @param string $plural
     * @param int $number
     *
     * @return string
     */
    public function translatePlural($singular, $plural, $number)
    {
        if (!(is_string($singular) && is_string($plural))) {
            return '';
        }
        $text = $number == 1 ? $singular : $plural;

        return $this->formatString($text, array_slice(func_get_args(), 2));
    }

    /**
     * Dummy translate context method for the plain translator adapter.
     *
     * Does exactly the same as the `translate()` method, skipping the passed
     * context string as it is not used.
     *
     * Calls the `translate()` method with other arguments than the context.
     *
     * @see TranslatorAdapter::translate()
     *
     * @param string $context
     * @param string $text
     *
     * @return string
     */
    public function translateContext($context, $text)
    {
        return call_user_func_array([$this, 'translate'], array_slice(func_get_args(), 1));
    }

    /**
     * Formats the string in the with the PHP sprintf format with the given
     * arguments.
     *
     * @param string $string
     * @param array $args
     */
    protected function formatString($string, array $args)
    {
        if (count($args) > 0) {
            return vsprintf($string, $args);
        }

        return $string;
    }
}
