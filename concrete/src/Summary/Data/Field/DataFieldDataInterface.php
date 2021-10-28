<?php
namespace Concrete\Core\Summary\Data\Field;

use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;

/**
 * Represents a piece of data that is stored against a data field. Needs to be serializable and
 * renderable.
 */
interface DataFieldDataInterface extends \JsonSerializable, DenormalizableInterface
{



}
