<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\RequestBody(
 *     request="NewPage",
 *     description="Page object that needs to be added to the CMS",
 *     required=true,
 *     @OA\JsonContent(ref="#/components/schemas/NewPage"),
 * )
 */
class NewPageRequestBody
{



}
