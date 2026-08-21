<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\RequestBody(
 *     request="NewPageVersion",
 *     description="Optional fields when creating a page version draft. An empty body is allowed.",
 *     required=false,
 *     @OA\JsonContent(ref="#/components/schemas/NewPageVersion"),
 * )
 */
class NewPageVersionRequestBody
{



}
