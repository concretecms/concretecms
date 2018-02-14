<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Files\Thumbnails;

use Concrete\Core\File\Image\BitmapFormat;
use Concrete\Core\File\Image\Thumbnail\ThumbnailFormatService;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Url\Resolver\Manager\ResolverManager;

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
            $thumbnail_format = BitmapFormat::FORMAT_JPEG;
        }
        $this->set('thumbnail_format', $thumbnail_format);
        $this->set('create_high_dpi_thumbnails', (bool) $config->get('concrete.file_manager.images.create_high_dpi_thumbnails'));
        $imageOptionsURL = null;
        $p = Page::getByPath('/dashboard/system/files/image_uploading');
        if ($p && !$p->isError()) {
            $pp = new Checker($p);
            if ($pp->canView()) {
                $imageOptionsURL = (string) $this->app->make(ResolverManager::class)->resolve([$p]);
            }
        }
        $this->set('imageOptionsURL', $imageOptionsURL);
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
            $create_high_dpi_thumbnails = (bool) $this->request->request('create_high_dpi_thumbnails');
            if (!$this->error->has()) {
                $config->save('concrete.misc.basic_thumbnailer_generation_strategy', $thumbnail_generation_strategy);
                $config->save('concrete.misc.default_thumbnail_format', $thumbnail_format);
                $config->save('concrete.file_manager.images.create_high_dpi_thumbnails', $create_high_dpi_thumbnails);
                $this->flash('message', t('Thumbnail options have been successfully saved.'));

                return $this->app->make(ResponseFactoryInterface::class)->redirect($this->action(''), 302);
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
        $this->view();
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
            BitmapFormat::FORMAT_PNG => t('Always create PNG thumbnails (slightly bigger file size, transparency is kept)'),
            BitmapFormat::FORMAT_JPEG => t('Always create JPEG thumbnails (slightly smaller file size, transparency is not available)'),
            ThumbnailFormatService::FORMAT_AUTO => t('Automatic: create a JPEG thumbnail if the source image is in JPEG format, otherwise create a PNG thumbnail'),
        ];
    }
}
