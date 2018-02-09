<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Files;

use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Url\Resolver\Manager\ResolverManager;

class ImageUploading extends DashboardPageController
{
    public $helpers = ['form', 'concrete/ui', 'validation/token', 'concrete/file'];

    public function view()
    {
        $config = $this->app->make('config');
        $this->set('restrict_uploaded_image_sizes', (bool) $config->get('concrete.file_manager.restrict_uploaded_image_sizes'));
        $this->set('restrict_max_width', (int) $config->get('concrete.file_manager.restrict_max_width'));
        $this->set('restrict_max_height', (int) $config->get('concrete.file_manager.restrict_max_height'));
    }

    public function save()
    {
        if ($this->token->validate('image_uploading')) {
            $post = $this->request->request;
            $config = $this->app->make('config');
            $restrict_max_width = (int) $post->get('restrict_max_width');
            if ($restrict_max_width < 1) {
                $restrict_max_width = null;
            }
            $restrict_max_height = (int) $post->get('restrict_max_height');
            if ($restrict_max_height < 1) {
                $restrict_max_height = null;
            }
            $restrict_uploaded_image_sizes = $post->get('restrict_uploaded_image_sizes') && ($restrict_max_width !== null || $restrict_max_height !== null);

            $config->save('concrete.file_manager.restrict_uploaded_image_sizes', $restrict_uploaded_image_sizes);
            $config->save('concrete.file_manager.restrict_max_width', $restrict_max_width);
            $config->save('concrete.file_manager.restrict_max_height', $restrict_max_height);
            $this->flash('success', t('Image uploading settings saved.'));
            $to = $this->app->make(ResolverManager::class)->resolve([$this->action('')]);

            return $this->app->make(ResponseFactoryInterface::class)->redirect($to, 302);
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
    }
}
