<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\RequestBody(
 *     request="NewBlock",
 *     description="Block object that needs to be added to the CMS",
 *     required=true,
 *     @OA\JsonContent(ref="#/components/schemas/NewBlock"),
 * )
 */
class NewBlockRequestBody
{



}
