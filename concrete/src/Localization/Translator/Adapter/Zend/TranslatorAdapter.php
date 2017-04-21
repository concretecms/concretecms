<?php
namespace Concrete\Core\Localization\Translator\Adapter\Zend;

use Concrete\Core\Localization\Translator\TranslatorAdapterInterface;
use Zend\I18n\Translator\Translator;

/**
 * Translator adapter that wraps the Zend translator to provide the
 * translator methods needed in concrete5.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class TranslatorAdapter implements TranslatorAdapterInterface
{
    protected $translator;

    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->setTranslator($translator);
    }

    /**
     * Sets the translator object.
     *
     * @param Translator $translator
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Returns an instance of a translator object.
     *
     * @return Translator The translator object
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->translator->getLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $this->translator->setLocale($locale);
    }

    /**
     * {@inheritdoc}
     */
    public function translate($text)
    {
        if (!is_string($text)) {
            return '';
        }
        $v = $this->translator->translate($text);
        if (is_array($v)) {
            if (isset($v[0]) && ($v[0] !== '')) {
                $text = $v[0];
            }
        } else {
            $text = $v;
        }

        return $this->formatString($text, array_slice(func_get_args(), 1));
    }

    /**
     * {@inheritdoc}
     */
    public function translatePlural($singular, $plural, $number)
    {
        if (!(is_string($singular) && is_string($plural))) {
            return '';
        }
        $text = $this->translator->translatePlural($singular, $plural, $number);

        return $this->formatString($text, array_slice(func_get_args(), 2));
    }

    /**
     * {@inheritdoc}
     */
    public function translateContext($context, $text)
    {
        if (!(is_string($context) && is_string($text))) {
            return '';
        }
        $msgid = $context . "\x04" . $text;
        $msgtxt = $this->translator->translate($msgid);
        if ($msgtxt != $msgid) {
            $text = $msgtxt;
        }

        return $this->formatString($text, array_slice(func_get_args(), 2));
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
