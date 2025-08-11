<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(title="Content")
 */
class Content
{

    /**
     * @OA\Property(type="string", title="Content", description="HTML content. The content has been sanitized, removing style and script tags.")
     *
     * @var string
     */
    private $content;

    /**
     * @OA\Property(type="string", title="Content (Raw HTML)", description="Unsanitized HTML content. Style and script tags are still present.")
     *
     * @var string
     */
    private $raw;


}