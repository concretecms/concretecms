<?php

namespace Concrete\Tests\Foundation;

use Concrete\Core\Application\Application as ServiceLocator;
use Concrete\Core\Foundation\Service\ProviderList;
use PHPUnit_Framework_TestCase;

class ServiceLocatorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->sl = new ServiceLocator();
    }

    public function testAllServiceProviders()
    {
        $groups = [
            '\Concrete\Core\File\FileServiceProvider',
            '\Concrete\Core\Encryption\EncryptionServiceProvider',
            '\Concrete\Core\Validation\ValidationServiceProvider',
            '\Concrete\Core\Localization\LocalizationServiceProvider',
            '\Concrete\Core\Feed\FeedServiceProvider',
            '\Concrete\Core\Html\HtmlServiceProvider',
            '\Concrete\Core\Mail\MailServiceProvider',
            '\Concrete\Core\Application\ApplicationServiceProvider',
            '\Concrete\Core\Utility\UtilityServiceProvider',
            '\Concrete\Core\Database\DatabaseServiceProvider',
            '\Concrete\Core\Form\FormServiceProvider',
            '\Concrete\Core\Session\SessionServiceProvider',
            '\Concrete\Core\Http\HttpServiceProvider',
            '\Concrete\Core\Events\EventsServiceProvider',
        ];

        $gr = new ProviderList($this->sl);
        $gr->registerProviders($groups);

        $this->assertTrue($this->sl->bound('helper/concrete/ui'));
        $this->assertTrue($this->sl->bound('helper/concrete/ui/help'));
        $this->assertTrue($this->sl->bound('helper/concrete/asset_library'));
        $this->assertTrue($this->sl->bound('helper/mime'));
        $this->assertTrue($this->sl->bound('director'));
    }
}
