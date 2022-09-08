<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Files;

use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\File\Upload\ClientSideUploader;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\Utility\Service\Number;
use Symfony\Component\HttpFoundation\Response;

class Uploads extends DashboardPageController
{
    public function view(): ?Response
    {
        $service = $this->app->make(ClientSideUploader::class);
        $this->set('numberService', $this->app->make(Number::class));
        $this->set('urlResolver', $this->app->make(ResolverManagerInterface::class));
        $this->set('chunkEnabled', $service->isChunkingEnabled());
        $this->set('chunkSize', $service->getConfiguredChunkSize());
        $this->set('phpMaxUploadSize', $service->getPHPMaxFileSize());
        $this->set('parallelUploads', $service->getParallelUploads());
        $maxImageSizePage = null;
        if ($service->supportClientSizeImageResizing()) {
            $maxImageSizePage = Page::getByPath('/dashboard/system/files/image_uploading');
            if (!$maxImageSizePage || $maxImageSizePage->isError()) {
                $maxImageSizePage = null;
            }
        }
        $this->set('maxImageSizePage', $maxImageSizePage);
        return null;
    }

    public function submit(): ?Response
    {
        $post = $this->request->request;
        $service = $this->app->make(ClientSideUploader::class);

        if (!$this->token->validate('ccm-system-files-uploads')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if ($post->get('chunkEnabled')) {
            $raw = trim($post->get('chunkSizeValue', ''));
            $chunkSizeValue = is_numeric($raw) ? (int) $raw : 0;
            $raw = trim($post->get('chunkSizeUnit', ''));
            $chunkSizeUnit = is_numeric($raw) ? (int) $raw : 0;
            $chunkSize = $chunkSizeValue * $chunkSizeUnit;
            if ($chunkSize <= 0) {
                $this->error->add(t('Please specify the size of the file chunks.'));
            } else {
                $maxChunkSize = $service->getPHPMaxFileSize();
                if ($maxChunkSize !== null && $chunkSize > $maxChunkSize) {
                    $this->error->add(t('The size of the file chunks should not be greather than %s.', $this->app->make(Number::class)->formatSize($maxChunkSize)));
                }
            }
        } else {
            $chunkSize = null;
        }
        $raw = trim($post->get('parallelUploads', ''));
        $parallelUploads = is_numeric($raw) ? (int) $raw : 0;
        if ($parallelUploads < 1) {
            $this->error->add(t('Please specify the number of parallel uploads.'));
        }

        if ($this->error->has()) {
            return $this->view();
        }

        $service
            ->setChunkingEnabled($chunkSize > 0)
            ->setParallelUploads($parallelUploads)
        ;
        if ($chunkSize > 0) {
            $service
                ->setConfiguredChunkSize($chunkSize)
                ->setChunkingEnabled(true)
            ;
        } else {
            $service->setChunkingEnabled(false);
        }
        $this->flash('success', t('Options saved successfully.'));

        return $this->app->make(ResponseFactoryInterface::class)->redirect($this->action(''), 302);
    }
}
