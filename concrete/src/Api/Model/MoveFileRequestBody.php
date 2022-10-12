<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\RequestBody(
 *     request="MoveFile",
 *     description="Move a file to a new folder.",
 *     required=true,
 *     @OA\MediaType(
 *         mediaType="multipart/form-data",
 *         @OA\Schema(ref="#/components/schemas/MoveFile")
 *     ),
 * )
 */
class MoveFileRequestBody
{



}
