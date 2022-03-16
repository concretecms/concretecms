<?php

namespace Concrete\Controller\Dialog\File;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\File\Component\Chooser\ChooserConfiguration;
use Concrete\Core\File\Component\Chooser\ChooserConfigurationInterface;
use Concrete\Core\File\Component\Chooser\FilterCollectionFactory;
use Concrete\Core\File\Filesystem;
use Concrete\Core\Foundation\Service\ProviderList;
use Concrete\Core\Permission\Checker;

class Search extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/file/search';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
    }

    public function view()
    {
        $configuration = $this->app->make(ChooserConfigurationInterface::class);
        $filters = $this->app->make(FilterCollectionFactory::class)->createFromRequest($this->request);
        if ($filters) {
            $configuration->setFilters($filters);
        }
        $this->set('configuration', $configuration);
        $this->set('multipleSelection', $this->request->query->getBoolean('multipleSelection') || $this->request->request->getBoolean('multipleSelection'));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    protected function canAccess()
    {
        $folder = $this->filesystem->getRootFolder();
        $cp = new Checker($folder);

        return $cp->canSearchFiles() || $cp->canAddFile();
    }
}
