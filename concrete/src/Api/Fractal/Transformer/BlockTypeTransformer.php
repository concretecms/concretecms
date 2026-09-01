<?php

declare(strict_types=1);

namespace Concrete\Core\Api\Fractal\Transformer;

use Concrete\Core\Api\Block\ApiValueSchemaFactory;
use Concrete\Core\Entity\Block\BlockType\BlockType;
use League\Fractal\TransformerAbstract;

defined('C5_EXECUTE') or die('Access Denied.');

class BlockTypeTransformer extends TransformerAbstract
{
    /**
     * @var \Concrete\Core\Api\Block\ApiValueSchemaFactory
     */
    protected $schemaFactory;

    public function __construct(ApiValueSchemaFactory $schemaFactory)
    {
        $this->schemaFactory = $schemaFactory;
    }

    public function transform(BlockType $blockType): array
    {
        // the method returns FALSE when the block type belongs to no package
        $packageHandle = (string) $blockType->getPackageHandle();

        return [
            'handle' => $blockType->getBlockTypeHandle(),
            'name' => $blockType->getBlockTypeName(),
            'description' => $blockType->getBlockTypeDescription(),
            // the block types of the core belong to no package
            'package' => $packageHandle === '' ? null : $packageHandle,
            'value_schema' => $this->schemaFactory->getSchema($blockType->getController()),
        ];
    }
}
