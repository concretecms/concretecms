<?php

namespace Concrete\Core\Board\Instance\Item\Data;

use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface DataInterface extends \JsonSerializable, DenormalizableInterface
{


}
