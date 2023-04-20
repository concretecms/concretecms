<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(
 *     title="Area model",
 * )
 */
class Area
{

    /**
     * @OA\Property(type="string", format="string", title="Block Area")
     *
     * @var string
     */
    private $name;

    /**
     * @OA\Property(type="array", title="Blocks", @OA\Items(ref="#/components/schemas/Block"))
     *
     * @var string
     */
    private $blocks;

    /**
     * @OA\Property(title="Content", ref="#/components/schemas/Content")
     *
     * @var string
     */
    private $content;




}