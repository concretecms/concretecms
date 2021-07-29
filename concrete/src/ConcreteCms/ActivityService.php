<?php
namespace Concrete\Core\ConcreteCms;

use Concrete\Core\Config\Repository\Repository;
use Config;
use Marketplace;
use Concrete\Core\File\Service\File;

class ActivityService
{

    /**
     * @var File
     */
    protected $fileService;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var ActivitySlotItem[]|null
     */
    protected $slots;

    public function __construct(Repository $config, File $fileService)
    {
        $this->fileService = $fileService;
        $this->config = $config;
    }

    /**
     * @return ActivitySlotItem[]|null
     */
    public function getSlotContents()
    {
        if ($this->slots === null) {
            $appVersion = $this->config->get('concrete.version');
            $url = $this->config->get('concrete.urls.activity_slots');
            $path = $url . '?appVersion=' . $appVersion;
            $response = $this->fileService->getContents($path);

            $nsi = new ActivitySlotItem();
            $this->slots = $nsi->parseResponse($response);
        }

        return $this->slots;
    }
}
