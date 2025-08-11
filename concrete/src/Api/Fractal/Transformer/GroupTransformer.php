<?php
namespace Concrete\Core\Api\Fractal\Transformer;

use Concrete\Core\User\Group\Group;
use League\Fractal\TransformerAbstract;

class GroupTransformer extends TransformerAbstract
{

    public function transform(Group $group)
    {
        $data['id'] = $group->getGroupID();
        $data['name'] = $group->getGroupName();
        return $data;
    }

}
