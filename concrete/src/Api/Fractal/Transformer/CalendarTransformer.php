<?php
namespace Concrete\Core\Api\Fractal\Transformer;

use Concrete\Core\Api\Resources;
use Concrete\Core\Entity\Calendar\Calendar;
use League\Fractal\TransformerAbstract;

class CalendarTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'site',
    ];

    /**
     * @param Calendar $calendar
     * @return array
     */
    public function transform(Calendar $calendar)
    {
        $data['id'] = $calendar->getID();
        $data['name'] = $calendar->getName();
        return $data;
    }

    public function includeSite(Calendar $calendar)
    {
        $site = $calendar->getSite();
        return $this->item($site, new SiteTransformer(), Resources::RESOURCE_SITES);
    }



}
