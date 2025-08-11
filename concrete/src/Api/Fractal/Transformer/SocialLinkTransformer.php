<?php
namespace Concrete\Core\Api\Fractal\Transformer;

use Concrete\Core\Entity\Attribute\Value\Value\SelectedSocialLink;
use League\Fractal\TransformerAbstract;

class SocialLinkTransformer extends TransformerAbstract
{

    public function transform(SelectedSocialLink $link)
    {
        $data['service'] = $link->getService();
        $data['service_info'] = $link->getServiceInfo();
        return $data;
    }

}
