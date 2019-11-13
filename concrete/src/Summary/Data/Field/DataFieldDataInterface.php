<?php
namespace Concrete\Core\Summary\Data\Field;

/**
 * Represents a piece of data that is stored against a data field. Needs to be serializable and
 * renderable.
 */
interface DataFieldDataInterface extends \JsonSerializable
{

    public function __toString();
    

}
