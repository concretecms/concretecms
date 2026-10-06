<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(
 *     title="MoveFile model",
 *     required={"folder"}
 * )
 */
class MoveFile
{
    
    /**
     * @OA\Property(type="int", title="Folder")
     */
    private $folder;
    
}
