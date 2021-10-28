<?php
namespace Concrete\Core\Express;

use Concrete\Core\Entity\Express\Entry;
use League\Fractal\TransformerAbstract;

class ExpressTransformer extends TransformerAbstract
{
    public function transform(Entry $expressEntry)
    {
        return (array) $expressEntry->jsonSerialize();
    }

}
