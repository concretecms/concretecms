<?php

return [
    'preconditions' => [
        'php_version' => Concrete\Core\Install\Preconditions\PhpVersion::class,
        'javascript' => Concrete\Core\Install\Preconditions\Javascript::class,
        'pdo_mysql_extension' => Concrete\Core\Install\Preconditions\PdoMysqlExtension::class,
        'request_urls' => Concrete\Core\Install\Preconditions\RequestUrls::class,
        'json_extension' => Concrete\Core\Install\Preconditions\JsonExtension::class,
        'dom_extension' => Concrete\Core\Install\Preconditions\DomExtension::class,
        'no_asp_style_tags' => Concrete\Core\Install\Preconditions\NoAspStyleTags::class,
        'fileinfo_extension' => Concrete\Core\Install\Preconditions\FileinfoExtension::class,
        'image_manipulation' => Concrete\Core\Install\Preconditions\ImageManipulation::class,
        'xml_support' => Concrete\Core\Install\Preconditions\XmlSupport::class,
        'writable_directories' => Concrete\Core\Install\Preconditions\WritableDirectories::class,
        'cookies' => Concrete\Core\Install\Preconditions\Cookies::class,
        'internationalization_support' => Concrete\Core\Install\Preconditions\InternationalizationSupport::class,
        'php_comments_preserved' => Concrete\Core\Install\Preconditions\PhpCommentsPreserved::class,
        'tokenizer_extension' => Concrete\Core\Install\Preconditions\TokenizerExtension::class,
        'memory_limit' => Concrete\Core\Install\Preconditions\MemoryLimit::class,
        'remote_file_importing' => Concrete\Core\Install\Preconditions\RemoteFileImporting::class,
        'zip_support' => Concrete\Core\Install\Preconditions\ZipSupport::class,
        // OptionsPreconditionInterface
        'canonical_urls' => Concrete\Core\Install\Preconditions\CanonicalUrls::class,
        'database_timezone' => Concrete\Core\Install\Preconditions\DatabaseTimeZone::class,
        'empty_database' => Concrete\Core\Install\Preconditions\EmptyDatabase::class,
        'innodb' => Concrete\Core\Install\Preconditions\InnoDB::class,
        'starting_point' => Concrete\Core\Install\Preconditions\StartingPoint::class,
        'table_case' => Concrete\Core\Install\Preconditions\TableCase::class,
    ],
];
