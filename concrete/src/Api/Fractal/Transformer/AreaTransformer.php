<?php
namespace Concrete\Core\Api\Fractal\Transformer;

use Concrete\Core\Page\Page;
use Concrete\Core\Area\ApiArea;
use Concrete\Core\Api\Fractal\Transformer\Traits\GetPageApiAreasTrait;
use Concrete\Core\Api\Resources;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceAbstract;
use League\Fractal\TransformerAbstract;

class AreaTransformer extends TransformerAbstract
{

    use GetPageApiAreasTrait;

    protected $defaultIncludes = [
        'blocks',
    ];

    protected $availableIncludes = [
        'content',
    ];

    public function transform(ApiArea $area)
    {
        $data = [];
        $data['name'] = $area->getAreaHandle();
        return $data;
    }

    public function includeBlocks(ApiArea $area)
    {
        $blocks = $area->getPage()->getBlocks($area->getAreaHandle());
        return new Collection($blocks, new BaseBlockTransformer(), Resources::RESOURCE_BLOCKS);
    }

    public function includeContent(ApiArea $area)
    {
        return new Item($area, new AreaContentTransformer());
    }


}
