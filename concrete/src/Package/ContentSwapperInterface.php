<?php
namespace Concrete\Core\Package;

interface ContentSwapperInterface
{

    function allowsFullContentSwap(Package $package);

    function swapContent(Package $package, $options);
}
