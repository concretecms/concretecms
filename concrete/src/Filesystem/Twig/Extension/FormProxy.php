<?php

namespace Concrete\Core\Filesystem\Twig\Extension;

use Concrete\Core\Form\Service\Form;
use Twig\Markup;

class FormProxy
{
    /** @var Form */
    private $inner;

    /** @var string */
    private $charset;

    /**
     * @param Form   $inner
     * @param string $charset
     */
    public function __construct(Form $inner, $charset = 'UTF-8')
    {
        $this->inner = $inner;
        $this->charset = $charset;
    }

    /**
     * Build an options array from object list using two method names. Enables
     * the ability to create select options from object lists.
     *
     * For example:
     * {% set userAttributesOptions = {'': t('** Select Attribute')}
     * + form_html.options(userAttributes, 'getAttributeKeyID', 'getAttributeKeyDisplayName')
     * %}
     * {{ form_html.select('userAttributeKeyID', userAttributesOptions, division.userKey) }}
     *
     * @param iterable $items
     * @param string   $keyMethod
     * @param string   $labelMethod
     *
     * @return array
     */
    public function options($items, $keyMethod, $labelMethod)
    {
        if (!is_iterable($items)) {
            return [];
        }

        $options = [];

        foreach ($items as $item) {
            if (!is_object($item)) {
                continue;
            }

            if (!is_callable([$item, $keyMethod]) ||
                !is_callable([$item, $labelMethod])) {
                continue;
            }

            $key   = $item->{$keyMethod}();
            $label = $item->{$labelMethod}();

            $options[$key] = $label;
        }

        return $options;
    }

    /**
     * Catch all method calls and forward to the real Form helper.
     * If the result is a string, wrap it in Twig\Markup so Twig treats it as safe HTML.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        if (!method_exists($this->inner, $name)) {
            throw new \BadMethodCallException(sprintf(
                'Method %s::%s does not exist',
                get_class($this->inner),
                $name
            ));
        }

        $result = $this->inner->{$name}(...$arguments);

        // Only wrap actual HTML strings; leave others alone.
        if (is_string($result)) {
            return new Markup($result, $this->charset);
        }

        return $result;
    }

}