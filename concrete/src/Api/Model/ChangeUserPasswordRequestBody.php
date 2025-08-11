<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\RequestBody(
 *     request="ChangeUserPassword",
 *     description="Change a user's password.",
 *     required=true,
 *     @OA\MediaType(
 *         mediaType="multipart/form-data",
 *         @OA\Schema(ref="#/components/schemas/ChangeUserPassword")
 *     ),
 * )
 */
class ChangeUserPasswordRequestBody
{



}
