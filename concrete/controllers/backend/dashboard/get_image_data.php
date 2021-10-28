<?php

namespace Concrete\Controller\Backend\Dashboard;

use Concrete\Core\Cache\Level\ObjectCache;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\File\Service\File;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Marketplace\Marketplace;
use Concrete\Core\Url\Url;
use Concrete\Core\Url\UrlInterface;
use Stash\Interfaces\ItemInterface;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class GetImageData extends AbstractController
{
    public function view(): Response
    {
        $imageData = '';
        $baseUrl = $this->getBaseUrl();
        if ($baseUrl !== '') {
            $image = $this->getImage();
            if ($image !== '') {
                $cacheItem = $this->getCacheItem($image);
                if ($cacheItem->isMiss()) {
                    $cacheItem->lock();
                    $imageDataUrl = $this->getImageDataUrl($baseUrl, $image);
                    $imageData = $this->fetchImageData($imageDataUrl);
                    $cacheItem->set($imageData);
                } else {
                    $imageData = $cacheItem->get();
                }
            }
        }

        return $this->buildResponse($imageData);
    }

    protected function getBaseUrl(): string
    {
        return (string) $this->app->make('config')->get('concrete.urls.background_info');
    }

    protected function getImage(): string
    {
        $image = $this->request->request->get('image', $this->request->query->get('image'));

        return is_string($image) && preg_match('/([0-9]+)\.jpg/i', $image) ? $image : '';
    }

    protected function getCacheItem(string $image): ItemInterface
    {
        return $this->app->make(ObjectCache::class)->getItem("dashboard_image_data/{$image}");
    }

    protected function getImageDataUrl(string $baseUrl, string $image): UrlInterface
    {
        $url = Url::createFromUrl($baseUrl, false);
        $url->getQuery()->modify([
            'image' => $image,
            'cfToken' => Marketplace::getSiteToken(),
        ]);

        return $url;
    }

    protected function fetchImageData(UrlInterface $imageDataUrl): string
    {
        return (string) $this->app->make(File::class)->getContents((string) $imageDataUrl);
    }

    protected function parseImageDataJson(string $imageData): ?array
    {
        if ($imageData === '') {
            return null;
        }
        set_error_handler(static function () {}, -1);
        $decoded = json_decode($imageData, true);
        restore_error_handler();

        return is_array($decoded) ? $decoded : null;
    }

    protected function buildResponse(string $imageData): Response
    {
        $responseFactory = $this->app->make(ResponseFactoryInterface::class);
        if ($imageData === '') {
            return $responseFactory->json(null);
        }
        $decoded = $this->parseImageDataJson($imageData);

        return $decoded === null ? $responseFactory->create($imageData) : $responseFactory->json($decoded);
    }
}
