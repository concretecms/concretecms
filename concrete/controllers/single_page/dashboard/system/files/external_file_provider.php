<?php /** @noinspection PhpInconsistentReturnPointsInspection */

namespace Concrete\Controller\SinglePage\Dashboard\System\Files;

use Concrete\Core\File\ExternalFileProvider\ExternalFileProviderFactory;
use Concrete\Core\File\ExternalFileProvider\Type\Type;
use Concrete\Core\Entity\File\ExternalFileProvider\Type\Type as TypeEntity;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Entity\File\ExternalFileProvider\ExternalFileProvider as ExternalFileProviderEntity;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Utility\Service\Validation\Strings;

class ExternalFileProvider extends DashboardPageController
{
    /** @var ResponseFactory */
    protected $responseFactory;

    public function on_start()
    {
        parent::on_start();
        $this->responseFactory = $this->app->make(ResponseFactory::class);
    }

    public function view()
    {
        $this->set('externalFileProviders', $this->app->make(ExternalFileProviderFactory::class)->fetchList());
        $types = [];

        foreach (Type::getList() as $type) {
            $types[$type->getID()] = $type->getName();
        }

        $this->set('types', $types);
    }

    public function select_type()
    {
        $efpTypeID = $this->request('efpTypeID');
        $type = Type::getByID($efpTypeID);
        $this->set('type', $type);
    }

    public function external_file_provider_added()
    {
        $this->set('message', t('External file provider added.'));
        $this->view();
    }

    public function external_file_provider_deleted()
    {
        $this->set('message', t('External file provider removed.'));
        $this->view();
    }

    public function external_file_provider_updated()
    {
        $this->set('message', t('External file provider saved.'));
        $this->view();
    }

    public function edit($efpID = false)
    {
        $externalFileProvider = $efpID ? $this->app->make(ExternalFileProviderFactory::class)->fetchByID($efpID) : null;

        if ($externalFileProvider !== null) {
            /* @var ExternalFileProviderEntity $externalFileProvider */
            $this->set('externalFileProvider', $externalFileProvider);
            $this->set('type', $externalFileProvider->getTypeObject());
        } else {
            return $this->responseFactory->redirect(Url::to('/dashboard/system/files/external_file_provider'));
        }
    }

    /**
     * @return ExternalFileProviderEntity|null
     */
    protected function validateStorageRequest()
    {
        /** @var Strings $val */
        $val = $this->app->make(Strings::class);

        $type = Type::getByID($this->request->get('efpTypeID'));

        if (!$type instanceof TypeEntity) {
            $this->error->add(t('Invalid type object.'));
        } else {
            $e = $type->getConfigurationObject()->validateRequest($this->request);

            if (is_object($e)) {
                $this->error->add($e);
            }
        }

        if (!$val->notempty($this->request->request->get('efpName'))) {
            $this->error->add(t('Your external file provider must have a name.'));
        }

        return $type;
    }

    public function update()
    {
        $type = $this->validateStorageRequest();
        $post = $this->request->request;

        $efpID = $post->get('efpID');
        $efp = $efpID ? $this->app->make(ExternalFileProviderFactory::class)->fetchByID($efpID) : null;

        /* @var ExternalFileProviderEntity|null $efp */
        if (!$this->token->validate('update')) {
            $this->error->add($this->token->getErrorMessage());
        }

        if ($efp === null) {
            $this->error->add(t('Invalid external file provider object.'));
        }

        if (!$this->error->has()) {
            $configuration = $type->getConfigurationObject();
            $configuration->loadFromRequest($this->request);
            $efp->setName($post->get('efpName'));

            $efp->setConfigurationObject($configuration);
            $efp->save();

            return $this->responseFactory->redirect(Url::to('/dashboard/system/files/external_file_provider', 'external_file_provider_updated'));
        }

        $this->edit($efpID);
    }

    public function delete()
    {
        if (!$this->token->validate('delete')) {
            $this->error->add($this->token->getErrorMessage());
        }

        $efpID = $this->request->request->get('efpID');

        $efp = $efpID ? $this->app->make(ExternalFileProviderFactory::class)->fetchByID($efpID) : null;

        /* @var ExternalFileProviderEntity|null $efp */
        if ($efp === null) {
            $this->error->add(t('Invalid external file provider object.'));
        }

        if (!$this->error->has()) {
            $efp->delete();
            return $this->responseFactory->redirect(Url::to('/dashboard/system/files/external_file_provider', 'external_file_provider_deleted'));
        }

        $this->edit($efpID);
    }

    public function add()
    {
        $type = $this->validateStorageRequest();

        if (!$this->token->validate('add')) {
            $this->error->add($this->token->getErrorMessage());
        }

        if (!$this->error->has()) {
            $configuration = $type->getConfigurationObject();
            $configuration->loadFromRequest($this->request);
            $factory = $this->app->make(ExternalFileProviderFactory::class);
            /* @var ExternalFileProviderFactory $factory */
            $externalFileProvider = $factory->create($configuration, $this->request->request->get('efpName'));
            $factory->persist($externalFileProvider);
            return $this->responseFactory->redirect(Url::to('/dashboard/system/files/external_file_provider', 'external_file_provider_added'));
        }

        $this->set('type', $type);
    }
}
