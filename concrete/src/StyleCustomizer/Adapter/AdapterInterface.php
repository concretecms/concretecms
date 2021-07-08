<?php
namespace Concrete\Core\StyleCustomizer\Adapter;

use Concrete\Core\StyleCustomizer\Normalizer\NormalizerInterface;
use Concrete\Core\StyleCustomizer\Processor\ProcessorInterface;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;

interface AdapterInterface
{

    /**
     * Returns the file in the theme to include when sniffing theme SCSS/LESS variables
     *
     * @param SkinInterface $skin
     * @return string
     */
    public function getVariablesFile(SkinInterface $skin): string;

    /**
     * Returns the file in the theme to build the theme CSS from
     *
     * @param SkinInterface $skin
     * @return string
     */
    public function getEntrypointFile(SkinInterface $skin): string;

    /**
     * Returns the object to use when converting the stylesheet's variables into our own internal unified representation.
     *
     * @return NormalizerInterface
     */
    public function getVariableNormalizer(): NormalizerInterface;

    /**
     * Return which processor we should use. Processor is a custom object that wraps a third party LESS or SCSS
     * library
     *
     * @return ProcessorInterface
     */
    public function getProcessor(): ProcessorInterface;



}
