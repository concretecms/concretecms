<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(title="NewFile model")
 */
class NewFile
{

    /**
     * @OA\Property(type="int", title="Folder ID")
     *
     * @var string
     */
    private $folder;

    /**
     * @OA\Property(type="string", format="binary", title="File to upload")
     *
     * @var string
     */
    private $file;



}
