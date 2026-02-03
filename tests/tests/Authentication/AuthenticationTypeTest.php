<?php

namespace Concrete\Tests\Authentication;

use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Authentication\AuthenticationTypeController;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Filesystem\TemplateService;
use Concrete\Tests\TestCase;
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

        $this->fileLocator->expects('getRecord')
            ->with('authentication/test_auth_type/' . $method, true)
            ->andReturn($this->record);
        $this->templateService->expects('renderTemplate')
            ->with($this->file, ['foo', $method], $this->authenticationType)
            ->andReturn('foo');

        ob_start();
        $this->authenticationType->{'render' . camelcase($method)}();
        $actual = ob_get_clean();
        $this->assertEquals($actual, 'foo');
    }

    public function basicRenderProvider()
    {
        return [['hook'], ['hooked']];
    }

    public function testRendersForm(): void
    {
        $this->setFullyDefinedController();

        $this->fileLocator->expects('getRecord')
            ->times(2)
            ->with('authentication/test_auth_type/form', true)
            ->andReturn($this->record);
        $this->templateService->expects('renderTemplate')
            ->with($this->file, ['foo', 'form'], $this->authenticationType)
            ->andReturn('foo');

        ob_start();
        $this->authenticationType->renderForm();
        $actual = ob_get_clean();
        $this->assertEquals($actual, 'foo');
    }

    public function testRendersTypeForm(): void
    {
        $this->setFullyDefinedController();

        $this->fileLocator->expects('getRecord')
            ->times(2)
            ->with('authentication/test_auth_type/type_form', true)
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

        $this->fileLocator->expects('getRecord')
            ->times(2)
            ->with('authentication/test_auth_type/type_form', true)
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

        $this->fileLocator->expects('getRecord')
            ->times(2)
            ->with('authentication/test_auth_type/form', true)
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

        $this->fileLocator->shouldReceive('getRecord')->andReturn($this->record);
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
        $this->templateService = Mockery::mock(TemplateService::class);

        $this->authenticationType = new class($this->fileLocator, $this->templateService) extends AuthenticationType {
            protected $authTypeHandle = 'test_auth_type';
            /** @var string|false */
            public $pkgHandle = false;
            /** @return string|false */
            public function getPackageHandle()
            {
                return $this->pkgHandle;
            }
        };
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