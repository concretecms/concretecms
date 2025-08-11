<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(title="Page model", description="A Concrete Page object.")
 */
class Page
{

    /**
     * @OA\Property(type="integer", title="ID")
     *
     * @var string
     */
    private $id;

    /**
     * @OA\Property(type="string", title="Page path")
     *
     * @var string
     */
    private $path;

    /**
     * @OA\Property(type="string", title="Page Name")
     *
     * @var string
     */
    private $name;

    /**
     * @OA\Property(type="string", title="Page Type")
     *
     * @var string
     */
    private $type;

    /**
     * @OA\Property(type="string", title="Page Template")
     *
     * @var string
     */
    private $template;

    /**
     * @OA\Property(type="date", title="Date page created")
     *
     * @var string
     */
    private $date_added;

    /**
     * @OA\Property(type="date", title="Date page last updated")
     *
     * @var string
     */
    private $date_last_updated;

    /**
     * @OA\Property(type="date", title="Locale of the page", description="Locale of the page - defaults to site if unset.")
     *
     * @var string
     */
    private $locale;

    /**
     * @OA\Property(type="string", title="External Link URL", description="If this page node is actually an external link, the value of the link URL.")
     *
     * @var string
     */
    private $external_link_url;

    /**
     * @OA\Property(type="string", title="Short description")
     *
     * @var string
     */
    private $description;

    /**
     * @OA\Property(type="array", title="Custom Attributes", @OA\Items(ref="#/components/schemas/CustomAttribute"))
     *
     * @var string
     */
    private $custom_attributes;

    /**
     * @OA\Property(type="array", title="Areas", @OA\Items(ref="#/components/schemas/Area"))
     *
     * @var string
     */
    private $areas;

    /**
     * @OA\Property(type="array", title="Custom Attributes", @OA\Items(ref="#/components/schemas/File"))
     *
     * @var string
     */
    private $files;

    /**
     * @OA\Property(title="Content", ref="#/components/schemas/Content")
     *
     * @var string
     */
    private $content;

    /**
     * @OA\Property(title="Page Version", ref="#/components/schemas/PageVersion")
     *
     * @var string
     */
    private $version;



}
