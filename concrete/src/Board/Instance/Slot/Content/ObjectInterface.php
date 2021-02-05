<?php
namespace Concrete\Core\Board\Instance\Slot\Content;

use Concrete\Core\Application\Application;
use Concrete\Core\Design\Tag\ProvidesTagsInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;

interface ObjectInterface extends \JsonSerializable, DenormalizableInterface, ProvidesTagsInterface
{

    /**
     * Refreshes the content object without changing any slot attributes. Useful for when
     * summary objects need to update their fields, etc...
     * 
     * @return mixed
     */
    public function refresh(Application $app) : void;

    /**
     * Prints out the current object, rendering it in a template.
     */
    public function display(Application $app) : void;

    /**
     * Returns the title of the content object. Useful in admin interfaces when we choose from potential
     * content object form factors
     *
     * @return string|null
     */
    public function getSlotContentObjectTitle() :? string;
}
