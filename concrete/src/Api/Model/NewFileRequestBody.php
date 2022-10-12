<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\RequestBody(
 *     request="NewFile",
 *     description="Adding a file to the CMS",
 *     required=true,
 *     @OA\MediaType(
 *         mediaType="multipart/form-data",
 *         @OA\Schema(ref="#/components/schemas/NewFile")
 *     ),
 * )
 */
class NewFileRequestBody
{



}
