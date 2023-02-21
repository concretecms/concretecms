<?php
namespace Concrete\Core\Api\Fractal\Transformer;

use Concrete\Core\Entity\Calendar\CalendarEventVersion;
use League\Fractal\TransformerAbstract;

class CalendarEventVersionTransformer extends TransformerAbstract
{

    public function transform(CalendarEventVersion $version)
    {
        $data = [];
        $data['id'] = $version->getID();
        $data['is_approved'] = $version->isApproved();
        return $data;
    }


}
