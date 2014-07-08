<?php
use Whoops\Provider\Illuminate\WhoopsServiceProvider;
defined('C5_EXECUTE') or die("Access Denied.");
return array(
    '\Concrete\Core\File\FileServiceProvider',
    '\Concrete\Core\Encryption\EncryptionServiceProvider',
    '\Concrete\Core\Validation\ValidationServiceProvider',
    '\Concrete\Core\Localization\LocalizationServiceProvider',
    '\Concrete\Core\Feed\FeedServiceProvider',
    '\Concrete\Core\Html\HtmlServiceProvider',
    '\Concrete\Core\Search\PaginationServiceProvider',
    '\Concrete\Core\Mail\MailServiceProvider',
    '\Concrete\Core\Application\ApplicationServiceProvider',
    '\Concrete\Core\Utility\UtilityServiceProvider',
    '\Concrete\Core\Database\DatabaseServiceProvider',
    '\Concrete\Core\Form\FormServiceProvider',
    '\Concrete\Core\Session\SessionServiceProvider',
    '\Concrete\Core\Http\HttpServiceProvider',
    '\Concrete\Core\Events\EventsServiceProvider',
    '\Concrete\Core\Error\Provider\WhoopsServiceProvider',
    '\Concrete\Core\Logging\LoggingServiceProvider'
);