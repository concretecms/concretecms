<?php
namespace Concrete\Core\Site\Tree;

use Concrete\Core\Entity\Site\Tree;

interface LocalizableTreeInterface extends TreeInterface
{

    function getLocale();


}
