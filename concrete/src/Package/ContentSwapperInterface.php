<?php
namespace Concrete\Core\Package;

/**
 * @since 8.0.1
 */
interface ContentSwapperInterface
{

    function allowsFullContentSwap(Package $package);

    function swapContent(Package $package, $options);
}
