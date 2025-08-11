<?php
namespace Concrete\Core\Api\Fractal\Transformer;

use Concrete\Core\Block\Block;
use Concrete\Core\Api\Resources;
use League\Fractal\Resource\Collection;

/**
 * Class BlockTransformer. Used when requested blocks directly via the /blocks API endpoint. This transformer
 * makes an include of "pages" available to show you what pages blocks are on. When blocks are requested through
 * other means, like the pages endpoints, they use the base block transformer because we don't want a recursive
 * situation
 *
 * @package Concrete\Core\Api\Fractal\Transformer
 */
class BlockTransformer extends BaseBlockTransformer
{

    protected $availableIncludes = [
        'pages',
    ];

    public function includePages(Block $block)
    {
        $pages = $block->getPageList();
        return new Collection($pages, new PageTransformer(), Resources::RESOURCE_PAGES);
    }




}
