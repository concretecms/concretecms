<?php
namespace Concrete\Core\Api\Fractal\Transformer;

use Concrete\Core\Page\Collection\Version\Version;
use League\Fractal\TransformerAbstract;

class CollectionVersionTransformer extends TransformerAbstract
{

    public function transform(Version $version)
    {
        $data = [];
        $data['id'] = $version->getVersionID();
        $data['is_approved'] = $version->isApproved();
        $data['is_new'] = $version->isNew();
        $data['comments'] = $version->getVersionComments(false);
        $data['date_created'] = $version->getVersionDateCreated();
        $data['date_approved'] = $version->getVersionDateApproved();
        $data['publish_end_date'] = $version->getPublishEndDate();
        return $data;
    }


}
