<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\RequestBody(
 *     request="NewFile",
 *     description="Adding a file to the CMS: send its contents (as a multipart/form-data part, or base64-encoded within a JSON document), or let Concrete download it from an URL",
 *     required=true,
 *     @OA\MediaType(
 *         mediaType="multipart/form-data",
 *         @OA\Schema(ref="#/components/schemas/NewFile")
 *     ),
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(ref="#/components/schemas/NewFileFromJson")
 *     ),
 * )
 */
class NewFileRequestBody
{



}
