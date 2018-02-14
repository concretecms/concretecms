<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Files;

use Concrete\Core\File\Image\BitmapFormat;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Url\Resolver\Manager\ResolverManager;
use Exception;
use Imagine\Image\Box;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ImageUploading extends DashboardPageController
{
    public function view()
    {
        $config = $this->app->make('config');
        $bitmapFormat = $this->app->make(BitmapFormat::class);

        $this->set('manipulation_libraries', $this->getManipulationLibraries());
        $this->set('manipulation_library', $config->get('concrete.file_manager.images.manipulation_library'));

        $this->set('jpeg_quality', $bitmapFormat->getDefaultJpegQuality());
        $this->set('png_compression', $bitmapFormat->getDefaultPngCompressionLevel());

        $this->set('restrict_max_width', (int) $config->get('concrete.file_manager.restrict_max_width'));
        $this->set('restrict_max_height', (int) $config->get('concrete.file_manager.restrict_max_height'));

        $this->set('use_exif_data_to_rotate_images', (bool) $config->get('concrete.file_manager.images.use_exif_data_to_rotate_images'));

        $thumbnailOptionsURL = null;
        $p = Page::getByPath('/dashboard/system/files/thumbnails/options');
        if ($p && !$p->isError()) {
            $pp = new Checker($p);
            if ($pp->canView()) {
                $thumbnailOptionsURL = (string) $this->app->make(ResolverManager::class)->resolve([$p]);
            }
        }
        $this->set('thumbnailOptionsURL', $thumbnailOptionsURL);
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

    public function save()
    {
        if ($this->token->validate('image-options')) {
            $post = $this->request->request;
            $valn = $this->app->make('helper/validation/numbers');
            $config = $this->app->make('config');

            $manipulation_library = $post->get('manipulation_library', '');
            if (!array_key_exists($manipulation_library, $this->getManipulationLibraries())) {
                $this->error->add(t('Invalid image manipulation library'));
            }

            $jpeg_quality = (int) $post->get('jpeg_quality');
            if ($valn->integer($jpeg_quality, 0, 100)) {
                $jpeg_quality = (int) $jpeg_quality;
            } else {
                $this->error->add(t('Invalid JPEG quality level'));
            }
            $png_compression = $post->get('png_compression');
            if ($valn->integer($png_compression, 0, 9)) {
                $png_compression = (int) $png_compression;
            } else {
                $this->error->add(t('Invalid PNG compression level'));
            }

            $restrict_max_width = $post->get('restrict_max_width');
            if ($valn->integer($restrict_max_width, 1)) {
                $restrict_max_width = (int) $restrict_max_width;
            } else {
                $restrict_max_width = null;
            }
            $restrict_max_height = $post->get('restrict_max_height');
            if ($valn->integer($restrict_max_height, 1)) {
                $restrict_max_height = (int) $restrict_max_height;
            } else {
                $restrict_max_height = null;
            }

            $use_exif_data_to_rotate_images = (bool) $post->get('use_exif_data_to_rotate_images');

            if (!$this->error->has()) {
                $bitmapFormat = $this->app->make(BitmapFormat::class);
                $config->save('concrete.file_manager.images.manipulation_library', $manipulation_library);
                $bitmapFormat->setDefaultJpegQuality($jpeg_quality);
                $bitmapFormat->setDefaultPngCompressionLevel($png_compression);
                $config->save('concrete.file_manager.images.use_exif_data_to_rotate_images', $use_exif_data_to_rotate_images);
                $config->save('concrete.file_manager.restrict_max_width', $restrict_max_width);
                $config->save('concrete.file_manager.restrict_max_height', $restrict_max_height);
                $this->flash('success', t('Image options saved.'));

                return $this->app->make(ResponseFactoryInterface::class)->redirect($this->action(''), 302);
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
        $this->view();
    }

    protected function getManipulationLibraries()
    {
        return [
            'gd' => t('GD Library: faster, available in almost any environment, but less powerful'),
            'imagick' => t('ImageMagick Library: much more powerful, but often not available or misconfigured'),
        ];
    }
}
