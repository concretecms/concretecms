<?php

namespace Concrete\Tests\Authentication;

use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Authentication\AuthenticationTypeController;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Filesystem\FileLocator\LocationInterface;
use Concrete\Core\Filesystem\TemplateService;
use Concrete\Core\Filesystem\TemplateVariantLocator;
use Concrete\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;
use Mockery;

final class AuthenticationTypeTest extends TestCase
{
    /** @var string */
    private $file;
    /** @var bool */
    private $exists;
    /** @var FileLocator\Record&Mockery\MockInterface */
    private $record;
    /** @var FileLocator&Mockery\MockInterface */
    private $fileLocator;
    /** @var TemplateVariantLocator&Mockery\MockInterface */
    private $templateVariantLocator;
    /** @var TemplateService&Mockery\MockInterface */
    private $templateService;
    /** @var AuthenticationType */
    private $authenticationType;

    /**
     * @dataProvider basicRenderProvider
     */
    public function testRendersBasicEndpoints(string $method): void
    {
        $this->setFullyDefinedController();

        $this->templateVariantLocator->expects('getRecord')
            ->with('authentication/test_auth_type/' . $method . '.php')
            ->andReturn($this->record);
        $this->templateService->expects('renderTemplate')
            ->with($this->file, ['foo', $method], $this->authenticationType)
            ->andReturn('foo');

        ob_start();
        $this->authenticationType->{'render' . camelcase($method)}();
        $actual = ob_get_clean();
        $this->assertEquals($actual, 'foo');
    }

    public static function basicRenderProvider()
    {
        return [['hook'], ['hooked']];
    }

    public function testRendersForm(): void
    {
        $this->setFullyDefinedController();

        $this->templateVariantLocator->expects('getRecord')
            ->times(2)
            ->with('authentication/test_auth_type/form.php')
            ->andReturn($this->record);
        $this->templateService->expects('renderTemplate')
            ->with($this->file, ['foo', 'form'], $this->authenticationType)
            ->andReturn('foo');

        ob_start();
        $this->authenticationType->renderForm();
        $actual = ob_get_clean();
        $this->assertEquals($actual, 'foo');
    }

    public function testRendersFormEndpointWithParameters(): void
    {
        $this->authenticationType->controller = new class() {
            /** @var array<string, string> */
            private $sets = [];

            public function change_password(string $hash = ''): void
            {
                $this->sets['hash'] = $hash;
            }

            public function getSets(): array
            {
                return $this->sets;
            }
        };

        $this->templateVariantLocator->expects('getRecord')
            ->once()
            ->with('authentication/test_auth_type/change_password.php')
            ->andReturn($this->record);
        $this->templateService->expects('renderTemplate')
            ->with($this->file, [0 => 'abc123', 'hash' => 'abc123'], $this->authenticationType)
            ->andReturn('foo');

        ob_start();
        $this->authenticationType->renderForm('change_password', ['abc123']);
        $actual = ob_get_clean();
        $this->assertEquals($actual, 'foo');
    }

    public function testRendersTypeForm(): void
    {
        $this->setFullyDefinedController();

        $this->templateVariantLocator->expects('getRecord')
            ->times(2)
            ->with('authentication/test_auth_type/type_form.php')
            ->andReturn($this->record);
        $this->templateService->expects('renderTemplate')
            ->with($this->file, ['foo', 'edit', 'type_form'], $this->authenticationType)
            ->andReturn('foo');

        ob_start();
        $this->authenticationType->renderTypeForm();
        $actual = ob_get_clean();
        $this->assertEquals($actual, 'foo');
    }

    public function testRendersTypeFormWithEditMethod(): void
    {
        $this->setEditController();

        $this->templateVariantLocator->expects('getRecord')
            ->times(2)
            ->with('authentication/test_auth_type/type_form.php')
            ->andReturn($this->record);
        $this->templateService->expects('renderTemplate')
            ->with($this->file, ['foo', 'edit'], $this->authenticationType)
            ->andReturn('foo');

        ob_start();
        $this->authenticationType->renderTypeForm();
        $actual = ob_get_clean();
        $this->assertEquals($actual, 'foo');
    }

    public function testFallsBackToView(): void
    {
        $this->setViewController();

        $this->templateVariantLocator->expects('getRecord')
            ->times(2)
            ->with('authentication/test_auth_type/form.php')
            ->andReturn($this->record);
        $this->templateService->expects('renderTemplate')
            ->with($this->file, ['foo', 'view'], $this->authenticationType)
            ->andReturn('foo');

        ob_start();
        $this->authenticationType->renderForm();
        $actual = ob_get_clean();
        $this->assertEquals($actual, 'foo');
    }

    public function testAddsPackageLocation(): void
    {
        $this->setViewController();
        $this->authenticationType->pkgHandle = 'fake_package';
        $this->authenticationType->templateVariantLocator = null;

        $location = Mockery::mock(LocationInterface::class);
        $location->shouldReceive('setFilesystem')->twice();
        $location->shouldReceive('contains')
            ->with('authentication/test_auth_type/form.html.twig')
            ->twice()
            ->andReturn(false);
        $location->shouldReceive('contains')
            ->with('authentication/test_auth_type/form.php')
            ->twice()
            ->andReturn($this->record);

        $this->fileLocator->shouldReceive('getFilesystem')
            ->twice()
            ->andReturn(new Filesystem());
        $this->fileLocator->shouldReceive('getSearchLocations')
            ->twice()
            ->andReturn([$location]);
        $this->templateService->shouldReceive('renderTemplate')->andReturn('foo');

        $this->fileLocator->expects('addPackageLocation')->with('fake_package');

        ob_start();
        $this->authenticationType->renderForm();
        ob_end_clean();
    }


    public function setUp(): void
    {
        $this->file = 'test/file';
        $this->exists = true;
        $this->record = Mockery::mock(FileLocator\Record::class);
        $this->record->shouldReceive('getFile')->andReturnUsing(function () {
            return $this->file;
        });
        $this->record->shouldReceive('exists')->andReturnUsing(function () {
            return $this->exists;
        });
        $this->fileLocator = Mockery::mock(FileLocator::class);
        $this->templateVariantLocator = Mockery::mock(TemplateVariantLocator::class);
        $this->templateService = Mockery::mock(TemplateService::class);

        $this->authenticationType = new class($this->fileLocator, $this->templateService) extends AuthenticationType {
            protected $authTypeHandle = 'test_auth_type';
            /** @var string|false */
            public $pkgHandle = false;
            /** @var TemplateVariantLocator|null */
            public $templateVariantLocator;
            /** @return string|false */
            public function getPackageHandle()
            {
                return $this->pkgHandle;
            }

            protected function getTemplateVariantLocator(): TemplateVariantLocator
            {
                return $this->templateVariantLocator ?: parent::getTemplateVariantLocator();
            }
        };
        $this->authenticationType->templateVariantLocator = $this->templateVariantLocator;
    }

    protected function setFullyDefinedController(): void
    {
        $this->authenticationType->controller = new Class() {
            public $sets = ['foo'];
            public function hook(): void
            {
                $this->sets[] = 'hook';
            }
            public function hooked(): void
            {
                $this->sets[] = 'hooked';
            }
            public function form(): void
            {
                $this->sets[] = 'form';
            }
            public function edit(): void
            {
                $this->sets[] = 'edit';
            }
            public function type_form(): void
            {
                $this->sets[] = 'type_form';
            }
            public function custom(): void
            {
                $this->sets[] = 'custom';
            }
            public function view(): void
            {
                $this->sets[] = 'view';
            }
            public function getSets(): array
            {
                return $this->sets;
            }
        };
    }

    protected function setEditController(): void
    {
        $this->authenticationType->controller = new Class() {
            public $sets = ['foo'];
            public function edit(): void
            {
                $this->sets[] = 'edit';
            }
            public function getSets(): array
            {
                return $this->sets;
            }
        };
    }

    protected function setViewController(): void
    {
        $this->authenticationType->controller = new Class() {
            public $sets = ['foo'];
            public function view(): void
            {
                $this->sets[] = 'view';
            }
            public function getSets(): array
            {
                return $this->sets;
            }
        };
    }
}
