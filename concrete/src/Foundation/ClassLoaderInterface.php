<?php

namespace Concrete\Core\Foundation;

interface ClassLoaderInterface
{
    function register();
    function unregister();
}