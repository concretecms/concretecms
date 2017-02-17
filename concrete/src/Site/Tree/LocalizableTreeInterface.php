<?php
namespace Concrete\Core\Site\Tree;

use Concrete\Core\Entity\Site\Tree;
use Concrete\Core\Localization\Locale\LocaleCollection;

interface LocalizableTreeInterface extends TreeInterface
{

    function getLocale();


}
