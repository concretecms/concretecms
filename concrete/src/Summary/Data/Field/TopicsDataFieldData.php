<?php
namespace Concrete\Core\Summary\Data\Field;

use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\Tree\Node\Type\Topic;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Concrete\Core\File\File;

class TopicsDataFieldData implements DataFieldDataInterface
{

    protected $topicTreeNodeIDs = [];
    
    public function __construct(array $topicTreeNodes = null)
    {
        if (!empty($topicTreeNodes)) {
            foreach($topicTreeNodes as $topicTreeNode) {
                $this->topicTreeNodeIDs[] = $topicTreeNode->getTreeNodeID();
            }
        }
    }

   
    public function __toString()
    {
        $output = [];
        foreach($this->topicTreeNodeIDs as $topicTreeNodeID) {
            $topic = Topic::getByID($topicTreeNodeID);
            if ($topic) {
                $output[] = $topic->getTreeNodeDisplayName();
            }
        }
        if ($output) {
            return implode(', ', $output);
        } else {
            return '';
        }
    }
    
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'class' => self::class,
            'topicTreeNodeIDs' => $this->topicTreeNodeIDs
        ];
    }
    
    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        if (isset($data['topicTreeNodeIDs'])) {
            $this->topicTreeNodeIDs = $data['topicTreeNodeIDs'];
        }
    }
}
