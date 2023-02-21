<?php
namespace Concrete\Core\Api\Fractal\Transformer;

use Concrete\Core\Tree\Node\Type\Topic;
use League\Fractal\TransformerAbstract;

class TopicTreeNodeTransformer extends TransformerAbstract
{

    public function transform(Topic $topic)
    {
        $data['id'] = $topic->getTreeNodeID();
        $data['name'] = $topic->getTreeNodeName();
        $data['path'] = $topic->getTreeNodeDisplayPath();
        $data['display_name'] = $topic->getTreeNodeDisplayName('text');
        return $data;
    }

}
