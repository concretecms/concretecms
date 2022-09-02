<?php

namespace Concrete\Core\Health\Search;

use PortlandLabs\ContentAudit\Entity\Finding;

class SearchMap
{
    public const TYPE_ATTRIBUTE_EXPRESS = 20;
    public const TYPE_ATTRIBUTE_PAGE = 21;
    public const TYPE_ATTRIBUTE_USER = 22;
    public const TYPE_ATTRIBUTE_EVENT = 23;
    public const TYPE_ATTRIBUTE_FILE = 24;
    public const TYPE_ATTRIBUTE_SITE = 25;
    public const TYPE_ATTRIBUTE_SITE_TYPE = 26;
    public const TYPE_ATTRIBUTE_LEGACY = 27;
    public const TYPE_ATTRIBUTE_UNKNOWN = 28;
    public const TYPE_BLOCK = 1;
    public const TYPE_CONFIGURATION = 2;

    public static array $findingTypeMap = [
        'express' => Finding::TYPE_ATTRIBUTE_EXPRESS,
        'collection' => Finding::TYPE_ATTRIBUTE_PAGE,
        'user' => Finding::TYPE_ATTRIBUTE_USER,
        'event' => Finding::TYPE_ATTRIBUTE_EVENT,
        'file' => Finding::TYPE_ATTRIBUTE_FILE,
        'site' => Finding::TYPE_ATTRIBUTE_SITE,
        'site_type' => Finding::TYPE_ATTRIBUTE_SITE_TYPE,
        'legacy' => Finding::TYPE_ATTRIBUTE_LEGACY,
        'unknown' => Finding::TYPE_ATTRIBUTE_UNKNOWN,
    ];

}
