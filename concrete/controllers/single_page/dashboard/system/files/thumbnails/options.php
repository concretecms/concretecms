<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Files\Thumbnails;

use Concrete\Core\File\Image\Thumbnail\ThumbnailFormatService;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\DashboardPageController;
use Exception;
use Imagine\Image\Box;
use Throwable;

class Options extends DashboardPageController
{
    public function view()
    {
        $config = $this->app->make('config');
        $this->set('thumbnail_generation_strategies', $this->getThumbnailGenerationStrategies());
        $this->set('thumbnail_generation_strategy', $config->get('concrete.misc.basic_thumbnailer_generation_strategy'));
        $this->set('thumbnail_formats', $this->getThumbnailsFormats());
        $thumbnail_format = $config->get('concrete.misc.default_thumbnail_format');
        if ($thumbnail_format === 'jpg') {
            $thumbnail_format = ThumbnailFormatService::FORMAT_JPEG;
        }
        $this->set('thumbnail_format', $thumbnail_format);
        $this->set('jpeg_compression', $config->get('concrete.misc.default_jpeg_image_compression'));
        $this->set('png_compression', $config->get('concrete.misc.default_png_image_compression'));
        $this->set('manipulation_library', $config->get('concrete.file_manager.images.manipulation_library'));
        $this->set('create_high_dpi_thumbnails', $config->get('concrete.file_manager.images.create_high_dpi_thumbnails'));
        $this->set('manipulation_libraries', $this->getManipulationLibraries());
    }

    public function submit()
    {
        if ($this->token->validate('thumbnails-options')) {
            $config = $this->app->make('config');
            $thumbnail_generation_strategy = $this->request->request('thumbnail_generation_strategy', '');
            if (!array_key_exists($thumbnail_generation_strategy, $this->getThumbnailGenerationStrategies())) {
                $this->error->add(t('Invalid thumbnail generation strategy'));
            }
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
            $create_high_dpi_thumbnails = $this->request->request('create_high_dpi_thumbnails', 0);
            if (!$this->error->has()) {
                $config->save('concrete.misc.basic_thumbnailer_generation_strategy', $thumbnail_generation_strategy);
                $config->save('concrete.misc.default_thumbnail_format', $thumbnail_format);
                $config->save('concrete.misc.default_jpeg_image_compression', $jpeg_compression);
                $config->save('concrete.misc.default_png_image_compression', $png_compression);
                $config->save('concrete.file_manager.images.manipulation_library', $manipulation_library);
                $config->save('concrete.file_manager.images.create_high_dpi_thumbnails', $create_high_dpi_thumbnails);
                $this->flash('message', t('Thumbnail options have been successfully saved.'));
                $this->redirect($this->app->make('url/manager')->resolve(['/dashboard/system/files/thumbnails']));
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
            $this->view();
        }
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

    protected function getThumbnailGenerationStrategies()
    {
        return [
            'now' => t('Create the thumbnails synchronously (may fail with out-of-memory errors)'),
            'async' => t('Create the thumbnails asynchronously (users may not see thumbnails immediately)'),
        ];
    }

    protected function getThumbnailsFormats()
    {
        return [
            ThumbnailFormatService::FORMAT_PNG => t('Always create PNG thumbnails (slightly bigger file size, transparency is kept)'),
            ThumbnailFormatService::FORMAT_JPEG => t('Always create JPEG thumbnails (slightly smaller file size, transparency is not available)'),
            ThumbnailFormatService::FORMAT_AUTO => t('Automatic: create a JPEG thumbnail if the source image is in JPEG format, otherwise create a PNG thumbnail'),
        ];
    }

    protected function getManipulationLibraries()
    {
        return [
            'gd' => t('GD Library: faster, available in almost any environment, but less powerful'),
            'imagick' => t('ImageMagick Library: much more powerful, but often not available or misconfigured'),
        ];
    }
}
