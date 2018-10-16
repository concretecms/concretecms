<?php

namespace Concrete\Controller\Element\Search\Files;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\File\Image\BitmapFormat;
use Concrete\Core\File\StorageLocation\StorageLocation;

class Header extends ElementController
{
    protected $query;
    protected $includeBreadcrumb = false;

    /**
     * @param bool $includeBreadcrumb
     */
    public function setIncludeBreadcrumb($includeBreadcrumb)
    {
        $this->includeBreadcrumb = $includeBreadcrumb;
    }

    public function __construct(Query $query = null)
    {
        $this->query = $query;
        parent::__construct();
    }

    public function getElement()
    {
        return 'files/search_header';
    }

    public function view()
    {
        $config = $this->app->make('config');
        $bitmapFormat = $this->app->make(BitmapFormat::class);
        $storageLocations = StorageLocation::getList();
        $locations = [];
        foreach ($storageLocations as $location) {
            $locations[$location->getID()] = $location->getName();
        }
        $this->set('currentFolder', 0);
        $this->set('includeBreadcrumb', $this->includeBreadcrumb);
        $this->set('addFolderAction', \URL::to('/ccm/system/file/folder/add'));
        $this->set('locations', $locations);
        $this->set('query', $this->query);
        $this->set('form', \Core::make('helper/form'));
        $this->set('token', \Core::make('token'));
        $this->set('breadcrumbClass', 'ccm-file-manager-breadcrumb');
        $imageMaxWidth = (int) $config->get('concrete.file_manager.restrict_max_width');
        $this->set('imageMaxWidth', $imageMaxWidth > 0 ? $imageMaxWidth : null);
        $imageMaxHeight = (int) $config->get('concrete.file_manager.restrict_max_height');
        $this->set('imageMaxHeight', $imageMaxHeight > 0 ? $imageMaxHeight : null);
        $this->set('jpegQuality', $bitmapFormat->getDefaultJpegQuality());
    }
}
