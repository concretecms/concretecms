<?php
namespace Concrete\Core\Site\Tree;

use Concrete\Core\Entity\Site\Tree;

/**
 * @since 8.2.0
 */
interface LocalizableTreeInterface extends TreeInterface
{

    function getLocale();


}
