<?php
namespace Concrete\Core\Board\Instance\Slot\Content;

use Concrete\Core\Application\Application;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;

interface ObjectInterface extends \JsonSerializable, DenormalizableInterface
{

    /**
     * Prints out the current object, rendering it in a template.
     */
    public function display(Application $app) : void;

}
