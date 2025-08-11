<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(title="UpdatedFile model")
 */
class UpdatedFile
{

    /**
     * @OA\Property(type="string", title="Title", description="Title/name as displayed in the file manager.")
     *
     * @var string
     */
    private $title;

    /**
     * @OA\Property(type="string", title="Description")
     *
     * @var string
     */
    private $description;

    /**
     * @OA\Property(type="string", title="Tags")
     *
     * @var string
     */
    private $tags;
    
}
