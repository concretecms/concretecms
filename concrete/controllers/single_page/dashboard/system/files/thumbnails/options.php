<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Files\Thumbnails;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Http\Response;
use Exception;
use Throwable;
use Imagine\Image\Box;

class Options extends DashboardPageController
{
    public function view()
    {
        $config = $this->app->make('config');
        $this->set('thumbnail_formats', $this->getThumbnailsFormats());
        $this->set('thumbnail_format', $config->get('concrete.misc.default_thumbnail_format'));
        $this->set('jpeg_compression', $config->get('concrete.misc.default_jpeg_image_compression'));
        $this->set('png_compression', $config->get('concrete.misc.default_png_image_compression'));
        $this->set('manipulation_library', $config->get('concrete.file_manager.images.manipulation_library'));
        $this->set('manipulation_libraries', $this->getManipulationLibraries());
    }

    public function submit()
    {
        if ($this->token->validate('thumbnails-options')) {
            $config = $this->app->make('config');
            $thumbnail_format = $this->request->request('thumbnail_format', '');
            if (!array_key_exists($thumbnail_format, $this->getThumbnailsFormats())) {
                $this->error->add(t('Invalid thumbnail format'));
            }
            $jpeg_compression = (int) $this->request->request('jpeg_compression', -1);
            if ($jpeg_compression < 0 || $jpeg_compression > 100) {
                $this->error->add(t('Invalid JPEG compression level'));
            }
            $png_compression = (int) $this->request->request('png_compression', -1);
            if ($png_compression < 0 || $png_compression > 9) {
                $this->error->add(t('Invalid PNG compression level'));
            }
            $manipulation_library = $this->request->request('manipulation_library', '');
            if (!array_key_exists($manipulation_library, $this->getManipulationLibraries())) {
                $this->error->add(t('Invalid image manipulation library'));
            }
            if (!$this->error->has()) {
                $config->save('concrete.misc.default_thumbnail_format', $thumbnail_format);
                $config->save('concrete.misc.default_jpeg_image_compression', $jpeg_compression);
                $config->save('concrete.misc.default_png_image_compression', $png_compression);
                $config->save('concrete.file_manager.images.manipulation_library', $manipulation_library);
                $this->flash('message', t('Thumbnail options have been succesfully saved.'));
                $this->redirect($this->app->make('url/manager')->resolve(['/dashboard/system/files/thumbnails']));
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
            $this->view();
        }
    }

    protected function getThumbnailsFormats()
    {
        return [
            'png' => t('Always create PNG thumbnails (slightly bigger file size, transparency is kept)'),
            'jpg' => t('Always create JPEG thumbnails (slightly smaller file size, transparency is not available)'),
            'auto' => t('Automatic: create a JPEG thumbnail if the source image is in JPEG format, otherwise create a PNG thumbnail'),
        ];
    }

    protected function getManipulationLibraries()
    {
        return [
            'gd' => t('GD Library: faster, available in almost any environment, but less powerful'),
            'imagick' => t('ImageMagick Library: much more powerful, but often not available or misconfigured'),
        ];
    }

    public function test_manipulation_library($handle, $token)
    {
        $rf = $this->app->make(ResponseFactoryInterface::class);
        if ($this->token->validate('thumbnail-check-library-' . $handle, $token)) {
            if ($this->app->bound('image/' . $handle)) {
                $error = null;
                try {
                    $library = $this->app->make('image/' . $handle);
                    $image = $library->create(new Box(1, 1));
                    $image->show('png');
                    die();
                } catch (Exception $x) {
                    $error = $x;
                } catch (Throwable $x) {
                    $error = $x;
                }
                $response = $rf->create($error->getMessage(), Response::HTTP_SERVICE_UNAVAILABLE);
            } else {
                $response = $rf->create($handle, Response::HTTP_SERVICE_UNAVAILABLE);
            }
        } else {
            $response = $rf->create($this->token->getErrorMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $response;
    }
}
