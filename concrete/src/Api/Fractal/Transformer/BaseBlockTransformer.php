<?php
namespace Concrete\Core\Api\Fractal\Transformer;

use Concrete\Core\Block\Block;
use Concrete\Core\Api\Resources;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class BaseBlockTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'page',
    ];

    public function transform(Block $block)
    {
        return [
            'id' => $block->getBlockID(),
            'type' => $block->getBlockTypeHandle(),
            'value' => $block->getController()->getApiValue(),
        ];
    }

    public function includePage(Block $block)
    {
        $page = $block->getBlockCollectionObject();
        return new Item($page, new PageTransformer(), Resources::RESOURCE_PAGES);
    }

}
