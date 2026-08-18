<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\RequestBody(
 *     request="NewFile",
 *     description="Adding a file to the CMS: upload its contents, or let Concrete download it from an URL",
 *     required=true,
 *     @OA\MediaType(
 *         mediaType="multipart/form-data",
 *         @OA\Schema(ref="#/components/schemas/NewFile")
 *     ),
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(ref="#/components/schemas/NewFile")
 *     ),
 * )
 */
class NewFileRequestBody
{



}
