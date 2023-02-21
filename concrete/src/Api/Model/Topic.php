<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(title="Topic model", description="A Concrete Topic object.")
 */
class Topic
{

    /**
     * @OA\Property(type="integer", title="ID")
     *
     * @var string
     */
    private $id;

    /**
     * @OA\Property(type="string", title="Path")
     *
     * @var string
     */
    private $path;

    /**
     * @OA\Property(type="string", title="Name")
     *
     * @var string
     */
    private $name;

    /**
     * @OA\Property(type="string", title="Display Name")
     *
     * @var string
     */
    private $display_name;


}
